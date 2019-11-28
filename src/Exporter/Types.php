<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Exporter;

class Types extends Exporter
{
    public function exportToArray(): array
    {
        $data = $this->getEvent()->getComposer()->getLocker()->getLockData();

        $packagesData = array_merge(
            $data['packages'],
            $data['packages-dev']
        );

        // out of the box composer supported types.
        $types = [
            'library' => [],
            'project' => [],
            'metapackage' => [],
            'composer-plugin' => [],
        ];

        foreach ($packagesData as $package) {
            $types[$package['type']][] = $package;
        }

        return compact('types');
    }
}
