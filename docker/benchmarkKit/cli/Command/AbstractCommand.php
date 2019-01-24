<?php

namespace App\Command;

use App\ComponentConfiguration;
use App\Exception\ValidationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /** @var string */
    protected $validationPrefix;

    private $validateDev = true;

    private $repositoriesCreated = true;

    /** @var ?string */
    private $resultTypeSlug;

    public function isValidateDev()
    {
        return $this->validateDev;
    }

    public function isRepositoriesCreated()
    {
        return $this->repositoriesCreated;
    }

    public function getResultTypeSlug()
    {
        return $this->resultTypeSlug;
    }

    protected function configure()
    {
        $this
            ->addArgument('validateDev', InputArgument::REQUIRED)
            ->addArgument('repositoriesCreated', InputArgument::REQUIRED)
            ->addArgument('resultTypeSlug', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validateDev = $input->getArgument('validateDev') === 'true';
        $this->repositoriesCreated = $input->getArgument('repositoriesCreated') === 'true';
        $this->resultTypeSlug = $input->getArgument('resultTypeSlug');

        return 0;
    }

    protected function validationSuccess(OutputInterface $output, $message)
    {
        $output->writeln("  \e[42m > \e[00m \e[32mValidated\e[00m " . $this->validationPrefix . $message);

        return $this;
    }

    protected function validationFailed(OutputInterface $output, $error)
    {
        throw new ValidationException($output, $this->validationPrefix . $error);
    }

    protected function warning(OutputInterface $output, $message)
    {
        $output->writeln("  \e[43m > \e[00m \e[43m " . $this->validationPrefix . $message . " \e[00m");

        return $this;
    }

    protected function repositoriesNotCreatedWarning(OutputInterface $output)
    {
        $this->warning(
            $output,
            'Branch names are not validated.'
                . ' Don\'t forget to remove "--repositories-not-created"'
                . ' parameter when repositories will be created.'
        );

        return $this;
    }

    protected function getInstallationPath()
    {
        return '/var/www/phpbenchmarks';
    }

    protected function getCommonRepositoryName()
    {
        return 'phpbenchmarks/' . ComponentConfiguration::COMMON_REPOSITORY;
    }

    protected function getCommonDevBranchName()
    {
        return
            'dev-'
            . ComponentConfiguration::SLUG
            . '_'
            . ComponentConfiguration::DEPENDENCY_MAJOR_VERSION
            . '_'
            . $this->getResultTypeSlug()
            . '_prepare';
    }

    protected function getCommonProdBranchPrefix(OutputInterface $output)
    {
        switch ($this->getResultTypeSlug()) {
            case 'hello-world': $commonMinorVersion = 1; break;
            case 'rest-api': $commonMinorVersion = 3; break;
            default:
                $this->validationFailed(
                    $output,
                    'Invalid benchmark type "' . $this->getResultTypeSlug() . '".'
                );
        }

        return  ComponentConfiguration::DEPENDENCY_MAJOR_VERSION . '.' . $commonMinorVersion . '.';
    }
}
