<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Twig;

use drupol\ComposerPackages\Utils\Name;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class CamelCaseExtension extends AbstractExtension
{
    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('camelize', [Name::class, 'camelize']),
        ];
    }
}
