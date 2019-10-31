<?php

declare(strict_types=1);

namespace drupol\ComposerPackages;

use Composer\Script\Event;
use drupol\ComposerPackages\Exporter\Dependencies;
use drupol\ComposerPackages\Exporter\Directories;
use drupol\ComposerPackages\Exporter\ExporterInterface;
use drupol\ComposerPackages\Exporter\Packages;
use drupol\ComposerPackages\Exporter\Types;
use drupol\ComposerPackages\Exporter\Versions;

final class ClassGenerator
{
    /**
     * @var Event
     */
    private $event;

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
            Dependencies::class,
        ];

        foreach ($data as $class) {
            $reflection = new \ReflectionClass($class);

            $template = sprintf(
                '%s.twig',
                mb_strtolower($reflection->getShortName())
            );

            $installPath = sprintf(
                '%s/../build/%s.php',
                __DIR__,
                $reflection->getShortName()
            );

            /** @var ExporterInterface $exporter */
            $exporter = $reflection->newInstance($this->event);

            $exporter
                ->exportToFile($template, $installPath);
        }
    }
}
