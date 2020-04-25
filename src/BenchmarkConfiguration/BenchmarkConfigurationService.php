<?php

declare(strict_types=1);

namespace App\BenchmarkConfiguration;

use App\PhpVersion\PhpVersion;

class BenchmarkConfigurationService
{
    public static function getAvailable(PhpVersion $phpVersion): BenchmarkConfigurationArray
    {
        $return = new BenchmarkConfigurationArray(
            [
                new BenchmarkConfiguration(false, false),
                new BenchmarkConfiguration(true, false)
            ]
        );

        if ($phpVersion->isPreloadAvailable() === true) {
            $return[] = new BenchmarkConfiguration(true, true);
        }

        return $return;
    }
}
