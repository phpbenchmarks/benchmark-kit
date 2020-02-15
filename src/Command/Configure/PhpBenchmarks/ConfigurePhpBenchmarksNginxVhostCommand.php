<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Utils\Path
};

final class ConfigurePhpBenchmarksNginxVhostCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:nginx:vhost';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . Path::rmPrefix(Path::getVhostPath()));
    }

    protected function doExecute(): AbstractCommand
    {
        $vhostPath = Path::rmPrefix(Path::getVhostPath());

        return $this
            ->outputTitle("Creation of $vhostPath")
            ->writeFileFromBenchmarkTemplate($vhostPath)
            ->outputWarning("$vhostPath virtual host has been created. Feel free to edit it.");
    }
}
