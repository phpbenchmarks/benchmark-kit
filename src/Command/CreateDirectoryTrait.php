<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Filesystem\Filesystem;

trait CreateDirectoryTrait
{
    private function createDirectory(string $directory, AbstractCommand $command): self
    {
        if (is_dir($directory) === false) {
            (new Filesystem())->mkdir($directory);
            $command->outputSuccess('Directory ' . $command->removeInstallationPathPrefix($directory) . ' created.');
        }

        return $this;
    }
}
