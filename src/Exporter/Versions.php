<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

class Versions extends Exporter
{
    /**
     * {@inheritdoc}
     */
    public function exportToArray(): array
    {
        $data = $this->getEvent()->getComposer()->getLocker()->getLockData();

        $packagesData = array_merge(
            $data['packages'],
            $data['packages-dev']
        );

        $packageNames = array_map(
            static function (array $data) {
                return $data['name'];
            },
            $packagesData
        );

        $packageVersions = array_map(
            static function (array $data) {
                return $data['version'];
            },
            $packagesData
        );

        if (false !== $versions = array_combine($packageNames, $packageVersions)) {
            ksort($versions);

            $regex = $this->buildRegex($versions);

            return compact('packageNames', 'regex');
        }

        return [];
    }

    /**
     * @param array<string, string> $versions
     *
     * @return array<string, array<int, string>>
     */
    private function buildRegex(array $versions): array
    {
        $groups = [];

        foreach ($versions as $package => $version) {
            [$prefix, $bundle] = explode('/', $package);
            $groups[sprintf('(?i:%s)(?|', $prefix)][] = sprintf('/?(?i:%s) (*MARK:%s)|', $bundle, $version);
        }

        return $groups;
    }
}
