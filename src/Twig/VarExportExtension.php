<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class VarExportExtension extends AbstractExtension
{
    /**
     * @param mixed $data
     *
     * @return string
     *   The exported variable.
     */
    public function export($data): string
    {
        return var_export($data, true);
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('export', [$this, 'export'], ['is_safe' => ['html']]),
        ];
    }
}
