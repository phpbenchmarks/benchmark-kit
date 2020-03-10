<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\OutputBlockTrait,
    Command\Behavior\PhpVersionArgumentTrait,
    Command\Behavior\ReloadNginxTrait,
    Utils\Path
};

final class NginxVhostStatisticsCreateCommand extends AbstractCommand
{
    use OutputBlockTrait;
    use PhpVersionArgumentTrait;
    use ReloadNginxTrait;

    /** @var string */
    protected static $defaultName = 'nginx:vhost:statistics:create';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create nginx vhost ' . BenchmarkUrlService::STATISTICS_HOST)
            ->addPhpVersionArgument($this)
            ->addOption('no-url-output')
            ->addOption('no-nginx-reload');
    }

    protected function doExecute(): int
    {
        $this
            ->outputTitle('Create ' . BenchmarkUrlService::STATISTICS_HOST . ' virtual host')
            ->assertPhpVersionArgument($this)
            ->createVhostFile()
            ->createPublicFile();
        if ($this->getInput()->getOption('no-nginx-reload') === false) {
            $this->reloadNginx($this);
        }

        $this->outputUrl();

        return 0;
    }

    private function createVhostFile(): self
    {
        return $this->filePutContent(
            '/etc/nginx/sites-enabled/statistics.benchmark-kit.loc.conf',
            $this->renderVhostTemplate(
                'vhost.conf.twig',
                [
                    'port' => BenchmarkUrlService::getNginxPort(),
                    'serverName' => BenchmarkUrlService::STATISTICS_HOST,
                    'root' => realpath(Path::getBenchmarkKitPath() . '/public'),
                    'entryPoint' => 'statistics.php',
                    'phpFpmSock' => 'php' . $this->getPhpVersionFromArgument($this)->toString() . '-fpm.sock'
                ]
            ),
            false
        );
    }

    private function createPublicFile(): self
    {
        return $this->filePutContent(
            Path::getBenchmarkKitPath() . '/public/statistics.php',
            $this->renderVhostTemplate(
                'statistics/statistics.php.twig',
                [
                    'entryPoint' => realpath(Path::getSourceCodePath()) . '/' . Benchmark::getSourceCodeEntryPoint(),
                    'statisticsPath' => Path::getStatisticsPath()
                ]
            ),
            false
        );
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
                'View statistics at this url: ' . BenchmarkUrlService::getStatisticsUrl(true),
                ''
            ],
            'green',
            $this->getOutput()
        );
    }
}
