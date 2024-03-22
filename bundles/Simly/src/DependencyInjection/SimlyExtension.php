<?php

namespace Simly\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SimlyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );

        $loader->load('doctrine.yaml');
        $loader->load('services.yaml');

        // Add your custom compiler pass here (if applicable)
//        if (class_exists(YourCustomDoctrineCompilerPass::class)) {
//            $container->addCompilerPass(new YourCustomDoctrineCompilerPass());
//        }

    }

//    private function configureDoctrine(ContainerBuilder $container)
//    {
//        $mappings = [
//            \Doctrine\Persistence\Mapping\ClassMetadataFactory::createDefaultMappingDirectories(__DIR__ . '/../../Entity')
//        ];
//
//        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDiscovery($mappings));
//    }
}