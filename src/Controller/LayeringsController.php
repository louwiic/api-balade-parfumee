<?php

namespace App\Controller;

use App\Entity\Layering;
use App\Repository\FragranceRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class LayeringsController extends AbstractController
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
    #[Route('api/layerings', name: 'app_create_Layerings', methods: "POST")]
    public function createLayerings(Request $request, StripeController $stripe)
    {

        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $layerings = $user->getLayerings();

        $list = [];
        foreach ($layerings as $layering) {
            if ($layering instanceof Layering && !$layering->getDeletedAT()) {
                $list[] =  [
                    "id" => $layering->getId(),
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

        $layering = new Layering();
        $layering->setUser($user);
        $layering->setCreateAt(new DateTimeImmutable());
        $layering->setDescription('');

        $this->entityManager->persist($layering);
        $this->entityManager->flush();

        return new JsonResponse([
            "id" => $layering->getId(),
            "description" => "",
            "layering1" => [],
            "layering2" => []
        ], Response::HTTP_OK);
    }

    #[Route('api/layerings/{layering}', name: 'app_update_Layerings', methods: "PUT")]
    public function updateLayerings(Layering $layering, Request $request,  StripeController $stripe)
    {

        $data = json_decode($request->getContent(), true);
        foreach ($data as $key => $value) {
            match ($key) {
                'description' => $layering->setDescription($value),
                'layering1' => $layering->setFragrance1($this->fragranceRepository->find($value)),
                'layering2' => $layering->setFragrance2($this->fragranceRepository->find($value)),
                default => null,
            };
        }
        $this->entityManager->flush();
        return new Response(1, Response::HTTP_OK);
    }
    #[Route('api/layerings/{layering}', name: 'app_DELETE_Layerings', methods: "DELETE")]
    public function daleteLayerings(Layering $layering, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($layering->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);
        $layering->setDeletedAT(new DateTimeImmutable());
        $this->entityManager->flush();
        return new Response(1, Response::HTTP_OK);
    }
    #[Route('api/layerings', name: 'app_get_Layerings', methods: "GET")]
    public function getLayerings()
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());

        $layerings = $user->getLayerings() ?: [];
        $result = [];
        foreach ($layerings as $layering) {
            if ($layering instanceof Layering  && !$layering->getDeletedAT()) {
                $frag = $layering->getFragrance1();
                $frag2 = $layering->getFragrance2();
                $result[] = [
                    'id' => $layering->getId(),
                    "description" => $layering->getDescription(),
                    "layering1" => $frag ? [
                        "id" => $frag->getId(),
                        "name" => $frag->getName(),
                        "brand" => $frag->getBrand(),
                        "concentration" => $frag->getConcentration(),
                        "value" => $frag->getValue(),
                        "img" => $frag->getImg(),
                        "description" => $frag->getDescription(),
                    ] : [],
                    "layering2" => $frag2 ? [
                        "id" => $frag2->getId(),
                        "name" => $frag2->getName(),
                        "brand" => $frag2->getBrand(),
                        "concentration" => $frag2->getConcentration(),
                        "value" => $frag2->getValue(),
                        "img" => $frag2->getImg(),
                        "description" => $frag2->getDescription(),
                    ] : [],
                ];
            }
        }
        return new JsonResponse($result, Response::HTTP_OK);
    }
}
