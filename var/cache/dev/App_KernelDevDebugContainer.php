<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerHjdc8BV\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerHjdc8BV/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerHjdc8BV.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerHjdc8BV\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerHjdc8BV\App_KernelDevDebugContainer([
    'container.build_hash' => 'Hjdc8BV',
    'container.build_id' => '91919c99',
    'container.build_time' => 1696860696,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerHjdc8BV');
