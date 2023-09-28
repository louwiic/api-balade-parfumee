<?php

namespace ContainerQzDRh2x;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_Qza7MBService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.qza7_MB' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.qza7_MB'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'notification' => ['privates', '.errored..service_locator.qza7_MB.App\\Entity\\Notification', NULL, 'Cannot autowire service ".service_locator.qza7_MB": it needs an instance of "App\\Entity\\Notification" but this type has been excluded in "config/services.yaml".'],
        ], [
            'notification' => 'App\\Entity\\Notification',
        ]);
    }
}
