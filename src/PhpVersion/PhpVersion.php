<?php

declare(strict_types=1);

namespace App\PhpVersion;

final class PhpVersion
{
    public static function getAll(): PhpVersionArray
    {
        return new PhpVersionArray(
            [
                new static(5, 6),
                new static(7, 0),
                new static(7, 1),
                new static(7, 2),
                new static(7, 3),
                new static(7, 4),
                new static(8, 0),
                new static(8, 1)
            ]
        );
    }

    public static function createFromString(string $phpVersion): self
    {
        $parts = explode('.', $phpVersion);

        return new static((int) $parts[0], (int) $parts[1]);
    }

    private int $major;

    private int $minor;

    public function __construct(int $major, int $minor)
    {
        $this->major = $major;
        $this->minor = $minor;
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function toString(): string
    {
        return $this->getMajor() . '.' . $this->getMinor();
    }

    public function isPreloadAvailable(): bool
    {
        return
            ($this->getMajor() === 7 && $this->getMinor() >= 4)
            || $this->getMajor() >= 8;
    }
}
