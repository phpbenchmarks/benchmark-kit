<?php

declare(strict_types=1);

namespace App\Command\Validate\Composer;

use App\{
    Command\AbstractCommand,
    Command\Configure\Composer\ConfigureComposerJsonCommand,
    Command\GetComposerConfiguration,
    ComponentConfiguration\ComponentConfiguration
};

final class ValidateComposerJsonCommand extends AbstractCommand
{
    use GetComposerConfiguration;

    /** @var string */
    protected static $defaultName = 'validate:composer:json';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate dependencies in composer.json');
    }

    protected function doExecute(): AbstractCommand
    {
        $this->outputTitle('Validation of composer.json');

        $composerConfiguration = $this->getComposerConfiguration();

        return $this
            ->validateName($composerConfiguration)
            ->validateLicense($composerConfiguration)
            ->validateRequireComponent($composerConfiguration);
    }

    private function validateName(array $composerConfiguration): self
    {
        ($composerConfiguration['name'] ?? null) === ConfigureComposerJsonCommand::getComposerName()
            ? $this->outputSuccess('Name ' . $composerConfiguration['name'] . ' is valid.')
            :
                $this->throwError(
                    'Repository name must be "' . ConfigureComposerJsonCommand::getComposerName() . '".'
                );

        return $this;
    }

    private function validateLicense(array $composerConfiguration): self
    {
        ($composerConfiguration['license'] ?? null) === ConfigureComposerJsonCommand::LICENSE
            ? $this->outputSuccess('License ' . $composerConfiguration['license'] . ' is valid.')
            : $this->throwError('License must be "' . ConfigureComposerJsonCommand::LICENSE . '".');

        return $this;
    }

    private function validateRequireComponent(array $composerConfiguration): self
    {
        if (is_null($composerConfiguration['require'][ComponentConfiguration::getCoreDependencyName()] ?? null)) {
            $this->throwError(
                'It should require '
                . ComponentConfiguration::getCoreDependencyName()
                . '. See README.md for more informations.'
            );
        }

        if (
            $composerConfiguration['require'][ComponentConfiguration::getCoreDependencyName()]
                === ComponentConfiguration::getCoreDependencyVersion()
            || $composerConfiguration['require'][ComponentConfiguration::getCoreDependencyName()]
                === 'v' . ComponentConfiguration::getCoreDependencyVersion()
        ) {
            $this->outputSuccess(
                'Require '
                    . ComponentConfiguration::getCoreDependencyName()
                    . ':'
                    . $composerConfiguration['require'][ComponentConfiguration::getCoreDependencyName()]
                    . '.'
            );
        } else {
            $this->throwError(
                'It should require '
                    . ComponentConfiguration::getCoreDependencyName()
                    . ': '
                    . ComponentConfiguration::getCoreDependencyVersion()
                    . '. See README.md for more informations.'
            );
        }

        return $this;
    }
}
