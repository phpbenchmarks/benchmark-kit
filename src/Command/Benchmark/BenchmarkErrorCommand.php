<?php

declare(strict_types=1);

namespace App\Command\Benchmark;

use App\Command\AbstractCommand;
use Symfony\Component\Console\Output\OutputInterface;

final class BenchmarkErrorCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'benchmark:error';

    protected const ERROR_LOG_PATH = '/var/log/nginx/error.log';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Output error log');
    }

    protected function doExecute(): parent
    {
        return $this
            ->outputWarning('Output logs contained in ' . static::ERROR_LOG_PATH . '.', false)
            ->outputWarning('CTRL + C to exit.', false)
            ->runProcess(
                ['tail', '-f', static::ERROR_LOG_PATH],
                OutputInterface::VERBOSITY_NORMAL,
                null,
                null
            );
    }
}
