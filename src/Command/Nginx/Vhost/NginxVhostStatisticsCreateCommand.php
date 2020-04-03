<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    Utils\Path
};

final class NginxVhostStatisticsCreateCommand extends AbstractNginxVhostCreateCommand
{
    /** @var string */
    protected static $defaultName = 'nginx:vhost:statistics:create';

    protected function getHost(): string
    {
        return BenchmarkUrlService::STATISTICS_HOST;
    }

    protected function getContainerVhostFileName(): string
    {
        return 'statistics.benchmark-kit.loc.conf';
    }

    protected function getEntryPointPath(): string
    {
        return 'public/statistics.php';
    }

    protected function getInstallationPath(): string
    {
        return Path::getBenchmarkKitPath();
    }

    protected function getOutputUrlMessage(): string
    {
        return 'View statistics at this url: ' . BenchmarkUrlService::getStatisticsUrl(true);
    }

    protected function onVhostCreated(): self
    {
        return $this->filePutContent(
            Path::getBenchmarkKitPath() . '/public/statistics.php',
            $this->renderTemplate(
                'public/statistics.php.twig',
                [
                    'entryPointPath' =>
                        realpath(Path::getSourceCodePath()) . '/' . Benchmark::getSourceCodeEntryPoint(),
                    'statisticsPath' => Path::getStatisticsPath()
                ]
            ),
            false
        );
    }
}
