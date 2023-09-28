<?php

namespace ContainerQzDRh2x;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_J38OCQService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.J38_oCQ' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.J38_oCQ'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'perfumeTrialSheet' => ['privates', '.errored..service_locator.J38_oCQ.App\\Entity\\PerfumeTrialSheet', NULL, 'Cannot autowire service ".service_locator.J38_oCQ": it needs an instance of "App\\Entity\\PerfumeTrialSheet" but this type has been excluded in "config/services.yaml".'],
        ], [
            'perfumeTrialSheet' => 'App\\Entity\\PerfumeTrialSheet',
        ]);
    }
}