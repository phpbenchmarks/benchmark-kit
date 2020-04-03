<?php

declare(strict_types=1);

namespace App\Command\Behavior;

use App\{
    Benchmark\Benchmark,
    PhpVersion\PhpVersion
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface
};

trait PhpVersionArgumentTrait
{
    protected function addPhpVersionArgument(Command $command, int $mode = InputArgument::REQUIRED): self
    {
        $command->addArgument('phpVersion', $mode, 'Version of PHP (example: 5.6, 7.0)');

        return $this;
    }

    protected function getPhpVersionFromArgument(InputInterface $input): ?PhpVersion
    {
        $phpVersionString = $input->getArgument('phpVersion');

        return is_string($phpVersionString) ? PhpVersion::createFromString($phpVersionString) : null;
    }

    protected function assertPhpVersionArgument(InputInterface $input, bool $allowNull = false): self
    {
        $phpVersion = $this->getPhpVersionFromArgument($input);

        if ($allowNull === true && $phpVersion === null) {
            return $this;
        }

        if (Benchmark::getCompatiblesPhpVersions()->exists($phpVersion) === false) {
            throw new \Exception(
                PhpVersion::getAll()->exists($phpVersion)
                    ?
                        'PHP ' . $phpVersion->toString() . ' is not compatible with this benchmark.'
                    :
                        'Invalid PHP version '
                            . $phpVersion->toString()
                            . '. Available versions: '
                            . Benchmark::getCompatiblesPhpVersions()->toString()
                            . '.'
            );
        }

        return $this;
    }
}
