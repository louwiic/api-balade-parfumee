<?php

namespace ContainerQzDRh2x;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_FZL4CLcService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.FZL4CLc' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.FZL4CLc'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'layering' => ['privates', '.errored..service_locator.FZL4CLc.App\\Entity\\Layering', NULL, 'Cannot autowire service ".service_locator.FZL4CLc": it needs an instance of "App\\Entity\\Layering" but this type has been excluded in "config/services.yaml".'],
        ], [
            'layering' => 'App\\Entity\\Layering',
        ]);
    }
}