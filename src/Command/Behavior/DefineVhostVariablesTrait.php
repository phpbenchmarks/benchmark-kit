<?php

declare(strict_types=1);

namespace App\Command\Behavior;

use App\{
    Benchmark\BenchmarkUrlService,
    PhpVersion\PhpVersion,
    Utils\Path
};

trait DefineVhostVariablesTrait
{
    private function defineVhostVariables(
        string $vhostFilePath,
        PhpVersion $phpVersion,
        string $host,
        string $entryPointRelativePath,
        callable $filePutContent,
        callable $outputSuccess
    ): self {
        $content = file_get_contents($vhostFilePath);
        if ($content === false) {
            throw new \Exception('Error while reading ' . $vhostFilePath . '.');
        }

        $content = str_replace('____PORT____', BenchmarkUrlService::getNginxPort(), $content);
        $content = str_replace('____HOST____', $host, $content);

        $sourceCodePath = realpath(Path::getSourceCodePath());
        if ($sourceCodePath === false) {
            throw new \Exception('Source code path "' . Path::getSourceCodePath() . '" not found.');
        }
        $content = str_replace('____INSTALLATION_PATH____', $sourceCodePath, $content);

        $phpFpm = 'php' . $phpVersion->toString() . '-fpm.sock';
        $content = str_replace('____PHP_FPM_SOCK____', $phpFpm, $content);

        $entryPointFilePath = dirname($entryPointRelativePath);
        $entryPointFileName = basename($entryPointRelativePath);
        $content = str_replace('____ENTRY_POINT_FILE_PATH____', $entryPointFilePath, $content);
        $content = str_replace('____ENTRY_POINT_FILE_NAME____', $entryPointFileName, $content);

        call_user_func_array($filePutContent, [$vhostFilePath, $content]);

        call_user_func($outputSuccess, '____PORT____ replaced by ' . BenchmarkUrlService::getNginxPort() . '.');
        call_user_func($outputSuccess, "____HOST____ replaced by $host.");
        call_user_func($outputSuccess, "____INSTALLATION_PATH____ replaced by $sourceCodePath.");
        call_user_func($outputSuccess, "____PHP_FPM_SOCK____ replaced by $phpFpm.");
        call_user_func($outputSuccess, "____ENTRY_POINT_FILE_PATH____ replaced by $entryPointFilePath.");
        call_user_func($outputSuccess, "____ENTRY_POINT_FILE_NAME____ replaced by $entryPointFileName.");

        return $this;
    }
}
