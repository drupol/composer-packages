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
        $regex = $this->buildRegex($versions);

        return \compact('versions', 'regex');
    }

    private function buildRegex($versions): array
    {
        asort($versions);

        foreach($versions as $package => $version) {
            [$prefix, $bundle] = explode('/', $package);
            $groups[sprintf('(?i:%s)(?|', $prefix)][] = sprintf('/?(?i:%s) (*MARK:%s)|', $bundle, $version);
        }

        return $groups;
    }
}
