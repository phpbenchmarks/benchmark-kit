<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument
};

trait PhpVersionArgumentTrait
{
    private function addPhpVersionArgument(Command $command, int $mode = InputArgument::REQUIRED): self
    {
        $command->addArgument('phpVersion', $mode, 'Version of PHP (example: 5.6, 7.0)');

        return $this;
    }

    private function getPhpVersionFromArgument(AbstractCommand $command): ?string
    {
        return $command->getInput()->getArgument('phpVersion');
    }

    private function assertPhpVersionArgument(AbstractCommand $command, bool $allowNull = false): self
    {
        $phpVersion = $this->getPhpVersionFromArgument($command);

        if ($allowNull === true && $phpVersion === null) {
            return $this;
        }

        if (in_array($phpVersion, ComponentConfiguration::getEnabledPhpVersions()) === false) {
            throw new \Exception(
                in_array($phpVersion, PhpVersion::getAll())
                    ?
                        'PHP '
                            . $phpVersion
                            . ' is not compatible with this benchmark. Enable it into '
                            . $command->getConfigurationFilePath(true)
                            . '.'
                    :
                        'Invalid PHP version '
                            . $phpVersion
                            . '. Available versions: '
                            . implode(', ', ComponentConfiguration::getEnabledPhpVersions())
                            . '.'
            );
        }

        return $this;
    }
}
