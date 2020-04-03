<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    Utils\Path
};

final class NginxVhostPreloadGeneratorCreateCommand extends AbstractNginxVhostCreateCommand
{
    /** @var string */
    protected static $defaultName = 'nginx:vhost:preload-generator:create';

    protected function getHost(): string
    {
        return BenchmarkUrlService::PRELOAD_GENERATOR_HOST;
    }

    protected function getContainerVhostFileName(): string
    {
        return 'preload-generator.benchmark-kit.loc.conf';
    }

    protected function getEntryPointPath(): string
    {
        return 'public/preload-generator.php';
    }

    protected function getInstallationPath(): string
    {
        return Path::getBenchmarkKitPath();
    }

    protected function getOutputUrlMessage(): string
    {
        return 'Generate preload files at this url: ' . BenchmarkUrlService::getPreloadGeneratorUrl();
    }

    protected function onVhostCreated(): self
    {
        return $this->filePutContent(
            Path::getBenchmarkKitPath() . '/public/preload-generator.php',
            $this->renderTemplate(
                'public/preload-generator.php.twig',
                [
                    'sourceCodePath' => Path::getSourceCodePath(),
                    'sourceCodeRelativePath' => '/../../../',
                    'entryPointPath' =>
                        realpath(Path::getSourceCodePath()) . '/' . Benchmark::getSourceCodeEntryPoint(),
                    'preloadFilePath' => Path::getPreloadPath(
                        $this->getPhpVersionFromArgument($this->getInput())
                    )
                ]
            ),
            false
        );
    }
}
