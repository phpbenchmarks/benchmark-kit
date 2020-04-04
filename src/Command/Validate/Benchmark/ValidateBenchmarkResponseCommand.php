<?php

declare(strict_types=1);

namespace App\Command\Validate\Benchmark;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkType,
    Benchmark\BenchmarkUrlService,
    BenchmarkConfiguration\BenchmarkConfiguration,
    BenchmarkConfiguration\BenchmarkConfigurationService,
    Command\AbstractCommand,
    Command\Behavior\CallUrlTrait,
    Command\Behavior\ValidateCircleCiOption,
    Command\Benchmark\BenchmarkInitCommand,
    Command\Validate\Configuration\ValidateConfigurationCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class ValidateBenchmarkResponseCommand extends AbstractCommand
{
    use CallUrlTrait;
    use ValidateCircleCiOption;

    /** @var string */
    protected static $defaultName = 'validate:benchmark:response';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate benchmark response')
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
                    . BenchmarkUrlService::getUrl(true)
                    . ' for PHP ' . $phpVersion->toString()
                    . ' with ' . $benchmarkConfiguration->toString()
            );

            $this->initBenchmark($phpVersion, $benchmarkConfiguration);

            $body = $this->callUrl(BenchmarkUrlService::getUrl(true));

            $this
                ->outputSuccess('Http code is 200.')
                ->validateBody($body, $phpVersion);
        }

        return $this;
    }

    protected function validateBody(string $body, PhpVersion $phpVersion): self
    {
        $validated = false;
        $responseBodyPath = Path::getResponseBodyPath($phpVersion);

        foreach (BenchmarkType::getResponseBodyFiles(Benchmark::getBenchmarkType()) as $file) {
            $responseFile = $responseBodyPath . '/' . $file;
            if ($body === file_get_contents($responseFile)) {
                $this->outputSuccess('Body is equal to ' . Path::rmPrefix($responseFile) . ' content.');
                $validated = true;
                break;
            }
        }

        if ($validated === false) {
            throw new \Exception(
                'Invalid body, it should be equal to a file in ' . Path::rmPrefix($responseBodyPath) . '.'
            );
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