<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Benchmark\BenchmarkType,
    Component\ComponentType,
    Exception\HiddenException,
    Exception\ValidationException,
    Utils\Directory
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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Twig\{
    Environment,
    Loader\FilesystemLoader
};

abstract class AbstractCommand extends Command
{
    abstract protected function doExecute(): self;

    protected const PHPBENCHMARKS_DIRECTORY = '.phpbenchmarks';

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var bool */
    private $skipSourceCodeUrls = false;

    public function skipSourceCodeUrls(): bool
    {
        return $this->skipSourceCodeUrls;
    }

    protected function configure(): void
    {
        $this->addOption('skip-source-code-urls', null, null, 'Do not validate source code urls');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->skipSourceCodeUrls = $input->getOption('skip-source-code-urls');

        $return = 0;
        try {
            $this->doExecute();
        } catch (HiddenException $exception) {
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

    /** @return $this */
    protected function outputCallPhpbenchkitWarning(string $command): self
    {
        return $this->outputWarning("You can use phpbenchkit $command to configure it.");
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

    protected function getBenchmarkKitPath(): string
    {
        return realpath(__DIR__ . '/../..');
    }

    protected function getInstallationPath(): string
    {
        return '/var/www/benchmark';
    }

    protected function getConfigurationPath(bool $relative = false): string
    {
        return ($relative ? null : $this->getInstallationPath() . '/') . static::PHPBENCHMARKS_DIRECTORY;
    }

    protected function getResponseBodyPath(bool $relative = false): string
    {
        return $this->getConfigurationPath($relative) . '/responseBody';
    }

    protected function getConfigurationFilePath(bool $relative = false): string
    {
        return $this->getConfigurationPath($relative) . '/Configuration.php';
    }

    protected function getInitBenchmarkFilePath(bool $relative = false): string
    {
        return $this->getConfigurationPath($relative) . '/initBenchmark.sh';
    }

    protected function renderTemplate(
        string $templatePath,
        array $templateParameters = [],
        int $componentType = null,
        int $benchmarkType = null
    ): string {
        static $twig;
        if ($twig instanceof Environment === false) {
            $twig = new Environment(new FilesystemLoader(__DIR__ . '/../../templates'));
        }

        $componentPath = 'benchmark/' . ComponentType::getCamelCaseName($componentType);
        $templates = [
            $componentPath . '/' . $templatePath . '.' . BenchmarkType::getCamelCaseName($benchmarkType) . '.twig',
            $componentPath . '/' . $templatePath . '.twig',
            'benchmark/default/' . $templatePath . '.twig'
        ];

        $templateTwigPath = null;
        foreach ($templates as $template) {
            if (is_readable($this->getBenchmarkKitPath() . '/templates/' . $template) === true) {
                $templateTwigPath = $template;
                break;
            }
        }

        if ($templateTwigPath === null) {
            throw new \Exception('Template ' . $templatePath . ' not found.');
        }

        return $twig->render($templateTwigPath, $templateParameters);
    }

    protected function writeFileFromTemplate(
        string $templatePath,
        array $templateParameters = [],
        int $componentType = null,
        int $benchmarkType = null
    ): self {
        $file = $this->getInstallationPath() . '/' . $templatePath;

        return $this
            ->createDirectory(dirname($file))
            ->filePutContent(
                $file,
                $this->renderTemplate($templatePath, $templateParameters, $componentType, $benchmarkType)
            );
    }

    protected function filePutContent(string $filename, string $content): self
    {
        $fileExists = file_exists($filename);
        (new Filesystem())->dumpFile($filename, $content);
        $this->outputSuccess(
            'File '
                . $this->removeInstallationPathPrefix($filename)
                . ' '
                . ($fileExists ? 'modified' : 'created')
                . '.'
        );

        return $this;
    }

    protected function createDirectory(string $directory): self
    {
        if (is_dir($directory) === false) {
            (new Filesystem())->mkdir($directory);
            $this->outputSuccess('Directory ' . Directory::removeBenchmarkPathPrefix($directory) . ' created.');
        }

        return $this;
    }

    protected function removeDirectory(string $directory): self
    {
        if (is_dir($directory)) {
            (new Filesystem())->remove($directory);
            $this->outputSuccess('Directory ' . Directory::removeBenchmarkPathPrefix($directory) . ' removed.');
        }

        return $this;
    }

    protected function removeFile(string $file): self
    {
        if (is_file($file)) {
            (new Filesystem())->remove($file);
            $this->outputSuccess('File ' . Directory::removeBenchmarkPathPrefix($file) . ' removed.');
        }

        return $this;
    }

    /** @return $this */
    protected function runProcess(
        array $commands,
        int $outputVerbosity = OutputInterface::VERBOSITY_NORMAL,
        string $cwd = null,
        ?int $timeout = 60
    ): self {
        (new Process($commands, $cwd ?? $this->getInstallationPath(), null, null, $timeout))
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
        if ($this->skipSourceCodeUrls()) {
            $arguments['--skip-source-code-urls'] = true;
        }

        $returnCode = $this
            ->getApplication()
            ->find($name)
            ->run(new ArrayInput($arguments), $this->output);
        if ($returnCode > 0) {
            throw new HiddenException();
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
    protected function assertFileExist(string $filePath, string $configureCommandName): self
    {
        if (is_readable($filePath) === false) {
            $this->throwError(
                'File '
                    . $this->removeInstallationPathPrefix($filePath)
                    . ' does not exist. Use "phpbenchkit '
                    . $configureCommandName
                    . '" to create it.'
            );
        }
        $this->outputSuccess('File ' . $this->removeInstallationPathPrefix($filePath) . ' exist.');

        return $this;
    }

    protected function removeInstallationPathPrefix(string $path): string
    {
        return substr($path, strlen($this->getInstallationPath()) + 1);
    }
}
