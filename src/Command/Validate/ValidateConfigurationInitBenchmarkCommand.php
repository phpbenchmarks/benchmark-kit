<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\Command\AbstractCommand;

final class ValidateConfigurationInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:init-benchmark';

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
