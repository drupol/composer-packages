<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Commands;

use Composer\Command\BaseCommand as Command;
use ComposerPackages\Dependencies as PackageDependencies;
use ComposerPackages\Packages;
use ComposerPackages\Versions;
use PBergman\Console\Helper\TreeHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Dependencies extends Command
{
    protected $color;

    protected function configure(): void
    {
        $this->setName('dependencies');
        $this->setDescription('Return the package dependencies.');
        $this->addOption('recursive', 'r', InputOption::VALUE_NONE, 'Return dependencies recursively.');
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

        $rows = $this->getDependenciesOf($helper->ask($input, $output, $question) ?? '', (bool) $input->getOption('recursive'));

        $tree->addArray($rows);
        $tree->printTree($output);
    }

    private function getDependenciesOf(string $search, bool $isRecursive, bool $isChild = false): array
    {
        $rows = [];
        $dependencies = PackageDependencies::get($search);

        foreach ($dependencies as $number => $dependency) {
            $row = $dependency . ': <fg=yellow>' . (Versions::get($dependency) ?? '~') . '</>';

            if ($isRecursive) {
                $rows += $this->getDependenciesOf($dependency, $isRecursive, true);
            } else {
                $rows[$row] = [];
            }
        }

        return [$search . ': <fg=yellow>' . (Versions::get($search) ?? '~') . '</>' => $rows];
    }
}
