<?php
// src/EventListener/JwtCreatedListener.php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedListener
{
    public function onJwtCreated(JWTCreatedEvent $event)
    {
        // Récupérez l'utilisateur à partir de l'événement
        $user = $event->getUser();

        // Modifiez les données du token
        $payload = $event->getData();
        $payload['username'] =  $user->getUserIdentifier();

        // Mettez à jour les données dans l'événement
        $event->setData($payload);
    }
}
