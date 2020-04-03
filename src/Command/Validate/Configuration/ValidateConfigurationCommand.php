<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration;

use App\{
    Command\AbstractCommand,
    Command\Validate\Configuration\Composer\ValidateConfigurationComposerJsonCommand,
    Command\Validate\Configuration\Composer\ValidateConfigurationComposerLockCommand,
    Command\Validate\Configuration\Nginx\ValidateConfigurationNginxVhostCommand,
    Command\Validate\Configuration\Php\ValidateConfigurationPhpIniCommand,
    Command\Validate\Configuration\Response\ValidateConfigurationResponseBodyCommand
};

final class ValidateConfigurationCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate configuration');
    }

    protected function doExecute(): int
    {
        $this
            ->runCommand(ValidateConfigurationCircleciCommand::getDefaultName())
            ->runCommand(ValidateConfigurationComposerJsonCommand::getDefaultName())
            ->runCommand(ValidateConfigurationComposerLockCommand::getDefaultName())
            ->runCommand(ValidateConfigurationEntryPointCommand::getDefaultName())
            ->runCommand(ValidateConfigurationGitignoreCommand::getDefaultName())
            ->runCommand(ValidateConfigurationReadmeCommand::getDefaultName())
            ->runCommand(ValidateConfigurationPhpBenchmarksCommand::getDefaultName())
            ->runCommand(ValidateConfigurationInitBenchmarkCommand::getDefaultName())
            ->runCommand(ValidateConfigurationPhpIniCommand::getDefaultName())
            ->runCommand(ValidateConfigurationResponseBodyCommand::getDefaultName())
            ->runCommand(ValidateConfigurationNginxVhostCommand::getDefaultName());

        if ($this->skipSourceCodeUrls() === false) {
            $this->runCommand(ValidateConfigurationSourceCodeUrlsCommand::getDefaultName());
        }

        return 0;
    }
}
