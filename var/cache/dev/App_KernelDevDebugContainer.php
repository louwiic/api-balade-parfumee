<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container7hIolgc\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container7hIolgc/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container7hIolgc.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container7hIolgc\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \Container7hIolgc\App_KernelDevDebugContainer([
    'container.build_hash' => '7hIolgc',
    'container.build_id' => 'e2c35f5a',
    'container.build_time' => 1713375843,
], __DIR__.\DIRECTORY_SEPARATOR.'Container7hIolgc');
