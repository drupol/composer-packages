<?php

declare(strict_types=1);

/**
 * @file.
 * ExporterInterface.php
 */

namespace drupol\ComposerPackages\Exporter;

interface ExporterInterface
{
    public function exportToArray(): array;

    public function exportToFile(string $filename);
}
