<?php

namespace App\Controller;

use App\Entity\CategoryNotification;
use App\Entity\ContentExclusive;
use App\Entity\ContentTag;
use App\Entity\Fragrance;
use App\Entity\Notification;
use App\Entity\NotificationsUsers;
use App\Entity\User;
use App\Kernel;
use App\Repository\CategoryNotificationRepository;
use App\Repository\ContentExclusiveRepository;
use App\Repository\ContentTagRepository;
use App\Repository\FragranceRepository;
use App\Repository\NotificationRepository;
use App\Repository\NotificationsUsersRepository;
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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Validator\Constraints as Assert;

class AdminController extends AbstractController
{
    private string $publicDir;
    private UserRepository $userRepository;

    public function __construct(
        private NormalizerInterface        $normalizer,
        private ContentExclusiveRepository $contentExclusiveRepository,
        private EntityManagerInterface     $em,
        Kernel                             $kernel,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        public  $monthlySubscription,
        public  $quarterlySubscription,
        public  $freeSubscription,

    ) {
        $this->publicDir = $kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'contentExclusive';

        $dotenv = new Dotenv();
        $dotenv->load($kernel->getProjectDir() . DIRECTORY_SEPARATOR . '.env');
        $this->userRepository = $userRepository;
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
                'maxSize' => '50M',
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
            } else {
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

    #[Route('api/admin/edit/contentExclusive/{id}', name: 'app_update_ContentExclusive', methods: "POST")]
    public function updateContentExclusive(Request $request, int $id, ValidatorInterface $validator, ContentTagRepository $contentTagRepository): JsonResponse
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
        $idContentTag = $request->request->get('idContentTag');

        $contentTag = $contentTagRepository->find($idContentTag);
        $contentExclusive = $this->em->getRepository(ContentExclusive::class)->find($id);

        if (!$contentExclusive) {
            return new JsonResponse(['message' => 'Contenu exclusif non trouvé.'], 404);
        }


        if (isset($file)) {
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
            $result = $this->getUniqueFileName($file);
            if ($result['isFailed']) {
                return new JsonResponse(['error' => 'Impossible de générer un nom de fichier unique après 50 tentatives. Veuillez réessayer.'], 400);
            }

            // Supprimez l'ancien fichier s'il existe
            $oldFileName = $contentExclusive->getAudio();

            if ($oldFileName) {
                $oldFilePath = $this->publicDir . '/' . $oldFileName;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $file->move($this->publicDir, $result['nameFile']);
            if ($contentTag->getId() === 2)
                $contentExclusive->setAudio($result['nameFile']);
            else
                $contentExclusive->setDesktopPdf($result['nameFile']);
        }

        if (isset($imageSrc)) {
            if (!$imageSrc instanceof UploadedFile) {
                return $this->json(['message' => 'image not found']);
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
            $uniqueNameImage = $this->getUniqueFileName($imageSrc);
            if ($uniqueNameImage['isFailed']) {
                return new JsonResponse(['error' => 'Impossible de générer un nom de fichier unique après 50 tentatives. Veuillez réessayer.'], 400);
            } else {
                $contentExclusive->setImageSrc($uniqueNameImage['nameFile']);
                $imageSrc->move($this->publicDir, $uniqueNameImage['nameFile']);
                $contentExclusive->setImageSrc($uniqueNameImage['nameFile']);
            }
        }

        if (isset($link)) {
            $contentExclusive->setLink($link);
        }
        if (isset($title)) {
            $contentExclusive->setTitle($title);
        }

        if (isset($description)) {
            $contentExclusive->setDescription($description);
        }

        $this->em->flush();

        return new JsonResponse(['message' => 'Mise à jour réussie']);
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
    public function getAllContentExclusive(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }

        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 15);
        $offset = ($page - 1) * $limit;

        $totalPage = ceil(count($this->contentExclusiveRepository->findAll()) / $limit);

        // Utiliser les paramètres pour paginer les résultats
        $contentExclusives = $this->contentExclusiveRepository->findBy([], null, $limit, $offset);
        $contentExclusives = $this->normalizer->normalize($contentExclusives, null, ['ignored_attributes' => ['tag']]);

        return new JsonResponse(['contentExclusives' => $contentExclusives, 'totalPage' => $totalPage]);
    }

    #[Route('api/admin/deleteContentExclusive/{id}', name: 'deleteContentExclusive', methods: "DELETE")]
    public function deleteContentExclusive(int $id)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }

        $contentExclusive = $this->contentExclusiveRepository->find($id);

        if (!$contentExclusive) {
            return new JsonResponse([
                'message' => 'Contenu  non trouvé.'
            ], 404);
        }

        $this->em->remove($contentExclusive);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Contenu  supprimé avec succès.'
        ], 200);
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

    #[Route('api/users/getAllNotification', name: 'app_getUsersAllNotification', methods: "GET")]
    public function getAllUserNotification(NotificationRepository $notificationRepository): JsonResponse
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $subscriptionStripeId = $user->getIdSubscriptionStripe();

        if ($subscriptionStripeId === null) {
            $notifications = $notificationRepository->findBy(['categoryNotification' => [1, 4]], ['createAt' => 'DESC']);

            // Ajouter la propriété 'subscriptionType' avant la normalisation
            foreach ($notifications as $notification) {
                $notification->subscriptionType = 'no_subscription'; // Valeur pour les utilisateurs sans abonnement
            }

            $normalizedNotifications = $this->normalizer->normalize($notifications, null, ['ignored_attributes' => ['user'], 'groups' => 'notification:read']);
            return new JsonResponse(['notifications' => $normalizedNotifications]);
        }

        // Récupération des informations d'abonnement Stripe
        $subscription = Subscription::retrieve($user->getIdSubscriptionStripe());
        $currentSub = $subscription->items->data[0]->plan->id;
        $subscriptionStatus = $subscription->status;

        $categoryFilter = [1, 4];

        // Vérifier si l'abonnement est actif et non annulé ou impayé
        if (!in_array($subscriptionStatus, ['active', 'trialing'])) {
            $notifications = $notificationRepository->findBy(['categoryNotification' => $categoryFilter], ['createAt' => 'DESC']);
            $normalizedNotifications = $this->normalizer->normalize($notifications, null, ['ignored_attributes' => ['user'], 'groups' => 'notification:read']);
            return new JsonResponse(['notifications' => $normalizedNotifications]);
        }

        // Définir les prix et les types d'abonnement
        $price_mensuel = 'price_1NwqwRFnV1sRkwn0cRKvCyLc';
        $price_trimestriel = 'price_1NwqvOFnV1sRkwn0yaK0jhlH';
        $forAboMensu = 2;
        $forAboTrim = 3;
        $forAboDiscover = 1;
        $forAllUser = 4;

        // Mise à jour des catégories en fonction de l'abonnement
        if ($currentSub === $price_mensuel) {
            $categoryFilter = [$forAboMensu, $forAboDiscover, $forAllUser];
        }

        if ($currentSub === $price_trimestriel) {
            $categoryFilter = [$forAboTrim, $forAboDiscover, $forAllUser];
        }

        // Récupérer les notifications en fonction du filtre de catégorie
        $notifications = $notificationRepository->findBy(['categoryNotification' => $categoryFilter], ['createAt' => 'DESC']);

        $enhancedNotifications = [];

        foreach ($notifications as $notification) {
            $notificationArray = $this->normalizer->normalize($notification, null, ['ignored_attributes' => ['user'], 'groups' => 'notification:read']);

            if ($currentSub === $price_mensuel) {
                $notificationArray['subscriptionType'] = 2;
            }

            if ($currentSub === $price_trimestriel) {
                $notificationArray['subscriptionType'] = 3;
            }

            $enhancedNotifications[] = $notificationArray;
        }

        return new JsonResponse(['notifications' => $enhancedNotifications, "subscriptionStripeId" => $subscriptionStripeId]);
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

        if (!$categoryNotification instanceof CategoryNotification) {
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

    #[Route('api/read/notification', name: 'app_readNotification', methods: "PUT")]
    public function readNotification(Request $request, SerializerInterface $serializer,  NotificationsUsersRepository $notificationsUsersRepository)
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $jsonData = json_decode($request->getContent(), true);
        $notificationId = $jsonData['id'];
        $userId = $user->getId();

        $notification = $this->em->getRepository(Notification::class)->find($notificationId);

        // Vérifier si la notification n'est pas déjà marquée comme lue
        //$existingNotificationUser = $this->em->getRepository(NotificationsUsers::class)->findAll();

        $existingNotificationUser = $notificationsUsersRepository->createQueryBuilder('nu')
            ->where(':notificationId MEMBER OF nu.notification_id')
            ->setParameter('notificationId', $notificationId)
            ->getQuery()
            ->getResult();

        $alreadyRead = count($existingNotificationUser) > 0;

        if ($alreadyRead) {
            return new JsonResponse([
                "success" => false,
                'message' => 'You already read notification'
            ], 200);
        }

        $notificationUser = new NotificationsUsers();
        $notificationUser->addUserId($user);
        $notificationUser->addNotificationId($notification);
        $notificationUser->setIsRead(true);

        $this->em->persist($notificationUser);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'notification is read',
            "success" => true,
            'user' => $user->getFirstName(),
        ], 200);


        /*  
        $notificationUser = new NotificationsUsers();
        $notificationUser->addUserId($user);
        $notificationUser->addNotificationId($notification);
        $notificationUser->setIsRead(true);

        $this->em->persist($notificationUser);
        $this->em->flush(); */

        /* $notificationIsRead = $notificationsUsersRepository->findAll();
        $listNotifications = [];
        foreach ($notificationIsRead as $notificationUser) {
            $notificationIds = [];
            foreach ($notificationUser->getNotificationId() as $notification) {
                if ($notification->getId() == $notificationId) {
                    $notificationIds[] = $notification->getId();
                }
            }

            if (!empty($notificationIds)) {
                $listNotifications[] = [
                    "id" => $notificationUser->getId(),
                    "isRead" => $notificationUser->isIsRead(),
                    'notification_id' => $notificationIds

                ];
            }
        } */
    }

    #[Route('api/isRead/notification', name: 'app_isReadNotification')]
    public function getNotificationIsRead(Request $request,  NotificationsUsersRepository $notificationsUsersRepository)
    {
        $user = $this->userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        //$jsonData = json_decode($request->getContent(), true);
        //$userId = $user->getId();
        $userId = $user->getId();
        $listNotifications = $notificationsUsersRepository->findIsReadNotificationsForUser($userId);

        return new JsonResponse([
            "success" => true,
            'listNotifications' => $listNotifications,
        ], 200);



        /* $notificationIsRead = $notificationsUsersRepository->findAll();
        $listNotifications = [];
        foreach ($notificationIsRead as $notificationUser) {
            $userIds = [];
            $notificationIds=[];
            if ($notificationUser->isIsRead() == true) {
                foreach ($notificationUser->getUserId() as $user) {
                    if ($user->getId() == $userId) {
                        $userIds[] = $user->getFirstName();
                    }
                }
                foreach ($notificationUser->getNotificationid() as $notif) {                     
                        $notificationIds[] = $user->getFirstName();                    
                }
            }

            if (!empty($userIds)) {
                $listNotifications[] = [
                    "id" => $notificationUser->getId(),
                    "isRead" => $notificationUser->isIsRead(),
                    'user_id' => $userIds

                ];
            }
        }

        return new JsonResponse([
            "success" => true,
            'listNotifications' => $listNotifications
        ], 200); */
    }

    #[Route('api/admin/update/notification', name: 'app_updateNotification', methods: "POST")]
    public function updateNotification(Request $request, CategoryNotificationRepository $categoryNotificationRepository)
    {
        $jsonData = json_decode($request->getContent(), true);
        $notification = $this->em->getRepository(Notification::class)->find($jsonData['id']);
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }

        if ($jsonData['title'] === null || $jsonData['description'] === null) {
            return new JsonResponse(['message' => 'Title or description not found'], 404);
        }


        $notification->setDescription($jsonData['description']);
        $notification->setTitle($jsonData['title']);

        $categoryNotification = $categoryNotificationRepository->find($jsonData['categoryNotificationId']);

        if (isset($jsonData['categoryNotificationId'])) {
            $notification->setCategoryNotification($categoryNotification);
        }
        /* return new JsonResponse([
            'title' => $notification->getTitle(), 'description' => $notification->getDescription(),
            "categ" => $notification->getCategoryNotification(),
            "categoryNotification" => $jsonData['categoryNotificationId']
        ]); */

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
            $listUsers[] = [
                "id" => $user->getId(),
                "firstName" => $user->getFirstName(),
                "lastName" => $user->getLastName(),
                "email" => $user->getEmail(),
                "createdAt" => $user->getCreatedAt(),
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

    #[Route('api/admin/fragrance/search', name: 'app_getFragranceByNameAndBrand', methods: "POST")]
    public function getFragranceByNameAndBrand(Request $request, FragranceRepository $fragranceRepository): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }

        //get name and brand from body request
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $brand = $data['brand'];
        $fragrance = $fragranceRepository->createQueryBuilder('f')
            ->where('f.name = :name')
            ->andWhere('f.brand = :brand')
            ->setParameter('name', $name)
            ->setParameter('brand', $brand)
            ->getQuery()
            ->getOneOrNullResult();
        return new JsonResponse($fragrance, Response::HTTP_OK);
    }


    #[Route('/api/admin/importFragrances', name: 'app_import_fragrances', methods: ['POST'])]
    public function importFragrances(Request $request, EntityManagerInterface $entityManager, FragranceRepository $fragranceRepository): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'message' => 'Vous n\'avez pas les droits pour effectuer cette action.'
            ], 403);
        }

        // Récupérer les données du corps de la requête
        $apiData = json_decode($request->getContent(), true);

        if (!is_array($apiData) || empty($apiData)) {
            return new JsonResponse([
                'message' => 'Les données reçues ne sont pas valides ou sont vides.'
            ], 400);
        }

        // Récupérer tous les parfums existants pour comparaison
        $allFragrances = $fragranceRepository->findAll();
        $updatedData = [];

        // Traiter chaque parfum reçu
        foreach ($apiData as $item) {
            if (!isset($item['fields'])) {
                continue; // Ignorer les éléments sans champ 'fields'
            }

            $fields = $item['fields'];
            $existingFragrance = $this->findExistingFragrance($fields, $allFragrances);

            if ($existingFragrance) {
                // Mettre à jour le parfum existant
                $updatedItem = $this->updateExistingFragrance($existingFragrance, $fields);
                $updatedData[] = $updatedItem;
            } /* else {
                // Créer un nouveau parfum
                $newItem = $this->createNewFragrance($fields, $item, $entityManager);
                $updatedData[] = $newItem;
            } */
        }

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Fragrances traitées avec succès.',
            'updatedData' => $updatedData
        ]);
    }

    private function findExistingFragrance(array $fields, array $allFragrances): ?Fragrance
    {
        foreach ($allFragrances as $fragrance) {
            if (
                $fragrance->getBrand() === $this->decodeSpecialCharacters($fields['name'] ?? '') &&
                $fragrance->getName() === $this->decodeSpecialCharacters($fields['brand'] ?? '')
            ) {
                return $fragrance;
            }
        }
        return null;
    }

    private function updateExistingFragrance(Fragrance $fragrance, array $fields): array
    {
        $fragrance->setConcentration($this->decodeSpecialCharacters($fields['concentration'] ?? null));
        $fragrance->setDescription($this->decodeSpecialCharacters($fields['description'] ?? null));
        $fragrance->setImg($fields['link'] ?? null);
        $fragrance->setValue($fields['value'] ?? null);

        return [
            'id' => $fragrance->getId(),
            'brand' => $fragrance->getBrand(),
            'name' => $fragrance->getName(),
            'updated' => true
        ];
    }

    private function createNewFragrance(array $fields, array $item, EntityManagerInterface $entityManager): array
    {
        $fragrance = new Fragrance();
        $fragrance->setName($this->decodeSpecialCharacters($fields['name']));
        $fragrance->setBrand($this->decodeSpecialCharacters($fields['brand']));
        $fragrance->setImg($fields['link'] ?? null);
        $fragrance->setConcentration($this->decodeSpecialCharacters($fields['concentration'] ?? null));
        $fragrance->setDescription($this->decodeSpecialCharacters($fields['description'] ?? null));
        $fragrance->setValue($fields['value'] ?? null);

        if (isset($item['createdTime'])) {
            try {
                $createAt = new \DateTimeImmutable($item['createdTime']);
                $fragrance->setCreateAt($createAt);
            } catch (\Exception $e) {
                $fragrance->setCreateAt(new \DateTimeImmutable());
            }
        } else {
            $fragrance->setCreateAt(new \DateTimeImmutable());
        }

        $entityManager->persist($fragrance);

        return [
            'id' => $fragrance->getId(),
            'brand' => $fragrance->getBrand(),
            'name' => $fragrance->getName(),
            'created' => true
        ];
    }

    private function decodeSpecialCharacters(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $text);

        return trim($text);
    }
}
