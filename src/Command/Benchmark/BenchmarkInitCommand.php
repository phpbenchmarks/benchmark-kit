<?php

declare(strict_types=1);

namespace App\Command\Benchmark;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Nginx\Vhost\NginxVhostBenchmarkKitCreateCommand,
    Command\Nginx\Vhost\NginxVhostPhpInfoCreateCommand,
    Command\OutputBlockTrait,
    Command\PhpFpm\PhpFpmRestartCommand,
    Command\PhpVersion\PhpVersionCliDefineCommand,
    Command\PhpVersionArgumentTrait,
    PhpVersion\PhpVersion,
    Utils\Path
};
use Symfony\Component\Console\{
    Input\InputOption,
    Output\OutputInterface
};

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
            ->addPhpVersionArgument($this)
            ->addOption('no-url-output')
            ->addOption('opcache-enabled', null, InputOption::VALUE_OPTIONAL);
    }

    protected function doExecute(): parent
    {
        $phpVersion = $this->getPhpVersionFromArgument($this);
        $this->assertPhpVersionArgument($this);

        $initBenchmarkPath = Path::getInitBenchmarkPath($phpVersion);
        $composerLockFilePath = Path::getComposerLockPath($phpVersion);

        return $this
            ->configureOpcache($phpVersion)
            ->runCommand(
                NginxVhostBenchmarkKitCreateCommand::getDefaultName(),
                ['phpVersion' => $phpVersion->toString(), '--no-url-output' => true]
            )
            ->runCommand(
                NginxVhostPhpInfoCreateCommand::getDefaultName(),
                ['phpVersion' => $phpVersion->toString(), '--no-url-output' => true]
            )
            ->runCommand(PhpVersionCliDefineCommand::getDefaultName(), ['phpVersion' => $phpVersion->toString()])
            ->runCommand(PhpFpmRestartCommand::getDefaultName(), ['phpVersion' => $phpVersion->toString()])
            ->outputTitle('Prepare composer.lock')
            ->runProcess(['cp', $composerLockFilePath, 'composer.lock'])
            ->outputSuccess(Path::rmPrefix($composerLockFilePath) . ' copied to composer.lock.')
            ->outputTitle('Call ' . Path::rmPrefix($initBenchmarkPath))
            ->runProcess([$initBenchmarkPath], OutputInterface::VERBOSITY_VERBOSE)
            ->outputSuccess(Path::rmPrefix($initBenchmarkPath) . ' called.')
            ->removeFile(Path::getBenchmarkPath() . '/composer.lock')
            ->outputUrls();
    }

    private function configureOpcache(PhpVersion $phpVersion): self
    {
        $this->outputTitle('Configure opcache');
        $opcacheEnabled = $this->getInput()->getOption('opcache-enabled');
        if ($opcacheEnabled === null) {
            $opcacheEnabled = $this->askConfirmationQuestion('Enable opcache?');
        } elseif (is_string($opcacheEnabled)) {
            $opcacheEnabled = $opcacheEnabled === 'true';
        }

        if ($opcacheEnabled) {
            $this->removeFile(Path::getOpcacheDisableIniPath($phpVersion), false);
        } else {
            $this->filePutContent(Path::getOpcacheDisableIniPath($phpVersion), 'opcache.enable=Off', false);
        }

        return $this;
    }

    private function outputUrls(): self
    {
        if ($this->getInput()->getOption('no-url-output') === true) {
            return $this;
        }

        $this->getOutput()->writeln('');

        return $this->outputBlock(
            [
                '',
                'You can test your code at this url: ' . BenchmarkUrlService::getUrl(false),
                'View phpinfo() at this url: ' . NginxVhostPhpInfoCreateCommand::getUrl(),
                ''
            ],
            'green',
            $this->getOutput()
        );
    }
}
