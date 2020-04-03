<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Utils\Path
};

final class ConfigureNginxVhostCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:nginx:vhost';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . Path::rmPrefix(Path::getVhostPath()));
    }

    protected function doExecute(): int
    {
        $vhostPath = Path::rmPrefix(Path::getVhostPath());

        $this
            ->outputTitle("Creation of $vhostPath")
            ->writeFileFromTemplate($vhostPath)
            ->outputWarning("$vhostPath virtual host has been created. Feel free to edit it.");

        return 0;
    }
}
