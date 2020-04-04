<?php

declare(strict_types=1);

namespace App\Console\Input;

use Symfony\Component\Console\Input\InputInterface;

class InputOptionService
{
    public static function getBoolValue(InputInterface $input, string $name): bool
    {
        return in_array($input->getOption($name), ['yes', 'y', 'Y', 'true', true, '1'], true);
    }
}
