<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Validate\ValidateConfigurationResponseBodyCommand,
    ComponentConfiguration\ComponentConfiguration
};

final class ConfigureResponseBodyCommand extends AbstractConfigureCommand
{
    /** @var string */
    protected static $defaultName = 'configure:response-body';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . $this->getResponseBodyPath(true) . ' files');
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->outputTitle('Creation of ' . $this->getResponseBodyPath(true) . ' files')
            ->copyResponseBodyFiles()
            ->runCommand(ValidateConfigurationResponseBodyCommand::getDefaultName());

        return $this;
    }

    private function copyResponseBodyFiles(): self
    {
        foreach (BenchmarkType::getResponseBodyFiles(ComponentConfiguration::getBenchmarkType()) as $file) {
            $this->copyDefaultConfigurationFile('responseBody/' . $file, true);
        }

        return $this;
    }
}
