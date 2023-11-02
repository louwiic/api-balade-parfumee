<?php

namespace App\Controller;

use App\Entity\CodeValidation;
use App\Entity\Profil;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\CodeValidationRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use DrewM\MailChimp\MailChimp;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Twilio\Rest\Client;
use OpenApi\Attributes as OA;

class UserController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private MailChimp $mailchimp;
    private EntityManagerInterface $entityManager;
    private Client $client;
    //   public function __construct(EmailVerifier $emailVerifier)
    // {
    //   $this->emailVerifier = $emailVerifier;
    //}
    public function __construct(MailChimp $mailchimp, EntityManagerInterface $entityManager, Client $client)
    {
        $this->mailchimp = $mailchimp;
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    #[Route('/api/auth/generate_code', name: 'generate_code', methods: ['POST'])]
    public function generateCode(Request $request): Response
    {
        // Récupérer les données JSON
        $jsonData = json_decode($request->getContent(), true);

        // Récupérer le numéro de téléphone depuis les données JSON
        $phoneNumber = $jsonData['tel'];

        // Générer un code de validation de six chiffres
        $validationCode = mt_rand(100000, 999999);

        $expirationDate = new \DateTimeImmutable();
        $expirationDate->modify('+2 minutes');

        // Créer une nouvelle instance de l'entité CodeValidation
        $codeValidation = new CodeValidation();
        $codeValidation->setSource($phoneNumber);
        $codeValidation->setCode((string)$validationCode);
        $codeValidation->setExpiredAt($expirationDate);
        $this->entityManager->persist($codeValidation);
        $this->entityManager->flush();
        $twilio_number = "+16193778287";

        //return new JsonResponse(['message' =>  $this->client]);
        $this->client->messages->create(
            // Where to send a text message (your cell phone?)
            $codeValidation->getSource(),
            array(
                'from' => $twilio_number,
                'body' => "Voici le code pour avoir accès à votre carnet olfactif : " . $validationCode
            )
        );

        $response = [
            'success' => true,
            'code' => $codeValidation
        ];

        return new Response(json_encode($response), 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/api/auth/verify_code', name: 'verify_code', methods: ['POST'])]
    public function verifyCode(Request $request, CodeValidationRepository $codeValidationRepository): Response
    {

        // Récupérer les données JSON
        $jsonData = json_decode($request->getContent(), true);

        // Vérifier si le code est présent dans les données JSON
        if (!isset($jsonData['code'])) {
            return new JsonResponse(['success' => false, 'message' => 'Code is missing'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer le numéro de téléphone depuis les données JSON
        $codeValidation = $jsonData['code'];

        $code = $codeValidationRepository->findOneBy(["code" => $codeValidation]);

        if (!$code) {
            return new JsonResponse(['success' => false, 'message' => 'Code not found'], Response::HTTP_NOT_FOUND);
        }

        $now = new \DateTimeImmutable();
        $expirationDate = $code->getExpiredAt();
        $expirationLimit = $expirationDate->modify('+2 minutes');

        if ($code && $now > $expirationLimit) {
            // Soit le code n'a pas été trouvé, soit il a expiré
            $response = [
                'success' => false,
                'message' => $code ? 'Code has expired' : 'Code not found'
            ];
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $response = [
            'succes' => true,
            'code' => $code->getCode()
        ];

        return new Response(json_encode($response), 200, ['Content-Type' => 'application/json']);
    }

    private function addSubscriberWithTags($listId, $email, $firstName, $lastName, $tags): bool
    {
        $subscriber = array(
            'email_address' => $email,
            'status'        => 'subscribed',
            'merge_fields'  => array(
                'FNAME'     => $firstName,
                'LNAME'     => $lastName
            ),
            'tags'          => $tags
        );
        $result = $this->mailchimp->post("lists/$listId/members", $subscriber);

        return $this->mailchimp->success();
    }
    #[Route('/register', name: 'app_register', methods: 'POST')]
    #[OA\Parameter(name: 'email', in: "email", required: true)]
    #[OA\Parameter(name: 'lastName', in: "lastName", required: true)]
    #[OA\Parameter(name: 'firstName', in: "firstName", required: true)]
    #[OA\Parameter(name: 'phone', in: "phone", required: true)]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        $user = new User();
        $list_id = "11f0aee7a2";
        $jsonData = json_decode($request->getContent(), true);

        $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $jsonData['email']]);
        if ($existingUserByEmail) {
            // L'e-mail existe déjà, renvoyer un message d'erreur
            return new Response(json_encode(["error" => true, "message" => 'L\'e-mail existe déjà.']), 400);
        }

        // Vérifier si le numéro de téléphone existe déjà
        $existingUserByPhone = $entityManager->getRepository(User::class)->findOneBy(['phone' => $jsonData['phone']]);
        if ($existingUserByPhone) {
            // Le numéro de téléphone existe déjà, renvoyer un message d'erreur
            return new Response(json_encode(["error" => true, "message" => 'Le numéro de téléphone existe déjà.']), 400);
        }

        /* 
         $subscription = $jsonData['subscriptionSelected'];
        $abo ='Abonné COD gratuit';
        if($subscription === 1){
            $abo="Abonnement mensuel";
        }else if($subscription === 2){
            $abo="Abonnement trimestriel";
        }
        return new Response(json_encode(["ok" => $abo]),  200);
         */

        $user->setEmail($jsonData['email']);
        $user->setLastName($jsonData['lastName']);
        $user->setPhone($jsonData['phone']);
        $user->setFirstName($jsonData['firstName']);
        $user->setTypeSubscription(3);
        //$user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $jsonData['password']
            )
        );
        $p = new Profil();
        $user->setProfil($p);
        $entityManager->persist($p);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addSubscriberWithTags($list_id, $user->getEmail(), $user->getFirstName(), $user->getLastName(), 'Abonné COD gratuit');
        return new Response(json_encode(["ok" => true, "message" => 'Inscription réussie !']),  200);
    }

    #[Route('/api/iSConnected', name: 'app_iSConnected')]
    public function iSConnected(Request $request): Response
    {
        return new JsonResponse(["succes" => true]);
    }
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
