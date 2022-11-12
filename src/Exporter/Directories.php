<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

use Composer\Package\Loader\ArrayLoader;

final class Directories extends Exporter
{
    public function exportToArray(): array
    {
        $data = $this->getEvent()->getComposer()->getLocker()->getLockData();

        $packagesData = array_merge(
            $data['packages'],
            $data['packages-dev']
        );

        $directories = [];

        foreach ($packagesData as $package) {
            $package = (new ArrayLoader())->load($package);

            $directories[$package->getName()] = $this
                ->getEvent()
                ->getComposer()
                ->getInstallationManager()
                ->getInstallPath($package);
        }

        return [
            'directories' => $directories,
            'regex' => $this->buildRegex($directories),
        ];
    }

    /**
     * @param array<string, string> $packages
     *
     * @return array<string, array<int, string>>
     */
    private function buildRegex(array $packages): array
    {
        $groups = [];

        foreach ($packages as $package => $directory) {
            [$prefix, $bundle] = explode('/', $package);
            $groups[sprintf('(?i:%s)(?|', $prefix)][] = sprintf(
                '/?(?i:%s) (*MARK:%s)|',
                str_replace('-', '-?', $bundle),
                $directory
            );
        }

        return $groups;
    }
}
