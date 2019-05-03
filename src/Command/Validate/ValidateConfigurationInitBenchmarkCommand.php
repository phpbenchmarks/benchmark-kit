<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\Command\AbstractCommand;

class ValidateConfigurationInitBenchmarkCommand extends AbstractCommand
{
    /** @var ?string */
    protected $vhostContent;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('validate:configuration:initBenchmark')
            ->setDescription('Validate ' . $this->getInitBenchmarkFilePath(true));
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Validation of ' . $this->getInitBenchmarkFilePath(true))
            ->assertFileExist($this->getInitBenchmarkFilePath(), $this->getInitBenchmarkFilePath(true));

        return $this;
    }
}
