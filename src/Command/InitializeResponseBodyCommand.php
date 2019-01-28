<?php

declare(strict_types=1);

namespace App\Command;

class InitializeResponseBodyCommand extends AbstractInitializeConfigurationCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('initialize:responseBody')
            ->setDescription('Create response body files');
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Creation of .phpbenchmarks/responseBody/responseBody.txt')
            ->copyDefaultConfigurationFile('responseBody/responseBody.txt', true)
            ->runCommand('validate:responseBody');

        return $this;
    }
}
