<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Command\AbstractCommand,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration
};

final class ValidateComposerJsonCommand extends AbstractComposerFilesCommand
{
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

        $composerJsonFile = $this->getInstallationPath() . '/composer.json';
        if (is_readable($composerJsonFile) === false) {
            $this->throwError('File does not exist.');
        }

        try {
            $data = json_decode(file_get_contents($composerJsonFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $this->throwError('Error while parsing: ' . $e->getMessage());
        }

        $this
            ->validateName($data)
            ->validateLicense($data)
            ->validateRequireComponent($data)
            ->validateRequireCommon($data);

        return $this;
    }

    private function validateName(array $data): self
    {
        ($data['name'] ?? null) === 'phpbenchmarks/' . ComponentConfiguration::getComponentSlug()
            ? $this->outputSuccess('Name ' . $data['name'] . ' is valid.')
            :
                $this->throwError(
                    'Repository name must be "phpbenchmarks/' . ComponentConfiguration::getComponentSlug() . '".'
                );

        return $this;
    }

    private function validateLicense(array $data): self
    {
        ($data['license'] ?? null) === 'proprietary'
            ? $this->outputSuccess('License ' . $data['license'] . ' is valid.')
            : $this->throwError('License must be "proprietary".');

        return $this;
    }

    private function validateRequireComponent(array $data): self
    {
        if (ComponentConfiguration::getComponentType() === ComponentType::PHP) {
            return $this;
        }

        if (is_null($data['require'][ComponentConfiguration::getCoreDependencyName()] ?? null)) {
            $this->throwError(
                'It should require '
                . ComponentConfiguration::getCoreDependencyName()
                . '. See README.md for more informations.'
            );
        }

        if (
            $data['require'][ComponentConfiguration::getCoreDependencyName()]
                === ComponentConfiguration::getCoreDependencyVersion()
            || $data['require'][ComponentConfiguration::getCoreDependencyName()]
                === 'v' . ComponentConfiguration::getCoreDependencyVersion()
        ) {
            $this->outputSuccess(
                'Require '
                    . ComponentConfiguration::getCoreDependencyName()
                    . ':'
                    . $data['require'][ComponentConfiguration::getCoreDependencyName()]
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

    private function validateRequireCommon(array $data): self
    {
        if ($this->skipBranchName() === false) {
            $commonRepository = $this->getCommonRepositoryName();
            $commonVersion = $data['require'][$commonRepository] ?? null;

            $branchPrefix = $this->getCommonProdBranchPrefix();
            $shouldRequire = $branchPrefix . 'z';
            $isBranchValid = substr((string) $commonVersion, 0, strlen($branchPrefix)) === $branchPrefix;

            if ($isBranchValid === false && $this->validateProd() === false) {
                $shouldRequire .= ' or ' . $this->getCommonDevBranchName();
                $isBranchValid = ($data['require'][$commonRepository] ?? null) === $this->getCommonDevBranchName();
            }

            $isBranchValid
                ? $this->outputSuccess('Require ' . $commonRepository . ': ' . $commonVersion . '.')
                :
                    $this
                        ->outputWarning('You can add --skip-branch-name parameter to skip this validation.')
                        ->throwError(
                            'It should require '
                            . $commonRepository
                            . ': '
                            . $shouldRequire
                            . ' but is '
                            . $commonVersion
                            . '. See README.md for more informations.'
                        );
        }

        return $this;
    }
}
