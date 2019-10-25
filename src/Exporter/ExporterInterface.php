<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

/**
 * Interface ExporterInterface.
 */
interface ExporterInterface
{
    public function exportToArray(): array;

    public function exportToFile(string $template, string $destination);
}
