<?php

namespace App\Controller;

use App\Entity\User;
use App\Kernel;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use DrewM\MailChimp\MailChimp;
use Stripe\Card;
use Stripe\Customer;
use Stripe\Event;
use Stripe\Exception\ApiErrorException;
use Stripe\Invoice;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\Subscription;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\DependencyInjection\EnvVarLoaderInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Stripe\PaymentIntent;

use function PHPUnit\Framework\isNull;

class StripeController extends AbstractController
{
    private string $endpoint_secret;
    private MailChimp $mailchimp;
    public function __construct(
        MailChimp $mailchimp,
        Kernel $kernel,
        EntityManagerInterface $entityManager,        
        public  $monthlySubscription,
        public  $quarterlySubscription,
        public  $freeSubscription,
    ) {
        $this->mailchimp = $mailchimp;
        $dotenv = new Dotenv();
        $dotenv->load($kernel->getProjectDir() . DIRECTORY_SEPARATOR . '.env');
        $this->entityManager = $entityManager;
        $this->endpoint_secret = $_ENV['ENDPOINT_SECRET'];
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }

    public function _checkSubscription(userRepository $userRepository)
    {
        // Configurez la clé secrète de l'API Stripe
        $userEmail = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneUserByEmail($userEmail);
        $subscriptionId = $user->getIdSubscriptionStripe();

        try {
            // Récupérez les informations de l'abonnement Stripe
            $subscription = Subscription::retrieve($subscriptionId);

            // Récupérez la date de fin de l'abonnement
            $endDate = date('Y-m-d', $subscription->current_period_end);
            $currentDate = date('Y-m-d');
            $data = (["subscription_is_not_expired" => $endDate > $currentDate ? true : false, "endDate" => $endDate, "currentDate" => $currentDate, "subscription" => $subscription]);

            return $data;
        } catch (\Exception $e) {
            //return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    #[Route('/api/getInvoice', name: 'get lists invoice')]
    public function getInvoicesAction(userRepository $userRepository)
    {
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $customerId = $user->getIdClientStripe();

        if (!$customerId) {
            return new JsonResponse(["success" => false, "response" => "Aucune facture trouvé pour ce client."], 400);
        }

        $allInvoices = Invoice::all([
            'customer' => $customerId
        ]);


        //$invoices = Invoice::retrieve(['customer' => $user->getIdClientStripe()]);

        return new JsonResponse($allInvoices);
    }

    #[Route('/api/getCurrentSubscription', name: 'getCurrentSubscription', methods: ['GET'])]
    public function getCurrentSubscription(userRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $customerId = $user->getIdClientStripe();

        if (!$customerId) {
            return new JsonResponse(["success" => false, "response" => "Aucun abonnement trouvé pour ce client."], 400);
        }

        $subscriptions = Subscription::all(['customer' => $customerId]);

        // Vérifier s'il y a un abonnement lié au client
        if ($subscriptions->total_count === 0) {
            return new JsonResponse("Aucun abonnement trouvé pour ce client.", 400);
        }

        $subscriptionId = $subscriptions->data[0]->id;

        // Récupérer les détails de l'abonnement
        $subscription = Subscription::retrieve($subscriptionId);

        return new JsonResponse($subscription);
    }
    
    #[Route('/api/cancelSubscription', name: 'cancelSubscription', methods: ['PUT'])]
    public function cancelSubscription(userRepository $userRepository)
    {
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $subscriptions = Subscription::all(['customer' => $user->getIdClientStripe()]);

         // Vérifier s'il y a un abonnement lié au client
         if ($subscriptions->total_count === 0) {
            return new Response("Aucun abonnement trouvé pour ce client.", 400);
        }
        $subscriptionId = $subscriptions->data[0]->id;

        //return new JsonResponse(($subscriptionId));
        try {
            Subscription::update(
                $subscriptionId,
                [
                    "cancel_at_period_end" => true,
                ]
            );
            return new JsonResponse(['message' => "Annulation effectuée, l'abonnement ne sera pas renouvelé"], 400);
        } catch (ApiErrorException $e) {
            return new JsonResponse(['message' => "Erreur lors de l'annulation de l'abonnement."], 400);
        }
    }

    #[Route('/api/infoCreditCard', name: 'infoCreditCard', methods: ['GET'])]
    public function getLastFourDigits(userRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $customerId = $user->getIdClientStripe();

        // Retrieve all payment methods for the customer
        $customer = Customer::retrieve($customerId);
        $defaultPaymentMethod = $customer->invoice_settings->default_payment_method;

        // Check if any payment method is a card
        $paymentMethod = PaymentMethod::retrieve($defaultPaymentMethod);
        return new JsonResponse([
            'last4' => $paymentMethod->card->last4,
            'exp_month' => $paymentMethod->card->exp_month,
            'exp_year' => $paymentMethod->card->exp_year,
            'name' => $user->getFirstName() . " " . $user->getLastName()
        ]);
    }
    public function getPlanSubscriptionSelected(int $subscriptionSelected)
    {
        if ($subscriptionSelected === 1)
            return $this->monthlySubscription;
        if ($subscriptionSelected === 2)
            return $this->quarterlySubscription;
        // if ($subscriptionSelected === 3)
        //   return $this->freeSubscription;
        return false;
    }


    #[Route('/api/updateCreditCard', name: 'updateCreditCard', methods: ['PUT'])]
    #[OA\Parameter(name: 'cardId', in: "query", required: true)]
    public function updateCreditCard(userRepository $userRepository, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $customerId = $user->getIdClientStripe();

        try {
            $customer = Customer::update($customerId, [
                'invoice_settings' => [
                    'default_payment_method' => $data['cardId'],
                ],
            ]);
            return new JsonResponse(['message' => 'Carte de crédit mise à jour avec succès.']);
        } catch (ApiErrorException $e) {
            return new JsonResponse(['message' => "Une erreur est survenue, le moyen de paiement n'a pas pu être mis à jour."]);
        }
    }

    #[Route('/api/changeTagMailChimp', name: 'addMailChimpMember', methods: ['POST'])]
    #[OA\Parameter(name: 'isFreeAccount', in: "query", required: true)]
    public function addMailChimpMember(Request $request, userRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $listId = "f9470226d5";
        $jsonData = json_decode($request->getContent(), true);

        $email = $user->getEmail();
        $firstName =$user->getFirstName();
        $lastName = $user->getLastName();

        if (!isset($jsonData['isFreeAccount'] )) {
            return $this->json(['message' => 'isFreeAccount doit être définie à true ou false'], 409);
        }
        $tags = $jsonData['isFreeAccount'] ? ['Abonné COD gratuit'] :  ['Abonné COD paying'];

        $member = [
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => [
                'FNAME' => $firstName,
                'LNAME' => $lastName,
            ],
            'tags' => $tags,
        ];

        if(($user->getMailchimpTag() === null)){
            $this->mailchimp->post("lists/$listId/members", $member);
            if (!$this->mailchimp->success()) {
                return $this->json(['message' => $this->mailchimp->getLastError()], 500);
            }
            $user->setMailchimpTag(json_encode($tags));
            $entityManager->flush();
            return new JsonResponse(['message' => 'Membre ajouté avec succès']);            
        }else{
            return new JsonResponse(['message'=>"Ce membre a déjà été rajouté à la liste mailchimp"], 400);
        }
     
    }
    


    #[Route('/api/changeSubscription', name: 'changeSubscription', methods: ['PUT'])]
    #[OA\Parameter(name: 'subscriptionSelected', in: "query", required: true)]
    public function changeSubscription(userRepository $userRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $list_id = "f9470226d5";
        $price_id_mensuel = "price_1NwqwRFnV1sRkwn0cRKvCyLc"; //9.99 eur
        $price_id_trimestriel = "price_1NwqvOFnV1sRkwn0yaK0jhlH"; //26.99 eur 
        $userEmail = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneUserByEmail($userEmail);
        $customerStripeId = $user->getIdClientStripe();
        $data = json_decode($request->getContent(), true);
        $tag = ['Abonné COD paying'];


        $planSubscriptionSelected = $this->getPlanSubscriptionSelected($data['subscriptionSelected']);
        $subscribed = $this->_checkSubscription(userRepository: $userRepository);
        $isSubscribed = true;

        if(!isset($subscribed)){
            $isSubscribed = false;
        }else{
            if($subscribed['subscription']['status'] === "canceled"){
                $isSubscribed = false;
            }
        }

        if ($planSubscriptionSelected === false)
            return new Response("Veuillez sélectionner un abonnement existant", 400);


         if ($customerStripeId === null || !$isSubscribed) {

            // Créez un client Stripe
            $stripeCustomer = Customer::create([
                //'source' => 'tok_visa', // Test token representing a Visa card
                'email' => $userEmail, // Adresse e-mail du client
                'name' =>  $user->getFirstName() . " " . $user->getLastName()
            ]);
            $stripeCustomerId = $stripeCustomer->id;

            $user->setIdClientStripe($stripeCustomerId);
            $entityManager->persist($user);
            $entityManager->flush();
            $newStripeCustomerId = $user->getIdClientStripe();

            $currentSubscription = Subscription::all(['customer' => $newStripeCustomerId]);

            if (count($currentSubscription->data) === 0) {
                $subscription = Subscription::create([
                    'customer' =>  $newStripeCustomerId,
                    'items' => [
                        [
                            'price' => $data['subscriptionSelected'] === 2 ? $price_id_trimestriel : $price_id_mensuel, // ID du produit
                        ],
                    ],
                    'payment_behavior' => 'default_incomplete',
                    'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                    'expand' => ['latest_invoice.payment_intent'],
                ]);

                $user->setIdSubscriptionStripe($subscription->id);
                $entityManager->persist($user);
                $entityManager->flush();

                return new Response(json_encode($subscription),  200, ['Content-Type' => 'application/json']);
            }
        }


        // Check if abonnement is not completed
        $subscription = Subscription::retrieve($user->getIdSubscriptionStripe());


        if ($subscription->status === 'incomplete') {
            // L'abonnement est incomplet et a une facture en attente de paiement
            $invoiceId = $subscription->latest_invoice;

            // Récupérez la facture en utilisant son ID
            $invoice = Invoice::retrieve($invoiceId);

            // Vérifiez si la facture a un paiement intention (payment intent)
            if ($invoice->payment_intent) {
                $paymentIntentId = $invoice->payment_intent;

                // Utilisez l'ID du paiement intention pour relancer le paiement
                $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

                return new JsonResponse(["latest_invoice" => [
                    "payment_intent" => [
                        "client_secret" => $paymentIntent->client_secret
                    ]
                ]]);

                // Mettez à jour le paiement intention pour effectuer une nouvelle tentative de paiement
                $paymentIntent->confirm();
            }
        }

        //Update abonnement

        $subscriptions = Subscription::all(['customer' => $user->getIdClientStripe()]);
        $subscriptionId = $subscriptions->data[0]->id;
        $subscription = Subscription::retrieve($subscriptionId);
        $planId = $subscription->items->data[0]->id;

        try {
            $customerId = $user->getIdClientStripe();
            $customer = Customer::retrieve($customerId);
            $defaultPaymentMethod = $customer->invoice_settings->default_payment_method;

            $subscriptionUpdateData = [
                'items' => [
                    [
                        'id' => $planId,
                        'price' => $data['subscriptionSelected'] === 2 ? $price_id_trimestriel : $price_id_mensuel,
                    ]
                ],
                'proration_behavior' => 'create_prorations',
            ];

            if ($defaultPaymentMethod !== null) {
                $subscriptionUpdateData['default_payment_method'] = $defaultPaymentMethod;
            }

            //return new JsonResponse($subscriptionUpdateData, 400);

            $subscription = Subscription::update($subscriptionId, $subscriptionUpdateData);

            $user->setIdSubscriptionStripe($subscription->id);
            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Mise à jour de l\'abonnement réussie.', 'data' => $subscription]);
        } catch (ApiErrorException $e) {
            return new JsonResponse(['message' => 'Erreur lors de la mise à jour de l\'abonnement.', 'success' => false, 'error' => $e], 400);
        }
    }



    #[Route('/api/check-subscription', name: 'check_subscription', methods: ['GET'])]
    public function checkSubscription(userRepository $userRepository)
    {
        // Configurez la clé secrète de l'API Stripe
        $userEmail = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneUserByEmail($userEmail);

        try {
            // Récupérez les informations de l'abonnement Stripe
            $subscription = Subscription::retrieve($user->getIdSubscriptionStripe());

            // Récupérez la date de fin de l'abonnement
            $endDate = date('Y-m-d', $subscription->current_period_end);
            $currentDate = date('Y-m-d');

            

            return new JsonResponse(["subscription_is_not_expired" => $endDate > $currentDate ? true : false, "endDate" => $endDate, "currentDate" => $currentDate, "subscription" => $subscription], 200);
        } catch (\Exception $e) {
            //return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @throws ApiErrorException
     */
    #[Route('/api/updateCreditCard', name: 'updateCreditCard', methods: ['PUT'])]
    #[OA\Parameter(name: 'cardId', in: "query", required: true)]

    public function updateCard(userRepository $userRepository, Request $request)
    {

        $data = json_decode($request->getContent(), true);
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        // Récupérer l'ID de l'utilisateur à partir des données de la requête
        $customerId = $user->getIdClientStripe();
        $customer = Customer::retrieve($customerId);

        try {
            $existingPaymentMethod = $customer->invoice_settings->default_payment_method;
            $newPaymentMethod = PaymentMethod::retrieve($data['cardId']);
            if ($newPaymentMethod) {
                if ($existingPaymentMethod) {
                    $paymentMethod = PaymentMethod::retrieve($existingPaymentMethod);
                    $paymentMethod->detach();
                }
                $newPaymentMethod->attach(['customer' => $customerId]);

                $customer->update($customerId, [
                    'invoice_settings' => [
                        'default_payment_method' => $newPaymentMethod->id
                    ]
                ]);
            } else {
                throw new \Exception("Erreur lors de la mise à jour de la carte bancaire, verifier les données envoyées.");
            }

            // Mettre à jour la méthode de paiement par défaut du client
            Customer::update(
                $customerId,
                ['invoice_settings' => ['default_payment_method' =>  $data['cardId']]]
            );
            // Mise à jour réussie
            return new JsonResponse(['message' => 'Mise à jour de la carte bancaire réussie.']);
        } catch (ApiErrorException $e) {
            return new JsonResponse(['error' => 'Erreur lors de la mise à jour de la carte bancaire'], 400);
        }
    }
    #[Route('/eeee', name: 'get lists ssasa')]
    public function eee(Request $request): Response
    {
        // Créez un client Stripe
        $customer = Customer::create([
            'source' => 'tok_visa', // Test token representing a Visa card
            'email' => 'exemple@exemple.com', // Adresse e-mail du client
        ]);

        // Créez l'abonnement Stripe
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [
                [
                    'price' => 'price_1N6ZrBBqzQ1hjv6pAcJMWxIx', // ID du produit
                ],
            ],
        ]);

        // Récupérez l'ID de l'abonnement
        $subscriptionId = $subscription->id;

        //  'ID de l'abonnement (   base de données)
        return new Response('succes', 200);
    }

    #[Route('/get_list_mailChimp', name: 'stripe_webhook', methods: "POST")]
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->headers->get('Stripe-Signature');
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $this->endpoint_secret
            );
        } catch (\UnexpectedValueException | \InvalidArgumentException $e) {
            $error = match (true) {
                $e instanceof \UnexpectedValueException => ["message" => 'Bad Signature Received', "codeHttp" => 400],
                $e instanceof \InvalidArgumentException => ["message" => 'Data has been modified after signing', 'codeHttp' => 400]
            };
            return new Response($error["message"], $error['codeHttp']);
        }

        $message = match ($event->type) {
            'customer.subscription.created'  => 1,
            'invoice.payment_succeeded' => 2,
            default => 3,
        };

        return new Response('Webhook Received', 200);
    }

    #[Route('/get_list_mailChimp', name: 'get lists mailChimp')]
    public function mailChimp(Request $request): Response
    {
        // Vérifiez si le client existe déjà sur Stripe en utilisant son e-mail
        $email = 'test@example.com';

        try {
            $customer = Customer::retrieve(['email' => $email]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Le client n'existe pas, on le crée
            $customer = Customer::create([
                'email' => $email,
            ]);
        }

        // Créez l'abonnement pour le client
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [
                [
                    'price' => 'prod_NsKWI9enNDvQkS',
                ],
            ],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
        ]);
        // Vérifiez si l'abonnement a été créé avec succès
        if ($subscription->status === 'active') {
            return new Response('Félicitations, votre abonnement a bien été ajouté !');
        }
        return new Response("Une erreur est survenue.");
    }


    private function getOrCreateIfCustomerNotExist($email)
    {
        // Vérification si l'adresse e-mail existe déjà dans Stripe
        $existingCustomer = Customer::all(['email' => $email]);
        if (empty($existingCustomer->data)) {
            // L'adresse e-mail n'existe pas encore, création d'un nouveau client dans Stripe
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

            return Customer::create([
                'email' => $email,
                'name' => 'John Doe',
                // Autres champs optionnels pour les informations du client
            ]);
        } else {
            // L'adresse e-mail existe déjà, récupération du premier client trouvé
            return $existingCustomer->data[0];
        }
    }
    public function createSubscription($customerId, $paymentMethodId, $priceId)
    {
        // Vérification si le client a déjà une carte avec le même identifiant de méthode de paiement
        $existingPaymentMethods = PaymentMethod::all([
            'customer' => $customerId,
            'type' => 'card',
        ]);

        $hasExistingPaymentMethod = false;
        foreach ($existingPaymentMethods->data as $paymentMethod) {
            if ($paymentMethod->id === $paymentMethodId) {
                $hasExistingPaymentMethod = true;
                break;
            }
        }

        if (!$hasExistingPaymentMethod) {
            // La carte fournie n'existe pas encore pour ce client, ajouter la carte au client
            $paymentMethod = PaymentMethod::attach($paymentMethodId, [
                'customer' => $customerId,
            ]);
        } else {
            // La carte fournie existe déjà pour ce client, utilise-la pour l'abonnement
            $paymentMethod = $paymentMethodId;
        }

        // Création de l'abonnement avec la carte
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $subscription = Subscription::create([
            'customer' => $customerId,
            'items' => [
                [
                    'price' => $priceId,
                ],
            ],
            'default_payment_method' => $paymentMethod,
            // Autres options d'abonnement
        ]);

        // Faites quelque chose avec l'abonnement, par exemple, enregistrez-le dans votre base de données

        // Retournez l'abonnement pour référence future
        return $subscription;
    }
}
