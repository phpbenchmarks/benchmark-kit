<?php

declare(strict_types=1);

namespace App\Command\Configure;

abstract class AbstractConfigureComponentCommand extends AbstractConfigureCommand
{
    protected function defineVariable(string $name, callable $getValue): self
    {
        $filePath = $this->getConfigurationPath() . '/AbstractComponentConfiguration.php';
        $content = file_get_contents($filePath);

        if (strpos($content, $name) !== false) {
            $value = call_user_func($getValue);
            file_put_contents($filePath, str_replace($name, $value, $content));
            $this->success($name . ' defined to ' . $value . '.');
        }

        return $this;
    }
}
