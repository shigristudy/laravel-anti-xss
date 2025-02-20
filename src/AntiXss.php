<?php

namespace Kabeer\LaravelAntiXss;

use Kabeer\LaravelAntiXss\Contracts\XssCleaner;
use Kabeer\LaravelAntiXss\Services\Cleaners\AttributeCleaner;
use Kabeer\LaravelAntiXss\Services\Cleaners\HtmlCleaner;
use Kabeer\LaravelAntiXss\Services\Cleaners\JavaScriptCleaner;
use Kabeer\LaravelAntiXss\Services\Cleaners\RegexCleaner;
use Kabeer\LaravelAntiXss\Services\Cleaners\StringCleaner;
use Kabeer\LaravelAntiXss\Support\Config;

class AntiXss implements XssCleaner
{
    private Config $config;
    private AttributeCleaner $attributeCleaner;
    private HtmlCleaner $htmlCleaner;
    private JavaScriptCleaner $jsCleaner;
    private RegexCleaner $regexCleaner;
    private StringCleaner $stringCleaner;
    private bool $xssFound = false;

    public function __construct(array $config)
    {
        $this->config = new Config($config);
        $this->attributeCleaner = new AttributeCleaner($this->config);
        $this->htmlCleaner = new HtmlCleaner($this->config);
        $this->jsCleaner = new JavaScriptCleaner($this->config);
        $this->regexCleaner = new RegexCleaner($this->config);
        $this->stringCleaner = new StringCleaner($this->config);
    }

    public function clean($str): string
    {
        $this->xssFound = false;

        if (is_array($str)) {
            return $this->cleanArray($str);
        }

        if (!is_string($str) || empty($str)) {
            return $str;
        }

        // Only run enabled cleaners
        if ($this->config->get('cleaners.string', true)) {
            $str = $this->stringCleaner->clean($str);
        }

        if ($this->config->get('cleaners.html', true)) {
            $str = $this->htmlCleaner->clean($str);
        }

        if ($this->config->get('cleaners.attribute', true)) {
            $str = $this->attributeCleaner->clean($str);
        }

        if ($this->config->get('cleaners.javascript', true)) {
            $str = $this->jsCleaner->clean($str);
        }

        if ($this->config->get('cleaners.regex', true)) {
            $str = $this->regexCleaner->clean($str);
        }

        $this->xssFound = $this->stringCleaner->isXssFound()
            || $this->htmlCleaner->isXssFound()
            || $this->attributeCleaner->isXssFound()
            || $this->jsCleaner->isXssFound()
            || $this->regexCleaner->isXssFound();

        return $str;
    }

    private function cleanArray(array $array): array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = $this->cleanArray($value);
            } else {
                $value = $this->clean($value);
            }
        }
        return $array;
    }

    public function isXssFound(): bool
    {
        return $this->xssFound;
    }
}
