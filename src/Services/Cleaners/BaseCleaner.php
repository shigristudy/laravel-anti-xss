<?php

namespace Kabeer\LaravelAntiXss\Services\Cleaners;

use Kabeer\LaravelAntiXss\Contracts\XssCleaner;
use Kabeer\LaravelAntiXss\Support\Config;

abstract class BaseCleaner implements XssCleaner
{
    protected Config $config;
    protected bool $xssFound = false;
    protected string $replacement;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->replacement = $config->get('replacement', '');
    }

    public function isXssFound(): bool
    {
        return $this->xssFound;
    }
}
