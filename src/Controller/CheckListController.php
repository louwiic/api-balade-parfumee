<?php

namespace App\Controller;

use App\Entity\CheckList;
use App\Entity\Fragrance;
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

class CheckListController extends AbstractController
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
    
    #[Route('api/checkList/{m}/{Y}', name: 'app_create_checkList', methods: "POST")]
    public function createCheckList(Request $request, $m = false, $Y = false): Response {
        $fragrance = json_decode($request->getContent(), true);

        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $checkList = new CheckList();
        if ($m && $Y)
            $checkList->setCreateAt(DateTimeImmutable::createFromFormat('d/m/Y', "01/$m/$Y"));
        else
            $checkList->setCreateAt(new DateTimeImmutable());
        
        /* if($fragrance["name"])  
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
            $checkList->setFragrance($fg); */
        
        $checkList->setUser($user);
        $checkList->setState('try');
        
        $this->entityManager->persist($checkList);
        $this->entityManager->flush();
        return new JsonResponse(["id" => $checkList->getId(), "createAt" => $checkList->getCreateAt()->format('m/Y')], Response::HTTP_OK);
    }


    #[Route('api/checkList/{checkList}/{fragrance}', name: 'app_put_checkList', methods: "PUT")]
    #[OA\Parameter(name: 'checkList', in: "path", required: true)]
    #[OA\Parameter(name: 'fragrance', in: "path", required: false)]
    #[OA\Parameter(name: 'state', in: "query", required: false)]
    public function putCheckList(CheckList $checkList, Fragrance $fragrance = null, Request $request): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($checkList->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $data = json_decode($request->getContent(), true);
        if (key_exists('state', $data) && $data["state"]) {
            $checkList->setState($data['state'] === "try" ? "try" : "tryEnd" );
        }
        if ($fragrance) {
            $checkList->setFragrance($fragrance);
        }
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }

    #[Route('api/checkList/{checkList}', name: 'app_DELETE_checkList',methods: "DELETE")]
    #[OA\Parameter(name: 'checkList', in: "path", required: true)]
    public function deleteCheckList(CheckList $checkList): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($checkList->getUser() !== $user)
            return new JsonResponse("not access", Response::HTTP_FORBIDDEN);

        $checkList->setDeleteAt(new DateTimeImmutable());
        $this->entityManager->flush();
        return new Response(true, Response::HTTP_OK);
    }
    #[Route('api/checkList', name: 'app_get_checkList')]
    public function getCheckList(): Response {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $list = [];

        foreach ($user->getCheckLists() as $checkList) {
            if ($checkList instanceof CheckList && !$checkList->getDeleteAt()) {

                $fragrance = $checkList->getFragrance();

                $list [] =  [
                    "id" => $checkList->getId(),
                    'state' => $checkList->getState(),
                    "createAt" =>  $checkList->getCreateAt()->format('m/Y'),
                    "idFragrance" => $fragrance ? $fragrance->getId() : "",
                    "brand" =>  $fragrance ? $fragrance->getBrand() : "",
                    "concentration" =>  $fragrance ? $fragrance->getConcentration(): "",
                    "value" =>  $fragrance ? $fragrance->getName(): "",
                    "name" =>  $fragrance ? $fragrance->getName(): "",
                    "img" =>  $fragrance ? $fragrance->getImg(): "/pictogrammeParfum.png",
                    "description" =>  $fragrance ? $fragrance->getDescription(): "",
                    "dateFragrance" => $fragrance ? $fragrance->getCreateAt()->format('m/y') : "",
                ];
            }
        }
        return new JsonResponse($list, Response::HTTP_OK);
    }
}