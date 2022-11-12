<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

final class Packages extends Exporter
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

        if (false === $packages = array_combine($packageNames, $packagesData)) {
            return [];
        }

        ksort($packages);

        return [
            'packages' => $packages,
            'regex' => $this->buildRegex($packages),
        ];
    }

    /**
     * @param array<string, array> $packages
     *
     * @return array<string, array<int, string>>
     */
    private function buildRegex(array $packages): array
    {
        $groups = [];

        foreach ($packages as $package) {
            [$prefix, $bundle] = explode('/', $package['name']);
            $groups[sprintf('(?i:%s)(?|', $prefix)][] = sprintf(
                '/?(?i:%s) (*MARK:%s)|',
                str_replace('-', '-?', $bundle),
                $package['name']
            );
        }

        return $groups;
    }
}
