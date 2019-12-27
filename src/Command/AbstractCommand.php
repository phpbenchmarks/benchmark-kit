<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Benchmark\BenchmarkType,
    Component\ComponentType,
    Exception\HiddenException,
    Utils\Path
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
use Symfony\Component\Process\{
    Exception\ProcessFailedException,
    Process
};
use Twig\{
    Environment,
    Loader\FilesystemLoader
};

abstract class AbstractCommand extends Command
{
    abstract protected function doExecute(): self;

    private ?InputInterface $input;

    private ?OutputInterface $output;

    private bool $skipSourceCodeUrls = false;

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
        $this->getOutput()->writeln("\e[44m " . $title . " \e[00m");

        return $this;
    }

    /** @return $this */
    protected function outputSuccess(string $message): self
    {
        $this->getOutput()->writeln("  \e[42m > \e[00m " . $message);

        return $this;
    }

    /** @return $this */
    protected function outputWarning(string $message, bool $indent = true): self
    {
        $prefix = $indent ? "  \e[43m > \e[00m " : null;
        $this->getOutput()->writeln($prefix . "\e[43m " . $message . " \e[00m");

        return $this;
    }

    /** @return $this */
    protected function outputCallPhpbenchkitWarning(string $command): self
    {
        return $this->outputWarning("You can use \"phpbenchkit $command\" to configure it.");
    }

    /** @return $this */
    protected function outputError(string $error): self
    {
        $this->getOutput()->writeln("  \e[41m > \e[00m \e[41m ERROR \e[00m \e[31m" . $error . "\e[00m");

        return $this;
    }

    protected function renderBenchmarkTemplate(
        string $templatePath,
        array $templateParameters = [],
        int $componentType = null,
        int $benchmarkType = null
    ): string {
        static $twig;
        if ($twig instanceof Environment === false) {
            $twig = new Environment(new FilesystemLoader(__DIR__ . '/../../templates/benchmark'));
        }

        $componentPath = ComponentType::getCamelCaseName($componentType);
        $templates = [
            $componentPath . '/' . $templatePath . '.' . BenchmarkType::getCamelCaseName($benchmarkType) . '.twig',
            $componentPath . '/' . $templatePath . '.twig',
            'default/' . $templatePath . '.twig'
        ];

        $templateTwigPath = null;
        foreach ($templates as $template) {
            if (is_readable(Path::getBenchmarkKitPath() . '/templates/benchmark/' . $template) === true) {
                $templateTwigPath = $template;
                break;
            }
        }

        if ($templateTwigPath === null) {
            throw new \Exception('Benchmark template ' . $templatePath . ' not found.');
        }

        return $twig->render($templateTwigPath, $templateParameters);
    }

    protected function renderVhostTemplate(string $templatePath, array $templateParameters = []): string
    {
        static $twig;
        if ($twig instanceof Environment === false) {
            $twig = new Environment(new FilesystemLoader(__DIR__ . '/../../templates/vhost'));
        }

        if (is_readable(Path::getBenchmarkKitPath() . '/templates/vhost/' . $templatePath) === false) {
            throw new \Exception('Vhost template ' . $templatePath . ' not found.');
        }

        return $twig->render($templatePath, $templateParameters);
    }

    protected function writeFileFromTemplate(
        string $templatePath,
        array $templateParameters = [],
        int $componentType = null,
        int $benchmarkType = null
    ): self {
        $file = Path::getBenchmarkPath() . '/' . $templatePath;

        return $this
            ->createDirectory(dirname($file))
            ->filePutContent(
                $file,
                $this->renderBenchmarkTemplate($templatePath, $templateParameters, $componentType, $benchmarkType)
            );
    }

    protected function filePutContent(string $filename, string $content, bool $rmPathPrefix = true): self
    {
        $fileExists = file_exists($filename);
        (new Filesystem())->dumpFile($filename, $content);
        $this->outputSuccess(
            'File '
                . realpath(($rmPathPrefix ? Path::rmPrefix($filename) : $filename))
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
            $this->outputSuccess('Directory ' . Path::rmPrefix($directory) . ' created.');
        }

        return $this;
    }

    protected function removeDirectory(string $directory): self
    {
        if (is_dir($directory)) {
            (new Filesystem())->remove($directory);
            $this->outputSuccess('Directory ' . Path::rmPrefix($directory) . ' removed.');
        }

        return $this;
    }

    protected function removeFile(string $file, bool $rmPrefix = true): self
    {
        if (is_file($file)) {
            (new Filesystem())->remove($file);
            $this->outputSuccess('File ' . ($rmPrefix ? Path::rmPrefix($file) : $file) . ' removed.');
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
        return $this->processMustRun(
            new Process($commands, $cwd ?? Path::getBenchmarkPath(), null, null, $timeout),
            $outputVerbosity
        );
    }

    /** @return $this */
    protected function runProcessFromShellCommmandLine(
        string $shellCommandLine,
        int $outputVerbosity = OutputInterface::VERBOSITY_NORMAL,
        string $cwd = null,
        ?int $timeout = 60,
        string $error = null
    ): self {
        return $this->processMustRun(
            Process::fromShellCommandline($shellCommandLine, $cwd, null, null, $timeout),
            $outputVerbosity,
            $error
        );
    }

    /** @return $this */
    protected function processMustRun(
        Process $process,
        int $outputVerbosity = OutputInterface::VERBOSITY_NORMAL,
        string $error = null
    ): self {
        $processResult = $process->run(
            function (string $type, string $line) use ($outputVerbosity) {
                if ($this->getOutput()->getVerbosity() >= $outputVerbosity) {
                    $this->getOutput()->writeln($line);
                }
            }
        );

        if ($processResult !== 0) {
            if (is_string($error)) {
                throw new \Exception($error);
            } else {
                throw new ProcessFailedException($process);
            }
        }

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
            throw new \Exception(
                'File '
                    . Path::rmPrefix($filePath)
                    . ' does not exist. Use "phpbenchkit '
                    . $configureCommandName
                    . '" to create it.'
            );
        }

        return $this->outputSuccess('File ' . Path::rmPrefix($filePath) . ' exists.');
    }
}
