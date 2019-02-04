<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Command\Validate\AbstractComposerFilesCommand,
    PhpVersion\PhpVersion
};

class VhostCreateCommand extends AbstractComposerFilesCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('vhost:create')
            ->setDescription('Create nginx vhosts phpXY.benchmark.loc');
    }

    protected function doExecute(): parent
    {
        foreach (PhpVersion::getAll() as $phpVersion) {
            $this->title('Create ' . $this->getHost($phpVersion) . ' virtual host');
            $phpVersionWithoutDot = str_replace('.', null, $phpVersion);

            $this
                ->createVhostFile($phpVersionWithoutDot)
                ->defineVhostVariables($phpVersion, $phpVersionWithoutDot);
        }

        $this->nginxReload();

        return $this;
    }

    protected function getVhostFilePath(string $phpVersionWithoutDot): string
    {
        return '/etc/nginx/sites-enabled/php' . $phpVersionWithoutDot . '.benchmark.loc.conf';
    }

    protected function createVhostFile(string $phpVersionWithoutDot): self
    {
        $source = '/var/www/phpbenchmarks/.phpbenchmarks/vhost.conf';
        $destination = $this->getVhostFilePath($phpVersionWithoutDot);
        $copied = copy($source, $destination);
        if ($copied === false) {
            $this->error('Error while copying ' . $source . ' to ' . $destination . '.');
        }
        $this->success('Create ' . $destination . '.');

        return $this;
    }

    protected function defineVhostVariables(string $phpVersion, string $phpVersionWithoutDot): self
    {
        $vhostFile = $this->getVhostFilePath($phpVersionWithoutDot);
        $content = file_get_contents($vhostFile);
        if ($content === false) {
            $this->error('Error while reading ' . $vhostFile . '.');
        }

        $host = $this->getHost($phpVersion, false);
        $content = str_replace('____HOST____', $host, $content);
        $content = str_replace('____INSTALLATION_PATH____', $this->getInstallationPath(), $content);
        $phpFpm = 'php' . $phpVersion . '-fpm.sock';
        $content = str_replace('____PHP_FPM_SOCK____', $phpFpm, $content);

        $writed = file_put_contents($vhostFile, $content);
        if ($writed === false) {
            $this->error('Error while writing ' . $vhostFile . '.');
        }

        $this
            ->success('____HOST____ replaced by ' . $host . '.')
            ->success('____INSTALLATION_PATH____ replaced by ' . $this->getInstallationPath() . '.')
            ->success('____PHP_FPM_SOCK____ replaced by ' . $phpFpm . '.');

        return $this;
    }

    protected function nginxReload(): self
    {
        $this
            ->title('Reload nginx configuration')
            ->exec('sudo /usr/sbin/service nginx reload')
            ->success('Nginx configuration reloaded.');

        return $this;
    }
}
