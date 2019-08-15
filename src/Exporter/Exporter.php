<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

use Composer\Config;
use Composer\Package\AliasPackage;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Script\Event;
use drupol\ComposerPackages\Twig\CamelCaseExtension;
use drupol\ComposerPackages\Twig\VarExportExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class Exporter.
 */
abstract class Exporter implements ExporterInterface
{
    /**
     * @var \Composer\Script\Event
     */
    private $event;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * Exporter constructor.
     *
     * @param \Composer\Script\Event $event
     */
    public function __construct(Event $event)
    {
        $this->twig = new Environment(
            new FilesystemLoader(__DIR__ . '/../../templates')
        );

        $this->twig->addExtension(new CamelCaseExtension());
        $this->twig->addExtension(new VarExportExtension());

        $this->event = $event;
    }

    /**
     * @param string $filename
     *
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function exportToFile(string $filename): void
    {
        $composer = $this->getEvent()->getComposer();

        $data = $this->exportToArray() + [
            'generatedAt' => \time(),
            'rootPackageName' => $this->getEvent()->getComposer()->getPackage()->getName(),
        ];

        $installPath = $this->locateRootPackageInstallPath($composer->getConfig(), $composer->getPackage())
            . '/build/' . (new \ReflectionClass($this))->getShortName() . '.php';

        $installPathTmp = $installPath . '_' . \uniqid('tmp', true);
        \file_put_contents($installPathTmp, $this->twig->render($filename, $data));
        \chmod($installPathTmp, 0664);
        \rename($installPathTmp, $installPath);
    }

    /**
     * @return \Composer\Script\Event
     */
    protected function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param \Composer\Package\RootPackageInterface $rootPackage
     *
     * @return \Composer\Package\PackageInterface
     */
    private function getRootPackageAlias(RootPackageInterface $rootPackage): PackageInterface
    {
        $package = $rootPackage;

        while ($package instanceof AliasPackage) {
            $package = $package->getAliasOf();
        }

        return $package;
    }

    /**
     * @param \Composer\Config $composerConfig
     * @param \Composer\Package\RootPackageInterface $rootPackage
     *
     * @return string
     */
    private function locateRootPackageInstallPath(Config $composerConfig, RootPackageInterface $rootPackage): string
    {
        if ('drupol/composer-packages' === $this->getRootPackageAlias($rootPackage)->getName()) {
            return \dirname($composerConfig->get('vendor-dir'));
        }

        return $composerConfig->get('vendor-dir') . '/drupol/composer-packages';
    }
}
