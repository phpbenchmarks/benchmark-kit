<?php

declare(strict_types=1);

namespace App\Command\Validate\Composer;

use App\{
    Command\AbstractCommand,
    Command\Configure\Composer\ConfigureComposerJsonCommand,
    Command\GetComposerConfigurationTrait,
    ComponentConfiguration\ComponentConfiguration
};

final class ValidateComposerJsonCommand extends AbstractCommand
{
    use GetComposerConfigurationTrait;

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
        if (($composerConfiguration['name'] ?? null) !== ConfigureComposerJsonCommand::getComposerName()) {
            throw new \Exception(
                'Repository name must be "' . ConfigureComposerJsonCommand::getComposerName() . '".'
            );
        }

        return $this->outputSuccess('Name ' . $composerConfiguration['name'] . ' is valid.');
    }

    private function validateLicense(array $composerConfiguration): self
    {
        if (($composerConfiguration['license'] ?? null) !== ConfigureComposerJsonCommand::LICENSE) {
            throw new \Exception('License must be "' . ConfigureComposerJsonCommand::LICENSE . '".');
        }

        return $this->outputSuccess('License ' . $composerConfiguration['license'] . ' is valid.');
    }

    private function validateRequireComponent(array $composerConfiguration): self
    {
        if (is_null($composerConfiguration['require'][ComponentConfiguration::getCoreDependencyName()] ?? null)) {
            throw new \Exception(
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
            throw new \Exception(
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
