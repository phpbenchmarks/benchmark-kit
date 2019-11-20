<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

trait DefineVariableTrait
{
    abstract protected function outputSuccess(string $message): AbstractCommand;

    protected function defineVariable(string $name, callable $getValue, string $file): self
    {
        $content = file_get_contents($file);

        if ($this->hasVariable($name, $file)) {
            $value = call_user_func($getValue);
            file_put_contents($file, str_replace($name, $value, $content));
            $this->outputSuccess($name . ' defined to ' . $value . '.');
        }

        return $this;
    }

    protected function hasVariable(string $name, string $file): bool
    {
        return is_int(strpos(file_get_contents($file), $name));
    }

    protected function defineStringVariable(string $name, string $value, string $file): self
    {
        return $this->defineVariable(
            $name,
            function () use ($value) {
                return $value;
            },
            $file
        );
    }
}
