<?php

declare(strict_types=1);

namespace drupol\ComposerPackages;

use Composer\Script\Event;
use drupol\ComposerPackages\Exporter\Directories;
use drupol\ComposerPackages\Exporter\Packages;
use drupol\ComposerPackages\Exporter\Types;
use drupol\ComposerPackages\Exporter\Versions;

/**
 * Class ClassGenerator.
 */
final class ClassGenerator
{
    /**
     * @var \Composer\Script\Event
     */
    private $event;

    /**
     * ClassGenerator constructor.
     *
     * @param \Composer\Script\Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @throws \ReflectionException
     */
    public function regenerateClasses(): void
    {
        $data = [
            Packages::class,
            Types::class,
            Directories::class,
            Versions::class,
        ];

        foreach ($data as $class) {
            $reflection = new \ReflectionClass($class);

            $template = \sprintf(
                '%s.twig',
                \mb_strtolower($reflection->getShortName())
            );

            $installPath = \sprintf(
                '%s/../build/%s.php',
                __DIR__,
                $reflection->getShortName()
            );

            /** @var \drupol\ComposerPackages\Exporter\ExporterInterface $exporter */
            $exporter = $reflection->newInstance($this->event);

            $exporter
                ->exportToFile($template, $installPath);
        }
    }
}
