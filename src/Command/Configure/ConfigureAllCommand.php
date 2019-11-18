<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Command\Composer\ComposerUpdateCommand
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
            ->runCommand(ConfigureDirectoryCommand::getDefaultName())
            ->runCommand(ConfigureConfigurationClassCommand::getDefaultName())
            ->outputWarningSourceCodeUrls()
            ->runCommand(
                ConfigureComponentSourceCodeUrlsCommand::getDefaultName(),
                ['--skip-class-creation' => true]
            )
            ->runCommand(ConfigureInitBenchmarkCommand::getDefaultName())
            ->runCommand(ConfigureVhostCommand::getDefaultName())
            ->runCommand(ConfigureResponseBodyCommand::getDefaultName())
            ->runCommand(ConfigureCircleCiCommand::getDefaultName())
            ->runCommand(ComposerUpdateCommand::getDefaultName());
    }

    private function outputWarningSourceCodeUrls(): self
    {
        if ($this->skipSourceCodeUrls() === false) {
            $this->outputWarning(
                'You can skip source code urls configuration with --skip-source-code-urls parameter.',
                false
            );
        }

        return $this;
    }
}
