<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Validate;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\Benchmark\BenchmarkInitCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class BenchmarkValidateBenchmarkCommand extends AbstractValidateBenchmarkCommand
{
    /** @var string */
    protected static $defaultName = 'benchmark:validate:benchmark';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate benchmark')
            ->addNoValidateConfigurationOption();
    }

    protected function initBenchmark(PhpVersion $phpVersion): AbstractValidateBenchmarkCommand
    {
        return $this
            ->removeFile(Path::getOpcacheDisableIniPath($phpVersion))
            ->runCommand(
                BenchmarkInitCommand::getDefaultName(),
                [
                    'phpVersion' => $phpVersion->toString(),
                    '--no-url-output' => true
                ]
            )
            ->outputTitle('Validation of ' . BenchmarkUrlService::getUrlWithPort(true));
    }
}
