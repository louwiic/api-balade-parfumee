<?php

namespace App\Controller;

use App\Entity\CheckList;
use App\Entity\Fragrance;
use App\Entity\PerfumeTrialSheet;
use App\Entity\Wishlist;
use App\Repository\FragranceRepository;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PerfumeTrialSheetController extends AbstractController
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
    #[Route('api/perfumeTrialSheet/{m}/{Y}', name: 'app_create_perfumeTrialSheet', methods: "POST")]
    public function createPerfumeTrialSheet($m, $Y, StripeController $stripe): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $list = [];
        foreach ($user->getPerfumeTrialSheets() as $perfumeTrialSheet) {
            if ($perfumeTrialSheet instanceof PerfumeTrialSheet && !$perfumeTrialSheet->getDeleteAt()) {
                $list[] =  [
                    "id" => $perfumeTrialSheet->getId(),
                ];
            }
        }

        $subscribed = $stripe->_checkSubscription(userRepository: $this->userRepository);

        //return new JsonResponse(["message" => $subscribed ], Response::HTTP_ACCEPTED);

        if (!isset($subscribed) && count($list) >= 5) {
            return new JsonResponse(["message" => 'limit trialsheet add exceeded'], Response::HTTP_NOT_FOUND);
        }

        if (isset($subscribed) && count($list) >= 5) {
            if ($subscribed["subscription_is_not_expired"] === false || $subscribed['subscription']['status'] !== "active") {
                return new JsonResponse(["message" => 'limit trialsheet add exceeded'], Response::HTTP_NOT_FOUND);
            }
        }

        $perfumeTrialSheet = new PerfumeTrialSheet();
        if ($m && $Y)
            $perfumeTrialSheet->setCreateAt(DateTimeImmutable::createFromFormat('d/m/Y', "01/$m/$Y"));
        else
            $perfumeTrialSheet->setCreateAt(new DateTimeImmutable());

        $perfumeTrialSheet->setUser($user);
        $perfumeTrialSheet->setDominantNotes("");
        $perfumeTrialSheet->setEvolutionOfPerfume("");
        $perfumeTrialSheet->setImpression("");
        $perfumeTrialSheet->setMore("");
        $perfumeTrialSheet->setLess("");

        $this->entityManager->persist($perfumeTrialSheet);
        $this->entityManager->flush();

        return new JsonResponse([
            "id" => $perfumeTrialSheet->getId(),
            "createAt" =>  $perfumeTrialSheet->getCreateAt()->format('m/Y'),
            "dominantNotes" =>  $perfumeTrialSheet->getDominantNotes(),
            "perfumePerformance" =>  $perfumeTrialSheet->getEvolutionOfPerfume(),
            "impression" =>  $perfumeTrialSheet->getImpression(),
            "more" =>  $perfumeTrialSheet->getMore(),
            "less" =>  $perfumeTrialSheet->getLess(),
            "idFragrance" =>  "",
            "brand" =>  "",
            "concentration" =>  "",
            "value" =>   "",
            "name" =>  "",
            "img" =>  "/pictogrammeParfum.png",
            "description" => "",
        ], Response::HTTP_OK);
    }

    #[Route('api/perfumeTrialSheet/{perfumeTrialSheet}/{fragrance}', name: 'app_put_perfumeTrialSheet', methods: "PUT")]
    #[OA\Parameter(name: 'perfumeTrialSheet', in: "path", required: true)]
    #[OA\Parameter(name: 'fragrance', in: "path", required: false)]
    #[OA\Parameter(name: 'more', in: "query", required: false)]
    #[OA\Parameter(name: 'less', in: "query", required: false)]
    #[OA\Parameter(name: 'impression', in: "query", required: false)]
    #[OA\Parameter(name: 'perfumePerformance', in: "query", required: false)]
    #[OA\Parameter(name: 'dominantNotes', in: "query", required: false)]
    public function putPerfumeTrialSheet(PerfumeTrialSheet $perfumeTrialSheet, Fragrance $fragrance = null, Request $request): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($perfumeTrialSheet->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $data = json_decode($request->getContent(), true);
        foreach ($data as $key => $value) {
            match ($key) {
                'dominantNotes' => $perfumeTrialSheet->setDominantNotes($value),
                'more' => $perfumeTrialSheet->setMore($value),
                'less' => $perfumeTrialSheet->setLess($value),
                'impression' => $perfumeTrialSheet->setImpression($value),
                'perfumePerformance' => $perfumeTrialSheet->setEvolutionOfPerfume($value),
                default => null,
            };
        }
        if ($fragrance) {
            $perfumeTrialSheet->setFragrance($fragrance);
        }
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }

    #[Route('api/perfumeTrialSheet/{perfumeTrialSheet}', name: 'app_delete_trialsheet_checkList', methods: "DELETE")]
    #[OA\Parameter(name: 'perfumeTrialSheet', in: "path", required: true)]
    public function deletePerfumeTrialSheet(PerfumeTrialSheet $perfumeTrialSheet): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($perfumeTrialSheet->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $perfumeTrialSheet->setGetDeleteAt(new DateTimeImmutable());
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }
    #[Route('api/perfumeTrialSheet', name: 'app_get_perfumeTrialSheet')]
    public function getPerfumeTrialSheet(WishlistRepository $wishlistRepository): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $list = [];
        $wishlistId = [];
        $wishlist = $user->getWishlists();

        foreach ($wishlist as $wish) {
            if ($wish->getFragrance())
                $wishlistId[] = $wish->getFragrance()->getId();
        }
        foreach ($user->getPerfumeTrialSheets() as $perfumeTrialSheet) {
            if ($perfumeTrialSheet instanceof PerfumeTrialSheet && !$perfumeTrialSheet->getDeleteAt()) {

                $fragrance = $perfumeTrialSheet->getFragrance();

                $list[] =  [
                    "id" => $perfumeTrialSheet->getId(),
                    "createAt" =>  $perfumeTrialSheet->getCreateAt()->format('m/Y'),
                    "dominantNotes" =>  $perfumeTrialSheet->getDominantNotes(),
                    "perfumePerformance" =>  $perfumeTrialSheet->getEvolutionOfPerfume(),
                    "impression" =>  $perfumeTrialSheet->getImpression(),
                    "more" =>  $perfumeTrialSheet->getMore(),
                    "less" =>  $perfumeTrialSheet->getLess(),
                    "idFragrance" => $fragrance ? $fragrance->getId() : "",
                    'isInWishlist' => $fragrance && in_array($fragrance->getId(), $wishlistId),
                    "brand" =>  $fragrance ? $fragrance->getBrand() : "",
                    "concentration" =>  $fragrance ? $fragrance->getConcentration() : "",
                    "value" =>  $fragrance ? $fragrance->getName() : "",
                    "name" =>  $fragrance ? $fragrance->getName() : "",
                    "img" =>  $fragrance ? $fragrance->getImg() : "/pictogrammeParfum.png",
                    "description" =>  $fragrance ? $fragrance->getDescription() : "",
                    "dateFragrance" => $fragrance ? $fragrance->getCreateAt()->format('m/y') : "",
                ];
            }
        }
        return new JsonResponse($list, Response::HTTP_OK);
    }
}
