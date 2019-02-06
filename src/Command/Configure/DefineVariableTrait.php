<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

trait DefineVariableTrait
{
    abstract protected function success(string $message): AbstractCommand;

    protected function defineVariable(string $name, callable $getValue, string $file): self
    {
        return $this->defineStringVariable($name, (string) call_user_func($getValue), $file);
    }

    protected function defineStringVariable(string $name, string $value, string $file): self
    {
        $content = file_get_contents($file);

        if (strpos($content, $name) !== false) {
            file_put_contents($file, str_replace($name, $value, $content));
            $this->success($name . ' defined to ' . $value . '.');
        }

        return $this;
    }
}
