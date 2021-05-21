<?php

declare(strict_types=1);

namespace App\Command\Configure\Php;

use App\{
    Command\AbstractCommand,
    Command\Behavior\GetComposerConfigurationTrait,
    PhpVersion\PhpVersion,
    PhpVersion\PhpVersionArray,
    Utils\Path
};
use Symfony\Component\Console\Input\InputOption;

final class ConfigurePhpCompatibleVersionCommand extends AbstractCommand
{
    use GetComposerConfigurationTrait;

    /** @var string */
    protected static $defaultName = 'configure:php:compatible-version';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create configuration for each compatible PHP version')
            ->addOption(
                'php-versions',
                null,
                InputOption::VALUE_REQUIRED,
                'PHP version separated by "," (exemple: 7.1,7.2)'
            );
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Configuration of PHP compatibles versions');

        $compatiblesPhpVersions = $this->getCompatiblesPhpVersions();
        if ($compatiblesPhpVersions === null || $compatiblesPhpVersions->count() === 0) {
            $compatiblesPhpVersions = $this->askCompatiblesPhpVersions();
        }

        foreach (PhpVersion::getAll() as $phpVersion) {
            $phpConfigurationPath = Path::getPhpConfigurationPath($phpVersion);

            if ($compatiblesPhpVersions->exists($phpVersion)) {
                $this
                    ->createDirectory($phpConfigurationPath)
                    ->writeFileFromTemplate(Path::rmPrefix($phpConfigurationPath) . '/php.ini');
                if ($phpVersion->isPreloadAvailable()) {
                    $this->writeFileFromTemplate(Path::rmPrefix(Path::getPreloadPath($phpVersion)));
                }
            } else {
                $this->removeDirectory($phpConfigurationPath);
            }
        }

        return 0;
    }

    private function getCompatiblesPhpVersions(): ?PhpVersionArray
    {
        $phpVersions = $this->getInput()->getOption('php-versions');
        if (is_string($phpVersions)) {
            $return = new PhpVersionArray();
            foreach (explode(',', $phpVersions) as $phpVersion) {
                [$major, $minor] = explode('.', $phpVersion);
                $return[] = new PhpVersion((int) $major, (int) $minor);
            }

            return $return;
        }

        $composerConfiguration = $this->getComposerConfiguration();
        /** @var string|null $phpVersionConfiguration */
        $phpVersionConfiguration = $composerConfiguration['require']['php'] ?? null;

        if (is_string($phpVersionConfiguration) === false) {
            return null;
        }

        $versionModifier = $this->getVersionModifier($phpVersionConfiguration);
        if (is_string($versionModifier)) {
            $phpVersionConfiguration = ltrim($phpVersionConfiguration, $versionModifier);
        }

        if (
            preg_match('/^([0-9]).([0-9])$/', $phpVersionConfiguration, $phpVersionParts) === 1
            || preg_match('/^([0-9]).([0-9]).[0-9*]$/', $phpVersionConfiguration, $phpVersionParts) === 1
        ) {
            $major = (int) $phpVersionParts[1];
            $minor = (int) $phpVersionParts[2];

            if ($versionModifier === '^') {
                return $this->getPhpVersionsFromCarretVersionRange($major, $minor);
            } elseif ($versionModifier === null) {
                return $this->getPhpVersionsFromNullModifier($major, $minor, $phpVersionConfiguration);
            }
        }

        return null;
    }

    private function askCompatiblesPhpVersions(): PhpVersionArray
    {
        $return = new PhpVersionArray();

        foreach (PhpVersion::getAll() as $phpVersion) {
            if ($this->askConfirmationQuestion('Is PHP ' . $phpVersion->toString() . ' compatible?')) {
                $return[] = $phpVersion;
            }
        }

        return $return;
    }

    private function getVersionModifier(string $version): ?string
    {
        $versionModifier = substr($version, 0, 1);

        return $versionModifier === '^' || $versionModifier === '~' ? $versionModifier : null;
    }

    private function getPhpVersionsFromNullModifier(
        int $major,
        int $minor,
        string $phpVersionConfiguration
    ): PhpVersionArray {
        $phpVersion = new PhpVersion($major, $minor);
        if (PhpVersion::getAll()->exists($phpVersion) === false) {
            throw new \Exception(
                'PHP version ' . $phpVersionConfiguration . ' is not compatible with Benchmark kit.'
                    . ' Compatibles PHP versions: ' . PhpVersion::getAll()->toString() . '.'
            );
        }

        return new PhpVersionArray([$phpVersion]);
    }

    private function getPhpVersionsFromCarretVersionRange(int $major, int $minor): PhpVersionArray
    {
        $return = new PhpVersionArray();

        foreach (PhpVersion::getAll() as $phpVersion) {
            if (
                $phpVersion->getMajor() > $major
                || (
                    $phpVersion->getMajor() === $major
                    && $phpVersion->getMinor() >= $minor
                )
            ) {
                $return[] = $phpVersion;
            }
        }

        return $return;
    }
}
