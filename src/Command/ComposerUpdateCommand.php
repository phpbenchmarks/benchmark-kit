<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Command\Validate\ValidateComposerLockFilesCommand,
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion
};

class ComposerUpdateCommand extends AbstractCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('composer:update')
            ->setDescription(
                'Execute composer update for all enabled PHP versions and create '
                . $this->getComposerLockFilePath('X.Y', true)
            )
            ->addArgument('phpVersion', null, 'Version of PHP: 5.6, 7.0, 7.1, 7.2 or 7.3');
    }

    protected function doExecute(): parent
    {
        $this->runCommand('validate:composer:json');

        foreach ($this->getPhpVersions() as $phpVersion) {
            $this
                ->title('PHP ' . $phpVersion)
                ->definePhpCliVersion($phpVersion)
                ->exec('cd ' . $this->getInstallationPath() . ' && composer update --ansi')
                ->success('Composer update done.')
                ->exec(
                    'cd '
                    . $this->getInstallationPath()
                    . ' && mv composer.lock '
                    . $this->getComposerLockFilePath($phpVersion, true)
                )
                ->success(
                    'Move composer.lock to '
                    . $this->getComposerLockFilePath($phpVersion, true)
                    . '.'
                );
        }

        $this->runCommand('validate:composer:lock', ['phpVersion' => $this->getInput()->getArgument('phpVersion')]);

        return $this;
    }

    protected function getPhpVersions(): array
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
                            . $this->getAbstractComponentConfigurationFilePath(true)
                            . '.'
                        : 'Invalid PHP version ' . $phpVersion . '.'
                );
            }
            $return = [$phpVersion];
        }

        return $return;
    }
}
