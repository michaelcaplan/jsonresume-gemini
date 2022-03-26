<?php

namespace michaelcaplan\JsonResume\Gemini\Server;

use Laminas\Config\Config;
use Laminas\View;
use michaelcaplan\JsonResume\Gemini\Resume;
use michaelcaplan\JsonResume\Gemini\View\Helper\DateRange;
use michaelcaplan\JsonResume\Gemini\View\Helper\Figlet;

class Router
{
    private Config $config;
    private View\View $view;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->initView();
    }

    protected function initView(): void
    {
        // Create template resolver
        $templateResolver = new View\Resolver\TemplatePathStack([
            'script_paths' => [__ROOT_DIR__ . '/templates'],
        ]);

        // Create the renderer
        $renderer = new View\Renderer\PhpRenderer();
        $renderer->setResolver($templateResolver);
        $helperPluginManager = $renderer->getHelperPluginManager();
        $helperPluginManager->setAlias('figlet', Figlet::class);
        $helperPluginManager->setFactory(Figlet::class, function () {

            return new Figlet();
        });
        $helperPluginManager->setAlias('dateRange', DateRange::class);
        $helperPluginManager->setFactory(DateRange::class, function () {

            return new DateRange();
        });

        // Initialize the view
        $view = new View\View();
        $view->getEventManager()->attach(
            View\ViewEvent::EVENT_RENDERER,
            static function () use ($renderer) {
                return $renderer;
            }
        );


        $this->view = $view;
    }

    public function route(Message $message): void
    {
        $message->setBody(
            $this->generateBody(
                $this->mapHostToResume($message->getRequestUri()->getHost()),
                $message
            )
        );
    }

    protected function generateBody(Resume $resume, Message $message): string
    {
        $sections = $resume->getSectionNames();
        $sectionName = $this->pathToSection($message->getRequestUri()->getPath(), $sections);
        $sectionData = $sectionName ? $resume->getSection($sectionName) : [];

        $theme = $resume->getConfig()->theme ?? 'default';

        $viewModel = new View\Model\ViewModel();
        $viewModel->setOption('has_parent', true);

        $layout = new View\Model\ViewModel();
        $layout->setTemplate($theme . '/layout');
        $layout->setOption('has_parent', true);
        $layout->setVariables([
            'basics' => $resume->getSection('basics'),
            'sections' => $sections
        ]);
        $layout->addChild($viewModel);

        if (empty($sectionData)) {
            $viewModel->setTemplate($theme . '/51');
            $message->setCode(51);
            return $this->view->render($layout);
        }

        $viewModel->setVariables([
            'sectionData' => $sectionData,
            'sectionName' => $sectionName
        ]);

        $viewModel->setTemplate($theme . '/' . $sectionName);

        try {
            return $this->view->render($layout);
        } catch (View\Exception\RuntimeException) {
            $message->setCode(51);
            $viewModel->setTemplate($theme . '/51');
            return $this->view->render($layout);
        } catch (\Exception) {
            $message->setCode(42);
            $viewModel->setTemplate($theme . '/42');
            return $this->view->render($layout);
        }
    }

    protected function mapHostToSection(string $host): string
    {
        $section = $host;

        if (empty($this->config[$host])) {
            $section = 'default';
        }

        return $section;
    }

    protected function mapHostToResume(string $host): Resume
    {
        static $maps = [];

        $section = $this->mapHostToSection($host);

        if (isset($maps[$section])) {
            return $maps[$section];
        }

        $maps[$section] = new Resume($this->config[$section]);

        return $maps[$section];
    }

    private function pathToSection(string $path, array $availableSections): string|null
    {
        $section = null;

        if (!empty($path)) {
            $section = pathinfo($path, PATHINFO_FILENAME);
        }

        if (empty($section)) {
            $section = 'basics';
        }

        if (!in_array($section, $availableSections)) {
            return null;
        }

        return $section;
    }
}
