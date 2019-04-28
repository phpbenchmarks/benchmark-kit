<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

class ConfigureResponseBodyCommand extends AbstractConfigureCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('configure:responseBody')
            ->setDescription('Create .phpbenchmarks/responseBody files');
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->title('Creation of .phpbenchmarks/responseBody files')
            ->copyResponseBodyFiles()
            ->runCommand('validate:configuration:responseBody');

        return $this;
    }

    protected function copyResponseBodyFiles(): self
    {
        foreach (BenchmarkType::getResponseBodyFiles(ComponentConfiguration::getBenchmarkType()) as $file) {
            $this->copyDefaultConfigurationFile('responseBody/' . $file, true);
        }

        return $this;
    }
}
