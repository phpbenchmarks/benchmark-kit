<?php

declare(strict_types=1);

namespace App\Nginx;

class NginxService
{
    public static function getVhostFilePath(): string
    {
        return '.phpbenchmarks/nginx/vhost.conf';
    }
}
