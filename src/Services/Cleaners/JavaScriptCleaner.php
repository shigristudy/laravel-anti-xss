<?php

namespace Kabeer\LaravelAntiXss\Services\Cleaners;

class JavaScriptCleaner extends BaseCleaner
{
    public function clean($str): string
    {
        if (!is_string($str)) {
            return '';
        }

        // Clean never allowed strings with their replacements first
        $neverAllowedStr = $this->config->get('never_allowed_str', []);
        foreach ($neverAllowedStr as $notAllowed => $replacement) {
            if (stripos($str, $notAllowed) !== false) {
                $this->xssFound = true;
                $str = str_ireplace($notAllowed, $replacement, $str);
            }
        }

        // Clean JavaScript callback patterns
        $jsCallbacks = $this->config->get('never_allowed_js_callback_regex', []);
        foreach ($jsCallbacks as $pattern) {
            if (preg_match('/' . $pattern . '/i', $str)) {
                $this->xssFound = true;
                // Keep only the part after = if it exists
                if (strpos($str, '=') !== false) {
                    $parts = explode('=', $str, 2);
                    $str = '=' . $parts[1];
                } else {
                    $str = preg_replace('/' . $pattern . '[^\n]*/i', '', $str);
                }
            }
        }

        // Clean never allowed call strings
        $neverAllowed = $this->config->get('never_allowed_call_strings', []);
        foreach ($neverAllowed as $notAllowed) {
            if (stripos($str, $notAllowed . ':') !== false) {
                $this->xssFound = true;
                $str = str_ireplace($notAllowed . ':', ':', $str);
            }
        }

        // Clean up duplicate equal signs
        $str = preg_replace('/={2,}/', '=', $str);

        return $str;
    }
}
