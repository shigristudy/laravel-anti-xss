<?php

namespace Kabeer\LaravelAntiXss\Contracts;

interface XssCleaner
{
    public function clean($str): string;
    public function isXssFound(): bool;
}
