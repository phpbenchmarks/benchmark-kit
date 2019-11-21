<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\Command\AbstractCommand;

final class ValidatePhpBenchmarksInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:initBenchmark';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . $this->getInitBenchmarkFilePath(true));
    }

    protected function doExecute(): parent
    {
        $this
            ->outputTitle('Validation of ' . $this->getInitBenchmarkFilePath(true))
            ->assertFileExist($this->getInitBenchmarkFilePath(), $this->getInitBenchmarkFilePath(true));

        return $this;
    }
}
