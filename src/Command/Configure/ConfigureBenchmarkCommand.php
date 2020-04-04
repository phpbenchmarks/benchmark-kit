<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Command\Composer\ComposerUpdateCommand,
    Command\Configure\Composer\ConfigureComposerJsonCommand,
    Command\Configure\Php\ConfigurePhpCompatibleVersionCommand,
    Command\Configure\Php\ConfigurePhpPreloadCommand
};
use Symfony\Component\Console\Input\InputOption;

final class ConfigureBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:benchmark';

    private $configurePhpBenchmarksConfigParameters = [
        'component' => 'Component id',
        'benchmark-type' => 'Benchmark type id',
        'entry-point' => 'Entry point file name',
        'benchmark-relative-url' => 'Benchmark relative url (example: /benchmark/helloworld)',
        'core-dependency-name' => 'Core dependency name (example: foo/bar)'
    ];

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Configure your benchmark for benchmark-kit');
        foreach ($this->configurePhpBenchmarksConfigParameters as $name => $description) {
            $this->addOption($name, null, InputOption::VALUE_REQUIRED, $description);
        }
    }

    protected function doExecute(): int
    {
        $this
            ->runConfigurePhpBenchmarksConfigCommand()
            ->runCommand(ConfigurePhpCompatibleVersionCommand::getDefaultName())
            ->runCommand(ConfigureInitBenchmarkCommand::getDefaultName())
            ->runCommand(ConfigureNginxVhostCommand::getDefaultName())
            ->runCommand(ConfigureResponseBodyCommand::getDefaultName())
            ->runCommand(ConfigureGitignoreCommand::getDefaultName())
            ->runCommand(ConfigureReadmeCommand::getDefaultName())
            ->runCommand(ConfigureCircleCiCommand::getDefaultName())
            ->runCommand(ConfigureComposerJsonCommand::getDefaultName())
            ->runCommand(ComposerUpdateCommand::getDefaultName())
            ->runCommand(ConfigurePhpPreloadCommand::getDefaultName());

        return 0;
    }

    protected function runConfigurePhpBenchmarksConfigCommand(): self
    {
        $parameters = [];
        foreach (array_keys($this->configurePhpBenchmarksConfigParameters) as $parameter) {
            if (is_string($this->getInput()->getOption($parameter))) {
                $parameters["--$parameter"] = $this->getInput()->getOption($parameter);
            }
        }

        return $this
            ->runCommand(
                ConfigurePhpBenchmarksCommand::getDefaultName(),
                $parameters
            );
    }
}
