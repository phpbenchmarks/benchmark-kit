<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

class ValidateBranchNameCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('validate:branch:name')
            ->setDescription('Validate branch name: component_X.Y_benchmark-type_prepare');
    }

    protected function doExecute(): parent
    {
        $this->title('Validation of git branch name');

        if ($this->skipBranchName() === false) {
            $this->validateBranchName();
        }

        return $this;
    }

    protected function validateBranchName(): self
    {
        $cmd =
            'git branch --no-color 2> /dev/null'
            . ' | sed -e \'/^[^*]/d\' -e \'s/* \(.*\)/(\1)/\' -e \'s/(//g\' -e \'s/)//g\'';
        $branchName =
            $this->execAndGetOutput(
                'cd ' . $this->getInstallationPath() . ' && ' . $cmd,
                'Can\'t get git branch name.'
            )[0] ?? null;
        $expectedGitBranch =
            ComponentConfiguration::getComponentSlug()
            . '_'
            . ComponentConfiguration::getCoreDependencyMajorVersion()
            . '.'
            . ComponentConfiguration::getCoreDependencyMinorVersion()
            . '_'
            . BenchmarkType::getSlug(ComponentConfiguration::getBenchmarkType());
        if ($this->validateProd() === false) {
            $expectedGitBranch .= '_prepare';
        }

        if ($branchName !== $expectedGitBranch) {
            $this
                ->warning('You can add --skip-branch-name parameter to skip this validation.')
                ->warning('You can add --validate-prod parameter to remove "_prepare" suffix in branch name.')
                ->error('Branch name should be ' . $expectedGitBranch . ' but is ' . $branchName . '.');
        }
        $this->success('Branch name is ' . $branchName . '.');

        return $this;
    }
}
