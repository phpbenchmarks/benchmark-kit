<?php

declare(strict_types=1);

namespace App\Command\Validate\Composer;

use App\{
    Command\AbstractCommand,
    Command\Behavior\GetComposerConfigurationTrait,
    Command\Configure\Composer\ConfigureComposerJsonCommand,
    Benchmark\Benchmark
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

    protected function doExecute(): int
    {
        $this->outputTitle('Validation of composer.json');

        $composerConfiguration = $this->getComposerConfiguration();

        $this
            ->validateName($composerConfiguration)
            ->validateLicense($composerConfiguration)
            ->validateRequireComponent($composerConfiguration);

        return 0;
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
        if (is_null($composerConfiguration['require'][Benchmark::getCoreDependencyName()] ?? null)) {
            throw new \Exception(
                'It should require '
                    . Benchmark::getCoreDependencyName()
                    . '. See README.md for more informations.'
            );
        }

        if (
            $composerConfiguration['require'][Benchmark::getCoreDependencyName()]
                === Benchmark::getCoreDependencyVersion()
            || $composerConfiguration['require'][Benchmark::getCoreDependencyName()]
                === 'v' . Benchmark::getCoreDependencyVersion()
        ) {
            $this->outputSuccess(
                'Require '
                    . Benchmark::getCoreDependencyName()
                    . ':'
                    . $composerConfiguration['require'][Benchmark::getCoreDependencyName()]
                    . '.'
            );
        } else {
            throw new \Exception(
                'It should require '
                    . Benchmark::getCoreDependencyName()
                    . ':'
                    . Benchmark::getCoreDependencyVersion()
                    . '. See README.md for more informations.'
            );
        }

        return $this;
    }
}
