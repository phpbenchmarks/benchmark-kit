<?php

declare(strict_types=1);

namespace App\Command\Export\Benchmark;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\CallUrlTrait,
    Utils\Path
};

final class ExportBenchmarkStatisticsCommand extends AbstractCommand
{
    use CallUrlTrait;

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
            ->callUrl(BenchmarkUrlService::getStatisticsUrl(false));

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
            $this->callUrl(BenchmarkUrlService::getStatisticsUrl(false));
        }

        return $this;
    }
}
