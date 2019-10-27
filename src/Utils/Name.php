<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Utils;

final class Name
{
    public static function camelize(string $str): string
    {
        $str = preg_replace(
            '/[^a-z0-9]+/i',
            ' ',
            $str
        );

        if (null === $str) {
            return '';
        }

        return lcfirst(
            str_replace(
                ' ',
                '',
                ucwords(
                    $str
                )
            )
        );
    }
}
