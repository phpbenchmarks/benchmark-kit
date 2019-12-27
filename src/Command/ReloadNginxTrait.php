<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

trait ReloadNginxTrait
{
    protected function reloadNginx(AbstractCommand $command): self
    {
        $command
            ->outputTitle('Reload nginx configuration')
            ->runProcess(['sudo', '/usr/sbin/service', 'nginx', 'reload'], OutputInterface::VERBOSITY_VERBOSE)
            ->outputSuccess('Nginx configuration reloaded.');

        return $this;
    }
}
