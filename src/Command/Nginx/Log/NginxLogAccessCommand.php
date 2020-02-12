<?php

declare(strict_types=1);

namespace App\Command\Nginx\Log;

use App\{
    Command\AbstractLogCommand,
    Server\Server
};

final class NginxLogAccessCommand extends AbstractLogCommand
{
    /** @var string */
    protected static $defaultName = 'nginx:log:access';

    protected function getLogPath(): string
    {
        return Server::getNginxAccessLogPath();
    }
}
