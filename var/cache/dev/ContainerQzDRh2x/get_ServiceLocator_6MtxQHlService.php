<?php

namespace ContainerQzDRh2x;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_6MtxQHlService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.6MtxQHl' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.6MtxQHl'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'fragrances' => ['privates', '.errored..service_locator.6MtxQHl.App\\Entity\\Fragrance', NULL, 'Cannot autowire service ".service_locator.6MtxQHl": it needs an instance of "App\\Entity\\Fragrance" but this type has been excluded in "config/services.yaml".'],
            'reviewPerfumeNote' => ['privates', '.errored..service_locator.6MtxQHl.App\\Entity\\ReviewPerfumeNote', NULL, 'Cannot autowire service ".service_locator.6MtxQHl": it needs an instance of "App\\Entity\\ReviewPerfumeNote" but this type has been excluded in "config/services.yaml".'],
        ], [
            'fragrances' => 'App\\Entity\\Fragrance',
            'reviewPerfumeNote' => 'App\\Entity\\ReviewPerfumeNote',
        ]);
    }
}