<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container5ZuXBOl\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container5ZuXBOl/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container5ZuXBOl.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container5ZuXBOl\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \Container5ZuXBOl\App_KernelDevDebugContainer([
    'container.build_hash' => '5ZuXBOl',
    'container.build_id' => '719c8a2e',
    'container.build_time' => 1706633584,
], __DIR__.\DIRECTORY_SEPARATOR.'Container5ZuXBOl');
