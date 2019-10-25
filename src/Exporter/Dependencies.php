<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

use Composer\Package\Loader\ArrayLoader;
use Composer\Package\PackageInterface;

class Dependencies extends Exporter
{
    public function exportToArray(): array
    {
        $data = $this->getEvent()->getComposer()->getLocker()->getLockData();

        $packagesData = \array_merge(
            $data['packages'],
            $data['packages-dev']
        );

        $packageDeps = [];

        foreach ($packagesData as $package) {
            $package = (new ArrayLoader())->load($package);
            $packageName = $package->getName();
            $packageDeps += [$packageName => []];
            $this->getDependenciesOf($packageDeps[$package->getName()], $package);

            $packageDeps[$packageName] = \array_values($packageDeps[$packageName]);
        }

        $regex = $this->buildRegex($packageDeps);

        return \compact('packageDeps', 'regex');
    }

    protected function getDependenciesOf(array &$carry, PackageInterface $package): void
    {
        foreach (\array_keys($package->getRequires()) as $key) {
            if ('php' === $key) {
                continue;
            }

            if (0 === \mb_strpos((string) $key, 'ext-')) {
                continue;
            }

            $carry += [$key => $key];
        }
    }

    private function buildRegex($packages): array
    {
        $groups = [];

        foreach ($packages as $package => $dependencies) {
            [$prefix, $bundle] = \explode('/', $package);
            $groups[\sprintf('(?i:%s)(?|', $prefix)][] = \sprintf('/?(?i:%s) (*MARK:%s)|', \str_replace('-', '-?', $bundle), $package);
        }

        return $groups;
    }
}
