<?php

namespace Kabeer\LaravelAntiXss\Services\Cleaners;

class AttributeCleaner extends BaseCleaner
{
    public function clean($str): string
    {
        if (!is_string($str)) {
            return '';
        }

        $evilAttributes = $this->config->get('evil_attributes', []);
        $onEvents = $this->config->get('never_allowed_on_events_afterwards', []);

        // Combine all attributes to check
        $attributes = array_merge($evilAttributes, $onEvents);

        foreach ($attributes as $attribute) {
            $patterns = [
                '#' . $attribute . '\s*=\s*".*?"#ius',
                '#' . $attribute . '\s*=\s*\'.*?\'#ius',
                '#' . $attribute . '\s*=\s*\w+#ius',
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $str)) {
                    $this->xssFound = true;
                    $str = preg_replace($pattern, $this->replacement, $str);
                }
            }
        }

        return $str;
    }
}
