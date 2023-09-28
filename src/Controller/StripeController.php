<?php

namespace App\Controller;

use App\Kernel;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

class StripeController extends AbstractController
{
    private string $endpoint_secret;
    public function __construct(
        Kernel $kernel,
        EntityManagerInterface $entityManager,
        public  $monthlySubscription,
        public  $quarterlySubscription,
        public  $freeSubscription
    )
    {
        $dotenv = new Dotenv();
        $dotenv->load($kernel->getProjectDir() . DIRECTORY_SEPARATOR . '.env');

        $this->entityManager = $entityManager;
        $this->endpoint_secret = $_ENV['ENDPOINT_SECRET'];
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }



    #[Route('/api/getInvoice', name: 'get lists invoice')]
    public function getInvoicesAction(userRepository $userRepository)
    {
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());

        $invoices = Invoice::all(['customer' => $user->getIdClientStripe()]);
        return new JsonResponse($invoices);
    }

    #[Route('/api/getCurrentSubscription', name: 'getCurrentSubscription', methods: ['GET'])]
    public function getCurrentSubscription(userRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        $customerId = $user->getIdClientStripe();

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
        try {
            $subscription = Subscription::update($subscriptionId, [
                'cancel_at_period_end' => true
            ]);
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

        // Check if any payment method is a card
        $defaultPaymentMethod = $customer->invoice_settings->default_payment_method;
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

    #[Route('/api/changeSubscription', name: 'changeSubscription', methods: ['PUT'])]
    #[OA\Parameter(name: 'subscriptionSelected', in: "query", required: true)]
    public function changeSubscription(userRepository $userRepository, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $planSubscriptionSelected = $this->getPlanSubscriptionSelected($data['subscriptionSelected']);
        $user = $userRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($planSubscriptionSelected === false)
            return new Response("Veuillez sélectionner un abonnement existant" , 400);
       // return new JsonResponse(['message' => $subscriptionId], 200);
        $subscriptions = Subscription::all(['customer' => $user->getIdClientStripe()]);

        // Vérifier s'il y a un abonnement lié au client
        if ($subscriptions->total_count === 0) {
            return new Response("Aucun abonnement trouvé pour ce client.", 400);
        }
        $subscriptionId = $subscriptions->data[0]->id;

        $subscription = Subscription::retrieve($subscriptionId);

        // Récupérer l'ID du plan associé à l'abonnement
        $planId = $subscription->items->data[0]->id;
        try {
            $subscription = Subscription::update($subscriptionId, [
                'items' => [
                    [
                        'id' => $planId,
                        'plan' => $planSubscriptionSelected, // new subscription
                    ]
                ],
                'proration_behavior' => 'create_prorations'
            ]);

            return new JsonResponse(['message' => 'Mise à jour de l\'abonnement réussie.']);
        } catch (ApiErrorException $e) {
            return new JsonResponse(['message' => 'Erreur lors de la mise à jour de l\'abonnement.'], 400);
        }
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/api/updateCreditCard', name: 'updateCreditCard', methods: ['PUT'])]
    #[OA\Parameter(name: 'cardId', in: "query", required: true)]

    public function updateCard(userRepository $userRepository,Request $request)
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
                if($existingPaymentMethod) {
                    $paymentMethod = PaymentMethod::retrieve($existingPaymentMethod);
                    $paymentMethod->detach();
                }
                $newPaymentMethod->attach(['customer' => $customerId]);

                $customer->update($customerId, [
                    'invoice_settings' => [
                        'default_payment_method' => $newPaymentMethod->id
                    ]
                ]);
            }
            else
            {
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
                $payload, $sig_header, $this->endpoint_secret
            );
        }catch (\UnexpectedValueException | \InvalidArgumentException $e) {
          $error = match (true) {
              $e instanceof \UnexpectedValueException => ["message" => 'Bad Signature Received' , "codeHttp" => 400],
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
            return new Response( 'Félicitations, votre abonnement a bien été ajouté !');
        }
        return new Response( "Une erreur est survenue.");
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
        }
        else {
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