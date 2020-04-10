<?php

declare(strict_types=1);

namespace App\Command\Export\Benchmark;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\GetBodyFromUrl,
    Utils\Path
};

final class ExportBenchmarkStatisticsCommand extends AbstractCommand
{
    use GetBodyFromUrl;

    /** @var string */
    protected static $defaultName = 'export:benchmark:statistics';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Export benchmark statistics (memory, declared classes etc) in JSON');
    }

    protected function doExecute(): int
    {
        $this
            ->initializePhpCaches()
            ->getBodyFromUrl(BenchmarkUrlService::getStatisticsUrl(false));

        try {
            $statistics = file_get_contents(Path::getStatisticsPath());
        } catch (\Throwable $exception) {
            throw new \Exception('Unable to parse statistics JSON file.', 0, $exception);
        }

        $this->getOutput()->writeln($statistics);

        return 0;
    }

    protected function initializePhpCaches(): self
    {
        for ($i = 0; $i < 10; $i++) {
            $this->getBodyFromUrl(BenchmarkUrlService::getStatisticsUrl(false));
        }

        return $this;
    }
}
