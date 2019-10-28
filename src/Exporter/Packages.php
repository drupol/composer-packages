<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

class Packages extends Exporter
{
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

        if (false !== $packages = \array_combine($packageNames, $packagesData)) {
            \ksort($packages);

            $regex = $this->buildRegex($packages);

            return \compact('packages', 'regex');
        }

        return [];
    }

    private function buildRegex(array $packages): array
    {
        $groups = [];

        foreach ($packages as $package) {
            [$prefix, $bundle] = \explode('/', $package['name']);
            $groups[\sprintf('(?i:%s)(?|', $prefix)][] = \sprintf(
                '/?(?i:%s) (*MARK:%s)|',
                \str_replace('-', '-?', $bundle),
                $package['name']
            );
        }

        return $groups;
    }
}
