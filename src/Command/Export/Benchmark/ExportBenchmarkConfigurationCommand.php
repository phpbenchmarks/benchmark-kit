<?php

declare(strict_types=1);

namespace App\Command\Export\Benchmark;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkType,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Component\ComponentType,
    Utils\Path
};

final class ExportBenchmarkConfigurationCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'export:benchmark:configuration';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Export benchmark configuration in JSON')
            ->addOption('pretty');
    }

    protected function doExecute(): int
    {
        $options = JSON_UNESCAPED_SLASHES;
        if ($this->getInput()->getOption('pretty')) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        $this->getOutput()->writeln(
            json_encode(
                [
                    'component' => [
                        'id' => Benchmark::getComponentId(),
                        'name' => Benchmark::getComponentName(),
                        'slug' => Benchmark::getComponentSlug(),
                        'type' => [
                            'id' => Benchmark::getBenchmarkType(),
                            'name' => ComponentType::getName(Benchmark::getComponentType())
                        ]
                    ],
                    'benchmark' => [
                        'domain' => BenchmarkUrlService::HOST,
                        'port' => BenchmarkUrlService::getNginxPort(),
                        'urls' => [
                            'showResult' => BenchmarkUrlService::getUrl(true),
                            'hideResult' => BenchmarkUrlService::getUrl(false),
                            'relative' => Benchmark::getBenchmarkRelativeUrl()
                        ],
                        'type' => [
                            'id' => Benchmark::getBenchmarkType(),
                            'name' => BenchmarkType::getName(Benchmark::getBenchmarkType()),
                            'slug' => BenchmarkType::getSlug(Benchmark::getBenchmarkType())
                        ]
                    ],
                    'statistics' => [
                        'domain' => BenchmarkUrlService::STATISTICS_HOST,
                        'port' => BenchmarkUrlService::getNginxPort(),
                        'urls' => [
                            'showStatistics' => BenchmarkUrlService::getStatisticsUrl(true),
                            'hideStatistics' => BenchmarkUrlService::getStatisticsUrl(false)
                        ]
                    ],
                    'phpInfo' => [
                        'domain' => BenchmarkUrlService::PHPINFO_HOST,
                        'port' => BenchmarkUrlService::getNginxPort(),
                        'url' => BenchmarkUrlService::getPhpinfoUrl()
                    ],
                    'sourceCode' => [
                        'entryPoint' => Benchmark::getSourceCodeEntryPoint(),
                        'urls' => Benchmark::getSourceCodeUrls()->toArray()
                    ],
                    'coreDependency' => [
                        'name' => Benchmark::getCoreDependencyName(),
                        'version' => [
                            'name' => Benchmark::getCoreDependencyVersion(),
                            'major' => Benchmark::getCoreDependencyMajorVersion(),
                            'minor' => Benchmark::getCoreDependencyMinorVersion(),
                            'patch' => Benchmark::getCoreDependencyPatchVersion()
                        ]
                    ],
                    'phpVersions' => $this->getPhpVersions(),
                    'nginx' => [
                        'vhost' => Path::rmPrefix(Path::getVhostPath())
                    ],
                ],
                $options
            )
        );

        return 0;
    }

    private function getPhpVersions(): array
    {
        $return = [];

        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $return[$phpVersion->toString()] = [
                'composer' => [
                    'lock' => Path::rmPrefix(Path::getComposerLockPath($phpVersion))
                ],
                'initBenchmark' => Path::rmPrefix(Path::getInitBenchmarkPath($phpVersion)),
                'ini' => Path::rmPrefix(Path::getPhpIniPath($phpVersion)),
                'responseBody' => [
                    'hideResultSize' => Benchmark::getResponseBodySize($phpVersion, false),
                    'showResultSize' => Benchmark::getResponseBodySize($phpVersion, true),
                    'showResultFiles' => array_map(
                        function (string $responseBody) use ($phpVersion) {
                            return Path::rmPrefix(Path::getResponseBodyPath($phpVersion) . '/' . $responseBody);
                        },
                        BenchmarkType::getResponseBodyFiles(Benchmark::getBenchmarkType())
                    )
                ]
            ];
        }

        return $return;
    }
}
