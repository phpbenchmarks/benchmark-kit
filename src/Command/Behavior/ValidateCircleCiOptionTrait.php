<?php

declare(strict_types=1);

namespace App\Command\Behavior;

use App\Console\Input\InputOptionService;
use Symfony\Component\Console\{
    Input\InputDefinition,
    Input\InputInterface,
    Input\InputOption
};

trait ValidateCircleCiOptionTrait
{
    protected function getValidateCircleCiOptionName(): string
    {
        return 'validate-circleci';
    }

    /** @return $this */
    protected function addValidateCircleCiOption(InputDefinition $definition): self
    {
        $definition->addOption(
            new InputOption(
                $this->getValidateCircleCiOptionName(),
                null,
                InputOption::VALUE_REQUIRED,
                'Validate CircleCI configuration',
                true
            )
        );

        return $this;
    }

    protected function getValidateCircleCiOption(InputInterface $input): bool
    {
        return InputOptionService::getBoolValue($input, $this->getValidateCircleCiOptionName());
    }

    protected function appendValidateCircleCiOption(InputInterface $input, array $parameters = []): array
    {
        return array_merge(
            $parameters,
            ['--' . $this->getValidateCircleCiOptionName() => $this->getValidateCircleCiOption($input)]
        );
    }
}
