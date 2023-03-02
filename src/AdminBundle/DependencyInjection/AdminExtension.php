<?php

namespace App\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class AdminExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
       /* $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config/')
        );

        $toLoad = array(
            'vich_uploader.yml', '2fa.yml ', 'fosckeditor.yml', 'one_uploader.yml', 'security.yml'
        );

        foreach ($toLoad as $file) {
            $loader->load($file);
        }*/
    }
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        /*$loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load("security.yml");*/
    }
}
