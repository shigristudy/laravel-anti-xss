<?php

namespace Kabeer\LaravelAntiXss\Tests\Unit;

use Kabeer\LaravelAntiXss\Services\Cleaners\JavaScriptCleaner;

class JavaScriptXssTest extends BaseCaseTest
{
    private JavaScriptCleaner $cleaner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleaner = new JavaScriptCleaner($this->config);
    }

    public function testBasicJavaScriptInjection()
    {
        $tests = [
            '<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>' => '',
            '<IMG SRC="javascript:alert(\'XSS\');">' => '<IMG SRC="(\'XSS\');">',
            '<IMG SRC=javascript:alert(\'XSS\')>' => '<IMG >',
            '<IMG SRC=JaVaScRiPt:alert(\'XSS\')>' => '<IMG >',
            '<IMG SRC=`javascript:alert("RSnake says, \'XSS\'")` >' => '<IMG >'
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testEncodedJavaScriptInjection()
    {
        $tests = [
            '<IMG SRC=&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;>' => '<IMG >',
            '<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>' => '<IMG >',
            '<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>' => '<IMG >'
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testObfuscatedJavaScriptInjection()
    {
        $tests = [
            '<IMG SRC="jav ascript:alert(\'XSS\');">' => '<IMG SRC="(\'XSS\');">',
            '<IMG SRC="jav&#x09;ascript:alert(\'XSS\');">' => '<IMG SRC="(\'XSS\');">',
            '<IMG SRC=java\0script:alert("XSS")>' => '<IMG >',
            '<IMG SRC=" &#14; javascript:alert(\'XSS\');">' => '<IMG SRC=" &#14; (\'XSS\');">',
            '<a href="javas/**/cript:alert(\'XSS\');">' => '<a href="">',
            '<a href="javascript:test">' => '<a href="">'
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testDataUriInjection()
    {
        $tests = [
            '<a href="data:">' => '<a href="">',
            '<a href="d a t a : ">' => '<a href="">',
            '<a href="data: html/text;">' => '<a href="">',
            '<a href="data:html/text;">' => '<a href="">',
            '<a href="data:html /text;">' => '<a href="">',
            '<a href="data: image/text;">' => '<a href="">',
            '<img src="data: aaa/text;">' => '<img src="">'
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertTrue($this->cleaner->isXssFound());
        }
    }

    public function testSafeUrls()
    {
        $tests = [
            '<a href="/javascript/a">' => '<a href="/javascript/a">',
            '<a href="http://aa.com">' => '<a href="http://aa.com">',
            '<a href="https://aa.com">' => '<a href="https://aa.com">',
            '<a href="mailto:me@ucdok.com">' => '<a href="mailto:me@ucdok.com">',
            '<a href="#hello">' => '<a href="#hello">',
            '<a href="other">' => '<a href="other">'
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, $this->cleaner->clean($input));
            $this->assertFalse($this->cleaner->isXssFound());
        }
    }
}
