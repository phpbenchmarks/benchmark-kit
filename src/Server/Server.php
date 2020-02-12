<?php

declare(strict_types=1);

namespace App\Server;

use Symfony\Component\Process\Process;

class Server
{
    public static function getPhpVersion(bool $addRelease = true): string
    {
        $php = 'echo PHP_MAJOR_VERSION . \'.\' . PHP_MINOR_VERSION';
        if ($addRelease === true) {
            $php .= ' . \'.\' . PHP_RELEASE_VERSION';
        }
        $php .= ';';

        return (new Process(['php', '-r', $php]))
            ->mustRun()
            ->getOutput();
    }

    public static function getNginxAccessLogPath(): string
    {
        return '/var/log/nginx/access.log';
    }

    public static function getNginxErrorLogPath(): string
    {
        return '/var/log/nginx/error.log';
    }

    public static function getPhpFpmLogPath(string $phpVersion = null): string
    {
        return
            '/var/log/php'
            . ($phpVersion ?? static::getPhpVersion(false))
            . '-fpm.log';
    }
}
