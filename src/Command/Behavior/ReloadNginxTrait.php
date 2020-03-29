<?php

declare(strict_types=1);

namespace App\Command\Behavior;

use App\Command\AbstractCommand;
use Symfony\Component\Console\Output\OutputInterface;

trait ReloadNginxTrait
{
    protected function reloadNginx(AbstractCommand $command, bool $waitNginxIsUp = false): self
    {
        $command
            ->outputTitle('Reload nginx configuration')
            ->runProcess(['sudo', '/usr/sbin/service', 'nginx', 'reload'], OutputInterface::VERBOSITY_VERBOSE);
        if ($waitNginxIsUp === true) {
            sleep(1);
        }

        $this->outputSuccess('Nginx configuration reloaded.');

        return $this;
    }
}
