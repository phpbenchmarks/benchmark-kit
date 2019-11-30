<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Utils\Directory
};

final class ConfigurePhpBenchmarksNginxVhostCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:nginx:vhost';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . Directory::getVhostFilePath());
    }

    protected function doExecute(): AbstractCommand
    {
        return $this
            ->outputTitle('Creation of ' . Directory::getVhostFilePath())
            ->writeFileFromTemplate(Directory::getVhostFilePath())
            ->outputWarning('Default virtual host has been created. Feel free to edit it.');
    }
}
