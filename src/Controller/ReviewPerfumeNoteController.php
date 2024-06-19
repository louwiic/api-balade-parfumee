<?php

namespace App\Controller;

use App\Entity\CheckList;
use App\Entity\Fragrance;
use App\Entity\PerfumeTrialSheet;
use App\Entity\ReviewPerfumeNote;
use App\Entity\Wishlist;
use App\Repository\FragranceRepository;
use App\Repository\ReviewPerfumeNoteRepository;
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

class ReviewPerfumeNoteController extends AbstractController
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
    #[Route('api/reviewPerfumeNote/{m}/{Y}', name: 'app_create_ReviewPerfumeNote', methods: "POST")]
    public function createReviewPerfumeNote($m, $Y, StripeController $stripe): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $reviewPerfumeNotes = $user->getReviewPerfumeNotes();
        $list = [];

        foreach ($reviewPerfumeNotes as $reviewPerfumeNote) {
            if ($reviewPerfumeNote instanceof ReviewPerfumeNote && !$reviewPerfumeNote->getDeleteAt()) {
                $list[] =  [
                    "id" => $reviewPerfumeNote->getId(),
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

        $ReviewPerfumeNote = new ReviewPerfumeNote();
        if ($m && $Y)
            $ReviewPerfumeNote->setCreateAt(DateTimeImmutable::createFromFormat('d/m/Y', "01/$m/$Y"));
        else
            $ReviewPerfumeNote->setCreateAt(new DateTimeImmutable());

        $ReviewPerfumeNote->setUser($user);
        $ReviewPerfumeNote->setTitle("");
        $ReviewPerfumeNote->setReview("");

        $this->entityManager->persist($ReviewPerfumeNote);
        $this->entityManager->flush();
        $listFragrance = [];
        foreach ($ReviewPerfumeNote->getFragrance() as $frag) {
            $listFragrance[] = [
                "id" => $frag->getId(),
                "name" => $frag->getName(),
                "brand" => $frag->getBrand(),
                "concentration" => $frag->getConcentration(),
                "value" => $frag->getValue(),
                "img" => $frag->getImg(),
                "description" => $frag->getDescription(),
            ];
        }
        return new JsonResponse([
            "id" => $ReviewPerfumeNote->getId(),
            "createAt" => $ReviewPerfumeNote->getCreateAt()->format('m/Y'),
            "review" => $ReviewPerfumeNote->getReview(),
            "title" => $ReviewPerfumeNote->getTitle(),
            "fragrances" => $listFragrance,
            "img" =>  "/pictogrammeParfum.png",
        ], Response::HTTP_OK);
    }

    #[Route('api/reviewPerfumeNoteAddFragrance/{reviewPerfumeNote}/{fragrances}')]
    public function addFragrancesReviewPerfumeNote(ReviewPerfumeNote $reviewPerfumeNote, Fragrance $fragrances): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());

        if ($reviewPerfumeNote->getUser() !== $user) {
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);
        }
        $reviewPerfumeNote->addFragrance($fragrances);
        $this->entityManager->persist($reviewPerfumeNote);
        $this->entityManager->flush();
        return new JsonResponse(1);
    }
    #[Route('api/reviewPerfumeNoteRemoveFragrance/{reviewPerfumeNote}/{fragrances}')]
    public function removeFragranceReviewPerfumeNote(ReviewPerfumeNote $reviewPerfumeNote, Fragrance $fragrances): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());

        if ($reviewPerfumeNote->getUser() !== $user) {
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);
        }
        $reviewPerfumeNote->removeFragrance($fragrances);
        $this->entityManager->persist($reviewPerfumeNote);
        $this->entityManager->flush();
        return new JsonResponse(1);
    }
    #[Route('api/reviewPerfumeNote/{ReviewPerfumeNote}', name: 'app_put_ReviewPerfumeNote', methods: "PUT")]
    #[OA\Parameter(name: 'title', in: "query", required: true)]
    #[OA\Parameter(name: 'review', in: "query", required: false)]
    public function putReviewPerfumeNote(ReviewPerfumeNote $ReviewPerfumeNote, Request $request): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($ReviewPerfumeNote->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $data = json_decode($request->getContent(), true);
        foreach ($data as $key => $value) {
            match ($key) {
                'title' => $ReviewPerfumeNote->setTitle($value),
                'review' => $ReviewPerfumeNote->setReview($value),
                default => null,
            };
        }
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }

    #[Route('api/reviewPerfumeNote/{ReviewPerfumeNote}', name: 'app_DELETE_ReviewPerfumeNote', methods: "DELETE")]
    #[OA\Parameter(name: 'ReviewPerfumeNote', in: "path", required: true)]
    public function deleteReviewPerfumeNote(ReviewPerfumeNote $ReviewPerfumeNote): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($ReviewPerfumeNote->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $ReviewPerfumeNote->setDeleteAt(new DateTimeImmutable());
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }
    #[Route('api/reviewPerfumeNote', name: 'app_get_ReviewPerfumeNote')]
    public function getReviewPerfumeNote(ReviewPerfumeNoteRepository $reviewPerfumeNoteRepository): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $reviewPerfumeNotes = $user->getReviewPerfumeNotes();
        $result = [];
        foreach ($reviewPerfumeNotes as $reviewPerfumeNote) {
            if ($reviewPerfumeNote instanceof ReviewPerfumeNote && !$reviewPerfumeNote->getDeleteAt()) {
                $listFragrance = [];
                foreach ($reviewPerfumeNote->getFragrance() as $frag) {
                    $listFragrance[] = [
                        "id" => $frag->getId(),
                        "name" => $frag->getName(),
                        "brand" => $frag->getBrand(),
                        "concentration" => $frag->getConcentration(),
                        "value" => $frag->getValue(),
                        "img" => $frag->getImg(),
                        "description" => $frag->getDescription(),
                    ];
                }
                $result[] =  [
                    "id" => $reviewPerfumeNote->getId(),
                    "createAt" => $reviewPerfumeNote->getCreateAt()->format('m/Y'),
                    "review" => $reviewPerfumeNote->getReview(),
                    "title" => $reviewPerfumeNote->getTitle(),
                    "fragrances" => $listFragrance,
                    "img" =>  "/pictogrammeParfum.png",
                ];
            }
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }
    #[Route('api/reviewPerfumeNote/{reviewPerfumeNote}/fragrance/{fragrance}', name: 'app_add_fragrance_in_ReviewPerfumeNote', methods: "POST")]
    public function addPerfumeInNote(ReviewPerfumeNote $reviewPerfumeNote, Fragrance $fragrance): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($reviewPerfumeNote->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);
        $reviewPerfumeNote->addFragrance($fragrance);
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }
    #[Route('api/reviewPerfumeNote/{reviewPerfumeNote}/fragrance/{fragrance}', name: 'app_delete_fragrance_in_ReviewPerfumeNote', methods: "DELETE")]
    public function deletePerfumeInNote(ReviewPerfumeNote $reviewPerfumeNote, Fragrance $fragrance): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($reviewPerfumeNote->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);
        $reviewPerfumeNote->removeFragrance($fragrance);
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }
}
