<?php

declare(strict_types=1);

namespace drupol\ComposerPackages;

use Composer\Script\Event;
use drupol\ComposerPackages\Exporter\Directories;
use drupol\ComposerPackages\Exporter\Packages;
use drupol\ComposerPackages\Exporter\Types;

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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function regenerateClasses(): void
    {
        (new Packages($this->event))
            ->exportToFile('packages.twig');

        (new Types($this->event))
            ->exportToFile('types.twig');

        (new Directories($this->event))
            ->exportToFile('directories.twig');
    }
}
