<?php

declare(strict_types=1);

namespace App\BenchmarkConfiguration;

class BenchmarkConfiguration
{
    protected bool $opcacheEnabled;

    protected bool $preloadEnabled;

    public function __construct(bool $opcacheEnabled, bool $preloadEnabled)
    {
        $this->opcacheEnabled = $opcacheEnabled;
        $this->preloadEnabled = $preloadEnabled;
    }

    public function isOpcacheEnabled(): bool
    {
        return $this->opcacheEnabled;
    }

    public function isPreloadEnabled(): bool
    {
        return $this->preloadEnabled;
    }

    public function toString(): string
    {
        return implode(
            ', ',
            [
                $this->isOpcacheEnabled() ? 'opcache enabled' : 'opcached disabled',
                $this->isPreloadEnabled() ? 'preload enabled' : 'preload disabled'
            ]
        );
    }
}
