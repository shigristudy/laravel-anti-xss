<?php

namespace Kabeer\LaravelAntiXss\Tests\Unit;

use Kabeer\LaravelAntiXss\Services\Cleaners\AttributeCleaner;

class AttributeCleanerTest extends BaseCaseTest
{
    private AttributeCleaner $cleaner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleaner = new AttributeCleaner($this->config);
    }

    public function testCleanRemovesEvilAttributes()
    {
        $tests = [
            '<div style="color:red">text</div>' => '<div >text</div>',
            '<img xmlns:xdp="evil">text</img>' => '<img >text</img>',
            '<form formaction="evil.php">text</form>' => '<form >text</form>',
            '<svg xlink:href="data:">test</svg>' => '<svg >test</svg>',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanRemovesEventHandlers()
    {
        $tests = [
            '<div onclick="evil()">text</div>' => '<div >text</div>',
            '<img onload="hack()">text</img>' => '<img >text</img>',
            '<a onmouseover="xss()">text</a>' => '<a >text</a>',
            '<input onfocus="alert(1)">' => '<input >',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanHandlesAttributeFormats()
    {
        $tests = [
            '<div style="evil">text</div>' => '<div >text</div>',
            "<div style='evil'>text</div>" => '<div >text</div>',
            '<div style=evil>text</div>' => '<div >text</div>',
            '<div STYLE="evil">text</div>' => '<div >text</div>',
            '<div style="url(\'javascript:alert(1)\')">text</div>' => '<div >text</div>',
            '<div style="width: \nexpression(alert(1));">text</div>' => '<div >text</div>',
            '<div style="background:\n url (javascript:ooxx);">text</div>' => '<div >text</div>',
            '<div style="background:url (javascript:ooxx);">text</div>' => '<div >text</div>',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testCleanPreservesSafeAttributes()
    {
        $tests = [
            '<div class="safe">text</div>' => '<div class="safe">text</div>',
            '<img alt="description" src="image.jpg">' => '<img alt="description" src="image.jpg">',
            '<a href="https://example.com">link</a>' => '<a href="https://example.com">link</a>',
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertFalse($this->cleaner->isXssFound());
        }
    }
}
