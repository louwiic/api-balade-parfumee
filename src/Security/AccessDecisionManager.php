<?php

namespace App\Security;

use App\Kernel;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Subscription;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class AccessDecisionManager
{
    private $decisionManager;
    private $userRepository;

    public function __construct(
        AccessDecisionManagerInterface $decisionManager, 
        Kernel $kernel,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
        
    )
    {
        $this->decisionManager = $decisionManager;
        $this->userRepository = $userRepository;

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }

    public function checkDataAccess($user)
    {
    
        // Récupérez les informations de l'abonnement Stripe
        $subscription = Subscription::retrieve($user);
        // Récupérez la date de fin de l'abonnement
        $endDate = date('Y-m-d', $subscription->current_period_end);
        //$endDate = new \DateTime('@' . $subscription->current_period_end);
        $currentDate = date('Y-m-d');

        return $endDate > $currentDate ? true : false;

     }
}
