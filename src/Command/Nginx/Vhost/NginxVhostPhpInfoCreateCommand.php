<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    Utils\Path
};

final class NginxVhostPhpInfoCreateCommand extends AbstractNginxVhostCreateCommand
{
    /** @var string */
    protected static $defaultName = 'nginx:vhost:phpinfo:create';

    protected function getHost(): string
    {
        return BenchmarkUrlService::PHPINFO_HOST;
    }

    protected function getContainerVhostFileName(): string
    {
        return 'phpinfo.benchmark-kit.loc.conf';
    }

    protected function getEntryPointPath(): string
    {
        return 'public/phpinfo.php';
    }

    protected function getInstallationPath(): string
    {
        return Path::getBenchmarkKitPath();
    }

    protected function getVhostTemplatePath(): string
    {
        return Path::getBenchmarkKitPath() . '/templates/vhost/phpinfo.conf';
    }

    protected function getOutputUrlMessage(): string
    {
        return 'View phpinfo() at this url: ' . BenchmarkUrlService::getPhpinfoUrl();
    }
}
