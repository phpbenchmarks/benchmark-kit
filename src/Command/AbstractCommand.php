<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Benchmark\BenchmarkType,
    Command\Configure\ConfigureAllCommand,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration,
    Exception\HiddenValidationException,
    Exception\ValidationException
};
use Symfony\Component\Console\{
    Command\Command,
    Input\ArrayInput,
    Input\InputInterface,
    Output\OutputInterface,
    Question\ChoiceQuestion,
    Question\ConfirmationQuestion,
    Question\Question
};
use Symfony\Component\Process\Process;

abstract class AbstractCommand extends Command
{
    abstract protected function doExecute(): self;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var bool */
    private $validateProd = true;

    /** @var bool */
    private $skipBranchName = true;

    /** @var bool */
    private $skipSourceCodeUrls = false;

    public function isValidateProd(): bool
    {
        return $this->validateProd;
    }

    public function skipBranchName(): bool
    {
        return $this->skipBranchName;
    }

    public function skipSourceCodeUrls(): bool
    {
        return $this->skipSourceCodeUrls;
    }

    protected function configure(): void
    {
        $this
            ->addOption('validate-prod', 'p', null, 'Validate data for prod instead of dev')
            ->addOption('skip-branch-name', null, null, 'Do not validate branch name')
            ->addOption('skip-source-code-urls', null, null, 'Do not validate source code urls');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->validateProd = $input->getOption('validate-prod');
        $this->skipBranchName = $input->getOption('skip-branch-name');
        $this->skipSourceCodeUrls = $input->getOption('skip-source-code-urls');

        $return = 0;
        try {
            $this->doExecute();
        } catch (HiddenValidationException $e) {
            $return = 1;
        } catch (\Throwable $e) {
            $this->outputError($e->getMessage());
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

    /** @return $this */
    protected function outputTitle(string $title): self
    {
        $this->output->writeln("\e[44m " . $title . " \e[00m");

        return $this;
    }

    /** @return $this */
    protected function outputSuccess(string $message): self
    {
        $this->output->writeln("  \e[42m > \e[00m " . $message);

        return $this;
    }

    /** @return $this */
    protected function outputWarning(string $message, bool $indent = true): self
    {
        $prefix = $indent ? "  \e[43m > \e[00m " : null;
        $this->output->writeln($prefix . "\e[43m " . $message . " \e[00m");

        return $this;
    }

    protected function throwError(string $error = null): void
    {
        throw new ValidationException($error);
    }

    /** @return $this */
    protected function outputError(string $error): self
    {
        $this->output->writeln("  \e[41m > \e[00m \e[41m ERROR \e[00m \e[31m" . $error . "\e[00m");

        return $this;
    }

    protected function getInstallationPath(): string
    {
        return '/var/www/benchmark';
    }

    protected function getConfigurationPath(bool $relative = false): string
    {
        return ($relative ? null : $this->getInstallationPath() . '/') . '.phpbenchmarks';
    }

    protected function getResponseBodyPath(bool $relative = false): string
    {
        return $this->getConfigurationPath($relative) . '/responseBody';
    }

    protected function getComposerPath(bool $relative = false): string
    {
        return $this->getConfigurationPath($relative) . '/composer';
    }

    protected function getConfigurationFilePath(bool $relative = false): string
    {
        return $this->getConfigurationPath($relative) . '/Configuration.php';
    }

    protected function getInitBenchmarkFilePath(bool $relative = false): string
    {
        return $this->getConfigurationPath($relative) . '/initBenchmark.sh';
    }

    protected function getVhostFilePath(bool $relative = false): string
    {
        return $this->getConfigurationPath($relative) . '/vhost.conf';
    }

    protected function getComposerLockFilePath(string $version, bool $relative = false): string
    {
        return $this->getComposerPath($relative) . '/composer.lock.php' . $version;
    }

    protected function getDefaultConfigurationPath(int $componentType = null): string
    {
        return
            __DIR__
            . '/../DefaultConfiguration/'
            . ComponentType::getUpperCamelCaseName($componentType ?? ComponentConfiguration::getComponentType())
            . '/MainRepository';
    }

    protected function getTypedDefaultConfigurationPath(int $componentType = null, int $benchmarkType = null): string
    {
        return
            $this->getDefaultConfigurationPath($componentType)
            . '/'
            . BenchmarkType::getUpperCamelCaseName($benchmarkType ?? ComponentConfiguration::getBenchmarkType());
    }

    /** @return $this */
    protected function runProcess(
        array $commands,
        int $outputVerbosity = OutputInterface::VERBOSITY_NORMAL,
        string $cwd = null
    ): self {
        (new Process($commands, $cwd ?? $this->getInstallationPath()))
            ->mustRun(
                function (string $type, string $line) use ($outputVerbosity) {
                    if ($this->getOutput()->getVerbosity() >= $outputVerbosity) {
                        $this->getOutput()->writeln($line);
                    }
                }
            );

        return $this;
    }

    /** @return $this */
    protected function runCommand(string $name, array $arguments = []): self
    {
        if ($this->isValidateProd()) {
            $arguments['--validate-prod'] = true;
        }
        if ($this->skipBranchName()) {
            $arguments['--skip-branch-name'] = true;
        }
        if ($this->skipSourceCodeUrls()) {
            $arguments['--skip-source-code-urls'] = true;
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

    protected function askConfirmationQuestion(string $question, bool $default = true): bool
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

    protected function askQuestion(string $question, string $default = null): ?string
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

    protected function askChoiceQuestion(string $question, array $choices): string
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
            $this->throwError(
                'File '
                    . $shortFilePath
                    . ' does not exist. Use "phpbenchkit '
                    . ConfigureAllCommand::getDefaultName()
                    . '" to create it.'
            );
        }
        $this->outputSuccess('File ' . $shortFilePath . ' exist.');

        return $this;
    }

    protected function removeInstallationPathPrefix(string $path): string
    {
        return substr($path, strlen($this->getInstallationPath()) + 1);
    }
}
