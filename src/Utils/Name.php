<?php

declare(strict_types = 1);

namespace drupol\ComposerPackages\Utils;

/**
 * Class Name.
 */
class Name
{
    /**
     * @param string $str
     *
     * @return string
     */
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
