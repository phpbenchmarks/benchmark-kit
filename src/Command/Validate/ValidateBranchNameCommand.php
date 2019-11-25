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

        $this->setDescription('Validate git branch name');
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

        $prodBranchName =
            ComponentConfiguration::getCoreDependencyMajorVersion()
            . '.'
            . ComponentConfiguration::getCoreDependencyMinorVersion()
            . '_'
            . BenchmarkType::getSlug(ComponentConfiguration::getBenchmarkType());

        if ($this->isValidateProd() === true && $branchName !== $prodBranchName) {
            $this
                ->outputSkipBranchNameWarning()
                ->throwError('Branch name should be ' . $prodBranchName . ' but is ' . $branchName . '.');
        } elseif ($this->isValidateProd() === false && $branchName === $prodBranchName) {
            $this
                ->outputSkipBranchNameWarning()
                ->throwError('Branch name should not be ' . $prodBranchName . ', it\'s reversed for prod.');
        }

        return $this->outputSuccess('Branch name ' . $branchName . ' is valid.');
    }

    private function getBranchName(): string
    {
        $command =
            'cd '
            . $this->getInstallationPath()
            . ' && git branch --no-color 2> /dev/null'
            . ' | sed -e \'/^[^*]/d\' -e \'s/* \(.*\)/(\1)/\' -e \'s/(//g\' -e \'s/)//g\'';

        // As command is tricky, I prefer using exec() instead of Process
        exec($command, $return, $returnCode);
        if ($returnCode > 0 || is_array($return) === false || count($return) !== 1) {
            $this->throwError('Can\'t get git branch name.');
        }

        return $return[0];
    }

    private function outputSkipBranchNameWarning(): self
    {
        return $this->outputWarning('You can add --skip-branch-name parameter to skip this validation.');
    }
}
