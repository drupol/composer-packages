<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\tests\Utils;

use drupol\ComposerPackages\Utils\Name;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \drupol\ComposerPackages\Utils\Name
 */
final class NameTest extends TestCase
{
    /**
     * @return Generator
     */
    public function namesProvider()
    {
        yield [
            ' foo ',
            'foo',
        ];

        yield [
            ' foo bar ',
            'fooBar',
        ];

        yield [
            '',
            '',
        ];
    }

    /**
     * @dataProvider namesProvider
     *
     * @param mixed $name
     * @param mixed $camelized
     */
    public function testNameCamelize($name, $camelized): void
    {
        self::assertSame($camelized, Name::camelize($name));
    }
}
