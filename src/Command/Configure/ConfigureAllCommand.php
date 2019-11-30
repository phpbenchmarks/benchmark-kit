<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Command\Composer\ComposerUpdateCommand,
    Command\Configure\Composer\ConfigureComposerJsonCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksConfigurationClassCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksInitBenchmarkCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksPhpVersionCompatibleCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksResponseBodyCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksNginxVhostCommand
};

final class ConfigureAllCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:all';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Call all configure commands and ' . ComposerUpdateCommand::getDefaultName());
    }

    protected function doExecute(): parent
    {
        return $this
            ->runCommand(ConfigurePhpBenchmarksConfigurationClassCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksPhpVersionCompatibleCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksInitBenchmarkCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksNginxVhostCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksResponseBodyCommand::getDefaultName())
            ->runCommand(ConfigureGitignoreCommand::getDefaultName())
            ->runCommand(ConfigureReadmeCommand::getDefaultName())
            ->runCommand(ConfigureCircleCiCommand::getDefaultName())
            ->runCommand(ConfigureEntryPointCommand::getDefaultName())
            ->runCommand(ConfigureComposerJsonCommand::getDefaultName())
            ->runCommand(ComposerUpdateCommand::getDefaultName());
    }
}
