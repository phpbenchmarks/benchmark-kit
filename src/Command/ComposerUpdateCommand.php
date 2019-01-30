<?php

declare(strict_types=1);

namespace App\Command;

use App\ComponentConfiguration\ComponentConfiguration;

class ComposerUpdateCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('composer:update')
            ->setDescription('Execute composer update for all enabled PHP versions and create composer.lock.phpX.Y');
    }

    protected function doExecute(): parent
    {
        $this->runCommand('validate:composer:json');

        foreach (ComponentConfiguration::getEnabledPhpVersions() as $phpVersion) {
            $this
                ->title('PHP ' . $phpVersion)
                ->definePhpCliVersion($phpVersion)
                ->exec('cd ' . $this->getInstallationPath() . ' && composer update --ansi')
                ->success('Composer update done.')
                ->exec(
                    'cd ' . $this->getInstallationPath() . ' && mv composer.lock composer.lock.php' . $phpVersion
                )
                ->success('Moving composer.lock to composer.lock.php' . $phpVersion . '.');
        }

        $this->runCommand('validate:composer:lock');

        return $this;
    }
}
