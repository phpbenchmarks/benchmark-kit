<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Validate;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\Benchmark\BenchmarkInitCommand,
    PhpVersion\PhpVersion
};
use steevanb\SymfonyOptionsResolver\OptionsResolver;

final class BenchmarkValidateStatisticsCommand extends AbstractValidateBenchmarkCommand
{
    private const STATISTICS_FILE_PATH = '/tmp/phpbenchmarks-statistics.json';

    /** @var string */
    protected static $defaultName = 'benchmark:validate:statistics';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate benchmark statistics (memory, declared classes etc)')
            ->addNoValidateConfigurationOption();
    }

    protected function initBenchmark(PhpVersion $phpVersion): parent
    {
        return $this
            ->outputTitle('Prepare benchmark')
            ->removeFile(static::STATISTICS_FILE_PATH, false)
            ->runCommand(
                BenchmarkInitCommand::getDefaultName(),
                [
                    'phpVersion' => $phpVersion->toString(),
                    '--no-url-output' => true,
                    '--opcache-enabled' => true,
                    '--preload-enabled' => true
                ]
            )
            ->outputTitle('Validation of statistics for ' . BenchmarkUrlService::getStatisticsUrl(false));
    }

    protected function getUrl(): string
    {
        return BenchmarkUrlService::getStatisticsUrl(false);
    }

    protected function afterBodyValidated(PhpVersion $phpVersion): self
    {
        if (is_readable(static::STATISTICS_FILE_PATH) === false) {
            throw new \Exception(static::STATISTICS_FILE_PATH . ' does not exists or is not readable.');
        }
        $this->outputSuccess('Statistics JSON file found.');

        try {
            $statistics = json_decode(
                file_get_contents(static::STATISTICS_FILE_PATH),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\Throwable $exception) {
            throw new \Exception('Unable to parse statistics JSON file.', 0, $exception);
        }
        $this->outputSuccess('Statistics JSON file is a valid JSON file.');

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
