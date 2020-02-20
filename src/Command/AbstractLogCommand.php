<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractLogCommand extends AbstractCommand
{
    abstract protected function getLogPath(): string;

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Output ' . $this->getLogPath() . ' log');
    }

    protected function doExecute(): int
    {
        $this
            ->outputWarning('Output logs contained in ' . $this->getLogPath() . '.', false)
            ->outputWarning('CTRL+C to exit.', false)
            ->runProcess(
                ['tail', '-f', $this->getLogPath()],
                OutputInterface::VERBOSITY_NORMAL,
                null,
                null
            );

        return 0;
    }
}
