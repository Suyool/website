<?php

namespace Simly\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomDoctrineCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Register your custom types or configurations here
        // Use service locator or tags to access services

        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();

        // Example: Registering a custom type
        $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('your_custom_type', 'Simly\DBAL\Types\YourCustomType');
    }
}