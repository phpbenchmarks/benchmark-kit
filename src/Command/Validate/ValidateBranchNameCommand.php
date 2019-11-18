<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

final class ValidateBranchNameCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:branch:name';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate branch name: component_X.Y_benchmark-type_prepare');
    }

    protected function doExecute(): parent
    {
        if ($this->skipBranchName() === false) {
            $this
                ->outputTitle('Validation of git branch name')
                ->validateBranchName();
        }

        return $this;
    }

    private function validateBranchName(): self
    {
        $branchName = $this->getBranchName();

        $expectedGitBranch =
            ComponentConfiguration::getComponentSlug()
            . '_'
            . ComponentConfiguration::getCoreDependencyMajorVersion()
            . '.'
            . ComponentConfiguration::getCoreDependencyMinorVersion()
            . '_'
            . BenchmarkType::getSlug(ComponentConfiguration::getBenchmarkType());
        if ($this->isValidateProd() === false) {
            $expectedGitBranch .= '_prepare';
        }

        if ($branchName !== $expectedGitBranch) {
            $this
                ->outputWarning('You can add --skip-branch-name parameter to skip this validation.')
                ->outputWarning('You can add --validate-prod parameter to remove "_prepare" suffix in branch name.')
                ->throwError('Branch name should be ' . $expectedGitBranch . ' but is ' . $branchName . '.');
        }
        $this->outputSuccess('Branch name is ' . $branchName . '.');

        return $this;
    }

    private function getBranchName(): string
    {
        $command =
            'cd ' . $this->getInstallationPath()
            . ' && git branch --no-color 2> /dev/null'
            . ' | sed -e \'/^[^*]/d\' -e \'s/* \(.*\)/(\1)/\' -e \'s/(//g\' -e \'s/)//g\'';

        // As command is tricky, I prefer using exec() instead of Process
        exec($command, $return, $returnCode);
        if ($returnCode > 0 || is_array($return) === false || count($return) !== 1) {
            $this->throwError('Can\'t get git branch name.');
        }

        return $return[0];
    }
}
