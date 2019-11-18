<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Filesystem\Filesystem;

trait FileContentTrait
{
    private function fileGetContent(string $filename): string
    {
        $return = file_get_contents($filename);
        if ($return === false) {
            throw new \Exception('File ' . $filename . ' does not exists or is not readable.');
        }

        return $return;
    }

    private function filePutContent(string $filename, string $content, AbstractCommand $command): self
    {
        $fileExists = file_exists($filename);
        (new Filesystem())->dumpFile($filename, $content);
        $command->outputSuccess(
            'File '
                . $command->removeInstallationPathPrefix($filename)
                . ' '
                . ($fileExists ? 'modified' : 'created')
                . '.'
        );

        return $this;
    }
}
