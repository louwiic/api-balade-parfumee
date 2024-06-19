<?php

namespace App\Controller;

use App\Entity\Fragrance;
use App\Entity\Wishlist;
use App\Repository\FragranceRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FragranceController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FragranceRepository $fragranceRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FragranceRepository $fragranceRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->fragranceRepository = $fragranceRepository;
        $this->userRepository = $userRepository;
    }
    #[Route('api/fragrance', name: 'app_getAllFragrance')]
    public function getAllFragrance(Request $request): Response
    {
        $fragrances = $this->fragranceRepository->findAll();
        $val = [];

        foreach ($fragrances as $fragrance) {
            if ($fragrance instanceof Fragrance) {
                $val[] = [
                    "id" => $fragrance->getId(),
                    "brand" => $fragrance->getBrand(),
                    "concentration" => $fragrance->getConcentration(),
                    "createAt" => $fragrance->getCreateAt(),
                    "value" => $fragrance->getName(),
                    "name" => $fragrance->getName(),
                    "img" => $fragrance->getImg(),
                    "description" => $fragrance->getDescription(),
                ];
            }
        }
        return new JsonResponse($val, Response::HTTP_OK);
    }

    #[Route('api/wishlist', name: 'app_create_wishlist', methods: "POST")]
    public function createWishlist(Request $request, Fragrance $f = null, StripeController $stripe): Response
    {

        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $wishProducts = $user->getWishlists();

        $list = [];
        foreach ($wishProducts as $wishProduct) {
            if ($wishProduct instanceof Wishlist && !$wishProduct->getDeleteAt()) {
                $list[] =  [
                    "id" => $wishProduct->getId(),
                ];
            }
        }
        $subscribed = $stripe->_checkSubscription(userRepository: $this->userRepository);

        if (!isset($subscribed) && count($list) >= 5) {
            return new JsonResponse(["message" => 'limit trialsheet add exceeded'], Response::HTTP_NOT_FOUND);
        }

        if (isset($subscribed) && count($list) >= 5) {
            if ($subscribed["subscription_is_not_expired"] === false || $subscribed['subscription']['status'] !== "active") {
                return new JsonResponse(["message" => 'limit trialsheet add exceeded'], Response::HTTP_NOT_FOUND);
            }
        }

        $newWishlist = new Wishlist();
        $newWishlist->setCreateAt(new DateTimeImmutable());
        $newWishlist->setUser($user);

        $this->entityManager->persist($newWishlist);
        $this->entityManager->flush();
        return new JsonResponse(["id" => $newWishlist->getId()], Response::HTTP_OK);
    }

    #[Route('api/wishlist/{wishlist}/{fragrance}', name: 'app_put_wishlist', methods: "PUT")]
    #[OA\Parameter(name: 'fragrance', in: "path", required: false)]
    public function PUTWishlist(Wishlist $wishlist, Fragrance $fragrance = null, StripeController $stripe): Response
    {

        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());

        if ($wishlist->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);


        $wishlist->setFragrance($fragrance);
        $wishlist->setUser($user);
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }
    #[Route('api/wishlist/{wishlist}', name: 'app_DELETE_wishlist', methods: "DELETE")]
    #[OA\Parameter(name: 'fragrance', in: "path", required: false)]
    public function DELETEWishlist(Wishlist $wishlist): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($wishlist->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $wishlist->setDeleteAt(new DateTimeImmutable());
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }

    #[Route('api/wishlist', name: 'app_getWishlist')]
    public function getWishlist(): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $list = [];

        foreach ($user->getWishlists() as $wishlist) {
            if ($wishlist instanceof Wishlist && !$wishlist->getDeleteAt()) {

                $fragrance = $wishlist->getFragrance();

                $list[] =  [
                    "id" => $wishlist->getId(),
                    "date" => $wishlist->getCreateAt()->format('d/m/y'),
                    "idFragrance" => $fragrance ? $fragrance->getId() : "",
                    "brand" =>  $fragrance ? $fragrance->getBrand() : "",
                    "concentration" =>  $fragrance ? $fragrance->getConcentration() : "",
                    "createAt" =>  $fragrance ? $fragrance->getCreateAt() : "",
                    "value" =>  $fragrance ? $fragrance->getName() : "",
                    "name" =>  $fragrance ? $fragrance->getName() : "",
                    "img" =>  $fragrance ? $fragrance->getImg() : "/pictogrammeParfum.png",
                    "description" =>  $fragrance ? $fragrance->getDescription() : "",
                ];
            }
        }
        return new JsonResponse($list, Response::HTTP_OK);
    }
}
