<?php

declare(strict_types=1);

namespace App\Command\Behavior;

use Symfony\Component\Console\Output\OutputInterface;

trait OutputBlockTrait
{
    /** @return $this */
    protected function outputBlock(array $lines, string $backgroundColor, OutputInterface $output): self
    {
        foreach ($lines as $line) {
            $output->writeln(
                "<fg=black;bg=$backgroundColor>  " . str_pad($line, 130) . '</>'
            );
        }

        return $this;
    }
}
