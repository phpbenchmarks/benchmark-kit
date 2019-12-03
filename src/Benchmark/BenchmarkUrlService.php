<?php

declare(strict_types=1);

namespace App\Benchmark;

use App\{
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration
};

class BenchmarkUrlService
{
    public const HOST = 'benchmark-kit.loc';

    public static function getUrl(bool $showResult): string
    {
        return 'http://' . static::HOST . static::getUrlWithoutHost($showResult);
    }

    public static function getUrlWithoutHost(bool $showResult): string
    {
        $return = ComponentConfiguration::getBenchmarkUrl();

        if ($showResult === true) {
            $showResultsQueryParameter = ComponentType::getShowResultsQueryParameter(
                ComponentConfiguration::getComponentType()
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
            . getenv('NGINX_PORT')
            . static::getUrlWithoutHost($showResult);
    }
}
