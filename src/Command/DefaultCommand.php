<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\Behavior\OutputBlockTrait,
    Command\Benchmark\BenchmarkInitCommand,
    Command\Composer\ComposerUpdateCommand,
    Command\Configure\ConfigureBenchmarkCommand,
    Command\Configure\ConfigureSourceCodeUrlsCommand,
    Command\Nginx\Log\NginxLogErrorCommand,
    Command\Php\Fpm\PhpFpmLogCommand,
    Command\Validate\Benchmark\ValidateBenchmarkCommand,
    Command\Validate\Benchmark\ValidateBenchmarkResponseCommand,
    Command\Validate\Configuration\ValidateConfigurationSourceCodeUrlsCommand,
    Command\Validate\Configuration\ValidateConfigurationCommand,
    Server\Server,
    Utils\Path,
    Version
};
use Huttopia\ConsoleBundle\Command\ListCommand;
use Symfony\Component\Console\{
    Input\ArrayInput,
    Input\InputInterface,
    Input\InputOption,
    Output\NullOutput,
    Output\OutputInterface
};

final class DefaultCommand extends ListCommand
{
    use OutputBlockTrait;

    public function __construct()
    {
        parent::__construct(
            OutputInterface::VERBOSITY_VERBOSE,
            OutputInterface::VERBOSITY_VERBOSE,
            OutputInterface::VERBOSITY_VERBOSE,
            0,
            [],
            [],
            [
                BenchmarkInitCommand::getDefaultName(),
                ValidateBenchmarkCommand::getDefaultName(),
                ValidateBenchmarkResponseCommand::getDefaultName(),
                ComposerUpdateCommand::getDefaultName(),
                ConfigureBenchmarkCommand::getDefaultName(),
                ConfigureSourceCodeUrlsCommand::getDefaultName(),
                NginxLogErrorCommand::getDefaultName(),
                PhpFpmLogCommand::getDefaultName(),
                ValidateConfigurationCommand::getDefaultName(),
                ValidateConfigurationSourceCodeUrlsCommand::getDefaultName()
            ]
        );
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('source-code-path', null, InputOption::VALUE_REQUIRED, 'Source code path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Path::setSourceCodePath($input->getOption('source-code-path'));

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
                'Current PHP version: ' . Server::getPhpVersion() . '.',
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
                    'Call "phpbenchkit configure:benchmark" to configure it.'
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
            ->find(ValidateConfigurationCommand::getDefaultName())
            ->run(
                new ArrayInput(
                    [
                        '--skip-source-code-urls' => true,
                        '--source-code-path' => Path::getSourceCodePath()
                    ]
                ),
                new NullOutput()
            ) === 0;
    }
}
