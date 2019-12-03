<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Benchmark\BenchmarkUrlService,
    PhpVersion\PhpVersion
};

trait PrepareBenchmarkCurlTrait
{
    /** @return resource */
    protected function prepareBenchmarkCurl(bool $showResult)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, BenchmarkUrlService::getUrl($showResult));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        return $curl;
    }
}
