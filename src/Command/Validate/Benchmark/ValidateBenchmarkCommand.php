<?php

declare(strict_types=1);

namespace App\Command\Validate\Benchmark;

use App\{
    Command\AbstractCommand,
    Command\Behavior\CallUrlTrait,
    Command\Behavior\ValidateCircleCiOption
};

final class ValidateBenchmarkCommand extends AbstractCommand
{
    use CallUrlTrait;
    use ValidateCircleCiOption;

    /** @var string */
    protected static $defaultName = 'validate:benchmark';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate benchmark')
            ->addValidateCircleCiOption($this->getDefinition());
    }

    protected function doExecute(): int
    {
        $this
            ->runCommand(
                ValidateBenchmarkResponseCommand::getDefaultName(),
                $this->appendValidateCircleCiOption($this->getInput())
            )
            ->runCommand(
                ValidateBenchmarkPhpInfoCommand::getDefaultName(),
                $this->appendValidateCircleCiOption($this->getInput())
            );
            // Todo: https://github.com/phpbenchmarks/benchmark-kit/issues/118
            // ->runCommand(ValidateBenchmarkStatisticsCommand::getDefaultName());

        return 0;
    }
}
