<?php

namespace Kabeer\LaravelAntiXss\Tests\Unit;

use Kabeer\LaravelAntiXss\Support\Config;
use PHPUnit\Framework\TestCase;

class BaseCaseTest extends TestCase
{
    protected Config $config;
    protected array $defaultConfig = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultConfig = include __DIR__ . '/../../config/anti-xss.php';
        $this->config = new Config($this->defaultConfig);
    }
}
