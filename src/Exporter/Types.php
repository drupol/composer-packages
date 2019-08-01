<?php

declare(strict_types = 1);

namespace drupol\ComposerPackages\Exporter;

class Types extends Exporter
{
    /**
     * @return array
     */
    public function exportToArray(): array
    {
        $data = $this->getEvent()->getComposer()->getLocker()->getLockData();

        $packagesData = array_merge(
            $data['packages'],
            $data['packages-dev']
        );

        $types = [];

        foreach ($packagesData as $package) {
            $types += [$package['type'] => []];

            $types[$package['type']][] = $package;
        }

        return compact('types');
    }
}
