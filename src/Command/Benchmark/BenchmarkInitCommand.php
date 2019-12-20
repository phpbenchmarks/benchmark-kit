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

    private string $opcachePreloadUser;

    public function __construct(string $opcachePreloadUser)
    {
        parent::__construct();

        $this->opcachePreloadUser = $opcachePreloadUser;
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Define PHP version and call initBenchmark.sh')
            ->addPhpVersionArgument($this)
            ->addOption('no-url-output')
            ->addOption('opcache-enabled', null, InputOption::VALUE_OPTIONAL)
            ->addOption('preload-enabled', null, InputOption::VALUE_OPTIONAL);
    }

    protected function doExecute(): parent
    {
        $phpVersion = $this->getPhpVersionFromArgument($this);
        $this->assertPhpVersionArgument($this);

        $initBenchmarkPath = Path::getInitBenchmarkPath($phpVersion);
        $composerLockFilePath = Path::getComposerLockPath($phpVersion);

        $opcacheEnabled = $this->configureOpcache($phpVersion);

        return $this
            ->configurePreload($phpVersion, $opcacheEnabled)
            ->configurePhpIni($phpVersion)
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

    private function configurePhpIni(PhpVersion $phpVersion): self
    {
        return $this->filePutContent(
            $this->getPhpConfPath($phpVersion) . '/97-benchmark.ini',
            file_get_contents(Path::getPhpIniPath($phpVersion))
        );
    }

    private function configureOpcache(PhpVersion $phpVersion): bool
    {
        $this->outputTitle('Configure opcache');
        $opcacheEnabled = $this->getInput()->getOption('opcache-enabled');
        if ($opcacheEnabled === null) {
            $opcacheEnabled = $this->askConfirmationQuestion('Enable opcache?');
        } elseif (is_string($opcacheEnabled)) {
            $opcacheEnabled = $opcacheEnabled === 'true';
        }

        $opCacheIniPath = $this->getPhpConfPath($phpVersion) . '/99-disable-opcache.ini';
        if ($opcacheEnabled) {
            $this->removeFile($opCacheIniPath, false);
        } else {
            $this->filePutContent($opCacheIniPath, 'opcache.enable=Off', false);
        }

        return $opcacheEnabled;
    }

    private function configurePreload(PhpVersion $phpVersion, bool $opcacheEnabled): self
    {
        $preloadIniPath = $this->getPhpConfPath($phpVersion) . '/98-preload.ini';

        if ($opcacheEnabled === true && $phpVersion->isPreloadAvailable() === true) {
            $this->outputTitle('Configure preload');
            $preloadEnabled = $this->getInput()->getOption('preload-enabled');
            if ($preloadEnabled === null) {
                $preloadEnabled = $this->askConfirmationQuestion('Enable preload?');
            } elseif (is_string($preloadEnabled)) {
                $preloadEnabled = $preloadEnabled === 'true';
            }

            if ($preloadEnabled) {
                $this->filePutContent(
                    $preloadIniPath,
                    'opcache.preload='
                        . Path::getPreloadPath($phpVersion)
                        . "\n"
                        . 'opcache.preload_user='
                        . $this->opcachePreloadUser,
                    false
                );
            } else {
                $this->removeFile($preloadIniPath, false);
            }
        } else {
            $this->removeFile($preloadIniPath, false);
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
                'You can test your code at this url: ' . BenchmarkUrlService::getUrlWithPort(false),
                'View phpinfo() at this url: ' . NginxVhostPhpInfoCreateCommand::getUrl(),
                ''
            ],
            'green',
            $this->getOutput()
        );
    }

    private function getPhpConfPath(PhpVersion $phpVersion): string
    {
        return '/etc/php/' . $phpVersion->toString() . '/fpm/conf.d/';
    }
}
