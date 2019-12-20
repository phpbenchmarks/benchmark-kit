<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Validate;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\Benchmark\BenchmarkInitCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class BenchmarkValidatePreloadDisabledCommand extends AbstractValidateBenchmarkCommand
{
    /** @var string */
    protected static $defaultName = 'benchmark:validate:preloadDisabled';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate benchmark with preload disabled')
            ->addNoValidateConfigurationOption();
    }

    protected function initBenchmark(PhpVersion $phpVersion): parent
    {
        if ($phpVersion->isPreloadAvailable() === false) {
            return $this;
        }

        return $this
            ->runCommand(
                BenchmarkInitCommand::getDefaultName(),
                [
                    'phpVersion' => $phpVersion->toString(),
                    '--no-url-output' => true,
                    '--opcache-enabled' => true,
                    '--preload-enabled' => false
                ]
            )
            ->outputTitle('Validation of ' . BenchmarkUrlService::getUrl(true) . ' with preload disabled.');
    }
}
