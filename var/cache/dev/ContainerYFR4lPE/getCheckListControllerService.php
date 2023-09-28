<?php

namespace ContainerYFR4lPE;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCheckListControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Controller\CheckListController' shared autowired service.
     *
     * @return \App\Controller\CheckListController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/CheckListController.php';

        $container->services['App\\Controller\\CheckListController'] = $instance = new \App\Controller\CheckListController(($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container)), ($container->privates['App\\Repository\\FragranceRepository'] ?? $container->load('getFragranceRepositoryService')), ($container->privates['App\\Repository\\UserRepository'] ?? $container->load('getUserRepositoryService')));

        $instance->setContainer(($container->privates['.service_locator._FWhGxE'] ?? $container->load('get_ServiceLocator_FWhGxEService'))->withContext('App\\Controller\\CheckListController', $container));

        return $instance;
    }
}
