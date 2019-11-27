<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Command\Benchmark\BenchmarkInitCommand,
    Command\Validate\ValidateAllCommand,
    Command\Vhost\VhostCreateCommand,
    ComponentConfiguration\ComponentConfiguration,
    Version
};
use Symfony\Component\Console\{
    Command\ListCommand,
    Input\ArrayInput,
    Input\InputInterface,
    Output\NullOutput,
    Output\OutputInterface
};
use Symfony\Component\Process\Process;

final class DefaultCommand extends ListCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isConfigurationValid = $this->isConfigurationValid();

        $this->writeHeaderLines(
            [
                '',
                'Welcome to http://www.phpbenchmarks.com benchmark kit ' . Version::getVersion() . '.',
                '',
                'Host source code path: ' . getenv('HOST_SOURCE_CODE_PATH') . '.'
            ],
            $output,
            $isConfigurationValid
        );

        if ($isConfigurationValid === true) {
            $this->writeHeaderLines(
                [
                    'Current PHP version: ' . $this->getBenchmarkPhpVersion() . '.',
                    'Use "phpbenchkit ' . BenchmarkInitCommand::getDefaultName() . ' X.Y" to change it.',
                    'Go to ' . $this->getBenchmarkUrl() . ' to execute your code.'
                ],
                $output,
                $isConfigurationValid
            );
        } else {
            $this->writeHeaderLines(
                [
                    'Benchmark need to be configured before doing anything.',
                    'Call "phpbenchkit configure:all" to configure it.'
                ],
                $output,
                $isConfigurationValid
            );
        }

        $this->writeHeaderLines([''], $output, $isConfigurationValid);

        $output->writeln('');

        parent::execute($input, $output);

        return 0;
    }

    private function isConfigurationValid(): bool
    {
        return $this
            ->getApplication()
            ->find(ValidateAllCommand::getDefaultName())
            ->run(new ArrayInput(['--skip-source-code-urls' => true]), new NullOutput()) === 0;
    }

    private function getBenchmarkPhpVersion(): string
    {
        return
            (
                new Process(
                    ['php', '-r', 'echo PHP_MAJOR_VERSION . \'.\' . PHP_MINOR_VERSION . \'.\' . PHP_RELEASE_VERSION;']
                )
            )
            ->mustRun()
            ->getOutput();
    }

    private function writeHeaderLines(array $lines, OutputInterface $output, bool $isConfigurationValid): self
    {
        foreach ($lines as $line) {
            $output->writeln(
                '<fg=black;bg=' . ($isConfigurationValid ? 'cyan' : 'yellow') . '>  ' . str_pad($line, 115) . '</>'
            );
        }

        return $this;
    }

    private function getBenchmarkUrl(): string
    {
        try {
            $benchmarkUrl = ComponentConfiguration::getBenchmarkUrl();
        } catch (\Throwable $throwable) {
            $benchmarkUrl = null;
        }

        return 'http://' . VhostCreateCommand::HOST . ':' . getenv('NGINX_PORT') . $benchmarkUrl;
    }
}
