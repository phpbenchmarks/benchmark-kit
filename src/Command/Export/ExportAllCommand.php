<?php

declare(strict_types=1);

namespace App\Command\Export;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkType,
    Component\ComponentType,
    Utils\Path
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};

final class ExportAllCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'export:all';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Export all configurations in JSON')
            ->addOption('pretty');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = JSON_UNESCAPED_SLASHES;
        if ($input->getOption('pretty')) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        $output->writeln(
            json_encode(
                [
                    'component' => [
                        'id' => Benchmark::getComponentId(),
                        'name' => Benchmark::getComponentName(),
                        'slug' => Benchmark::getComponentSlug(),
                        'type' => [
                            'id' => Benchmark::getBenchmarkType(),
                            'name' => ComponentType::getName(Benchmark::getBenchmarkType())
                        ]
                    ],
                    'benchmark' => [
                        'url' => Benchmark::getBenchmarkUrl(),
                        'type' => [
                            'id' => Benchmark::getBenchmarkType(),
                            'name' => BenchmarkType::getName(Benchmark::getBenchmarkType()),
                            'slug' => BenchmarkType::getSlug(Benchmark::getBenchmarkType())
                        ]
                    ],
                    'sourceCode' => [
                        'entryPoint' => Benchmark::getSourceCodeEntryPoint(),
                        'urls' => Benchmark::getSourceCodeUrls()->toArray()
                    ],
                    'coreDependency' => [
                        'name' => Benchmark::getCoreDependencyName(),
                        'majorVersion' => Benchmark::getCoreDependencyMajorVersion(),
                        'minorVersion' => Benchmark::getCoreDependencyMinorVersion(),
                        'patchVersion' => Benchmark::getCoreDependencyPatchVersion()
                    ],
                    'phpVersions' => [
                        $this->getPhpVersions(),
                    ],
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
                'configuration' => Path::rmPrefix(Path::getPhpIniPath($phpVersion)),
                'responseBody' => [
                    'size' => Benchmark::getResponseBodySize($phpVersion),
                    'files' => array_map(
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
