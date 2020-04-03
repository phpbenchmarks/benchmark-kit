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
    Command\Benchmark\BenchmarkInitCommand,
    Command\Validate\Configuration\ValidateConfigurationCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};
use Symfony\Component\Console\Input\InputOption;

final class ValidateBenchmarkResponseCommand extends AbstractCommand
{
    use CallUrlTrait;

    /** @var string */
    protected static $defaultName = 'validate:benchmark:response';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate benchmark response')
            ->addOption('no-validate-configuration')
            ->addOption(
                'init-calls',
                null,
                InputOption::VALUE_REQUIRED,
                'Calls to benchmark url before doing anything to init caches',
                0
            );
    }

    protected function doExecute(): int
    {
        if ($this->getInput()->getOption('no-validate-configuration') === false) {
            $this->runCommand(ValidateConfigurationCommand::getDefaultName());
        }

        $initCacheCalls = $this->getInput()->getOption('init-calls');
        $initCacheCalls = (is_numeric($initCacheCalls) === true) ? (int) $initCacheCalls : 0;

        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $this->validateForPhpVersion($phpVersion, $initCacheCalls);
        }

        return 0;
    }

    protected function validateForPhpVersion(PhpVersion $phpVersion, int $initCacheCalls): self
    {
        foreach (BenchmarkConfigurationService::getAvailable($phpVersion) as $benchmarkConfiguration) {
            $this->outputTitle(
                'Validation of '
                    . BenchmarkUrlService::getUrl(true)
                    . ' for PHP ' . $phpVersion->toString()
                    . ' with ' . $benchmarkConfiguration->toString()
            );

            $benchmarkUrl = BenchmarkUrlService::getUrl(true);
            $this->initBenchmark($phpVersion, $benchmarkConfiguration, $benchmarkUrl, $initCacheCalls);

            $body = $this->callUrl($benchmarkUrl);

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

    protected function initBenchmark(
        PhpVersion $phpVersion,
        BenchmarkConfiguration $benchmarkConfiguration,
        string $benchmarkUrl,
        int $initCacheCalls
    ): parent {
        $this->runCommand(
            BenchmarkInitCommand::getDefaultName(),
            [
                'phpVersion' => $phpVersion->toString(),
                '--no-url-output' => true,
                '--opcache-enabled' => $benchmarkConfiguration->isOpcacheEnabled(),
                '--preload-enabled' => $benchmarkConfiguration->isPreloadEnabled()
            ]
        );

        if ($initCacheCalls > 0) {
            for ($i = 0; $i < $initCacheCalls; $i++) {
                $this->callUrl($benchmarkUrl);
            }
            $this->outputSuccess(
                "Init caches with $initCacheCalls call" . ($initCacheCalls > 1 ? 's' : null) . ' to benchmark url.'
            );
        }

        return $this;
    }
}
