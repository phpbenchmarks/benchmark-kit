<?php

declare(strict_types=1);

namespace App\Command\Validate\Benchmark;

use App\{
    BenchmarkConfiguration\BenchmarkConfiguration,
    BenchmarkConfiguration\BenchmarkConfigurationService,
    Command\AbstractCommand,
    Command\Behavior\CallUrlTrait,
    Benchmark\Benchmark,
    Command\Behavior\ValidateCircleCiOptionTrait,
    Command\Benchmark\BenchmarkInitCommand,
    Command\Validate\Configuration\ValidateConfigurationCommand,
    PhpVersion\PhpVersion,
};

abstract class AbstractValidateBenchmarkUrlCommand extends AbstractCommand
{
    use CallUrlTrait;
    use ValidateCircleCiOptionTrait;

    abstract protected function getUrl(): string;

    protected function configure(): void
    {
        parent::configure();

        $this
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
            $this->validatePhpVersion($phpVersion);
        }

        return 0;
    }

    protected function validatePhpVersion(PhpVersion $phpVersion): self
    {
        foreach (BenchmarkConfigurationService::getAvailable($phpVersion) as $benchmarkConfiguration) {
            $this->outputTitle(
                'Validation of '
                    . $this->getUrl()
                    . ' for PHP ' . $phpVersion->toString()
                    . ' with ' . $benchmarkConfiguration->toString()
            );

            $this->validateBenchmarkConfiguration($phpVersion, $benchmarkConfiguration);
        }

        return $this;
    }

    protected function validateBenchmarkConfiguration($phpVersion, $benchmarkConfiguration): self
    {
        $this
            ->initBenchmark($phpVersion, $benchmarkConfiguration)
            ->outputSuccess('Benchmark initialized.');

        $body = $this->callUrl($this->getUrl());

        return $this
            ->outputSuccess('Http code is 200.')
            ->afterHttpCodeValidated($phpVersion, $benchmarkConfiguration, $body);
    }

    protected function afterHttpCodeValidated(
        PhpVersion $phpVersion,
        BenchmarkConfiguration $benchmarkConfiguration,
        ?string $body
    ): self {
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
