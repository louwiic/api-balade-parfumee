<?php

namespace App\Controller;

use App\Entity\CategoryNotification;
use App\Entity\ContentExclusive;
use App\Entity\ContentTag;
use App\Entity\Notification;
use App\Kernel;
use App\Repository\CategoryNotificationRepository;
use App\Repository\ContentExclusiveRepository;
use App\Repository\ContentTagRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Validator\Constraints as Assert;

class AdminController extends AbstractController
{
    private string $publicDir;

    public function __construct(
        private NormalizerInterface        $normalizer,
        private ContentExclusiveRepository $contentExclusiveRepository,
        private EntityManagerInterface     $em,
        Kernel                             $kernel,
        EntityManagerInterface $entityManager,
        public  $monthlySubscription,
        public  $quarterlySubscription,
        public  $freeSubscription,
    )
    {
        $this->publicDir = $kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'contentExclusive';

        $dotenv = new Dotenv();
        $dotenv->load($kernel->getProjectDir() . DIRECTORY_SEPARATOR . '.env');

        $this->entityManager = $entityManager;
        $this->endpoint_secret = $_ENV['ENDPOINT_SECRET'];
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }

    /**
     * @throws \Exception
     */
    public function getRandomString($length = 20): string
    {
        $randomString = '';
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function getUniqueFileName(UploadedFile $file): array
    {
        $maxAttempts = 50;

        $attempts = 0;
        $nameFile = '';
        while ($attempts < $maxAttempts) {
            $nameFile = $this->getRandomString() . "." . $file->getClientOriginalExtension();
            $filePath = $this->publicDir . DIRECTORY_SEPARATOR . "$nameFile";
            if (!file_exists($filePath)) {
                break;
            }
            $attempts++;
        }
        return ['isFailed' => $attempts === $maxAttempts, 'nameFile' => $nameFile, 'filePath' => $filePath];
    }


    #[Route('api/admin/getAllTag', name: 'app_getAllTag', methods: "GET")]
    public function getAllTag(ContentTagRepository $contentTagRepository): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }

        $contentTagAll = $contentTagRepository->findAll();

        $normalizedContentTagAll = $this->normalizer->normalize($contentTagAll, null, ['ignored_attributes' => ['contentExclusives']]);
        return new JsonResponse(['contentTagAll' => $normalizedContentTagAll]);
    }

    #[Route('api/admin/addContentExclusive', name: 'app_addContentExclusive', methods: "POST")]
    public function addContentExclusive(Request $request, ValidatorInterface $validator, ContentTagRepository $contentTagRepository): JsonResponse
    { 
        
        
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }
        $file = $request->files->get('file');
        $imageSrc = $request->files->get('image');
        $title = $request->request->get('title');
        $link = $request->request->get('link');
        $description = $request->request->get('description');
        $contentTag = $contentTagRepository->find($request->request->get('idContentTag'));

        if (!$contentTag instanceof ContentTag) {
            return new JsonResponse([
                'message' => 'Tag not found',
            ]);
        }
        if (!$imageSrc instanceof UploadedFile) {
            return $this->json(['message' => 'image not found']);
        }
        if ($contentTag->getId() === 3 && $link === null) {
            return new JsonResponse(['message' => 'Link not found, required for this tag']);
        }
        if ($contentTag->getId() === 2 && !$file instanceof UploadedFile) {
            return new JsonResponse(['message' => 'file not found, required for this tag']);
        }
        if ($contentTag->getId() === 1 && !$file instanceof UploadedFile) {
            return new JsonResponse(['message' => 'file not found, required for this tag']);
        }

        if ($title === null) {
            return $this->json([
                'message' => 'Title not found',
            ]);
        }

        $violations = $validator->validate($imageSrc, [
            new Assert\NotBlank([
                'message' => 'Veuillez télécharger un fichier.'
            ]),
            new Assert\File([
                'maxSize' => '500M',
                'mimeTypes' => ['image/png', 'image/jpeg'],
                'mimeTypesMessage' => 'Veuillez télécharger un fichier PNG ou JPEG valide.'
            ])
        ]);
        if (count($violations) > 0) {
            return $this->json(['message' => $violations[0]->getMessage()], 400);
        }

        $violations = $validator->validate($file, [
            new Assert\File([
                'maxSize' => '500M',
                'mimeTypes' => ['application/pdf', 'audio/mpeg'],
                'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF ou MP3 valide.'
            ])
        ]);

        if (count($violations) > 0) {
            return $this->json(['message' => $violations[0]->getMessage()], 400);
        }

        $contentExclusive = new ContentExclusive();
        $contentExclusive->setTag($contentTag);
        $contentExclusive->setTitle($title);
        $contentExclusive->setDescription($description);
        $contentExclusive->setLink($contentTag->getId() === 3 ? $link : '');

        try {
 
            $uniqueNameImage = $this->getUniqueFileName($imageSrc);
            if ($uniqueNameImage['isFailed']) {
                return new JsonResponse(['error' => 'Impossible de générer un nom de fichier unique après 50 tentatives. Veuillez réessayer.'], 400);
            }else{
                $contentExclusive->setImageSrc($uniqueNameImage['nameFile']);
                $imageSrc->move($this->publicDir, $uniqueNameImage['nameFile']);
                $contentExclusive->setImageSrc($uniqueNameImage['nameFile']);
            }

            if ($contentTag->getId() === 2 || $contentTag->getId() === 1) {
                $result = $this->getUniqueFileName($file);
                if ($result['isFailed']) {
                    return new JsonResponse(['error' => 'Impossible de générer un nom de fichier unique après 50 tentatives. Veuillez réessayer.'], 400);
                }
                $file->move($this->publicDir, $result['nameFile']);
                if ($contentTag->getId() === 2)
                    $contentExclusive->setAudio($result['nameFile']);
                else
                    $contentExclusive->setDesktopPdf($result['nameFile']);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la génération du nom de fichier.']);
        }
        $this->em->persist($contentExclusive);
        $this->em->flush();
        return new JsonResponse(['message' => 'Fichier ajouté avec succès']);
    }

    #[Route('api/admin/contentExclusive/{contentExclusive}', name: 'app_updateContentExclusive', methods: "DELETE")]
    public function removeContentExclusive(ContentExclusive $contentExclusive): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }
        $this->em->remove($contentExclusive);
        $this->em->flush();
        return new JsonResponse(['message' => 'Supprimé avec succès']);
    }

    #[Route('api/admin/getAllContentExclusive', name: 'getAllContentExclusive', methods: "get")]
    public function getAllContentExclusive()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }
        $contentExclusives = $this->contentExclusiveRepository->findAll();
        $contentExclusives = $this->normalizer->normalize($contentExclusives, null, ['ignored_attributes' => ['tag']]);
        return new JsonResponse(['contentExclusives' => $contentExclusives]);
    }

    #[Route('api/admin/getAllNotification', name: 'app_getAllNotification', methods: "GET")]
    public function getAllNotification(NotificationRepository $notificationRepository): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }
        $notifications = $notificationRepository->findBy(['deletedAt' => null], ['createAt' => 'DESC']);
        $normalizedNotifications = $this->normalizer->normalize($notifications, null, ['ignored_attributes' => ['user'], 'groups' => 'notification:read']);
        return new JsonResponse(['notifications' => $normalizedNotifications]);

    }
    #[Route('api/admin/deleteNotification/{notification}', name: 'app_deleteNotification', methods: "delete")]
    public function deleteNotification(Notification $notification): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }
        if ($notification->getDeletedAt() !== null) {
            return new JsonResponse(['message' => 'Notification déjà supprimé'], 400);
        }
        $notification->setDeletedAt(new \DateTimeImmutable());
        $this->em->flush();
        return new JsonResponse(['message' => 'Notification supprimé avec succès']);
    }


    #[Route('api/admin/addNotification', name: 'app_addNotification', methods: "POST")]
    public function addNotification(Request $request, UserRepository $userRepository, CategoryNotificationRepository $categoryNotificationRepository): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }
        $notification = new Notification();
        $jsonData = json_decode($request->getContent(), true);
        $categoryNotification = $categoryNotificationRepository->find($jsonData['categoryNotificationId']);

        if(!$categoryNotification instanceof CategoryNotification){
            return new JsonResponse(['message' => 'Category not found'], 404);
        }
        if ($jsonData['title'] === null || $jsonData['description'] === null) {
            return new JsonResponse(['message' => 'Title not found'], 404);
        }
        if (strlen($jsonData['title']) === 0 || strlen($jsonData['description']) === 0) {
            return new JsonResponse(['message' => 'Title et description ne peuvent pas être vide'], 404);
        }
        if ($categoryNotification->getId() === 4) {
            $users = $userRepository->findAll();
        } else {
            $users = $userRepository->findByTypeSubscription($categoryNotification->getId());
        }
        foreach ($users as $user)
            $notification->addUser($user);

        $notification->setTitle($jsonData['title']);
        $notification->setDescription($jsonData['description']);
        $notification->setCreateAt(new \DateTimeImmutable());
        $notification->setTotalSend(count($users));
        $notification->setNotificationOpen(0);
        $notification->setCategoryNotification($categoryNotification);
        $this->em->persist($notification);
        $this->em->flush();
        return new JsonResponse(['message' => 'Notification ajoutée avec succès']);
    }

    #[Route('api/admin/notification/{notification}', name: 'app_updateNotification', methods: "PUT")]
    public function updateNotification(Notification $notification, Request $request) {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }

        $jsonData = json_decode($request->getContent(), true);
        if ($jsonData['title'] === null || $jsonData['description'] === null) {
            return new JsonResponse(['message' => 'Title or description not found'], 404);
        }
        $notification->setDescription($jsonData['description']);
        $notification->setTitle($jsonData['title']);
        $this->em->flush();
        return new JsonResponse(['message' => 'Notification modifiée avec succès']);
    }

    #[Route('api/admin/getUsers/{page}', name: 'app_admin_getAll_users', methods: "GET")]
    public function getAllUser($page, UserRepository $userRepository): JsonResponse
    {
        if ($page == 0) {
            return new JsonResponse(['message' => 'Page > 0 '], 400);
        }
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }
        $usersPerPage = 20;
        $offset = ($page - 1) * $usersPerPage;

        $users = $userRepository->findBy([], null, $usersPerPage, $offset);
        $totalPage = ceil(count($userRepository->findAll()) / $usersPerPage);
        $listUsers = [];
        // boucle pour récupérer les utilisateurs
        foreach ($users as $user) {
            $listUsers [] = [
                "id" => $user->getId(),
                "firstName" => $user->getFirstName(),
                "lastName" => $user->getLastName(),
                "email" => $user->getEmail(),
                "createdAt"=> $user->getCreatedAt(),
                "isAdmin" => in_array("ROLE_ADMIN", $user->getRoles()),
            ];
        }
        return new JsonResponse([
            'users' => $listUsers,
            "nextPage" => $page < $totalPage ?  $page + 1 : $page,
            "currentPage" => $page,
            'totalPage' => $totalPage
        ]);
    }

    #[Route('api/admin/getTotalSubscriptions', name: 'get_all_total_subscriptions', methods: "GET")]
    public function getAllSubscriptions(): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }
        try {
            $monthlySubscription =  \Stripe\Subscription::all([
                'plan' => $this->monthlySubscription
            ]);
            $freeSubscription = \Stripe\Subscription::all([
                'plan' => $this->freeSubscription
            ]);
            $quarterlySubscription = \Stripe\Subscription::all([
                'plan' => $this->quarterlySubscription
            ]);
            return new JsonResponse([
                'countFreeSubscribe' => count($freeSubscription->data),
                'countOneMonthSubscribe' => count($monthlySubscription->data),
                'countQuarterlySubscribe' => count($quarterlySubscription->data)
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return new JsonResponse(['error' => 'Une erreur s\'est produite lors de la récuperation des abonnements.'], 500);
        }
    }

}