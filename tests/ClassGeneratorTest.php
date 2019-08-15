<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\tests;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use drupol\ComposerPackages\ClassGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \drupol\ComposerPackages\ClassGenerator
 *
 * @internal
 */
final class ClassGeneratorTest extends TestCase
{
    public function testInstantiation(): void
    {
        $composer = $this->createMock(Composer::class);
        $io = $this->createMock(IOInterface::class);

        $event = new Event(
            'post-install-cmd',
            $composer,
            $io
        );

        $test = new ClassGenerator($event);

        self::assertInstanceOf(ClassGenerator::class, $test);
    }
}
