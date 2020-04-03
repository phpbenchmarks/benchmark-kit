<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\Benchmark\BenchmarkUrlService;

final class NginxVhostBenchmarkKitCreateCommand extends AbstractNginxVhostCreateCommand
{
    /** @var string */
    protected static $defaultName = 'nginx:vhost:benchmark-kit:create';

    protected function getHost(): string
    {
        return BenchmarkUrlService::HOST;
    }

    protected function getContainerVhostFileName(): string
    {
        return 'benchmark-kit.loc.conf';
    }

    protected function getOutputUrlMessage(): string
    {
        return 'You can test your code at this url: ' . BenchmarkUrlService::getUrl(false);
    }
}
