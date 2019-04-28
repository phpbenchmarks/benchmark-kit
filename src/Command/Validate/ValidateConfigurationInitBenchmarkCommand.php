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
            ->setDescription('Validate .phpbenchmarks/initBenchmark.sh');
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Validation of .phpbenchmarks/initBenchmark.sh')
            ->assertFileExist($this->getConfigurationPath() . '/initBenchmark.sh', '.phpbenchmarks/initBenchmark.sh');

        return $this;
    }
}
