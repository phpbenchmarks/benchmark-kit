<?php

declare(strict_types=1);

namespace App\Command\Validate\Benchmark;

use App\{
    Command\AbstractCommand,
    Command\Behavior\GetBodyFromUrl,
    Command\Behavior\ValidateCircleCiOptionTrait,
    Command\Validate\Configuration\ValidateConfigurationCommand
};

final class ValidateBenchmarkCommand extends AbstractCommand
{
    use GetBodyFromUrl;
    use ValidateCircleCiOptionTrait;

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
                ValidateConfigurationCommand::getDefaultName(),
                $this->appendValidateCircleCiOption($this->getInput())
            )
            ->runCommand(
                ValidateBenchmarkResponseCommand::getDefaultName(),
                ['--no-validate-configuration' => true]
            )
            ->runCommand(
                ValidateBenchmarkStatisticsCommand::getDefaultName(),
                ['--no-validate-configuration' => true]
            )
            ->runCommand(
                ValidateBenchmarkPhpInfoCommand::getDefaultName(),
                ['--no-validate-configuration' => true]
            );

        return 0;
    }
}
