<?php

declare(strict_types=1);

namespace App\Command;

use App\{Benchmark\BenchmarkType,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration,
    Exception\HiddenValidationException,
    Exception\ValidationException};
use Symfony\Component\Console\{
    Command\Command,
    Input\ArrayInput,
    Input\InputInterface,
    Output\OutputInterface,
    Question\ChoiceQuestion,
    Question\ConfirmationQuestion,
    Question\Question
};

abstract class AbstractCommand extends Command
{
    abstract protected function doExecute(): self;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var ?string */
    private $validationPrefix;

    private $validateProd = true;

    private $repositoriesCreated = true;

    public function isValidateProd()
    {
        return $this->validateProd;
    }

    public function isRepositoriesCreated()
    {
        return $this->repositoriesCreated;
    }

    protected function configure()
    {
        $this
            ->addOption('validate-prod', 'p', null, 'Validate data for prod instead of dev')
            ->addOption('skip-branch-name', 'sbn', null, 'Do not validate branch name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->validateProd = $input->getOption('validate-prod');
        $this->repositoriesCreated = $input->getOption('skip-branch-name') === false;

        $return = 0;
        try {
            $this->doExecute();
        } catch (HiddenValidationException $e) {
            $return = 1;
        } catch (\Throwable $e) {
            $this->showError($e->getMessage());
            $this->onError();

            $return = 1;
        }

        return $return;
    }

    protected function onError(): self
    {
        return $this;
    }

    protected function getInput(): InputInterface
    {
        return $this->input;
    }

    protected function getOutput(): OutputInterface
    {
        return $this->output;
    }

    protected function setValidationPrefix(?string $prefix): self
    {
        $this->validationPrefix = $prefix;

        return $this;
    }

    /** @return $this */
    protected function title(string $title): self
    {
        $this->output->writeln("\e[44m " . $title . " \e[00m");

        return $this;
    }

    /** @return $this */
    protected function success(string $message): self
    {
        $this->output->writeln("  \e[42m > \e[00m " . $this->validationPrefix . $message);

        return $this;
    }

    /** @return $this */
    protected function warning(string $message): self
    {
        $this->output->writeln("  \e[43m > \e[00m \e[43m " . $this->validationPrefix . $message . " \e[00m");

        return $this;
    }

    protected function error(string $error = null): void
    {
        throw new ValidationException($this->validationPrefix . $error);
    }

    /** @return $this */
    protected function showError(string $error): self
    {
        $this->output->writeln("  \e[41m > \e[00m \e[41m ERROR \e[00m \e[31m" . $error . "\e[00m");

        return $this;
    }

    protected function getInstallationPath(): string
    {
        return '/var/www/phpbenchmarks';
    }

    protected function getConfigurationPath(): string
    {
        return $this->getInstallationPath() . '/.phpbenchmarks';
    }

    protected function getResponseBodyPath(): string
    {
        return $this->getConfigurationPath() . '/responseBody';
    }

    protected function getDefaultConfigurationPath(int $componentType = null): string
    {
        return
            __DIR__
            . '/../DefaultConfiguration/'
            . ComponentType::getCamelCaseName($componentType ?? ComponentConfiguration::getComponentType())
            . '/MainRepository';
    }

    protected function getTypedDefaultConfigurationPath(int $componentType = null, int $benchmarkType = null): string
    {
        return
            $this->getDefaultConfigurationPath($componentType)
            . '/'
            . BenchmarkType::getCamelCaseName($benchmarkType ?? ComponentConfiguration::getBenchmarkType());
    }

    /** @return $this */
    protected function exec(string $command, string $error = 'Error'): self
    {
        $this->execAndGetOutput($command, $error);

        return $this;
    }

    protected function execAndGetOutput(string $command, string $error = 'Error'): array
    {
        exec($command, $return, $returnCode);
        if ($returnCode > 0) {
            $this->error($error);
        }

        return $return;
    }

    /** @return $this */
    protected function definePhpCliVersion(string $version): self
    {
        $this->exec('sudo /usr/bin/update-alternatives --set php /usr/bin/php' . $version);

        return $this;
    }

    /** @return $this */
    protected function runCommand(string $name, array $arguments = []): self
    {
        if ($this->isValidateProd()) {
            $arguments['--validate-prod'] = true;
        }
        if ($this->isRepositoriesCreated() === false) {
            $arguments['--skip-branch-name'] = true;
        }

        $returnCode = $this
            ->getApplication()
            ->find($name)
            ->run(new ArrayInput($arguments), $this->output);
        if ($returnCode > 0) {
            throw new HiddenValidationException();
        }

        return $this;
    }

    protected function confirmationQuestion(string $question, bool $default = true): bool
    {
        return $this
            ->getHelper('question')
            ->ask(
                $this->getInput(),
                $this->getOutput(),
                new ConfirmationQuestion(
                    "  \e[45m > \e[00m \e[45m "
                        . $question
                        . ($default ? ' [Y/n] ' : ' [y/N] ')
                        . "\e[00m ",
                    $default
                )
            );
    }

    protected function question(string $question, string $default = null): ?string
    {
        return $this
            ->getHelper('question')
            ->ask(
                $this->getInput(),
                $this->getOutput(),
                new Question(
                    "  \e[45m > \e[00m \e[45m "
                        . $question
                        . ($default === null ? null : ' [' . $default . ']')
                        . " \e[00m ",
                    $default
                )
            );
    }

    protected function choiceQuestion(string $question, array $choices): string
    {
        return $this
            ->getHelper('question')
            ->ask(
                $this->getInput(),
                $this->getOutput(),
                new ChoiceQuestion(
                    "  \e[45m > \e[00m \e[45m "
                        . $question
                        . " \e[00m ",
                    $choices
                )
            );
    }

    /** @return $this */
    protected function assertFileExist(string $filePath, string $shortFilePath): self
    {
        if (is_readable($filePath) === false) {
            $this->error('File ' . $shortFilePath . ' does not exist. Use "phpbench initialize:branch" to create it.');
        }
        $this->success('File ' . $shortFilePath . ' exist.');

        return $this;
    }

    protected function skipBranchNameWarning(): self
    {
        $this->warning(
            'Branch name is not validated.'
            . ' Don\'t forget to remove "--skip-branch-name"'
            . ' parameter to validate it.'
        );

        return $this;
    }
}
