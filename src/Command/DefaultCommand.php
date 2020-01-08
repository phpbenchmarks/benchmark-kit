<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\Benchmark\BenchmarkInitCommand,
    Command\Nginx\Vhost\NginxVhostPhpInfoCreateCommand,
    Command\Validate\ValidateAllCommand,
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
                'Host source code path: ' . $_ENV['HOST_SOURCE_CODE_PATH'] . '.'
            ],
            $backgroundColor,
            $output
        );

        if ($isConfigurationValid === true) {
            $lines = [
                'Current PHP version: ' . $this->getBenchmarkPhpVersion() . '.',
                'Use "phpbenchkit ' . BenchmarkInitCommand::getDefaultName() . ' X.Y" to change it.'
            ];
            try {
                $lines[] = 'Go to ' . BenchmarkUrlService::getPhpinfoUrl() . ' to view phpinfo().';
                $lines[] = 'Go to ' . BenchmarkUrlService::getStatisticsUrl(true) . ' to view statistics.';
                $lines[] = 'Go to ' . BenchmarkUrlService::getUrl(false) . ' to execute your code.';
            } catch (\Throwable $exception) {
                // Don't add url when impossible
            }

            $this->outputBlock($lines, $backgroundColor, $output);
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
}
