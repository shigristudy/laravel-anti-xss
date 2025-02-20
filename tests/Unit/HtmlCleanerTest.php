<?php

namespace Kabeer\LaravelAntiXss\Tests\Unit;

use Kabeer\LaravelAntiXss\Services\Cleaners\HtmlCleaner;

class HtmlCleanerTest extends BaseCaseTest
{
    private HtmlCleaner $cleaner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleaner = new HtmlCleaner($this->config);
    }

    public function testCleanRemovesEvilTags()
    {
        $tests = [
            '<script>alert(1)</script>' => '',
            '<iframe src="evil.html"></iframe>' => '',
            '<base href="evil.com">' => '',
            '<style>body{color:red}</style>' => '',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanRemovesSelfClosingTags()
    {
        $tests = [
            '<script/>' => '',
            '<iframe />' => '',
            '<base/>' => '',
            '<link/>' => '',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanHandlesNestedTags()
    {
        $tests = [
            '<div><script>alert(1)</script></div>' => '<div></div>',
            '<p><iframe>test</iframe></p>' => '<p></p>',
            '<span><base href="evil.com"></span>' => '<span></span>',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanPreservesSafeTags()
    {
        $tests = [
            '<p>Hello World</p>' => '<p>Hello World</p>',
            '<div class="safe">Content</div>' => '<div class="safe">Content</div>',
            '<span>Test</span>' => '<span>Test</span>',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertFalse($this->cleaner->isXssFound());
        }
    }
}
