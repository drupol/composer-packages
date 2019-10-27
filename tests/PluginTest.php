<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\tests;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Installer\InstallationManager;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Link;
use Composer\Package\Locker;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackage;
use Composer\Package\RootPackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Repository\RepositoryManager;
use Composer\Script\Event;
use ComposerPackages\Dependencies;
use ComposerPackages\Directories;
use ComposerPackages\Packages;
use ComposerPackages\Types;
use ComposerPackages\Versions;
use drupol\ComposerPackages\Plugin;
use drupol\ComposerPackages\Utils\Name;
use PHPUnit\Framework\TestCase;

/**
 * @covers *
 *
 * @internal
 */
final class PluginTest extends TestCase
{
    /** @var \Composer\Composer */
    private $composer;

    /** @var \Composer\EventDispatcher\EventDispatcher */
    private $eventDispatcher;

    /** @var \Composer\IO\IOInterface */
    private $io;

    /** @var Plugin */
    private $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plugin = new Plugin();
        $this->io = $this->createMock(IOInterface::class);
        $this->composer = $this->createMock(Composer::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);

        $repositoryManager = $this->createMock(RepositoryManager::class);
        $installationManager = $this->createMock(InstallationManager::class);

        $installationManager->method('getInstallPath')->willReturn(__DIR__ . '/../build');

        $this->composer->setInstallationManager($installationManager);
        $this->composer->setRepositoryManager($repositoryManager);

        $locker = new Locker(
            $this->io,
            new JsonFile(__DIR__ . '/../composer.lock'),
            $repositoryManager,
            $installationManager,
            file_get_contents(__DIR__ . '/../composer.json')
        );

        $rootPackage = new RootPackage('drupol/composer-packages', '1', '1.0.0');
        $config = new Config(false, realpath(__DIR__ . '/../'));

        $this->composer->method('getEventDispatcher')->willReturn($this->eventDispatcher);
        $this->composer->method('getInstallationManager')->willReturn($installationManager);
        $this->composer->method('getLocker')->willReturn($locker);
        $this->composer->method('getPackage')->willReturn($rootPackage);
        $this->composer->method('getConfig')->willReturn($config);
    }

    public function testDumpVersionsClass(): void
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $vendorDir = __DIR__ . '/../build';
        $config->expects(self::any())->method('get')->with('vendor-dir')->willReturn($vendorDir . '/foo');

        $locker = $this->getMockBuilder(Locker::class)->disableOriginalConstructor()->getMock();
        $repositoryManager = $this->getMockBuilder(RepositoryManager::class)->disableOriginalConstructor()->getMock();
        $installManager = $this->getMockBuilder(InstallationManager::class)->disableOriginalConstructor()->getMock();
        $installManager
            ->method('getInstallPath')
            ->willReturnCallback(
                static function (PackageInterface $package) {
                    return Name::camelize($package->getName());
                }
            );

        $repository = $this->createMock(InstalledRepositoryInterface::class);
        $expectedPath = realpath($vendorDir);

        if (!file_exists($expectedPath)) {
            mkdir($expectedPath, 0777, true);
        }

        $locker
            ->expects(self::any())
            ->method('getLockData')
            ->willReturn([
                'packages' => [
                    [
                        'name' => 'a/b',
                        'version' => '1.0.0',
                        'type' => 'a',
                        'require' => [
                            'drupol/a' => '1.0',
                            'drupol/b' => '1.0',
                            'drupol/c' => '1.0',
                        ],
                    ],
                    [
                        'name' => 'foo/bar',
                        'version' => '1.2.3',
                        'source' => [
                            'type' => 'url',
                            'url' => 'http://foo',
                            'reference' => 'abc123',
                        ],
                        'type' => 'b',
                    ],
                    [
                        'name' => 'baz/tab',
                        'version' => '4.5.6',
                        'source' => [
                            'type' => 'url',
                            'url' => 'http://foo',
                            'reference' => 'def456',
                        ],
                        'type' => 'c',
                        'url' => 'http://foo',
                    ],
                ],
                'packages-dev' => [
                    [
                        'name' => 'tar/taz',
                        'version' => '7.8.9',
                        'source' => [
                            'type' => 'url',
                            'url' => 'http://foo',
                            'reference' => 'ghi789',
                        ],
                        'type' => 'd',
                    ],
                ],
            ]);
        $repositoryManager->expects(self::any())->method('getLocalRepository')->willReturn($repository);

        $composer = $this->createMock(Composer::class);

        $composer->expects(self::any())->method('getConfig')->willReturn($config);
        $composer->expects(self::any())->method('getLocker')->willReturn($locker);
        $composer->expects(self::any())->method('getRepositoryManager')->willReturn($repositoryManager);
        $composer->expects(self::any())->method('getPackage')->willReturn($this->getRootPackageMock());
        $composer->expects(self::any())->method('getInstallationManager')->willReturn($installManager);

        $this->plugin::regeneration(
            new Event(
                'post-install-cmd',
                $composer,
                $this->io
            )
        );

        include $expectedPath . '/Directories.php';

        include $expectedPath . '/Packages.php';

        include $expectedPath . '/Types.php';

        include $expectedPath . '/Versions.php';

        $packages = new Packages();
        $directories = new Directories();
        $types = new Types();
        $versions = new Versions();
        $dependencies = new Dependencies();

        self::assertSame('drupol/composer-packages', \ComposerPackages\Packages::ROOT_PACKAGE_NAME);
        self::assertInstanceOf(PackageInterface::class, \ComposerPackages\Packages::fooBar());
        self::assertCount(4, $packages);
        self::assertInstanceOf(PackageInterface::class, \ComposerPackages\Packages::get('foo/bar'));
        self::assertNull($packages::unexistent());

        self::assertCount(8, $types);
        self::assertIsIterable($types::a());
        self::assertIsIterable($types::library());
        self::assertIsIterable($types::application());
        self::assertIsIterable($types::metapackage());
        self::assertIsIterable($types::composerPlugin());
        self::assertIsIterable($types::a());
        self::assertIsIterable($types::a());
        self::assertCount(1, $types::a());
        self::assertCount(0, $types::unexistent());

        self::assertCount(4, $directories);
        self::assertIsIterable($directories);
        self::assertSame('bazTab', $directories::bazTab());
        self::assertNull($directories::unexistent());

        self::assertCount(4, $versions);
        self::assertIsIterable($versions);
        self::assertSame('4.5.6', $versions::bazTab());
        self::assertNull($versions::unexistent());

        self::assertCount(3, $dependencies::aB());
        // I had to use `yield from [];` instead of just `yield;` @see https://github.com/sebastianbergmann/phpunit/pull/3316
        self::assertCount(0, $dependencies::unexistent());
    }

    public function testGetSubscribedEvents(): void
    {
        $events = Plugin::getSubscribedEvents();

        self::assertSame(
            ['post-autoload-dump' => 'regeneration'],
            $events
        );

        foreach ($events as $callback) {
            self::assertIsCallable([$this->plugin, $callback]);
        }
    }

    public function testRegeneration(): void
    {
        $event = $this->createMock(Event::class);
        $event->method('getComposer')->willReturn($this->composer);
        $event->method('getIO')->willReturn($this->io);
        $event->method('getName')->willReturn('post-install-cmd');

        $this
            ->io
            ->expects(self::exactly(2))
            ->method('write')
            ->withConsecutive(
                ['<info>drupol/composer-packages:</info> Regenerating classes...'],
                ['<info>drupol/composer-packages:</info> Done.']
            );

        $this->plugin::regeneration($event);

        self::assertFileExists(__DIR__ . '/../build/Directories.php');
        self::assertFileExists(__DIR__ . '/../build/Packages.php');
        self::assertFileExists(__DIR__ . '/../build/Types.php');
    }

    private function getRootPackageMock(): RootPackageInterface
    {
        $package = $this->createMock(RootPackageInterface::class);
        $package->expects(self::any())->method('getName')->willReturn('drupol/composer-packages');
        $package->expects(self::any())->method('getPrettyVersion')->willReturn('1.3.5');
        $package->expects(self::any())->method('getSourceReference')->willReturn('aaabbbcccddd');
        $link = $this->createMock(Link::class);
        $link->expects(self::any())->method('getTarget')->willReturn('some-replaced/package');
        $link->expects(self::any())->method('getPrettyConstraint')->willReturn('self.version');
        $package->expects(self::any())->method('getReplaces')->willReturn([$link]);

        return $package;
    }
}
