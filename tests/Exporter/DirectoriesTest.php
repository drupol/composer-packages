<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\tests\Exporter;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Installer\InstallationManager;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Locker;
use Composer\Package\RootPackage;
use Composer\Repository\RepositoryManager;
use Composer\Script\Event;
use drupol\ComposerPackages\Exporter\Directories;
use drupol\ComposerPackages\Plugin;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \drupol\ComposerPackages\Exporter\Directories
 */
final class DirectoriesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->plugin = new Plugin();
        $this->io = $this->createMock(IOInterface::class);
        $this->composer = $this->createMock(Composer::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);

        $repositoryManager = $this->createMock(RepositoryManager::class);
        $installationManager = $this->createMock(InstallationManager::class);

        $installationManager->method('getInstallPath')->willReturn(\realpath(__DIR__ . '/../../build'));

        $this->composer->setInstallationManager($installationManager);
        $this->composer->setRepositoryManager($repositoryManager);

        $locker = new Locker(
            $this->io,
            new JsonFile(__DIR__ . '/../../composer.lock'),
            $repositoryManager,
            $installationManager,
            \file_get_contents(__DIR__ . '/../../composer.json')
        );

        $rootPackage = new RootPackage('drupol/composer-packages', '1', '1.0.0');
        $config = new Config(false, \realpath(__DIR__ . '/../'));

        $this->composer->method('getEventDispatcher')->willReturn($this->eventDispatcher);
        $this->composer->method('getInstallationManager')->willReturn($installationManager);
        $this->composer->method('getLocker')->willReturn($locker);
        $this->composer->method('getPackage')->willReturn($rootPackage);
        $this->composer->method('getConfig')->willReturn($config);
    }

    public function testExportToFile(): void
    {
        $event = new Event(
            'post-install-cmd',
            $this->composer,
            $this->io
        );

        $test = new Directories($event);
        $test->exportToFile('directories.twig', __DIR__ . '/../../build/Directories.php');

        self::assertFileExists(__DIR__ . '/../../build/Directories.php');
    }

    public function testInstantiation(): void
    {
        $event = new Event(
            'post-install-cmd',
            $this->composer,
            $this->io
        );

        $test = new Directories($event);

        self::assertInstanceOf(Directories::class, $test);
    }
}
