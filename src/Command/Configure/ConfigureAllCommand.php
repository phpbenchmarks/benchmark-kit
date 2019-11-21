<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Command\Composer\ComposerUpdateCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksConfigurationClassCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksInitBenchmarkCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksResponseBodyCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksVhostCommand
};

final class ConfigureAllCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:all';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Call all configure commands and composer:update');
    }

    protected function doExecute(): parent
    {
        return $this
            ->runCommand(ConfigurePhpBenchmarksConfigurationClassCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksInitBenchmarkCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksVhostCommand::getDefaultName())
            ->runCommand(ConfigurePhpBenchmarksResponseBodyCommand::getDefaultName())
            ->runCommand(ConfigureCircleCiCommand::getDefaultName())
            ->runCommand(ComposerUpdateCommand::getDefaultName());
    }
}
