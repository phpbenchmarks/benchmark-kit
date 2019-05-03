<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

class ConfigureInitBenchmarkCommand extends AbstractConfigureCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('configure:initBenchmark')
            ->setDescription('Create ' . $this->getInitBenchmarkFilePath(true));
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->title('Creation of ' . $this->getInitBenchmarkFilePath(true))
            ->copyDefaultConfigurationFile(
                'initBenchmark.sh',
                false,
                'File has been created but is very basic. Don\'t forget to edit it.'
            )
            ->exec('chmod +x ' . $this->getInitBenchmarkFilePath())
            ->success('Make ' . $this->getInitBenchmarkFilePath(true) . ' executable.')
            ->runCommand('validate:configuration:initBenchmark');

        return $this;
    }
}
