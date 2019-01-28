<?php

declare(strict_types=1);

namespace App\Command;

class InitializeAllCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('initialize:all')
            ->setDescription('Call all initialize commands');
    }

    protected function doExecute(): parent
    {
        $this
            ->runCommand('initialize:configuration:directory')
            ->runCommand('initialize:configuration:component')
            ->runCommand('initialize:configuration:initBenchmark')
            ->runCommand('initialize:configuration:vhost')
            ->runCommand('initialize:responseBody');

        return $this;
    }
}
