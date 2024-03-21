<?php

namespace SharedBundle;

use SharedBundle\DependencyInjection\SharedBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
// use TestUnit\DependencyInjection\HelloWorldExtension;

class SharedBundleClass extends Bundle
{
    
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SharedBundleExtension();
    }
}