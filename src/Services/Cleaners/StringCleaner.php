<?php

namespace Kabeer\LaravelAntiXss\Services\Cleaners;

class StringCleaner extends BaseCleaner
{
    public function clean($str): string
    {
        if (!is_string($str)) {
            return '';
        }

        $original = $str;

        // Remove invisible characters
        $str = preg_replace('/[\x00-\x1F\x7F]/u', '', $str);

        // Convert problematic unicode characters
        $str = htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Strip 4-byte characters if configured
        if ($this->config->get('strip_4byte_chars', false)) {
            $str = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $str);
        }

        // Check if any modifications were made
        $this->xssFound = ($str !== $original);

        return $str;
    }
}
