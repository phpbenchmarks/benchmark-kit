<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Command\Composer\ComposerUpdateCommand,
    Command\Configure\Composer\ConfigureComposerJsonCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksConfigCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksInitBenchmarkCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksPhpVersionCompatibleCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksPreloadCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksResponseBodyCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksNginxVhostCommand
};
use Symfony\Component\Console\Input\InputOption;

final class ConfigureAllCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:all';

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

        $this->setDescription('Call all configure commands and ' . ComposerUpdateCommand::getDefaultName());
        foreach ($this->configurePhpBenchmarksConfigParameters as $name => $description) {
            $this->addOption($name, null, InputOption::VALUE_REQUIRED, $description);
        }
    }

    protected function doExecute(): int
    {
        $this
            ->runConfigurePhpBenchmarksConfigCommand()
            ->runCommand(ConfigurePhpBenchmarksPhpVersionCompatibleCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksInitBenchmarkCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksNginxVhostCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksResponseBodyCommand::getDefaultName())
            ->runCommand(ConfigureGitignoreCommand::getDefaultName())
            ->runCommand(ConfigureReadmeCommand::getDefaultName())
            ->runCommand(ConfigureCircleCiCommand::getDefaultName())
            ->runCommand(ConfigureComposerJsonCommand::getDefaultName())
            ->runCommand(ComposerUpdateCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksPreloadCommand::getDefaultName());

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
                ConfigurePhpBenchmarksConfigCommand::getDefaultName(),
                $parameters
            );
    }
}
