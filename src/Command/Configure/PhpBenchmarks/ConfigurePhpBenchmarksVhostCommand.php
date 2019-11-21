<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\Command\AbstractCommand;

final class ConfigurePhpBenchmarksVhostCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:vhost';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . $this->getVhostFilePath(true));
    }

    protected function doExecute(): AbstractCommand
    {
        return $this
            ->outputTitle('Creation of ' . $this->getVhostFilePath(true))
            ->writeFileFromTemplate($this->getVhostFilePath(true))
            ->outputWarning('Default virtual host has been created. Feel free to edit it.');
    }
}
