<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\ComponentConfiguration\ComponentConfiguration;

class ValidateComposerJsonFilesCommand extends AbstractComposerFilesCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('validate:composer:json')
            ->setDescription('Validate dependencies in composer.json');
    }

    protected function doExecute(): parent
    {
        $this->title('Validation of composer.json');

        $composerJsonFile = $this->getInstallationPath() . '/composer.json';
        if (is_readable($composerJsonFile) === false) {
            $this->error('File does not exist.');
        }

        try {
            $data = json_decode(file_get_contents($composerJsonFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $this->error('Error while parsing: ' . $e->getMessage());
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
            ? $this->success('Name ' . $data['name'] . ' is valid.')
            :
                $this->error(
                    'Repository name must be "phpbenchmarks/' . ComponentConfiguration::getComponentSlug() . '".'
                );

        return $this;
    }

    private function validateLicense(array $data): self
    {
        ($data['license'] ?? null) === 'proprietary'
            ? $this->success('License ' . $data['license'] . ' is valid.')
            : $this->error('License must be "proprietary".');

        return $this;
    }

    private function validateRequireComponent(array $data): self
    {
        if (is_null($data['require'][ComponentConfiguration::getCoreDependencyName()] ?? null)) {
            $this->error(
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
            $this->success(
                'Require '
                . ComponentConfiguration::getCoreDependencyName()
                . ': '
                . $data['require'][ComponentConfiguration::getCoreDependencyName()]
                . '.'
            );
        } else {
            $this->error(
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

            if ($this->validateProd()) {
                $branchPrefix = $this->getCommonProdBranchPrefix();
                $branch = $branchPrefix . 'z';
                $isBranchValid = substr((string) $commonVersion, 0, strlen($branchPrefix)) === $branchPrefix;
            } else {
                $branch = $this->getCommonDevBranchName();
                $isBranchValid = ($data['require'][$commonRepository] ?? null) === $branch;
            }

            $isBranchValid
                ? $this->success('Require ' . $commonRepository . ': ' . $branch . '.')
                :
                    $this->error(
                        'It should require '
                        . $commonRepository
                        . ': '
                        . $branch
                        . ' but is '
                        . $commonVersion
                        . '. See README.md for more informations.'
                    );
        }

        return $this;
    }
}
