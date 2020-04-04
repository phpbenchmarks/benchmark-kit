<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration;

use App\{
    Command\AbstractCommand,
    Command\Behavior\ValidateCircleCiOption,
    Command\Validate\Configuration\Composer\ValidateConfigurationComposerJsonCommand,
    Command\Validate\Configuration\Composer\ValidateConfigurationComposerLockCommand,
    Command\Validate\Configuration\Nginx\ValidateConfigurationNginxVhostCommand,
    Command\Validate\Configuration\Php\ValidateConfigurationPhpIniCommand,
    Command\Validate\Configuration\Php\ValidateConfigurationPhpPreloadCommand,
    Command\Validate\Configuration\Response\ValidateConfigurationResponseBodyCommand
};

final class ValidateConfigurationCommand extends AbstractCommand
{
    use ValidateCircleCiOption;

    /** @var string */
    protected static $defaultName = 'validate:configuration';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate configuration')
            ->addValidateCircleCiOption($this->getDefinition());
    }

    protected function doExecute(): int
    {
        if ($this->getValidateCircleCiOption($this->getInput()) === true) {
            $this->runCommand(ValidateConfigurationCircleciCommand::getDefaultName());
        }

        $this
            ->runCommand(ValidateConfigurationComposerJsonCommand::getDefaultName())
            ->runCommand(ValidateConfigurationComposerLockCommand::getDefaultName())
            ->runCommand(ValidateConfigurationEntryPointCommand::getDefaultName())
            ->runCommand(ValidateConfigurationGitignoreCommand::getDefaultName())
            ->runCommand(ValidateConfigurationReadmeCommand::getDefaultName())
            ->runCommand(ValidateConfigurationPhpBenchmarksCommand::getDefaultName())
            ->runCommand(ValidateConfigurationInitBenchmarkCommand::getDefaultName())
            ->runCommand(ValidateConfigurationPhpIniCommand::getDefaultName())
            ->runCommand(ValidateConfigurationPhpPreloadCommand::getDefaultName())
            ->runCommand(ValidateConfigurationResponseBodyCommand::getDefaultName())
            ->runCommand(ValidateConfigurationNginxVhostCommand::getDefaultName());

        if ($this->skipSourceCodeUrls() === false) {
            $this->runCommand(ValidateConfigurationSourceCodeUrlsCommand::getDefaultName());
        }

        return 0;
    }
}
