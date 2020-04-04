<?php

declare(strict_types=1);

namespace App\Command\Validate\Benchmark;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    BenchmarkConfiguration\BenchmarkConfiguration,
    BenchmarkConfiguration\BenchmarkConfigurationService,
    Command\AbstractCommand,
    Command\Behavior\CallUrlTrait,
    Command\Behavior\ValidateCircleCiOption,
    Command\Benchmark\BenchmarkInitCommand,
    Command\Validate\Configuration\ValidateConfigurationCommand,
    PhpVersion\PhpVersion
};

final class ValidateBenchmarkPhpInfoCommand extends AbstractCommand
{
    use CallUrlTrait;
    use ValidateCircleCiOption;

    /** @var string */
    protected static $defaultName = 'validate:benchmark:php-info';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate benchmark phpinfo()')
            ->addOption('no-validate-configuration')
            ->addValidateCircleCiOption($this->getDefinition());
    }

    protected function doExecute(): int
    {
        if ($this->getInput()->getOption('no-validate-configuration') === false) {
            $this->runCommand(
                ValidateConfigurationCommand::getDefaultName(),
                $this->appendValidateCircleCiOption($this->getInput())
            );
        }

        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $this->validateForPhpVersion($phpVersion);
        }

        return 0;
    }

    protected function validateForPhpVersion(PhpVersion $phpVersion): self
    {
        foreach (BenchmarkConfigurationService::getAvailable($phpVersion) as $benchmarkConfiguration) {
            $this->outputTitle(
                'Validation of '
                    . BenchmarkUrlService::getPhpinfoUrl()
                    . ' for PHP ' . $phpVersion->toString()
                    . ' with ' . $benchmarkConfiguration->toString()
            );

            $this->initBenchmark($phpVersion, $benchmarkConfiguration);

            $body = $this->callUrl(BenchmarkUrlService::getPhpinfoUrl());
            $this->outputSuccess('Http code is 200.');

            if (is_string($body) === false || strlen($body) === 0) {
                throw new \Exception('phpinfo() should not output an empty string.');
            }
            $this->outputSuccess('Response body is not empty.');
        }

        return $this;
    }

    protected function initBenchmark(PhpVersion $phpVersion, BenchmarkConfiguration $benchmarkConfiguration): self
    {
        return $this->runCommand(
            BenchmarkInitCommand::getDefaultName(),
            [
                'phpVersion' => $phpVersion->toString(),
                '--no-url-output' => true,
                '--opcache-enabled' => $benchmarkConfiguration->isOpcacheEnabled(),
                '--preload-enabled' => $benchmarkConfiguration->isPreloadEnabled()
            ]
        );
    }
}
