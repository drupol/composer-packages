<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

class Versions extends Exporter
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

        $packageNames = \array_map(
            static function (array $data) {
                return $data['name'];
            },
            $packagesData
        );

        $packageVersions = \array_map(
            static function (array $data) {
                return $data['version'];
            },
            $packagesData
        );

        $versions = \array_combine($packageNames, $packageVersions);

        return \compact('versions');
    }
}
