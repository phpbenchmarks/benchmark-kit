<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion,
    Utils\Path
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

    private function getPhpVersionFromArgument(AbstractCommand $command): ?PhpVersion
    {
        $phpVersionString = $command->getInput()->getArgument('phpVersion');

        return is_string($phpVersionString) ? PhpVersion::createFromString($phpVersionString) : null;
    }

    private function assertPhpVersionArgument(AbstractCommand $command, bool $allowNull = false): self
    {
        $phpVersion = $this->getPhpVersionFromArgument($command);

        if ($allowNull === true && $phpVersion === null) {
            return $this;
        }

        if (ComponentConfiguration::getCompatiblesPhpVersions()->exists($phpVersion) === false) {
            throw new \Exception(
                PhpVersion::getAll()->exists($phpVersion)
                    ?
                        'PHP '
                            . $phpVersion->toString()
                            . ' is not compatible with this benchmark. Enable it into '
                            . Path::removeBenchmarkPathPrefix(Path::getBenchmarkConfigurationClassPath())
                            . '.'
                    :
                        'Invalid PHP version '
                            . $phpVersion->toString()
                            . '. Available versions: '
                            . ComponentConfiguration::getCompatiblesPhpVersions()->toString()
                            . '.'
            );
        }

        return $this;
    }
}
