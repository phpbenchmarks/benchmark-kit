<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ValidationException;
use Symfony\Component\Console\{
    Command\Command,
    Output\OutputInterface
};

abstract class AbstractCommand extends Command
{
    /** @var string */
    protected $validationPrefix;

    protected function validationSuccess(OutputInterface $output, string $message): self
    {
        $output->writeln("\e[32m  Validated\e[00m " . $this->validationPrefix . $message);

        return $this;
    }

    protected function validationFailed(OutputInterface $output, string $error): void
    {
        throw new ValidationException($output, "  \e[44m > \e[00m " . $this->validationPrefix . $error);
    }

    protected function getInstallationPath(): string
    {
        return '/var/www/phpbenchmarks';
    }
}
