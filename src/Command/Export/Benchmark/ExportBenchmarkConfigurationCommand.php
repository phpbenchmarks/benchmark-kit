<?php

declare(strict_types=1);

namespace App\Command\Export\Benchmark;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkType,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\GetBodyFromUrl,
    Component\ComponentType,
    Utils\Path
};
use Symfony\Component\Console\{
    Input\InputOption,
    Output\OutputInterface
};
use Symfony\Component\Filesystem\Filesystem;

final class ExportBenchmarkConfigurationCommand extends AbstractCommand
{
    use GetBodyFromUrl;

    public const OPTION_GITHUB_REPOSITORY_NAME = 'github-repository-name';
    public const OPTION_GITHUB_REPOSITORY_GIT_REF = 'github-repository-git-ref';

    /** @var string */
    protected static $defaultName = 'export:benchmark:configuration';

    private string $clonedRepositoryDir;

    public function __construct(string $varDir)
    {
        parent::__construct();

        $this->clonedRepositoryDir = $varDir . '/export-benchmark-configuration';
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Export benchmark configuration in JSON')
            ->addOption('pretty', 'p', InputOption::VALUE_NONE, 'Add JSON_PRETTY_PRINT to json_encode()')
            ->addOption(
                static::OPTION_GITHUB_REPOSITORY_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'GitHub repository name to download source code'
            )
            ->addOption(
                static::OPTION_GITHUB_REPOSITORY_GIT_REF,
                null,
                InputOption::VALUE_REQUIRED,
                'GitRef to download source code'
            );
    }

    protected function doExecute(): int
    {
        $gitHubRepositoryName = $this->getInput()->getOption(static::OPTION_GITHUB_REPOSITORY_NAME);
        if (is_string($gitHubRepositoryName) === true) {
            $gitHubRepositoryGitRef = $this->getInput()->getOption(static::OPTION_GITHUB_REPOSITORY_GIT_REF);
            if (is_string($gitHubRepositoryGitRef) === false) {
                throw new \Exception('You must pass --' . static::OPTION_GITHUB_REPOSITORY_GIT_REF . ' option.');
            }
            $cloneDir = $this->cloneRepository($gitHubRepositoryName, $gitHubRepositoryGitRef);
            Path::setSourceCodePath($cloneDir);
            Benchmark::reload();
        }

        $options = JSON_UNESCAPED_SLASHES;
        if ($this->getInput()->getOption('pretty')) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        $this->getOutput()->writeln(
            json_encode(
                [
                    'component' => [
                        'slug' => Benchmark::getComponentSlug(),
                        'name' => Benchmark::getComponentName(),
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

        $this->removeClonedRepository();

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

    private function cloneRepository(string $name, string $gitRef): string
    {
        $this->removeClonedRepository();

        $this->runProcess(
            [
                'git',
                'clone',
                '--single-branch',
                "--branch=$gitRef",
                "https://github.com/phpbenchmarks/$name.git",
                $this->clonedRepositoryDir
            ],
            OutputInterface::VERBOSITY_VERBOSE
        );

        return $this->clonedRepositoryDir;
    }

    private function removeClonedRepository(): self
    {
        if (is_dir($this->clonedRepositoryDir) === true) {
            (new Filesystem())->remove($this->clonedRepositoryDir);
        }

        return $this;
    }
}
