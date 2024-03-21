<?php

namespace Simly;

use Simly\DependencyInjection\SimlyExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TestUnit\DependencyInjection\HelloWorldExtension;

class SimlyBundle extends Bundle
{
    
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SimlyExtension();
    }
}