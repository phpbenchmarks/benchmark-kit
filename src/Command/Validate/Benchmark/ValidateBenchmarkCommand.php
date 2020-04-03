<?php

declare(strict_types=1);

namespace App\Command\Validate\Benchmark;

use App\{
    Command\AbstractCommand,
    Command\Behavior\CallUrlTrait
};

final class ValidateBenchmarkCommand extends AbstractCommand
{
    use CallUrlTrait;

    /** @var string */
    protected static $defaultName = 'validate:benchmark';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate benchmark');
    }

    protected function doExecute(): int
    {
        $this
            ->runCommand(ValidateBenchmarkResponseCommand::getDefaultName())
            ->runCommand(ValidateBenchmarkStatisticsCommand::getDefaultName());

        return 0;
    }
}
