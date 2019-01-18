<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    ComponentConfiguration,
    Exception\ValidationException
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};

class ValidateComposerJsonCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('phpbenchmarks:validate:composerjson')
            ->setDescription('Validation of dependencies in composer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composerJsonFile = $this->getInstallationPath() . '/composer.json';
        if (is_readable($composerJsonFile) === false) {
            $this->validationFailed($output, 'File does not exist.');
        }

        try {
            $data = json_decode(file_get_contents($composerJsonFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            $this->validationFailed($output, 'Error while parsing: ' . $e->getMessage());
        }

        try {
            $this
                ->validateName($output, $data)
                ->validateLicense($output, $data)
                ->validateRequireCommon($output, $data)
                ->validateRequireComponent($output, $data);
        } catch (ValidationException $e) {
            return 1;
        }

        return 0;
    }

    private function validateName(OutputInterface $output, array $data): self
    {
        ($data['name'] ?? null) !== 'phpbenchmarks/' . ComponentConfiguration::SLUG
            ?
                $this->validationFailed(
                    $output,
                    'Repository name must be "phpbenchmarks/' . ComponentConfiguration::SLUG . '".'
                )
            : $this->validationSuccess($output, 'Name ' . $data['name'] . ' is valid.');

        return $this;
    }

    private function validateLicense(OutputInterface $output, array $data): self
    {
        ($data['license'] ?? null) !== 'proprietary'
            ? $this->validationFailed($output, 'License must be "proprietary".')
            : $this->validationSuccess($output, 'License ' . $data['license']  .' is valid.');

        return $this;
    }

    private function validateRequireCommon(OutputInterface $output, array $data): self
    {
        $commonRepository = 'phpbenchmarks/' . ComponentConfiguration::SLUG . '-common';
        (is_null($data['require'][$commonRepository] ?? null))
            ?
                $this->validationFailed(
                    $output,
                    'It should require ' . $commonRepository . '. See README.md for more informations.'
                )
            : $this->validationSuccess($output, 'Require ' . $commonRepository . '.');

        return $this;
    }

    private function validateRequireComponent(OutputInterface $output, array $data): self
    {
        if (is_null($data['require'][ComponentConfiguration::MAIN_REPOSITORY] ?? null)) {
            $this->validationFailed(
                $output,
                'It should require ' . ComponentConfiguration::MAIN_REPOSITORY . '. See README.md for more informations.'
            );
        }

        if (
            $data['require'][ComponentConfiguration::MAIN_REPOSITORY] === ComponentConfiguration::getVersion()
            || $data['require'][ComponentConfiguration::MAIN_REPOSITORY] === 'v' . ComponentConfiguration::getVersion()
        ) {
            $this->validationSuccess(
                $output,
                'Require '
                    . ComponentConfiguration::MAIN_REPOSITORY
                    . ': '
                    . $data['require'][ComponentConfiguration::MAIN_REPOSITORY]
                    . '.'
            );
        } else {
            $this->validationFailed(
                $output,
                'It should require '
                    . ComponentConfiguration::MAIN_REPOSITORY
                    . ' as '
                    . ComponentConfiguration::getVersion()
                    . '. See README.md for more informations.'
            );
        }

        return $this;
    }
}
