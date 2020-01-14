<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Utils\Path
};

final class ConfigurePhpBenchmarksInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:initBenchmark';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create initBenchmark.sh');
    }

    protected function doExecute(): AbstractCommand
    {
        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $initBenchmarkPath = Path::getInitBenchmarkPath($phpVersion);
            $initBenchmarkRelativePath = Path::rmPrefix($initBenchmarkPath);

            $this
                ->outputTitle('Creation of ' . $initBenchmarkRelativePath)
                ->writeFileFromTemplate($initBenchmarkRelativePath)
                ->runProcess(['chmod', '+x', $initBenchmarkPath])
                ->outputSuccess('Make ' . $initBenchmarkRelativePath . ' executable.')
                ->outputWarning(
                    "$initBenchmarkRelativePath (called to initialize your benchmark) has been created."
                        . ' Feel free to edit it.'
                );
        }

        return $this;
    }
}
