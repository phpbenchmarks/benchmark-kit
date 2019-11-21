<?php

declare(strict_types=1);

namespace App\Command\Vhost;

use App\{
    Command\AbstractCommand,
    Command\GitRepositoryTrait,
    Command\PhpVersionArgumentTrait
};
use Symfony\Component\Console\Output\OutputInterface;

final class VhostCreateCommand extends AbstractCommand
{
    use GitRepositoryTrait;
    use PhpVersionArgumentTrait;

    public const HOST = 'benchmark-kit.loc';

    /** @var string */
    protected static $defaultName = 'vhost:create';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create nginx vhost ' . static::HOST)
            ->addPhpVersionArgument($this);
    }

    protected function doExecute(): AbstractCommand
    {
        return $this
            ->outputTitle('Create ' . static::HOST . ' virtual host')
            ->assertPhpVersionArgument($this)
            ->createVhostFile()
            ->defineVhostVariables()
            ->reloadNginx();
    }

    private function getContainerVhostFilePath(): string
    {
        return '/etc/nginx/sites-enabled/benchmark-kit.loc.conf';
    }

    private function createVhostFile(): self
    {
        $destination = $this->getContainerVhostFilePath();

        return $this
            ->runProcess(['cp', $this->getVhostFilePath(), $destination])
            ->outputSuccess('Create ' . $destination . '.');
    }

    private function defineVhostVariables(): self
    {
        $vhostFile = $this->getContainerVhostFilePath();
        $content = file_get_contents($vhostFile);
        if ($content === false) {
            $this->throwError('Error while reading ' . $vhostFile . '.');
        }

        $content = str_replace('____HOST____', static::HOST, $content);
        $content = str_replace('____INSTALLATION_PATH____', $this->getInstallationPath(), $content);
        $phpFpm = 'php' . $this->getPhpVersionFromArgument($this) . '-fpm.sock';
        $content = str_replace('____PHP_FPM_SOCK____', $phpFpm, $content);

        $writed = file_put_contents($vhostFile, $content);
        if ($writed === false) {
            $this->throwError('Error while writing ' . $vhostFile . '.');
        }

        return $this
            ->outputSuccess('____HOST____ replaced by ' . static::HOST . '.')
            ->outputSuccess('____INSTALLATION_PATH____ replaced by ' . $this->getInstallationPath() . '.')
            ->outputSuccess('____PHP_FPM_SOCK____ replaced by ' . $phpFpm . '.');
    }

    private function reloadNginx(): self
    {
        return $this
            ->outputTitle('Reload nginx configuration')
            ->runProcess(['sudo', '/usr/sbin/service', 'nginx', 'reload'], OutputInterface::VERBOSITY_VERBOSE)
            ->outputSuccess('Nginx configuration reloaded.');
    }
}
