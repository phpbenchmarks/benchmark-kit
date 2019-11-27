<?php

declare(strict_types=1);

namespace App\Command\Composer;

use App\{
    Command\AbstractCommand,
    Command\PhpVersion\PhpVersionCliDefineCommand,
    Command\Validate\Composer\ValidateComposerJsonCommand,
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
        $this
            ->runCommand(ValidateComposerJsonCommand::getDefaultName())
            ->outputTitle('Remove ' . $this->getComposerPath(true) . ' directory')
            ->removeDirectory($this->getComposerPath());

        foreach (ComponentConfiguration::getEnabledPhpVersions() as $phpVersion) {
            $this
                ->runCommand(PhpVersionCliDefineCommand::getDefaultName(), ['phpVersion' => $phpVersion])
                ->outputTitle('Update Composer dependencies')
                ->runProcess(['composer', 'update', '--ansi'], OutputInterface::VERBOSITY_VERBOSE)
                ->outputSuccess('Composer update done.')
                ->createDirectory($this->getComposerPath())
                ->runProcess(['mv', 'composer.lock', $this->getComposerLockFilePath($phpVersion, true)])
                ->outputSuccess(
                    'Move composer.lock to '
                        . $this->getComposerLockFilePath($phpVersion, true)
                        . '.'
                );
        }

        return $this;
    }
}
