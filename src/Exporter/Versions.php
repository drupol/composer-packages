<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

final class Versions extends Exporter
{
    public function exportToArray(): array
    {
        $data = $this->getEvent()->getComposer()->getLocker()->getLockData();

        $packagesData = array_merge(
            $data['packages'],
            $data['packages-dev']
        );

        $packageNames = array_map(
            static function (array $data): string {
                return $data['name'];
            },
            $packagesData
        );

        $packageVersions = array_map(
            static function (array $data): string {
                return $data['version'];
            },
            $packagesData
        );

        if (false === $versions = array_combine($packageNames, $packageVersions)) {
            return [];
        }

        ksort($versions);

        return [
            'package_names' => $packageNames,
            'regex' => $this->buildRegex($versions),
        ];
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
