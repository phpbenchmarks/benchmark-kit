<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Command\AbstractCommand,
    Command\Validate\Composer\ValidateComposerJsonCommand,
    Command\Validate\Composer\ValidateComposerLockCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksComposerLockCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksConfigCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksConfigSourceCodeUrlsCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksInitBenchmarkCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksPhpIniCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksResponseBodyCommand,
    Command\Validate\PhpBenchmarks\ValidatePhpBenchmarksNginxVhostCommand
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
            ->runCommand(ValidateCircleciCommand::getDefaultName())
            ->runCommand(ValidateComposerJsonCommand::getDefaultName())
            ->runCommand(ValidateComposerLockCommand::getDefaultName())
            ->runCommand(ValidateEntryPointCommand::getDefaultName())
            ->runCommand(ValidateGitignoreCommand::getDefaultName())
            ->runCommand(ValidateReadmeCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksComposerLockCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksConfigCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksInitBenchmarkCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksPhpIniCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksResponseBodyCommand::getDefaultName())
            ->runCommand(ValidatePhpBenchmarksNginxVhostCommand::getDefaultName());

        if ($this->skipSourceCodeUrls() === false) {
            $this->runCommand(ValidatePhpBenchmarksConfigSourceCodeUrlsCommand::getDefaultName());
        }

        return $this;
    }
}
