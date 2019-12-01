<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Command\Benchmark\BenchmarkInitCommand,
    Command\Nginx\Vhost\NginxVhostBenchmarkKitCreateCommand,
    Command\Validate\ValidateAllCommand,
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
    use OutputBlockTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isConfigurationValid = $this->isConfigurationValid();
        $backgroundColor = $isConfigurationValid ? 'cyan' : 'yellow';

        $this->outputBlock(
            [
                '',
                'Welcome to http://www.phpbenchmarks.com benchmark kit ' . Version::getVersion() . '.',
                '',
                'Host source code path: ' . getenv('HOST_SOURCE_CODE_PATH') . '.'
            ],
            $backgroundColor,
            $output
        );

        if ($isConfigurationValid === true) {
            $this->outputBlock(
                [
                    'Current PHP version: ' . $this->getBenchmarkPhpVersion() . '.',
                    'Use "phpbenchkit ' . BenchmarkInitCommand::getDefaultName() . ' X.Y" to change it.',
                    'Go to ' . $this->getBenchmarkUrl() . ' to execute your code.'
                ],
                $backgroundColor,
                $output
            );
        } else {
            $this->outputBlock(
                [
                    'Benchmark need to be configured before doing anything.',
                    'Call "phpbenchkit configure:all" to configure it.'
                ],
                $backgroundColor,
                $output
            );
        }

        $this->outputBlock([''], $backgroundColor, $output);

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

    private function getBenchmarkUrl(): string
    {
        try {
            $benchmarkUrl = ComponentConfiguration::getBenchmarkUrl();
        } catch (\Throwable $throwable) {
            $benchmarkUrl = null;
        }

        return 'http://' . NginxVhostBenchmarkKitCreateCommand::HOST . ':' . getenv('NGINX_PORT') . $benchmarkUrl;
    }
}
