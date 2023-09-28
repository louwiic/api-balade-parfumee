<?php

namespace ContainerYFR4lPE;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAdminControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Controller\AdminController' shared autowired service.
     *
     * @return \App\Controller\AdminController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/AdminController.php';

        $a = ($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container));

        $container->services['App\\Controller\\AdminController'] = $instance = new \App\Controller\AdminController(($container->privates['debug.serializer'] ?? $container->load('getDebug_SerializerService')), ($container->privates['App\\Repository\\ContentExclusiveRepository'] ?? $container->load('getContentExclusiveRepositoryService')), $a, ($container->services['kernel'] ?? $container->get('kernel', 1)), $a, $container->getEnv('ID_STRIPE_SUBSCRIPTION_MONTHLY'), $container->getEnv('ID_STRIPE_SUBSCRIPTION_QUARTERLY'), $container->getEnv('ID_STRIPE_SUBSCRIPTION_FREE'));

        $instance->setContainer(($container->privates['.service_locator._FWhGxE'] ?? $container->load('get_ServiceLocator_FWhGxEService'))->withContext('App\\Controller\\AdminController', $container));

        return $instance;
    }
}
