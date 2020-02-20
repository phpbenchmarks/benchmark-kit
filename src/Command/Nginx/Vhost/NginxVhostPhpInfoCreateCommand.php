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

final class NginxVhostPhpInfoCreateCommand extends AbstractCommand
{
    use OutputBlockTrait;
    use PhpVersionArgumentTrait;
    use ReloadNginxTrait;

    /** @var string */
    protected static $defaultName = 'nginx:vhost:phpInfo:create';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create nginx vhost ' . BenchmarkUrlService::PHPINFO_HOST)
            ->addPhpVersionArgument($this)
            ->addOption('no-url-output')
            ->addOption('no-nginx-reload');
    }

    protected function doExecute(): int
    {
        $this
            ->outputTitle('Create ' . BenchmarkUrlService::PHPINFO_HOST . ' virtual host')
            ->assertPhpVersionArgument($this)
            ->createVhostFile();
        if ($this->getInput()->getOption('no-nginx-reload') === false) {
            $this->reloadNginx($this);
        }

        $this->outputUrl();

        return 0;
    }

    private function createVhostFile(): self
    {
        return $this->filePutContent(
            '/etc/nginx/sites-enabled/phpinfo.benchmark-kit.loc.conf',
            $this->renderVhostTemplate(
                'vhost.conf.twig',
                [
                    'port' => BenchmarkUrlService::getNginxPort(),
                    'serverName' => BenchmarkUrlService::PHPINFO_HOST,
                    'root' => realpath(Path::getBenchmarkKitPath() . '/public'),
                    'entryPoint' => 'phpinfo.php',
                    'phpFpmSock' => 'php' . $this->getPhpVersionFromArgument($this)->toString() . '-fpm.sock'
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
                'View phpinfo() at this url: ' . BenchmarkUrlService::getPhpinfoUrl(),
                ''
            ],
            'green',
            $this->getOutput()
        );
    }
}
