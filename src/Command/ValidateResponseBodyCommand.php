<?php

declare(strict_types=1);

namespace App\Command;

class ValidateResponseBodyCommand extends AbstractCommand
{
    /** @var ?string */
    protected $vhostContent;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('validate:responseBody')
            ->setDescription('Validate .phpbenchmarks/responseBody directory');
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Validation of .phpbenchmarks/responseBody')
            ->assertFileExist(
                $this->getResponseBodyPath() . '/responseBody.txt',
                '.phpbenchmarks/responseBody/responseBody.txt'
            );

        return $this;
    }
}
