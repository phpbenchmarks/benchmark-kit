<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Validate;

use App\{
    Command\AbstractCommand,
    Command\Validate\ValidateAllCommand
};

final class BenchmarkValidateAllCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'benchmark:validate:all';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Call all benchmark:validate commands');
    }

    protected function doExecute(): parent
    {
        return $this
            ->runCommand(ValidateAllCommand::getDefaultName())
            ->runCommand(
                BenchmarkValidateBenchmarkCommand::getDefaultName(),
                [
                    '--no-validate-configuration' => true,
                    '--no-url-output' => true
                ]
            )
            ->runCommand(
                BenchmarkValidateStatisticsCommand::getDefaultName(),
                ['--no-validate-configuration' => true]
            );
    }
}
