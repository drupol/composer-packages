<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

use Composer\Package\Loader\ArrayLoader;

class Directories extends Exporter
{
    /**
     * @return array
     */
    public function exportToArray(): array
    {
        $data = $this->getEvent()->getComposer()->getLocker()->getLockData();

        $packagesData = \array_merge(
            $data['packages'],
            $data['packages-dev']
        );

        foreach ($packagesData as $package) {
            $package = (new ArrayLoader())->load($package);

            $directories[$package->getName()] = $this
                ->getEvent()
                ->getComposer()
                ->getInstallationManager()
                ->getInstallPath($package);
        }

        return \compact('directories');
    }
}
