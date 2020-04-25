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
    Input\InputOption,
    Output\NullOutput,
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
    abstract protected function doExecute(): int;

    private ?InputInterface $input;

    private ?OutputInterface $output;

    private bool $skipSourceCodeUrls = false;

    public function skipSourceCodeUrls(): bool
    {
        return $this->skipSourceCodeUrls;
    }

    protected function configure(): void
    {
        $this
            ->addOption('source-code-path', null, InputOption::VALUE_REQUIRED, 'Source code path')
            ->addOption('skip-source-code-urls', null, null, 'Do not validate source code urls');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->skipSourceCodeUrls = $input->getOption('skip-source-code-urls');
        Path::setSourceCodePath($input->getOption('source-code-path'));

        try {
            $return = $this->doExecute();
        } catch (HiddenException $exception) {
            $return = 1;
        } catch (\Throwable $exception) {
            $this->outputError($exception->getMessage(), 'EXCEPTION');
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
    protected function outputError(string $error, string $title = 'ERROR'): self
    {
        $this->getOutput()->writeln("  \e[41m > \e[00m \e[41m $title \e[00m \e[31m" . $error . "\e[00m");

        return $this;
    }

    /** @param string[] $templateParameters */
    protected function renderTemplate(string $templatePath, array $templateParameters = []): string
    {
        static $twig;
        if ($twig instanceof Environment === false) {
            $twig = new Environment(new FilesystemLoader(__DIR__ . '/../../templates'));
        }

        return $twig->render($templatePath, $templateParameters);
    }

    /** @param string[] $templateParameters */
    protected function renderBenchmarkTemplate(
        string $templatePath,
        array $templateParameters = [],
        string $componentType = null,
        string $benchmarkType = null
    ): string {
        $componentPath = ComponentType::getCamelCaseName($componentType);
        $templates = [
            $componentPath . '/' . $templatePath . '.' . BenchmarkType::getCamelCaseName($benchmarkType) . '.twig',
            "$componentPath/$templatePath.twig",
            "default/$templatePath.twig"
        ];

        $templateTwigPath = null;
        foreach ($templates as $template) {
            if (is_readable(Path::getBenchmarkKitPath() . '/templates/benchmark/' . $template) === true) {
                $templateTwigPath = $template;
                break;
            }
        }

        if ($templateTwigPath === null) {
            throw new \Exception("Benchmark template $templatePath not found.");
        }

        return $this->renderTemplate("benchmark/$templateTwigPath", $templateParameters);
    }

    /** @param string[] $templateParameters */
    protected function writeFileFromTemplate(
        string $templatePath,
        array $templateParameters = [],
        string $componentType = null,
        string $benchmarkType = null
    ): self {
        $file = Path::getSourceCodePath() . '/' . $templatePath;

        return $this
            ->createDirectory(dirname($file))
            ->filePutContent(
                $file,
                $this->renderBenchmarkTemplate($templatePath, $templateParameters, $componentType, $benchmarkType)
            );
    }

    /** @return $this */
    protected function filePutContent(string $filename, string $content, bool $rmPathPrefix = true): self
    {
        $fileExists = file_exists($filename);
        (new Filesystem())->dumpFile($filename, $content);
        $this->outputSuccess(
            'File '
                . ($rmPathPrefix ? Path::rmPrefix($filename) : realpath($filename))
                . ' '
                . ($fileExists ? 'modified' : 'created')
                . '.'
        );

        return $this;
    }

    /** @return $this */
    protected function createDirectory(string $directory): self
    {
        if (is_dir($directory) === false) {
            (new Filesystem())->mkdir($directory);
            $this->outputSuccess('Directory ' . Path::rmPrefix($directory) . ' created.');
        }

        return $this;
    }

    /** @return $this */
    protected function removeDirectory(string $directory): self
    {
        if (is_dir($directory)) {
            (new Filesystem())->remove($directory);
            $this->outputSuccess('Directory ' . Path::rmPrefix($directory) . ' removed.');
        }

        return $this;
    }

    /** @return $this */
    protected function removeFile(string $file, bool $rmPrefix = true): self
    {
        if (is_file($file)) {
            (new Filesystem())->remove($file);
            $this->outputSuccess('File ' . ($rmPrefix ? Path::rmPrefix($file) : $file) . ' removed.');
        }

        return $this;
    }

    /**
     * @param string[] $commands
     * @return $this
     */
    protected function runProcess(
        array $commands,
        int $outputVerbosity = OutputInterface::VERBOSITY_NORMAL,
        string $cwd = null,
        ?int $timeout = 60,
        string $error = null,
        string $silencedError = null
    ): self {
        return $this->doRunProcess(
            new Process($commands, $cwd ?? Path::getSourceCodePath(), null, null, $timeout),
            $outputVerbosity,
            $error,
            $silencedError
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
        return $this->doRunProcess(
            Process::fromShellCommandline($shellCommandLine, $cwd, null, null, $timeout),
            $outputVerbosity,
            $error
        );
    }

    /** @return $this */
    protected function doRunProcess(
        Process $process,
        int $outputVerbosity = OutputInterface::VERBOSITY_NORMAL,
        string $error = null,
        string $silencedError = null
    ): self {
        $processResult = $process->run(
            function (string $type, string $line) use ($outputVerbosity): void {
                if ($this->getOutput()->getVerbosity() >= $outputVerbosity) {
                    $this->getOutput()->writeln($line);
                }
            }
        );

        $isSilencedError = is_int(strpos($process->getOutput(), $silencedError));
        if ($processResult !== 0 || $isSilencedError === true) {
            if ($isSilencedError === true && $this->getOutput()->getVerbosity() <= OutputInterface::VERBOSITY_NORMAL) {
                $this->getOutput()->writeln($process->getOutput());
            }

            if (is_string($error)) {
                throw new \Exception($error);
            } else {
                throw new ProcessFailedException($process);
            }
        }

        return $this;
    }

    /**
     * @param array<mixed> $arguments
     * @return $this
     */
    protected function runCommand(
        string $name,
        array $arguments = [],
        bool $showOutput = true,
        bool $hideException = true
    ): self {
        if ($this->skipSourceCodeUrls() === true) {
            $arguments['--skip-source-code-urls'] = true;
        }
        $arguments['--source-code-path'] = Path::getSourceCodePath();

        $returnCode = $this
            ->getApplication()
            ->find($name)
            ->run(new ArrayInput($arguments), $showOutput ? $this->output : new NullOutput());

        if ($returnCode > 0) {
            throw ($hideException ? new HiddenException() : new \Exception("Error while running $name."));
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

    /** @param array<mixed> $choices */
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

    /** @return $this */
    protected function assertFileNotExists(string $filePath): self
    {
        if (is_readable($filePath) === true) {
            throw new \Exception('File ' . Path::rmPrefix($filePath) . ' should not exist.');
        }

        return $this->outputSuccess('File ' . Path::rmPrefix($filePath) . ' does not exists.');
    }
}
