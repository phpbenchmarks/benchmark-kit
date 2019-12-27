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
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractValidateBenchmarkCommand extends AbstractCommand
{
    abstract protected function initBenchmark(PhpVersion $phpVersion): self;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->addOption('no-validate-configuration')
            ->addOption(
                'init-calls',
                null,
                InputOption::VALUE_REQUIRED,
                'Calls to benchmark url before doing anything to init caches',
                0
            );
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

        $initCalls = $this->getInput()->getOption('init-calls');
        if (is_numeric($initCalls) && $initCalls > 0) {
            for ($i = 0; $i < $this->getInput()->getOption('init-calls'); $i++) {
                $this->callBenchmarkUrl();
            }
            $this->outputSuccess(
                "Init caches with $initCalls call" . ($initCalls > 1 ? 's' : null) . ' to benchmark url.'
            );
        }

        $body = $this->callBenchmarkUrl();

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

    protected function callBenchmarkUrl(): ?string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->getUrl());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            throw new \Exception('Http code should be 200 but is ' . $httpCode . '.');
        }

        return $body;
    }

    protected function afterBodyValidated(PhpVersion $phpVersion): self
    {
        return $this;
    }

    protected function getUrl(): string
    {
        return BenchmarkUrlService::getUrl(true);
    }
}
