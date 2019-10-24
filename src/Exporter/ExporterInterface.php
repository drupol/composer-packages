<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

/**
 * Interface ExporterInterface.
 */
interface ExporterInterface
{
    /**
     * @return array
     */
    public function exportToArray(): array;

    /**
     * @param string $template
     * @param string $destination
     *
     * @return mixed
     */
    public function exportToFile(string $template, string $destination);
}
