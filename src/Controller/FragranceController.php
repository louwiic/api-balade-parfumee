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
    )
    {
        $this->entityManager = $entityManager;
        $this->fragranceRepository = $fragranceRepository;
        $this->userRepository = $userRepository;
    }
    #[Route('/fragrance', name: 'app_getAllFragrance')]
    public function getAllFragrance(Request $request): Response {
        $fragrances = $this->fragranceRepository->findAll();
        $val = [];

        foreach ($fragrances as $fragrance) {
            if ($fragrance instanceof Fragrance) {
                $val [] = [
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

    #[Route('api/wishlist', name: 'app_create_wishlist',methods: "POST")]
    public function createWishlist(Request $request, Fragrance $f = null): Response {
        $fragrance = json_decode($request->getContent(), true);

        //return new JsonResponse(($fragrance));
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $wishlist = new Wishlist();        
        $wishlist->setCreateAt(new DateTimeImmutable());
        $wishlist->setUser($user);

        /* if($fragrance)  
            $fg = new Fragrance();
            $fg->setCreateAt(new DateTimeImmutable());
            $fg->setName($fragrance["name"]);            
            $fg->setBrand($fragrance["brand"]);
            $fg->setDescription($fragrance["description"]);            
            $fg->setValue($fragrance["value"]);            
            $fg->setImg("test.pgn");
            $fg->setConcentration($fragrance["concentration"]);    
            
            $this->entityManager->persist($fg);
            $this->entityManager->flush();        
        
            $wishlist->setFragrance($fg); */
        
        $this->entityManager->persist($wishlist);
        $this->entityManager->flush();
        return new JsonResponse(["id" => $wishlist->getId()], Response::HTTP_OK);
    }

    #[Route('api/wishlist/{wishlist}/{fragrance}', name: 'app_put_wishlist',methods: "PUT")]
    #[OA\Parameter(name: 'fragrance', in: "path", required: false)]
    public function PUTWishlist(Wishlist $wishlist, Fragrance $fragrance = null): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($wishlist->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);
            

        $wishlist->setFragrance($fragrance);
        $wishlist->setUser($user);
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }
    #[Route('api/wishlist/{wishlist}', name: 'app_DELETE_wishlist',methods: "DELETE")]
    #[OA\Parameter(name: 'fragrance', in: "path", required: false)]
    public function DELETEWishlist(Wishlist $wishlist): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($wishlist->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $wishlist->setDeleteAt(new DateTimeImmutable());
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }
    #[Route('api/wishlist', name: 'app_getWishlist')]
    public function getWishlist(): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $list = [];

        foreach ($user->getWishlists() as $wishlist) {
            if ($wishlist instanceof Wishlist && !$wishlist->getDeleteAt()) {

                $fragrance = $wishlist->getFragrance();

                $list [] =  [
                    "id" => $wishlist->getId(),
                    "date" => $wishlist->getCreateAt()->format('d/m/y'),
                    "idFragrance" => $fragrance ? $fragrance->getId() : "",
                    "brand" =>  $fragrance ? $fragrance->getBrand() : "",
                    "concentration" =>  $fragrance ? $fragrance->getConcentration(): "",
                    "createAt" =>  $fragrance ? $fragrance->getCreateAt(): "",
                    "value" =>  $fragrance ? $fragrance->getName(): "",
                    "name" =>  $fragrance ? $fragrance->getName(): "",
                    "img" =>  $fragrance ? $fragrance->getImg(): "/pictogrammeParfum.png",
                    "description" =>  $fragrance ? $fragrance->getDescription(): "",
                ];
            }
        }
        return new JsonResponse($list, Response::HTTP_OK);
    }

}