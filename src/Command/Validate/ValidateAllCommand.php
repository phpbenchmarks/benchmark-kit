<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\Command\AbstractCommand;

class ValidateAllCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('validate:all')
            ->setDescription('Call all validate commands');
    }

    protected function doExecute(): parent
    {
        return $this
            ->runCommand('validate:branch:name')
            ->runCommand('validate:composer:json')
            ->runCommand('validate:composer:lock')
            ->runCommand('validate:configuration:component')
            ->runCommand('validate:configuration:initBenchmark')
            ->runCommand('validate:configuration:responseBody')
            ->runCommand('validate:configuration:vhost');
    }
}
