<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Command\AbstractCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksComposerLockCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksConfigurationClassCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksConfigurationClassGetSourceCodeUrlsCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksInitBenchmarkCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksResponseBodyCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksVhostCommand
};

final class ValidateAllCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:all';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Call all validate commands');
    }

    protected function doExecute(): parent
    {
        $this
            ->runCommand(ValidateBranchNameCommand::getDefaultName())
            ->runCommand(ValidateComposerJsonCommand::getDefaultName())
            ->runCommand(ValidateEntryPointCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksComposerLockCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksConfigurationClassCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksInitBenchmarkCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksResponseBodyCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksVhostCommand::getDefaultName());

        if ($this->skipSourceCodeUrls() === false) {
            $this->runCommand(ValidatePhpBenchmarksConfigurationClassGetSourceCodeUrlsCommand::getDefaultName());
        }

        return $this;
    }
}
