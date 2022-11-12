<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

interface ExporterInterface
{
    /**
     * Export the data into an array.
     *
     * @return array<string>
     */
    public function exportToArray(): array;

    /**
     * Export the data into a file.
     *
     * @param string $template
     *   The template file to use.
     * @param string $destination
     *   The filepath.
     *
     * @return mixed
     */
    public function exportToFile(string $template, string $destination);
}
