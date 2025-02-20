<?php

namespace Kabeer\LaravelAntiXss\Services\Cleaners;

class RegexCleaner extends BaseCleaner
{
    public function clean($str): string
    {
        if (!is_string($str)) {
            return '';
        }

        // Handle base64 data
        if (preg_match('/data\s*:[^\n]*?base64[^\n]*?,([^"\'
]*)/i', $str)) {
            $this->xssFound = true;
            $str = preg_replace('/data\s*:[^\n]*?base64[^\n]*?,([^"\'
]*)/i', '', $str);
        }

        // Handle javascript protocol with various obfuscation techniques
        if (preg_match('/j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:/i', $str) ||
            preg_match('/javascript[\s\t\x00-\x20]*:/i', $str)) {
            $this->xssFound = true;
            $str = preg_replace('/j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:[^\n]*/i', ':alert(1)', $str);
            $str = preg_replace('/javascript[\s\t\x00-\x20]*:[^\n]*/i', ':alert(1)', $str);
            return $str;
        }

        // Handle document.location patterns
        if (preg_match('/(document|(document\.)?window)\.(location|on\w*)/i', $str)) {
            $this->xssFound = true;
            $str = preg_replace('/(document|(document\.)?window)\.(location|on\w*)[^\n]*/i', '', $str);
        }

        // Process other regex patterns
        $patterns = $this->config->get('never_allowed_regex', []);
        foreach ($patterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $str)) {
                $this->xssFound = true;
                $str = preg_replace('/' . $pattern . '/i', '', $str);
            }
        }

        return $str;
    }
}
