<?php

namespace ContainerQzDRh2x;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getProfilControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Controller\ProfilController' shared autowired service.
     *
     * @return \App\Controller\ProfilController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/ProfilController.php';

        $container->services['App\\Controller\\ProfilController'] = $instance = new \App\Controller\ProfilController(($container->privates['debug.serializer'] ?? $container->load('getDebug_SerializerService')), ($container->privates['App\\Repository\\ProfilRepository'] ?? $container->load('getProfilRepositoryService')), ($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container)), ($container->privates['App\\Repository\\FragranceRepository'] ?? $container->load('getFragranceRepositoryService')), ($container->privates['App\\Repository\\UserRepository'] ?? $container->load('getUserRepositoryService')));

        $instance->setContainer(($container->privates['.service_locator._FWhGxE'] ?? $container->load('get_ServiceLocator_FWhGxEService'))->withContext('App\\Controller\\ProfilController', $container));

        return $instance;
    }
}
