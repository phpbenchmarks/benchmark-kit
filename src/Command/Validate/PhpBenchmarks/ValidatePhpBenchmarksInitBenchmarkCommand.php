<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksInitBenchmarkCommand,
    Benchmark\Benchmark,
    Utils\Path};

final class ValidatePhpBenchmarksInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:initBenchmark';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate initBenchmark.sh');
    }

    protected function doExecute(): int
    {
        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $this
                ->outputTitle('Validation of ' . Path::rmPrefix(Path::getInitBenchmarkPath($phpVersion)))
                ->assertFileExist(
                    Path::getInitBenchmarkPath($phpVersion),
                    ConfigurePhpBenchmarksInitBenchmarkCommand::getDefaultName()
                );
        }

        return 0;
    }
}
