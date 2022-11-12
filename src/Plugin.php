<?php

declare(strict_types=1);

namespace drupol\ComposerPackages;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use ReflectionException;

final class Plugin implements EventSubscriberInterface, PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [ScriptEvents::POST_AUTOLOAD_DUMP => 'regeneration'];
    }

    /**
     * @throws ReflectionException
     */
    public static function regeneration(Event $composerEvent): void
    {
        // This is to prevent issue when removing the package with composer.
        if (false === class_exists(ClassGenerator::class)) {
            return;
        }

        $composerEvent->getIO()->write('<info>drupol/composer-packages:</info> Regenerating classes...');

        (new ClassGenerator($composerEvent))->regenerateClasses();

        $composerEvent->getIO()->write('<info>drupol/composer-packages:</info> Done.');
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        $files = glob(
            sprintf(
                '%s/../build/',
                __DIR__
            )
        );

        if (false === $files) {
            return;
        }

        foreach ($files as $file) {
            unlink($file);
        }
    }
}
