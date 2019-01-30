<?php

declare(strict_types=1);

namespace App\Command\Configure;

use AbstractComponentConfiguration\AbstractComponentConfiguration;
use App\{
    Command\Validate\ValidateConfigurationComponentCommand,
    ComponentConfiguration\ComponentConfiguration
};
use Symfony\Component\Validator\ConstraintViolationInterface;

class ConfigureComponentSourceCodeUrlsCommand extends AbstractConfigureComponentCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('configure:component:sourceCodeUrls')
            ->setDescription(
                'Create .phpbenchmarks/AbstractComponentConfiguration.php and configure getSourceCodeUrls()'
            )
            ->addOption('skip-component-creation', null, null, 'Skip component creation ()');
    }

    protected function doExecute(): parent
    {
        if ($this->getInput()->getOption('skip-component-creation') === false) {
            $this->runCommand('configure:component');
        }

        if ($this->skipSourceCodeUrls() === false) {
            $this
                ->title('Configuration of AbstractComponentConfiguration::getSourceCodeUrls()')
                ->defineSourceCodeUrls()
                ->runCommand('validate:configuration:component:sourceCodeUrls');
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
                }
            );
        }

        return $this;
    }

    protected function getSourceCodeUrls(): array
    {
        $urls = ComponentConfiguration::getSourceCodeUrls();
        $return = [
            'route' => [
                'question' => 'URL to route code?',
                'url' => $urls['route'] ?? null,
                'variable' => '____PHPBENCHMARKS_ROUTE_SOURCE_CODE_URL____'
            ],
            'controller' => [
                'question' => 'URL to Controller code?',
                'url' => $urls['controller'] ?? null,
                'variable' => '____PHPBENCHMARKS_CONTROLLER_SOURCE_CODE_URL____'
            ]
        ];

        foreach ($return as &$url) {
            $violations = ValidateConfigurationComponentCommand::validateSourCodeUrl($url['url']);
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
            } while (count($violations = ValidateConfigurationComponentCommand::validateSourCodeUrl($url['url'])) > 0);
        }

        return $return;
    }
}
