<?php

declare(strict_types=1);

namespace App\Command\Export\Benchmark;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\CallUrlTrait
};

final class ExportBenchmarkPhpInfoCommand extends AbstractCommand
{
    use CallUrlTrait;

    /** @var string */
    protected static $defaultName = 'export:benchmark:phpinfo';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Export benchmark phpinfo() HTML');
    }

    protected function doExecute(): int
    {
        $body = $this->callUrl(BenchmarkUrlService::getPhpinfoUrl());
        if (is_string($body) === false || strlen($body) === 0) {
            throw new \Exception('phpinfo() should not output an empty string.');
        }

        $this->getOutput()->writeln($body);

        return 0;
    }
}
