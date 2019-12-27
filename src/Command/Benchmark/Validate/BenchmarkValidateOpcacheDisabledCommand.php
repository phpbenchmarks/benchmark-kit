<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Validate;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\Benchmark\BenchmarkInitCommand,
    PhpVersion\PhpVersion
};

final class BenchmarkValidateOpcacheDisabledCommand extends AbstractValidateBenchmarkCommand
{
    /** @var string */
    protected static $defaultName = 'benchmark:validate:opcacheDisabled';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate benchmark with opcache disabled');
    }

    protected function initBenchmark(PhpVersion $phpVersion): parent
    {
        return $this
            ->runCommand(
                BenchmarkInitCommand::getDefaultName(),
                [
                    'phpVersion' => $phpVersion->toString(),
                    '--no-url-output' => true,
                    '--opcache-enabled' => false
                ]
            )
            ->outputTitle('Validation of ' . BenchmarkUrlService::getUrl(false) . ' with opcache disabled');
    }
}
