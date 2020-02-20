<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Utils\Path
};

final class ConfigurePhpBenchmarksResponseBodyCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:responseBody';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create responseBody files');
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Creation of responseBody files');

        foreach (Benchmark::getIncompatiblesPhpVersions() as $phpVersion) {
            $this->removeDirectory(Path::getResponseBodyPath($phpVersion));
        }

        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $this->removeDirectory(Path::getResponseBodyPath($phpVersion));

            foreach (BenchmarkType::getResponseBodyFiles(Benchmark::getBenchmarkType()) as $file) {
                $this->writeFileFromTemplate(
                    Path::rmPrefix(Path::getResponseBodyPath($phpVersion)) . '/' . $file
                );
            }
        }

        return 0;
    }
}
