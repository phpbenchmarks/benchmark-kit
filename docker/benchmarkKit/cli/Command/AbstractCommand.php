<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    ComponentConfiguration,
    Exception\ValidationException
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface
};

abstract class AbstractCommand extends Command
{
    /** @var string */
    protected $validationPrefix;

    private $validateDev = true;

    private $repositoriesCreated = true;

    /** @var ?string */
    private $resultTypeSlug;

    public function isValidateDev(): bool
    {
        return $this->validateDev;
    }

    public function isRepositoriesCreated(): bool
    {
        return $this->repositoriesCreated;
    }

    public function getResultTypeSlug(): ?string
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

    protected function validationSuccess(OutputInterface $output, string $message): self
    {
        $output->writeln("  \e[42m > \e[00m \e[32mValidated\e[00m " . $this->validationPrefix . $message);

        return $this;
    }

    protected function validationFailed(OutputInterface $output, string $error): void
    {
        throw new ValidationException($output, $this->validationPrefix . $error);
    }

    protected function warning(OutputInterface $output, string $message): self
    {
        $output->writeln("  \e[43m > \e[00m \e[43m " . $this->validationPrefix . $message . " \e[00m");

        return $this;
    }

    protected function repositoriesNotCreatedWarning(OutputInterface $output): self
    {
        $this->warning(
            $output,
            'Branch names are not validated.'
                . ' Don\'t forget to remove "--repositories-not-created"'
                . ' parameter when repositories will be created.'
        );

        return $this;
    }

    protected function getInstallationPath(): string
    {
        return '/var/www/phpbenchmarks';
    }

    protected function getCommonRepositoryName(): string
    {
        return 'phpbenchmarks/' . ComponentConfiguration::COMMON_REPOSITORY;
    }

    protected function getCommonDevBranchName(): string
    {
        return
            'dev-'
            . ComponentConfiguration::SLUG
            . '_'
            . ComponentConfiguration::VERSION_MAJOR
            . '_'
            . $this->getResultTypeSlug()
            . '_prepare';
    }

    protected function getCommonProdBranchPrefix(OutputInterface $output): string
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

        return  ComponentConfiguration::VERSION_MAJOR . '.' . $commonMinorVersion . '.';
    }
}
