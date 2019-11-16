<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Command\Validate\ValidateComposerJsonCommand,
    Command\Validate\ValidateConfigurationComposerLockCommand,
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion
};

final class ComposerUpdateCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'composer:update';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription(
                'Execute composer update for all enabled PHP versions and create '
                . $this->getComposerLockFilePath('X.Y', true)
            )
            ->addArgument('phpVersion', null, 'Version of PHP: 5.6, 7.0, 7.1, 7.2 or 7.3');
    }

    protected function doExecute(): parent
    {
        $this->runCommand(ValidateComposerJsonCommand::getDefaultName());

        foreach ($this->getPhpVersions() as $phpVersion) {
            $this
                ->outputTitle('PHP ' . $phpVersion)
                ->definePhpCliVersion($phpVersion)
                ->exec('cd ' . $this->getInstallationPath() . ' && composer update --ansi')
                ->outputSuccess('Composer update done.')
                ->exec(
                    'cd '
                        . $this->getInstallationPath()
                        . ' && mv composer.lock '
                        . $this->getComposerLockFilePath($phpVersion, true)
                )
                ->outputSuccess(
                    'Move composer.lock to '
                        . $this->getComposerLockFilePath($phpVersion, true)
                        . '.'
                );
        }

        $this->runCommand(
            ValidateConfigurationComposerLockCommand::getDefaultName(),
            ['phpVersion' => $this->getInput()->getArgument('phpVersion')]
        );

        return $this;
    }

    private function getPhpVersions(): array
    {
        $phpVersion = $this->getInput()->getArgument('phpVersion');
        if ($phpVersion === null) {
            $return = ComponentConfiguration::getEnabledPhpVersions();
        } else {
            if (in_array($phpVersion, ComponentConfiguration::getEnabledPhpVersions()) === false) {
                throw new \Exception(
                    in_array($phpVersion, PhpVersion::getAll())
                        ?
                            'PHP '
                            . $phpVersion
                            . ' is disabled. Enable it into '
                            . $this->getConfigurationFilePath(true)
                            . '.'
                        : 'Invalid PHP version ' . $phpVersion . '.'
                );
            }
            $return = [$phpVersion];
        }

        return $return;
    }
}
