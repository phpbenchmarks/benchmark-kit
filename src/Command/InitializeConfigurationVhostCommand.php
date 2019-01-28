<?php

declare(strict_types=1);

namespace App\Command;

class InitializeConfigurationVhostCommand extends AbstractInitializeConfigurationCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('initialize:configuration:vhost')
            ->setDescription('Create .phpbenchmarks/vhost.conf');
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Creation of .phpbenchmarks/vhost.conf')
            ->copyDefaultConfigurationFile('vhost.conf')
            ->runCommand('validate:configuration:vhost')
            ->runCommand('vhost:create');

        return $this;
    }
}
