<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

class ConfigureVhostCommand extends AbstractConfigureCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('configure:vhost')
            ->setDescription('Create .phpbenchmarks/vhost.conf, create phpXY.benchmark.loc vhosts and reload nginx');
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->title('Creation of .phpbenchmarks/vhost.conf')
            ->copyDefaultConfigurationFile('vhost.conf')
            ->runCommand('validate:configuration:vhost')
            ->runCommand('vhost:create');

        return $this;
    }
}
