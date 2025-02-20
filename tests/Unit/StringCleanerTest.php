<?php

namespace Kabeer\LaravelAntiXss\Tests\Unit;

use Kabeer\LaravelAntiXss\Services\Cleaners\StringCleaner;
use Kabeer\LaravelAntiXss\Support\Config;

class StringCleanerTest extends BaseCaseTest
{
    private StringCleaner $cleaner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleaner = new StringCleaner($this->config);
    }

    public function testCleanRemovesInvisibleCharacters()
    {
        $input = "Hello\x00World\x1F";
        $expected = "HelloWorld";
        $this->assertEquals($expected, $this->cleaner->clean($input));
        $this->assertTrue($this->cleaner->isXssFound());
    }

    public function testCleanHandlesNonStringInput()
    {
        $this->assertEquals('', $this->cleaner->clean(null));
        $this->assertEquals('', $this->cleaner->clean([]));
        $this->assertEquals('', $this->cleaner->clean(new \stdClass()));
    }

    public function testCleanConvertsSpecialCharacters()
    {
        $input = '<script>alert("XSS")</script>';
        $expected = '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;';
        $this->assertEquals($expected, $this->cleaner->clean($input));
        $this->assertTrue($this->cleaner->isXssFound());
    }

    public function testStrip4ByteCharacters()
    {
        $config = new Config(array_merge($this->defaultConfig, [
            'strip_4byte_chars' => true
        ]));
        $this->cleaner = new StringCleaner($config);

        $input = "Hello 👋 World";  // Contains 4-byte emoji
        $expected = "Hello  World";  // Emoji removed
        $this->assertEquals($expected, $this->cleaner->clean($input));
        $this->assertTrue($this->cleaner->isXssFound());
    }
}
