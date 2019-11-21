<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

final class ConfigurePhpBenchmarksResponseBodyCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:responseBody';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . $this->getResponseBodyPath(true) . ' files');
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->outputTitle('Creation of ' . $this->getResponseBodyPath(true) . ' files')
            ->removeDirectory($this->getResponseBodyPath());

        foreach (BenchmarkType::getResponseBodyFiles(ComponentConfiguration::getBenchmarkType()) as $file) {
            $this->writeFileFromTemplate($this->getResponseBodyPath(true) . '/' . $file);
        }

        return $this;
    }
}
