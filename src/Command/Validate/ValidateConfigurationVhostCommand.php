<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\Command\AbstractCommand;

class ValidateConfigurationVhostCommand extends AbstractCommand
{
    /** @var ?string */
    protected $vhostContent;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('validate:configuration:vhost')
            ->setDescription('Validate .phpbenchmarks/vhost.conf');
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Validation of .phpbenchmarks/vhost.conf')
            ->assertFileExist($this->getVhostFilePath(), '.phpbenchmarks/vhost.conf')
            ->assertContainsVariable('____HOST____')
            ->assertContainsVariable('____INSTALLATION_PATH____')
            ->assertContainsVariable('____PHP_FPM_SOCK____');

        return $this;
    }

    protected function getVhostFilePath(): string
    {
        return $this->getConfigurationPath() . '/vhost.conf';
    }

    protected function assertContainsVariable(string $name): self
    {
        if ($this->vhostContent === null) {
            $this->vhostContent = file_get_contents($this->getVhostFilePath());
        }

        if (strpos($this->vhostContent, $name) === false) {
            $this->error('File should contains ' . $name . ' variable.');
        }
        $this->success('File contains ' . $name . ' variable.');

        return $this;
    }
}
