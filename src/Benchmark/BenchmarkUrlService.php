<?php

declare(strict_types=1);

namespace App\Benchmark;

use App\Component\ComponentType;

class BenchmarkUrlService
{
    public const HOST = 'benchmark-kit.loc';
    public const STATISTICS_HOST = 'statistics.benchmark-kit.loc';
    public const PHPINFO_HOST = 'phpinfo.benchmark-kit.loc';

    public static function getUrl(bool $showResult): string
    {
        $url = 'http://' . static::HOST . ':' . static::getNginxPort() . Benchmark::getBenchmarkRelativeUrl();

        if ($showResult === true) {
            $showResultsQueryParameter = ComponentType::getShowResultsQueryParameter(
                Benchmark::getComponentType()
            );
            if (is_string($showResultsQueryParameter)) {
                $url = static::appendToQueryString($url, $showResultsQueryParameter);
            }
        }

        return $url;
    }

    public static function getStatisticsUrl(bool $showStatistics): string
    {
        $url =
            'http://' . static::STATISTICS_HOST . ':' . static::getNginxPort() . Benchmark::getBenchmarkRelativeUrl();

        if ($showStatistics === true) {
            $url = static::appendToQueryString($url, 'phpBenchmarksShowStatistics=true');
        }

        return $url;
    }

    public static function getPhpinfoUrl(): string
    {
        return 'http://' . static::PHPINFO_HOST . ':' . static::getNginxPort();
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
