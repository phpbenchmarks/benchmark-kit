<?php

declare(strict_types=1);

namespace App\Command\Validate\Benchmark;

use App\{
    Benchmark\BenchmarkUrlService,
    BenchmarkConfiguration\BenchmarkConfiguration,
    PhpVersion\PhpVersion,
    Utils\Path
};
use steevanb\SymfonyOptionsResolver\OptionsResolver;

final class ValidateBenchmarkStatisticsCommand extends AbstractValidateBenchmarkUrlCommand
{
    /** @var string */
    protected static $defaultName = 'validate:benchmark:statistics';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate benchmark statistics (memory, declared classes etc)');
    }

    protected function initBenchmark(PhpVersion $phpVersion, BenchmarkConfiguration $benchmarkConfiguration): parent
    {
        parent::initBenchmark($phpVersion, $benchmarkConfiguration);

        return $this->removeFile(Path::getStatisticsPath(), false);
    }

    protected function getUrl(): string
    {
        return BenchmarkUrlService::getStatisticsUrl(false);
    }

    protected function afterHttpCodeValidated(
        PhpVersion $phpVersion,
        BenchmarkConfiguration $benchmarkConfiguration,
        ?string $body
    ): self {
        // Wait for statistics.json file to be written, sometimes it's not the case at this stage
        sleep(1);

        if (is_readable(Path::getStatisticsPath()) === false) {
            throw new \Exception(Path::getStatisticsPath() . ' does not exists or is not readable.');
        }
        $this->outputSuccess('Statistics JSON file found.');

        try {
            $statistics = json_decode(
                file_get_contents(Path::getStatisticsPath()),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\Throwable $exception) {
            throw new \Exception('Unable to parse statistics JSON file.', 0, $exception);
        }
        $this->outputSuccess('Statistics JSON file is a valid JSON file.');

        $this->removeFile(Path::getStatisticsPath());

        try {
            (new OptionsResolver())
                ->configureRequiredOption('memory', ['array'])
                ->configureRequiredOption('code', ['array'])
                ->resolve($statistics);

            (new OptionsResolver())
                ->configureRequiredOption('usage', ['int'])
                ->configureRequiredOption('realUsage', ['int'])
                ->configureRequiredOption('peakUsage', ['int'])
                ->configureRequiredOption('realPeakUsage', ['int'])
                ->resolve($statistics['memory']);

            (new OptionsResolver())
                ->configureRequiredOption('classes', ['int'])
                ->configureRequiredOption('interfaces', ['int'])
                ->configureRequiredOption('traits', ['int'])
                ->configureRequiredOption('functions', ['int'])
                ->configureRequiredOption('constants', ['int'])
                ->resolve($statistics['code']);
        } catch (\Throwable $exception) {
            throw new \Exception('Invalid statistics JSON file format.', 0, $exception);
        }

        return $this->outputSuccess('Statistics found.');
    }
}
