<?php

namespace michaelcaplan\JsonResume\Gemini\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Text;

class Figlet extends AbstractHelper
{
    protected Text\Figlet\Figlet $figlet;

    public function __construct()
    {
        $this->figlet = new Text\Figlet\Figlet();
    }

    public function __invoke(string $text, $font = null): string
    {
        if ($font) {
            $this->figlet->setFont(__DIR__ . '/Figlet/Fonts/' . pathinfo($font, PATHINFO_FILENAME) . '.flf');
        }

        return $this->figlet->render($text);
    }
}