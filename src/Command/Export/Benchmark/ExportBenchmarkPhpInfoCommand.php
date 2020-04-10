<?php

declare(strict_types=1);

namespace App\Command\Export\Benchmark;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\GetBodyFromUrl
};

final class ExportBenchmarkPhpInfoCommand extends AbstractCommand
{
    use GetBodyFromUrl;

    /** @var string */
    protected static $defaultName = 'export:benchmark:phpinfo';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Export benchmark phpinfo() HTML');
    }

    protected function doExecute(): int
    {
        $body = $this->getBodyFromUrl(BenchmarkUrlService::getPhpinfoUrl());
        if (is_string($body) === false || strlen($body) === 0) {
            throw new \Exception('phpinfo() should not output an empty string.');
        }

        $this->getOutput()->writeln($body);

        return 0;
    }
}
