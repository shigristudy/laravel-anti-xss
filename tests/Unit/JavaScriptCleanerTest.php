<?php

namespace Kabeer\LaravelAntiXss\Tests\Unit;

use Kabeer\LaravelAntiXss\Services\Cleaners\JavaScriptCleaner;

class JavaScriptCleanerTest extends BaseCaseTest
{
    private JavaScriptCleaner $cleaner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleaner = new JavaScriptCleaner($this->config);
    }

    public function testCleanRemovesJavaScriptCallbacks()
    {
        $tests = [
            'window.location="evil.com"' => '="evil.com"',
            'document.cookie="data"' => '="data"',
            'history.back()' => '()',
            'ScriptElement.src' => '',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanRemovesNeverAllowedCallStrings()
    {
        $tests = [
            'javascript:alert(1)' => ':alert(1)',
            'jar:file://evil.jar' => ':file://evil.jar',
            'vbscript:msgbox("xss")' => ':msgbox("xss")',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanReplacesNeverAllowedStrings()
    {
        $tests = [
            'document.cookie' => '[removed]',
            'document.write("hack")' => '[removed]("hack")',
            '.innerHTML=' => '[removed]',
            '.appendChild(' => '[removed](',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanPreservesSafeJavaScript()
    {
        $tests = [
            'console.log("Hello")' => 'console.log("Hello")',
            'myFunction()' => 'myFunction()',
            'array.push(item)' => 'array.push(item)',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertFalse($this->cleaner->isXssFound());
        }
    }
}
