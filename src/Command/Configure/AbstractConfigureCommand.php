<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

abstract class AbstractConfigureCommand extends AbstractCommand
{
    protected function copyDefaultConfigurationFile(
        string $file,
        bool $isComponentTypeDirectory = false,
        string $copyWarning = null
    ): self {
        $sourceFile =
            (
            $isComponentTypeDirectory
                ? $this->getTypedDefaultConfigurationPath()
                : $this->getDefaultConfigurationPath()
            )
            . '/'
            . $file;
        $destinationFile = $this->getConfigurationPath() . '/' . $file;

        if (
            file_exists($destinationFile) === false
            || $this->confirmationQuestion('.phpbenchmarks/' . $file . ' already exist. Overwrite it?', false)
        ) {
            $copied = copy($sourceFile, $destinationFile);
            if ($copied === false) {
                $this->error(
                    'Error while copying '
                    . $sourceFile
                    . ' to '
                    . '.phpbenchmarks/' . $file . '.'
                );
            }
            $this->success('.phpbenchmarks/' . $file . ' created.');
            if (is_string($copyWarning)) {
                $this->warning($copyWarning);
            }
        }

        return $this;
    }
}
