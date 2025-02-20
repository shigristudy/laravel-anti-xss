<?php

namespace Kabeer\LaravelAntiXss\Services\Cleaners;

class HtmlCleaner extends BaseCleaner
{
    public function clean($str): string
    {
        if (!is_string($str)) {
            return '';
        }

        $evilTags = $this->config->get('evil_html_tags', []);

        foreach ($evilTags as $tag) {
            // Remove complete tag with content
            $pattern = '#<\s*' . preg_quote($tag) . '[^>]*>.*?</\s*' . preg_quote($tag) . '\s*>#ius';
            if (preg_match($pattern, $str)) {
                $this->xssFound = true;
                $str = preg_replace($pattern, '', $str);
            }

            // Remove self-closing tags
            $pattern = '#<\s*' . preg_quote($tag) . '[^>]*/?\s*>#ius';
            if (preg_match($pattern, $str)) {
                $this->xssFound = true;
                $str = preg_replace($pattern, '', $str);
            }

            // Remove orphaned closing tags
            $pattern = '#</\s*' . preg_quote($tag) . '\s*>#ius';
            if (preg_match($pattern, $str)) {
                $this->xssFound = true;
                $str = preg_replace($pattern, '', $str);
            }
        }

        return $str;
    }
}
