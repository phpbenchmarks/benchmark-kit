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
            || $this->confirmationQuestion(
                $this->getConfigurationPath(true) . '/' . $file . ' already exist. Overwrite it?',
                false
            )
        ) {
            $copied = copy($sourceFile, $destinationFile);
            if ($copied === false) {
                $this->error(
                    'Error while copying '
                    . $sourceFile
                    . ' to '
                    . $this->getConfigurationPath(true)
                    . '/'
                    . $file
                    . '.'
                );
            }
            $this->success($this->getConfigurationPath(true) . '/' . $file . ' created.');
            if (is_string($copyWarning)) {
                $this->warning($copyWarning);
            }
        }

        return $this;
    }
}
