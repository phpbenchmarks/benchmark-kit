<?php

declare(strict_types=1);

namespace App\Benchmark;

class BenchmarkUrlService
{
    public const HOST = 'benchmark-kit.loc';
    public const STATISTICS_HOST = 'statistics.benchmark-kit.loc';
    public const PHPINFO_HOST = 'phpinfo.benchmark-kit.loc';
    public const PRELOAD_GENERATOR_HOST = 'preload-generator.benchmark-kit.loc';

    public static function getUrl(bool $showResult): string
    {
        $url = 'http://' . static::HOST . ':' . static::getNginxPort() . Benchmark::getBenchmarkRelativeUrl();

        if ($showResult === true) {
            $url = static::appendToQueryString($url, 'phpBenchmarksShowResults=1');
        }

        return $url;
    }

    public static function getStatisticsUrl(bool $showStatistics): string
    {
        $url =
            'http://'
            . static::STATISTICS_HOST
            .
            ':'
            . static::getNginxPort()
            . Benchmark::getBenchmarkRelativeUrl();

        if ($showStatistics === true) {
            $url = static::appendToQueryString($url, 'phpBenchmarksShowStatistics=true');
        }

        return $url;
    }

    public static function getPhpinfoUrl(): string
    {
        return 'http://' . static::PHPINFO_HOST . ':' . static::getNginxPort();
    }

    public static function getPreloadGeneratorUrl(): string
    {
        return
            'http://'
            . static::PRELOAD_GENERATOR_HOST
            . ':'
            . static::getNginxPort()
            . Benchmark::getBenchmarkRelativeUrl();
    }

    public static function getNginxPort(): int
    {
        return (int) $_ENV['NGINX_PORT'];
    }

    protected static function appendToQueryString(string $url, string $queryStringParameter): string
    {
        $return = $url . (strpos($url, '?') === false ? '?' : '&');
        $return .= $queryStringParameter;

        return $return;
    }
}
