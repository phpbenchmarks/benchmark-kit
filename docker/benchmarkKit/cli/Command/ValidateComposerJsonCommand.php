<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    ComponentConfiguration,
    Exception\ValidationException
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgumpent,
    Input\InputInterface,
    Output\OutputInterface
};

class ValidateComposerJsonCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('phpbenchmarks:validate:composerjson')
            ->setDescription('Validation of dependencies in composer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        try {
            $composerJsonFile = $this->getInstallationPath() . '/composer.json';
            if (is_readable($composerJsonFile) === false) {
                $this->validationFailed($output, 'File does not exist.');
            }

            try {
                $data = json_decode(file_get_contents($composerJsonFile), true, 512, JSON_THROW_ON_ERROR);
            } catch (\Exception $e) {
                $this->validationFailed($output, 'Error while parsing: ' . $e->getMessage());
            }

            $this
                ->validateName($output, $data)
                ->validateLicense($output, $data)
                ->validateRequireComponent($output, $data)
                ->validateRequireCommon($input, $output, $data);
        } catch (ValidationException $e) {
            return 1;
        }

        return 0;
    }

    private function validateName(OutputInterface $output, array $data): self
    {
        ($data['name'] ?? null) === 'phpbenchmarks/' . ComponentConfiguration::SLUG
            ? $this->validationSuccess($output, 'Name ' . $data['name'] . ' is valid.')
            :
                $this->validationFailed(
                    $output,
                    'Repository name must be "phpbenchmarks/' . ComponentConfiguration::SLUG . '".'
                );

        return $this;
    }

    private function validateLicense(OutputInterface $output, array $data): self
    {
        ($data['license'] ?? null) === 'proprietary'
            ? $this->validationSuccess($output, 'License ' . $data['license']  .' is valid.')
            : $this->validationFailed($output, 'License must be "proprietary".');

        return $this;
    }

    private function validateRequireComponent(OutputInterface $output, array $data): self
    {
        if (is_null($data['require'][ComponentConfiguration::DEPENDENCY_NAME] ?? null)) {
            $this->validationFailed(
                $output,
                'It should require ' . ComponentConfiguration::DEPENDENCY_NAME . '. See README.md for more informations.'
            );
        }

        if (
            $data['require'][ComponentConfiguration::DEPENDENCY_NAME] === ComponentConfiguration::getDependencyVersion()
            || $data['require'][ComponentConfiguration::DEPENDENCY_NAME] === 'v' . ComponentConfiguration::getDependencyVersion()
        ) {
            $this->validationSuccess(
                $output,
                'Require '
                    . ComponentConfiguration::DEPENDENCY_NAME
                    . ': '
                    . $data['require'][ComponentConfiguration::DEPENDENCY_NAME]
                    . '.'
            );
        } else {
            $this->validationFailed(
                $output,
                'It should require '
                    . ComponentConfiguration::DEPENDENCY_NAME
                    . ' as '
                    . ComponentConfiguration::getDependencyVersion()
                    . '. See README.md for more informations.'
            );
        }

        return $this;
    }

    private function validateRequireCommon(InputInterface $input, OutputInterface $output, array $data): self
    {
        if ($this->isRepositoriesCreated()) {
            $commonRepository = $this->getCommonRepositoryName();
            $commonVersion = $data['require'][$commonRepository] ?? null;

            if ($this->isValidateDev()) {
                $branch = $this->getCommonDevBranchName();
                $isBranchValid = ($data['require'][$commonRepository] ?? null) === $branch;
            } else {
                $branchPrefix = $this->getCommonProdBranchPrefix($output);
                $branch = $branchPrefix . '.z';
                $isBranchValid = substr((string) $commonVersion, 0, strlen($branchPrefix)) === $branchPrefix;
            }

            $isBranchValid
                ? $this->validationSuccess($output, 'Require ' . $commonRepository . ' as ' . $branch . '.')
                :
                    $this->validationFailed(
                        $output,
                        'It should require '
                        . $commonRepository
                        . ' as '
                        . $branch
                        . ' but is '
                        . $commonVersion
                        . '. See README.md for more informations.'
                    );
        } else {
            $this->repositoriesNotCreatedWarning($output);
        }

        return $this;
    }
}
