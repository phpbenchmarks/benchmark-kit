<?php

declare(strict_types=1);

namespace App\Command\Configure;

class ConfigureResponseBodyCommand extends AbstractConfigureCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('configure:responseBody')
            ->setDescription('Create .phpbenchmarks/responseBody files');
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Creation of .phpbenchmarks/responseBody/responseBody.txt')
            ->copyDefaultConfigurationFile('responseBody/responseBody.txt', true)
            ->runCommand('validate:configuration:responseBody');

        return $this;
    }
}
