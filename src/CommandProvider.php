<?php

declare(strict_types=1);

namespace drupol\ComposerPackages;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use drupol\ComposerPackages\Commands\Dependencies;
use drupol\ComposerPackages\Commands\PackagesType;
use drupol\ComposerPackages\Commands\ReverseDependencies;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return [
            new Dependencies(),
            new PackagesType(),
            new ReverseDependencies(),
        ];
    }
}
