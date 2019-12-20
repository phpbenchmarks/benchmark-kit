<?php

declare(strict_types=1);

namespace App\Benchmark;

use App\Component\ComponentType;

class BenchmarkUrlService
{
    public const HOST = 'benchmark-kit.loc';

    public static function getUrl(bool $showResult): string
    {
        return 'http://' . static::HOST . static::getUrlWithoutHost($showResult);
    }

    public static function getUrlWithoutHost(bool $showResult): string
    {
        $return = Benchmark::getBenchmarkUrl();

        if ($showResult === true) {
            $showResultsQueryParameter = ComponentType::getShowResultsQueryParameter(
                Benchmark::getComponentType()
            );
            if (is_string($showResultsQueryParameter)) {
                $return .= (strpos($return, '?') === false) ? '?' : '&';
                $return .= $showResultsQueryParameter;
            }
        }

        return $return;
    }

    public static function getUrlWithPort(bool $showResult): string
    {
        return
            'http://'
            . static::HOST
            . ':'
            . $_ENV['NGINX_PORT']
            . static::getUrlWithoutHost($showResult);
    }
}
