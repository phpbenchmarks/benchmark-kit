<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\{
    Command\AbstractCommand,
    Command\OutputBlockTrait,
    Command\PhpVersionArgumentTrait,
    Command\ReloadNginxTrait,
    Utils\Path
};

final class NginxVhostPhpInfoCreateCommand extends AbstractCommand
{
    use OutputBlockTrait;
    use PhpVersionArgumentTrait;
    use ReloadNginxTrait;

    protected const HOST = 'phpinfo.benchmark-kit.loc';

    /** @var string */
    protected static $defaultName = 'nginx:vhost:phpInfo:create';

    public static function getUrl(): string
    {
        return
            'http://'
            . static::HOST
            . ':'
            . $_ENV['NGINX_PORT'];
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create nginx vhost ' . static::HOST)
            ->addPhpVersionArgument($this)
            ->addOption('no-url-output')
            ->addOption('no-nginx-reload');
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->outputTitle('Create ' . static::HOST . ' virtual host')
            ->assertPhpVersionArgument($this)
            ->createVhostFile();
        if ($this->getInput()->getOption('no-nginx-reload') === false) {
            $this->reloadNginx($this);
        }

        return $this->outputUrl();
    }

    private function createVhostFile(): self
    {
        return $this->filePutContent(
            '/etc/nginx/sites-enabled/phpinfo.benchmark-kit.loc.conf',
            $this->renderVhostTemplate(
                'vhost.conf.twig',
                [
                    'port' => $_ENV['NGINX_PORT'],
                    'serverName' => static::HOST,
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
                'View phpinfo() at this url: ' . static::getUrl(),
                ''
            ],
            'green',
            $this->getOutput()
        );
    }
}
