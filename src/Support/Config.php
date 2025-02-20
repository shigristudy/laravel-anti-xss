<?php

namespace Kabeer\LaravelAntiXss\Support;

class Config
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get(string $key, $default = null)
    {
        $parts = explode('.', $key);
        $config = $this->config;

        foreach ($parts as $part) {
            if (!is_array($config) || !array_key_exists($part, $config)) {
                return $default;
            }
            $config = $config[$part];
        }

        return $config;
    }

    public function getReplacement(): string
    {
        return $this->get('replacement', '');
    }

    public function getEvilAttributes(): array
    {
        return $this->get('evil_attributes', []);
    }

    public function getEvilHtmlTags(): array
    {
        return $this->get('evil_html_tags', []);
    }

    public function getNeverAllowedStrings(): array
    {
        return $this->get('never_allowed_strings', []);
    }

    public function getNeverAllowedRegex(): array
    {
        return $this->get('never_allowed_regex', []);
    }
}
