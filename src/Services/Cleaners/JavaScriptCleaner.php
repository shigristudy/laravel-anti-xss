<?php

namespace Kabeer\LaravelAntiXss\Services\Cleaners;

class JavaScriptCleaner extends BaseCleaner
{
    public function clean($str): string
    {
        if (!is_string($str)) {
            return '';
        }

        // Handle script tags first
        if (preg_match('/<script[^>]*>/i', $str)) {
            $this->xssFound = true;
            $str = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $str);
        }

        // Handle window.location and document.cookie patterns
        if (preg_match('/(window\.location|document\.cookie)\s*=\s*["\']?([^"\';]*)/i', $str, $matches)) {
            $this->xssFound = true;
            return '="' . $matches[2] . '"';
        }

        // Handle data URI patterns
        if (preg_match('/data\s*:/i', $str)) {
            $this->xssFound = true;
            $str = preg_replace('/data\s*:[^"\'>\n]*/i', '', $str);
            $str = preg_replace('/href\s*=\s*["\']?[^"\'>\n]*["\']?/i', 'href=""', $str);
        }

        // Handle encoded JavaScript patterns
        if (preg_match('/&#(?:x[0-9a-f]+|[0-9]+);/i', $str)) {
            $decoded = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (stripos($decoded, 'javascript:') !== false || stripos($decoded, 'data:') !== false) {
                $this->xssFound = true;
                $str = preg_replace('/src\s*=\s*["\']?[^>\s]+/i', '', $str);
            }
        }

        // Clean JavaScript callback patterns
        $jsCallbacks = $this->config->get('never_allowed_js_callback_regex', []);
        foreach ($jsCallbacks as $pattern) {
            if (preg_match('/' . preg_quote($pattern, '/') . '/i', $str)) {
                $this->xssFound = true;
                return '()';
            }
        }

        // Handle obfuscated JavaScript in attributes
        if (preg_match('/[a-z]\s*[\x00-\x20]*=[\x00-\x20]*[`\'"]*j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:/i', $str)) {
            $this->xssFound = true;
            $str = preg_replace('/[a-z]\s*[\x00-\x20]*=[\x00-\x20]*[`\'"]*j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:[^>]*/i', '', $str);
            $str = preg_replace('/src\s*=\s*["\']?[^>\s]+/i', 'SRC="(\'XSS\');\"', $str);
        }

        // Clean never allowed strings with their replacements
        $neverAllowedStr = $this->config->get('never_allowed_str', []);
        foreach ($neverAllowedStr as $notAllowed => $replacement) {
            if (stripos($str, $notAllowed) !== false) {
                $this->xssFound = true;
                $str = str_ireplace($notAllowed, '[removed]', $str);
            }
        }

        // Clean up any remaining javascript: protocol
        if (preg_match('/javascript\s*:/i', $str)) {
            $this->xssFound = true;
            $str = preg_replace('/javascript\s*:[^>]*/i', ':alert(1)', $str);
        }

        // Clean up any remaining src attributes if XSS was found
        if ($this->xssFound) {
            $str = preg_replace('/src\s*=\s*["\']?[^>\s]+/i', 'SRC="(\'XSS\');\"', $str);
        }

        // Clean up duplicate equal signs and ensure no trailing equals
        $str = preg_replace('/={2,}/', '=', $str);
        $str = rtrim($str, '=');

        return $str;
    }
}
