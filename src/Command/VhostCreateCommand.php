<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Command\Validate\AbstractComposerFilesCommand,
    PhpVersion\PhpVersion
};

final class VhostCreateCommand extends AbstractComposerFilesCommand
{
    /** @var string */
    protected static $defaultName = 'vhost:create';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create nginx vhosts phpXY.benchmark.loc');
    }

    protected function doExecute(): AbstractCommand
    {
        foreach (PhpVersion::getAll() as $phpVersion) {
            $this->outputTitle('Create ' . $this->getHost($phpVersion) . ' virtual host');
            $phpVersionWithoutDot = str_replace('.', null, $phpVersion);

            $this
                ->createVhostFile($phpVersionWithoutDot)
                ->defineVhostVariables($phpVersion, $phpVersionWithoutDot);
        }

        $this->reloadNginx();

        return $this;
    }

    private function getPhpVersionVhostFilePath(string $phpVersionWithoutDot): string
    {
        return '/etc/nginx/sites-enabled/php' . $phpVersionWithoutDot . '.benchmark.loc.conf';
    }

    private function createVhostFile(string $phpVersionWithoutDot): self
    {
        $source = $this->getVhostFilePath();
        $destination = $this->getPhpVersionVhostFilePath($phpVersionWithoutDot);
        $copied = copy($source, $destination);
        if ($copied === false) {
            $this->throwError('Error while copying ' . $source . ' to ' . $destination . '.');
        }
        $this->outputSuccess('Create ' . $destination . '.');

        return $this;
    }

    private function defineVhostVariables(string $phpVersion, string $phpVersionWithoutDot): self
    {
        $vhostFile = $this->getPhpVersionVhostFilePath($phpVersionWithoutDot);
        $content = file_get_contents($vhostFile);
        if ($content === false) {
            $this->throwError('Error while reading ' . $vhostFile . '.');
        }

        $host = $this->getHost($phpVersion, false);
        $content = str_replace('____HOST____', $host, $content);
        $content = str_replace('____INSTALLATION_PATH____', $this->getInstallationPath(), $content);
        $phpFpm = 'php' . $phpVersion . '-fpm.sock';
        $content = str_replace('____PHP_FPM_SOCK____', $phpFpm, $content);

        $writed = file_put_contents($vhostFile, $content);
        if ($writed === false) {
            $this->throwError('Error while writing ' . $vhostFile . '.');
        }

        $this
            ->outputSuccess('____HOST____ replaced by ' . $host . '.')
            ->outputSuccess('____INSTALLATION_PATH____ replaced by ' . $this->getInstallationPath() . '.')
            ->outputSuccess('____PHP_FPM_SOCK____ replaced by ' . $phpFpm . '.');

        return $this;
    }

    private function reloadNginx(): self
    {
        $this
            ->outputTitle('Reload nginx configuration')
            ->exec('sudo /usr/sbin/service nginx reload')
            ->outputSuccess('Nginx configuration reloaded.');

        return $this;
    }
}
