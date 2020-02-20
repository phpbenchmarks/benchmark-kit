<?php

declare(strict_types=1);

namespace App\Command\Export\Benchmark;

use App\{
    Command\AbstractCommand,
    Command\Benchmark\Validate\BenchmarkValidateStatisticsCommand,
    Utils\Path
};

final class ExportBenchmarkStatisticsCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'export:benchmark:statistics';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Export benchmark statistics (memory, declared classes etc) in JSON');
    }

    protected function doExecute(): int
    {
        $this->runCommand(
            BenchmarkValidateStatisticsCommand::getDefaultName(),
            ['--init-calls' => 100],
            false
        );

        try {
            $statistics = file_get_contents(Path::getStatisticsPath());
        } catch (\Throwable $exception) {
            throw new \Exception('Unable to parse statistics JSON file.', 0, $exception);
        }

        $this->getOutput()->writeln($statistics);

        return 0;
    }
}
