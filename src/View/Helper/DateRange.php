<?php

namespace michaelcaplan\JsonResume\Gemini\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class DateRange extends AbstractHelper
{
    public function __construct()
    {
    }

    public function __invoke(string $startDate = null, string $endDate = null): string
    {
        $range = '';

        if (!empty($startDate)) {
            $range = $startDate;
        }

        if (!empty($endDate) && $startDate != $endDate) {
            $range .= ' - ' . $endDate;
        }

        return $range;
    }
}