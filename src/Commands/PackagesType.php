<?php

declare(strict_types=1);

namespace drupol\ComposerPackages\Commands;

use Composer\Command\BaseCommand as Command;
use Composer\Package\CompletePackage;
use ComposerPackages\Packages;
use ComposerPackages\Types;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PackagesType extends Command
{
    protected $color;

    protected function configure()
    {
        $this->setName('packages-type');
        $this->setDescription('Return the packages of a type.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $types = Types::getTypes();

        $question = new Question('<question>Please provide type of packages:</question>' . \PHP_EOL);

        if (method_exists($question, 'setAutocompleterCallback')) {
            $callback = static function (string $userInput) use ($types): array {
                return array_filter($types, static function (string $type) use ($userInput) {
                    return false !== mb_stripos($type, $userInput);
                });
            };
            $question->setAutocompleterCallback($callback);
        } else {
            $question->setAutocompleterValues($types);
        }

        $helper = $this->getHelper('question');

        $type = $helper->ask($input, $output, $question) ?? '';
        /** @var CompletePackage[] $packages */
        $packages = iterator_to_array(Types::get($type));

        $names = array_map(static function (CompletePackage $completePackage) {
            return $completePackage->getName();
        }, $packages);

        foreach ($packages as $package) {
            $output->writeln($package->getName());
        }

        $question = new Question('<question>Please type the package name you want to explore:</question>' . \PHP_EOL);

        if (method_exists($question, 'setAutocompleterCallback')) {
            $callback = static function (string $userInput) use ($names): array {
                return array_filter($names, static function (string $packageName) use ($userInput) {
                    return false !== mb_stripos($packageName, $userInput);
                });
            };
            $question->setAutocompleterCallback($callback);
        } else {
            $question->setAutocompleterValues($names);
        }

        $packageName = $helper->ask($input, $output, $question) ?? '';
        $package = Packages::get($packageName);

        if (null === $package) {
            return null;
        }

        $output->writeln('<fg=green>name    </> :' . $package->getName());
        $output->writeln('<fg=green>descrip.</> :' . $package->getDescription() ?? '~');
        $output->writeln('<fg=green>keywords</> :' . implode(', ', $package->getKeywords() ?? ['~']));
        $output->writeln('<fg=green>version </> :' . $package->getVersion() ?? '~');
        $output->writeln('<fg=green>type    </> :' . $package->getType());
        $output->writeln('<fg=green>license </> :' . implode(' ', $package->getLicense() ?? ['~']));
        $output->writeln('<fg=green>source  </> :[' . $package->getSourceType() . '] ' . $package->getSourceUrl() . ' ' . $package->getSourceReference());
        $output->writeln('<fg=green>dist    </> :[' . $package->getDistType() . '] ' . $package->getDistUrl() . ' ' . $package->getDistReference());

        $output->writeln('');
        $output->writeln('<fg=green>requires</>');

        foreach ($package->getRequires() as $link) {
            $output->writeln($link->getTarget() . ' <fg=yellow>' . $link->getPrettyConstraint() . '</>');
        }

        $output->writeln('');
        $output->writeln('<fg=green>requires-dev</>');

        foreach ($package->getDevRequires() as $link) {
            $output->writeln($link->getTarget() . ' <fg=yellow>' . $link->getPrettyConstraint() . '</>');
        }

        $output->writeln('');
        $output->writeln('<fg=green>suggest</>');

        foreach ($package->getSuggests() as $name => $suggest) {
            $output->writeln("{$name} <fg=yellow>{$suggest}</>");
        }

        return 0;
    }
}
