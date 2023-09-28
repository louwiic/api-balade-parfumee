<?php

namespace App\Controller;

use App\Entity\Fragrance;
use App\Entity\MyFavoriteTypesOfPerfumes;
use App\Entity\Profil;
use App\Repository\FragranceRepository;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProfilController extends AbstractController
{
    private ProfilRepository $profilRepository;
    private EntityManagerInterface $entityManager;
    private FragranceRepository $fragranceRepository;
    private UserRepository $userRepository;

    public function __construct(
        private NormalizerInterface $normalizer,
        ProfilRepository $profilRepository,
        EntityManagerInterface $entityManager,
        FragranceRepository $fragranceRepository,
        UserRepository $userRepository
    )
    {
        $this->profilRepository = $profilRepository;
        $this->entityManager = $entityManager;
        $this->fragranceRepository = $fragranceRepository;
        $this->userRepository = $userRepository;
    }
    #[Route('/api/profil', name: 'app_getProfil')]
    public function getProfil(Request $request): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $profil = $user->getProfil();

        if (!$profil) {
            return new Response('Profil not found', Response::HTTP_NOT_FOUND);
        }
        $fragrance = $profil->getMySymbolicFragrance();
        return new JsonResponse([
            'firstName' => $user->getFirstName(),
            'notesToDiscover' => $profil->getNotesToDiscover(),
            'childhoodScents'=> $profil->getChildhoodScents(),
            'mySymbolicFragrance' => $fragrance ? [
                "id" => $fragrance->getId(),
                "brand" => $fragrance->getBrand(),
                "concentration" => $fragrance->getConcentration(),
                "createAt" => $fragrance->getCreateAt(),
                "value" => $fragrance->getName(),
                "name" => $fragrance->getName(),
                "img" => $fragrance->getImg(),
                "description" => $fragrance->getDescription(),
            ] : false,
        ], Response::HTTP_OK);
    }
    #[Route('/api/profilPUT', name: 'app_updateProfilprofilPUT', methods: 'PUT')]
    #[OA\Parameter(name: 'fragrance_id', in: "query", required: true)]
    #[OA\Parameter(name: 'notes_to_discover', in: "query", required: true)]
    #[OA\Parameter(name: 'childhood_scents', in: "query", required: true)]
    public function updateProfil(Request $request): Response
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $data = json_decode($request->getContent(), true);
        $profil = $user->getProfil();

        if (!$profil) {
            return new Response('Profil not found', Response::HTTP_NOT_FOUND);
        }
        $profil->setNotesToDiscover($data['notes_to_discover']);
        $profil->setChildhoodScents($data['childhood_scents']);

        $fragrance = $data['fragrance_id'] != 'false' ? $this->fragranceRepository->find($data['fragrance_id']) : null;
        $profil->setMySymbolicFragrance($fragrance);
        $this->entityManager->persist($profil);
        $this->entityManager->flush();

        return new Response('Profil mis à jour', Response::HTTP_OK);
    }
    #[Route('/api/profil/feltOnMyCollection', name: 'app_get_feltOnMyCollection', methods: 'PUT')]
    #[OA\Parameter(name: 'feltOnMyCollection', in: "query", required: true)]
    public function getFeltOnMyCollection(Request $request): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $data = json_decode($request->getContent(), true);
        $profil = $user->getProfil();
        $profil->setFeltOnMyCollection($data['feltOnMyCollection']);
        $this->entityManager->flush();
        return new Response('feltOnMyCollection mis à jour', Response::HTTP_OK);
    }
    #[Route('/api/profil/tag', name: 'app_add_tag', methods: 'POST')]
    public function addMyFavoriteTypesOfPerfumes(Request $request): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if(count($user->getMyFavoriteTypesOfPerfumes()) >= 5) {
            return new Response(false, Response::HTTP_FORBIDDEN);
        }
        $myFavoriteTypesOfPerfumes = new MyFavoriteTypesOfPerfumes();
        $myFavoriteTypesOfPerfumes->setName('');
        $this->entityManager->persist($myFavoriteTypesOfPerfumes);

        $user->addMyFavoriteTypesOfPerfume($myFavoriteTypesOfPerfumes);
        $this->entityManager->flush();

        return new JsonResponse([
            "id" => $myFavoriteTypesOfPerfumes->getId(),
            "name" => $myFavoriteTypesOfPerfumes->getName()
        ], Response::HTTP_OK);
    }

    #[Route('/api/profil/tag', name: 'app_get_tag', methods: 'GET')]
    public function getMyFavoriteTypesOfPerfumes(Request $request): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $myFavoriteTypesOfPerfumes = $user->getMyFavoriteTypesOfPerfumes();
        $lis = [];
        foreach ($myFavoriteTypesOfPerfumes as $myFavoriteTypesOfPerfume) {
            $lis [] = [
                'id' => $myFavoriteTypesOfPerfume->getId(),
                'name' => $myFavoriteTypesOfPerfume->getName()
            ];
        }
        return new JsonResponse($lis, Response::HTTP_OK);
    }
    #[Route('/api/profil/tag/{myFavoriteTypesOfPerfumes}', name: 'app_edit_tag', methods: 'PUT')]
    #[OA\Parameter(name: 'myFavoriteTypesOfPerfumes', in: "path", required: true)]
    #[OA\Parameter(name: 'name', in: "query", required: true)]
    public function editMyFavoriteTypesOfPerfumes(MyFavoriteTypesOfPerfumes $myFavoriteTypesOfPerfumes, Request $request): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($myFavoriteTypesOfPerfumes->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $data = json_decode($request->getContent(), true);
        $myFavoriteTypesOfPerfumes->setName($data['name']);
        $this->entityManager->flush();

        return new JsonResponse([
            "id" => $myFavoriteTypesOfPerfumes->getId(),
            "name" => $myFavoriteTypesOfPerfumes->getName()
        ], Response::HTTP_OK);
    }
    #[Route('/api/profil/tag/{myFavoriteTypesOfPerfumes}', name: 'app_edit_dele', methods: 'DELETE')]
    #[OA\Parameter(name: 'myFavoriteTypesOfPerfumes', in: "path", required: true)]
    public function removeMyFavoriteTypesOfPerfumes(MyFavoriteTypesOfPerfumes $myFavoriteTypesOfPerfumes, Request $request): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($myFavoriteTypesOfPerfumes->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $this->entityManager->remove($myFavoriteTypesOfPerfumes);
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }
    #[Route('/api/', name: 'aze', methods: 'GET')]
    public function aze(Request $request): Response
    {
        return new Response('eezez', Response::HTTP_OK);
    }
}
