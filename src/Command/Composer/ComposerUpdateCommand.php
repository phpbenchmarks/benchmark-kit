<?php

declare(strict_types=1);

namespace App\Command\Composer;

use App\{
    Command\AbstractCommand,
    Command\PhpVersion\PhpVersionCliDefineCommand,
    Command\Validate\ValidateComposerJsonCommand,
    Command\Validate\ValidateConfigurationComposerLockCommand,
    ComponentConfiguration\ComponentConfiguration
};
use Symfony\Component\Console\Output\OutputInterface;

final class ComposerUpdateCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'composer:update';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription(
            'Execute composer update for all enabled PHP versions and create '
                . $this->getComposerLockFilePath('X.Y', true)
        );
    }

    protected function doExecute(): parent
    {
        $this->runCommand(ValidateComposerJsonCommand::getDefaultName());

        foreach (ComponentConfiguration::getEnabledPhpVersions() as $phpVersion) {
            $this
                ->runCommand(PhpVersionCliDefineCommand::getDefaultName(), ['phpVersion' => $phpVersion])
                ->outputTitle('Update Composer dependencies')
                ->runProcess(['composer', 'update', '--ansi'], OutputInterface::VERBOSITY_VERBOSE)
                ->outputSuccess('Composer update done.')
                ->runProcess(['mv', 'composer.lock', $this->getComposerLockFilePath($phpVersion, true)])
                ->outputSuccess(
                    'Move composer.lock to '
                        . $this->getComposerLockFilePath($phpVersion, true)
                        . '.'
                )
                ->runCommand(
                    ValidateConfigurationComposerLockCommand::getDefaultName(),
                    ['phpVersion' => $phpVersion]
                );
        }

        return $this;
    }
}
