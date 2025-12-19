<?php

namespace Tourze\UserAttributeBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

final class UserAttributeExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return \dirname(__DIR__) . '/Resources/config';
    }
}
