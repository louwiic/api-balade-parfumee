<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerCjHDrY7\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerCjHDrY7/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerCjHDrY7.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerCjHDrY7\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerCjHDrY7\App_KernelDevDebugContainer([
    'container.build_hash' => 'CjHDrY7',
    'container.build_id' => '3f5689b6',
    'container.build_time' => 1696861390,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerCjHDrY7');
