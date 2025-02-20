<?php

namespace Kabeer\LaravelAntiXss\Tests\Unit;

use Kabeer\LaravelAntiXss\Services\Cleaners\RegexCleaner;

class RegexCleanerTest extends BaseCaseTest
{
    private RegexCleaner $cleaner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleaner = new RegexCleaner($this->config);
    }

    public function testCleanRemovesJavaScriptProtocol()
    {
        $tests = [
            'javascript:alert(1)' => ':alert(1)',
            'JAVASCRIPT:alert(1)' => ':alert(1)',
            'javascript :alert(1)' => ':alert(1)',
            'javascript\t:alert(1)' => ':alert(1)',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanRemovesDocumentLocationPatterns()
    {
        $tests = [
            'document.location="evil.com"' => '',
            'document.location.href="evil"' => '',
            'window.location="phishing"' => '',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanRemovesBase64Data()
    {
        $tests = [
            'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==' => '',
            "data:image/svg+xml;base64,PHN2ZyBvbmxvYWQ9YWxlcnQoMSk+" => '',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanPreservesSafeContent()
    {
        $tests = [
            'Hello World' => 'Hello World',
            'http://example.com' => 'http://example.com',
            'user@example.com' => 'user@example.com',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertFalse($this->cleaner->isXssFound());
        }
    }
}
