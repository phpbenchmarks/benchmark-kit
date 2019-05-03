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
            ->setDescription(
                'Create ' . $this->getVhostFilePath(true) . ' and phpXY.benchmark.loc vhosts then reload nginx'
            );
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->title('Creation of ' . $this->getVhostFilePath(true))
            ->copyDefaultConfigurationFile('vhost.conf')
            ->runCommand('validate:configuration:vhost')
            ->runCommand('vhost:create');

        return $this;
    }
}
