<?php

declare(strict_types=1);

namespace App\Command\Php\Fpm;

use App\{
    Command\AbstractLogCommand,
    Server\Server
};

final class PhpFpmLogCommand extends AbstractLogCommand
{
    /** @var string */
    protected static $defaultName = 'php:fpm:log';

    protected function getLogPath(): string
    {
        return Server::getPhpFpmLogPath();
    }
}
