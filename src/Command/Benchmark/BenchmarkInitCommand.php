<?php

declare(strict_types=1);

namespace App\Command\Benchmark;

use App\{
    Command\AbstractCommand,
    Command\Nginx\Vhost\NginxVhostBenchmarkKitCreateCommand,
    Command\Nginx\Vhost\NginxVhostPhpInfoCreateCommand,
    Command\OutputBlockTrait,
    Command\PhpVersion\PhpVersionCliDefineCommand,
    Command\PhpVersionArgumentTrait,
    Utils\Path
};
use Symfony\Component\Console\Output\OutputInterface;

final class BenchmarkInitCommand extends AbstractCommand
{
    use OutputBlockTrait;
    use PhpVersionArgumentTrait;

    /** @var string */
    protected static $defaultName = 'benchmark:init';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Define PHP version and call initBenchmark.sh')
            ->addPhpVersionArgument($this);
    }

    protected function doExecute(): parent
    {
        $phpVersion = $this->getPhpVersionFromArgument($this);
        $initBenchmarkPath = Path::getInitBenchmarkPath($phpVersion);
        $composerLockFilePath = Path::getComposerLockPath($phpVersion);

        return $this
            ->assertPhpVersionArgument($this)
            ->runCommand(
                NginxVhostBenchmarkKitCreateCommand::getDefaultName(),
                ['phpVersion' => $phpVersion->toString(), '--no-url-output' => true]
            )
            ->runCommand(
                NginxVhostPhpInfoCreateCommand::getDefaultName(),
                ['phpVersion' => $phpVersion->toString(), '--no-url-output' => true]
            )
            ->runCommand(PhpVersionCliDefineCommand::getDefaultName(), ['phpVersion' => $phpVersion->toString()])
            ->outputTitle('Prepare composer.lock')
            ->runProcess(['cp', $composerLockFilePath, 'composer.lock'])
            ->outputSuccess(Path::rmPrefix($composerLockFilePath) . ' copied to composer.lock.')
            ->outputTitle('Call ' . Path::rmPrefix($initBenchmarkPath))
            ->runProcess([$initBenchmarkPath], OutputInterface::VERBOSITY_VERBOSE)
            ->outputSuccess(Path::rmPrefix($initBenchmarkPath) . ' called.')
            ->removeFile(Path::getBenchmarkPath() . '/composer.lock')
            ->outputUrls();
    }

    private function outputUrls(): self
    {
        $this->getOutput()->writeln('');

        return $this->outputBlock(
            [
                '',
                'You can test your code at this url: ' . NginxVhostBenchmarkKitCreateCommand::getUrl(),
                'View phpinfo() at this url: ' . NginxVhostPhpInfoCreateCommand::getUrl(),
                ''
            ],
            'green',
            $this->getOutput()
        );
    }
}
