<?php

declare(strict_types=1);

namespace App\Command\Configure;

use AbstractComponentConfiguration\AbstractComponentConfiguration;
use App\{
    Command\AbstractCommand,
    Command\Validate\ValidateConfigurationComponentSourceCodeUrlsCommand,
    ComponentConfiguration\ComponentConfiguration
};
use Symfony\Component\Validator\ConstraintViolationInterface;

class ConfigureComponentSourceCodeUrlsCommand extends AbstractConfigureCommand
{
    use DefineVariableTrait;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('configure:component:sourceCodeUrls')
            ->setDescription(
                'Create .phpbenchmarks/AbstractComponentConfiguration.php and configure getSourceCodeUrls()'
            )
            ->addOption('skip-component-creation', null, null, 'Skip component creation ()');
    }

    protected function doExecute(): AbstractCommand
    {
        if ($this->getInput()->getOption('skip-component-creation') === false) {
            $this->runCommand('configure:component');
        }

        if ($this->skipSourceCodeUrls() === false) {
            $this
                ->title('Configuration of AbstractComponentConfiguration::getSourceCodeUrls()')
                ->defineSourceCodeUrls();
        }

        return $this;
    }

    protected function defineSourceCodeUrls(): self
    {
        $reflection = new \ReflectionClass(AbstractComponentConfiguration::class);
        $method = $reflection->getMethod('getSourceCodeUrls');

        $classCode = file($reflection->getFileName());
        for ($i = $method->getStartLine() - 1; $i < $method->getEndLine(); $i++) {
            unset($classCode[$i]);
        }
        unset($classCode[$reflection->getEndLine() - 1]);

        $getSourceCodeUrls = file($this->getTypedDefaultConfigurationPath() . '/getSourceCodeUrls.tpl');

        file_put_contents(
            $reflection->getFileName(),
            implode(null, array_merge($classCode, $getSourceCodeUrls, ['}']))
        );

        foreach ($this->getSourceCodeUrls() as $url) {
            $this->defineVariable(
                $url['variable'],
                function () use ($url) {
                    return $url['url'];
                },
                $this->getConfigurationPath() . '/AbstractComponentConfiguration.php'
            );
        }

        return $this;
    }

    protected function getSourceCodeUrls(): array
    {
        $urls = ComponentConfiguration::getSourceCodeUrls();
        $return = [
            'route' => [
                'question' => 'URL to benchmark route code?',
                'url' => $urls['route'] ?? null,
                'variable' => '____PHPBENCHMARKS_ROUTE_SOURCE_CODE_URL____'
            ],
            'controller' => [
                'question' => 'URL to Controller code?',
                'url' => $urls['controller'] ?? null,
                'variable' => '____PHPBENCHMARKS_CONTROLLER_SOURCE_CODE_URL____'
            ],
            'randomizeLanguageDispatchEvent' => [
                'question' => 'URL to code who dispatch event to randomize language?',
                'url' => $urls['randomizeLanguageDispatchEvent'] ?? null,
                'variable' => '____PHPBENCHMARKS_RANDOMIZE_LANGUAGE_DISPATCH_EVENT_SOURCE_CODE_URL____'
            ],
            'randomizeLanguageEventListener' => [
                'question' => 'URL to code who listen event to randomize language?',
                'url' => $urls['randomizeLanguageEventListener'] ?? null,
                'variable' => '____PHPBENCHMARKS_RANDOMIZE_LANGUAGE_EVENT_LISTENER_SOURCE_CODE_URL____'
            ],
            'translations' => [
                'question' => 'URL to en_GB translations code?',
                'url' => $urls['translations'] ?? null,
                'variable' => '____PHPBENCHMARKS_TRANSLATIONS_SOURCE_CODE_URL____'
            ],
            'translate' => [
                'question' => 'URL to code who translate translated.1000 key?',
                'url' => $urls['translate'] ?? null,
                'variable' => '____PHPBENCHMARKS_TRANSLATE_SOURCE_CODE_URL____'
            ],
            'serialize' => [
                'question' => 'URL to code who serialize User?',
                'url' => $urls['serialize'] ?? null,
                'variable' => '____PHPBENCHMARKS_SERIALIZE_SOURCE_CODE_URL____'
            ]
        ];

        foreach ($return as &$url) {
            $violations = ValidateConfigurationComponentSourceCodeUrlsCommand::validateSourCodeUrl($url['url']);
            $showWarning = false;
            do {
                if (count($violations) > 0) {
                    if ($showWarning) {
                        $errors = [];
                        /** @var ConstraintViolationInterface $violation */
                        foreach ($violations as $violation) {
                            $errors[] = $violation->getMessage();
                        }
                        $this->warning(implode(', ', $errors));
                    }

                    $url['url'] = $this->question($url['question']);
                }

                $showWarning = true;
            } while (
                count(
                    $violations = ValidateConfigurationComponentSourceCodeUrlsCommand::validateSourCodeUrl($url['url'])
                ) > 0
            );
        }

        return $return;
    }
}
