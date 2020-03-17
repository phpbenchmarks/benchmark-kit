<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\OutputBlockTrait,
    Command\Behavior\PhpVersionArgumentTrait,
    Command\Behavior\ReloadNginxTrait,
    Utils\Path
};

final class NginxVhostBenchmarkKitCreateCommand extends AbstractCommand
{
    use OutputBlockTrait;
    use PhpVersionArgumentTrait;
    use ReloadNginxTrait;

    /** @var string */
    protected static $defaultName = 'nginx:vhost:benchmarkKit:create';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create nginx vhost ' . BenchmarkUrlService::HOST)
            ->addPhpVersionArgument($this)
            ->addOption('no-url-output')
            ->addOption('no-nginx-reload');
    }

    protected function doExecute(): int
    {
        $this
            ->outputTitle('Create ' . BenchmarkUrlService::HOST . ' virtual host')
            ->assertPhpVersionArgument($this)
            ->createVhostFile()
            ->defineVhostVariables();
        if ($this->getInput()->getOption('no-nginx-reload') === false) {
            $this->reloadNginx($this);
        }

        $this->outputUrl();

        return 0;
    }

    private function getContainerVhostFilePath(): string
    {
        return '/etc/nginx/sites-enabled/benchmark-kit.loc.conf';
    }

    private function createVhostFile(): self
    {
        $destination = $this->getContainerVhostFilePath();

        return $this
            ->runProcess(['cp', Path::getVhostPath(), $destination])
            ->outputSuccess('Create ' . $destination . '.');
    }

    private function defineVhostVariables(): self
    {
        $vhostFile = $this->getContainerVhostFilePath();
        $content = file_get_contents($vhostFile);
        if ($content === false) {
            throw new \Exception('Error while reading ' . $vhostFile . '.');
        }

        $content = str_replace('____PORT____', BenchmarkUrlService::getNginxPort(), $content);
        $content = str_replace('____HOST____', BenchmarkUrlService::HOST, $content);
        $content = str_replace('____INSTALLATION_PATH____', $this->getRealSourceCodePath(), $content);
        $phpFpm = 'php' . $this->getPhpVersionFromArgument($this)->toString() . '-fpm.sock';
        $content = str_replace('____PHP_FPM_SOCK____', $phpFpm, $content);

        $this->filePutContent($vhostFile, $content);

        return $this
            ->outputSuccess('____PORT____ replaced by ' . BenchmarkUrlService::getNginxPort() . '.')
            ->outputSuccess('____HOST____ replaced by ' . BenchmarkUrlService::HOST . '.')
            ->outputSuccess('____INSTALLATION_PATH____ replaced by ' . Path::getSourceCodePath() . '.')
            ->outputSuccess('____PHP_FPM_SOCK____ replaced by ' . $phpFpm . '.');
    }

    private function outputUrl(): self
    {
        if ($this->getInput()->getOption('no-url-output') === true) {
            return $this;
        }

        $this->getOutput()->writeln('');

        return $this->outputBlock(
            [
                '',
                'You can test your code at this url: ' . BenchmarkUrlService::getUrl(false),
                ''
            ],
            'green',
            $this->getOutput()
        );
    }

    private function getRealSourceCodePath(): string
    {
        $return = realpath(Path::getSourceCodePath());
        if ($return === false) {
            throw new \Exception('Source code path "' . Path::getSourceCodePath() . '" not found.');
        }

        return $return;
    }
}
