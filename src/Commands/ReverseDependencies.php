<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Commands;

use Composer\Command\BaseCommand as Command;
use ComposerPackages\Dependencies;
use ComposerPackages\Packages;
use PBergman\Console\Helper\TreeHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ReverseDependencies extends Command
{
    protected $color;

    protected function configure()
    {
        $this->setName('reverse-dependencies');
        $this->setDescription('Retrace the tree of dependencies originally asking for a package.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $tree = new TreeHelper();

        $packages = array_keys(iterator_to_array(Packages::packages()));
        $question = new Question('<question>Please type the package name:</question>' . \PHP_EOL);

        if (method_exists($question, 'setAutocompleterCallback')) {
            $callback = static function (string $userInput) use ($packages): array {
                return array_filter($packages, static function (string $packageName) use ($userInput) {
                    return false !== mb_stripos($packageName, $userInput);
                });
            };
            $question->setAutocompleterCallback($callback);
        } else {
            $question->setAutocompleterValues($packages);
        }

        $search = $helper->ask($input, $output, $question) ?? '';

        $packages = Packages::packages();
        $parents = $this->lookForTree($packages, $search);

        foreach ($parents as $children) {
            $shouldBeDeleted = array_filter($parents, static function ($value) use ($children) {
                return \in_array($children, $value, true);
            });

            if (!$shouldBeDeleted && \is_array($children) && !empty($children)) {
                $tree->addArray($children);
            }
        }

        $tree->printTree($output);
    }

    private function countLevels($filtered): int
    {
        return \count($filtered, \COUNT_RECURSIVE);
    }

    private function lookForChildren($packageName, $search): array
    {
        $dependencies = Dependencies::get($packageName);

        foreach ($dependencies as $dependencyName) {
            if ($dependencyName === $search) {
                return [$packageName => [$dependencyName]];
            }

            $deeperLook = $this->lookForChildren($dependencyName, $search);

            if (!empty($deeperLook)) {
                return [$packageName => $deeperLook];
            }
        }

        return [];
    }

    private function lookForTree($packages, $search): array
    {
        $children = [];

        foreach ($packages as $package) {
            $children[] = $this->lookForChildren($package['name'], $search);
        }

        $filtered = array_filter($children);
        $sortCriteria = array_map([$this, 'countLevels'], $filtered);
        array_multisort($sortCriteria, \SORT_ASC, $filtered);

        return $filtered;
    }
}
