<?php

namespace ContainerYFR4lPE;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_Yv6xARzService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.Yv6xARz' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.Yv6xARz'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'myFavoriteTypesOfPerfumes' => ['privates', '.errored..service_locator.Yv6xARz.App\\Entity\\MyFavoriteTypesOfPerfumes', NULL, 'Cannot autowire service ".service_locator.Yv6xARz": it needs an instance of "App\\Entity\\MyFavoriteTypesOfPerfumes" but this type has been excluded in "config/services.yaml".'],
        ], [
            'myFavoriteTypesOfPerfumes' => 'App\\Entity\\MyFavoriteTypesOfPerfumes',
        ]);
    }
}
