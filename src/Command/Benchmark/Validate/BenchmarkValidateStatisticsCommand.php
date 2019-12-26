<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Validate;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Benchmark\BenchmarkInitCommand,
    Command\Configure\ConfigureEntryPointCommand,
    Benchmark\Benchmark,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class BenchmarkValidateStatisticsCommand extends AbstractValidateBenchmarkCommand
{
    private const REQUIRE_CODE = 'require(\'/var/benchmark-kit/benchmark/statistics.php\');';
    private const STATS_RESULTS_FILE_PATH = '/tmp/phpbenchmarks-stats-php';

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
            ->removeFile(static::STATS_RESULTS_FILE_PATH, false)
            ->replaceInEntryPoint(ConfigureEntryPointCommand::STATS_COMMENT, static::REQUIRE_CODE)
            ->runCommand(
                BenchmarkInitCommand::getDefaultName(),
                [
                    'phpVersion' => $phpVersion->toString(),
                    '--no-url-output' => true,
                    '--opcache-enabled' => true,
                    '--preload-enabled' => false
                ]
            )
            ->outputTitle('Validation of statistics for ' . BenchmarkUrlService::getUrl(false));
    }

    protected function onError(): AbstractCommand
    {
        return $this->replaceInEntryPoint(static::REQUIRE_CODE, ConfigureEntryPointCommand::STATS_COMMENT);
    }

    protected function afterBodyValidated(PhpVersion $phpVersion): self
    {
        if (is_readable(static::STATS_RESULTS_FILE_PATH) === false) {
            throw new \Exception(static::STATS_RESULTS_FILE_PATH . ' does not exists or is not readable.');
        }

        $stats = explode("\n", file_get_contents(static::STATS_RESULTS_FILE_PATH));
        if (count($stats) !== 10) {
            throw new \Exception('Invalid format for statistics.');
        }

        array_pop($stats);
        foreach ($stats as $stat) {
            if (is_numeric($stat) === false) {
                throw new \Exception('Stat "' . $stat . '" should be numeric.');
            }
        }

        return $this
            ->outputSuccess('Statistics found.')
            ->replaceInEntryPoint(static::REQUIRE_CODE, ConfigureEntryPointCommand::STATS_COMMENT);
    }

    private function replaceInEntryPoint(string $search, string $replace): self
    {
        $entryPointFilePath = Path::getBenchmarkPath() . '/' . Benchmark::getSourceCodeEntryPoint();
        $entryPointContent = file_get_contents($entryPointFilePath);

        return $this->filePutContent(
            $entryPointFilePath,
            str_replace($search, $replace, $entryPointContent)
        );
    }
}
