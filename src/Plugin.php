<?php

declare(strict_types=1);

namespace drupol\ComposerPackages;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

/**
 * Class Plugin.
 */
final class Plugin implements EventSubscriberInterface, PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [ScriptEvents::POST_AUTOLOAD_DUMP => 'regeneration'];
    }

    /**
     * @param Event $composerEvent
     *
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function regeneration(Event $composerEvent): void
    {
        $composerEvent->getIO()->write('<info>drupol/composer-packages:</info> Regenerating classes...');

        (new ClassGenerator($composerEvent))->regenerateClasses();

        $composerEvent->getIO()->write('<info>drupol/composer-packages:</info> Done.');
    }
}
