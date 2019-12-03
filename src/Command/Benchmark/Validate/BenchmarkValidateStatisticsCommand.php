<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Validate;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Benchmark\BenchmarkInitCommand,
    Command\Configure\ConfigureEntryPointCommand,
    Command\PrepareBenchmarkCurlTrait,
    Command\Validate\ValidateAllCommand,
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class BenchmarkValidateStatisticsCommand extends AbstractCommand
{
    use PrepareBenchmarkCurlTrait;

    private const REQUIRE_CODE = 'require(\'/var/benchmark-kit/benchmark/statistics.php\');';
    private const STATS_RESULTS_FILE_PATH = '/tmp/phpbenchmarks-stats-php';

    /** @var string */
    protected static $defaultName = 'benchmark:validate:statistics';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate PHP statistics')
            ->addOption('no-validate')
            ->addOption('no-url-output');
    }

    protected function doExecute(): parent
    {
        if ($this->getInput()->getOption('no-validate') === false) {
            $this->runCommand(ValidateAllCommand::getDefaultName());
        }

        foreach (ComponentConfiguration::getCompatiblesPhpVersions() as $phpVersion) {
            $this->validateForPhpVersion($phpVersion);
        }

        return $this;
    }

    protected function onError(): AbstractCommand
    {
        return $this->replaceInEntryPoint(static::REQUIRE_CODE, ConfigureEntryPointCommand::STATS_COMMENT);
    }

    private function validateForPhpVersion(PhpVersion $phpVersion): self
    {
        $this
            ->outputTitle('Prepare benchmark')
            ->removeFile(static::STATS_RESULTS_FILE_PATH, false)
            ->replaceInEntryPoint(ConfigureEntryPointCommand::STATS_COMMENT, static::REQUIRE_CODE)
            ->runCommand(
                BenchmarkInitCommand::getDefaultName(),
                [
                    'phpVersion' => $phpVersion->toString(),
                    '--no-url-output' => $this->getInput()->getOption('no-url-output')
                ]
            )
            ->outputTitle('Validation of statistics for ' . BenchmarkUrlService::getUrlWithPort(false));

        $curl = $this->prepareBenchmarkCurl(false);

        curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            throw new \Exception('Http code should be 200 but is ' . $httpCode . '.');
        }

        $this->outputSuccess('Http code is 200.');

        return $this
            ->validateStatistics()
            ->replaceInEntryPoint(static::REQUIRE_CODE, ConfigureEntryPointCommand::STATS_COMMENT);
    }

    private function replaceInEntryPoint(string $search, string $replace): self
    {
        $entryPointFilePath = Path::getBenchmarkPath() . '/' . ComponentConfiguration::getEntryPointFileName();
        $entryPointContent = file_get_contents($entryPointFilePath);

        return $this->filePutContent(
            $entryPointFilePath,
            str_replace($search, $replace, $entryPointContent)
        );
    }

    private function validateStatistics(): self
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

        return $this->outputSuccess('Statistics found.');
    }
}
