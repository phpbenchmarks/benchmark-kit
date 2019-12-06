<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Validate;

use App\{
    Benchmark\BenchmarkType,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Validate\ValidateAllCommand,
    Benchmark\Benchmark,
    PhpVersion\PhpVersion,
    Utils\Path
};

abstract class AbstractValidateBenchmarkCommand extends AbstractCommand
{
    abstract protected function initBenchmark(PhpVersion $phpVersion): self;

    protected function addNoValidateConfigurationOption(): self
    {
        return $this->addOption('no-validate-configuration');
    }

    protected function doExecute(): parent
    {
        if ($this->getInput()->getOption('no-validate-configuration') === false) {
            $this->runCommand(ValidateAllCommand::getDefaultName());
        }

        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $this->validateForPhpVersion($phpVersion);
        }

        return $this;
    }

    protected function validateForPhpVersion(PhpVersion $phpVersion): self
    {
        $this->initBenchmark($phpVersion);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, BenchmarkUrlService::getUrl(false));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            throw new \Exception('Http code should be 200 but is ' . $httpCode . '.');
        }

        return $this
            ->outputSuccess('Http code is 200.')
            ->validateBody($body, $phpVersion)
            ->afterBodyValidated($phpVersion);
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

    protected function afterBodyValidated(PhpVersion $phpVersion): self
    {
        return $this;
    }
}
