<?php

declare(strict_types=1);

namespace App;

final class Version
{
    /** @var int  */
    public const MAJOR = 4;

    /** @var int  */
    public const MINOR = 0;

    /** @var int  */
    public const BUGFIX = 0;

    /** @var bool */
    public const DEV = true;

    public static function getVersion(): string
    {
        return
            (string) static::MAJOR
            . '.'
            . (string) static::MINOR
            . '.'
            . (string) static::BUGFIX
            . (static::DEV ? '-dev' : null);
    }
}
