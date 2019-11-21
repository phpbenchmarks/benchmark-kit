<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\Command\AbstractCommand;

final class ValidatePhpBenchmarksVhostCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:vhost';

    /** @var ?string */
    protected $vhostContent;

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . $this->getVhostFilePath(true));
    }

    protected function doExecute(): parent
    {
        $this
            ->outputTitle('Validation of ' . $this->getVhostFilePath(true))
            ->assertFileExist($this->getVhostFilePath(), $this->getVhostFilePath(true))
            ->assertContainsVariable('____HOST____')
            ->assertContainsVariable('____INSTALLATION_PATH____')
            ->assertContainsVariable('____PHP_FPM_SOCK____');

        return $this;
    }

    private function assertContainsVariable(string $name): self
    {
        if ($this->vhostContent === null) {
            $this->vhostContent = file_get_contents($this->getVhostFilePath());
        }

        if (strpos($this->vhostContent, $name) === false) {
            $this->throwError('File should contains ' . $name . ' variable.');
        }
        $this->outputSuccess('File contains ' . $name . ' variable.');

        return $this;
    }
}
