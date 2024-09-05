<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use UrlHelper\UrlHelper;

class UrlHelperTest extends TestCase
{
    /**
     * test isValidDomainName
     */
    public function testIsValidDomainName()
    {
        $helper = new UrlHelper();
        $this->assertFalse($helper->isValidDomainName('https://example.com'));
        $this->assertTrue($helper->isValidDomainName('example.com'));
        $this->assertTrue($helper->isValidDomainName('test.example.com'));
        $this->assertTrue($helper->isValidDomainName('example.com.uk'));
        $this->assertFalse($helper->isValidDomainName('Frodo Baggins'));

        // test different parts being too long
        $this->assertFalse($helper->isValidDomainName('abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz.com'));
        $this->assertFalse($helper->isValidDomainName('abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz.example.com'));
        $this->assertFalse($helper->isValidDomainName('example.com.abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz'));
        $this->assertFalse($helper->isValidDomainName('abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.com'));
        $this->assertFalse($helper->isValidDomainName('example.com.a'));
    }

    /**
     * test getHostname
     */
    public function testGetHostname()
    {
        $helper = new UrlHelper();
        $this->assertEquals('example.com', $helper->getHostname('https://example.com'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/'));
        $this->assertEquals('www.example.com', $helper->getHostname('https://www.example.com'));
        $this->assertEquals('app.example.com', $helper->getHostname('https://app.example.com/'));
        $this->assertEquals('app.example.com', $helper->getHostname('https://app.example.com'));
        $this->assertEquals('123.app.example.com', $helper->getHostname('https://123.app.example.com/'));
        $this->assertEquals('123.app.example.com', $helper->getHostname('https://123.app.example.com'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/test'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/test/'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/test/test'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/test/test/'));

        $this->assertEquals('example.com', $helper->getHostname('https://example.com?test=12345'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/?test=12345'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/test?test=12345'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/test/?test=12345'));

        $this->assertEquals('example.com', $helper->getHostname('https://example.com/filter:test:12345'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/filter:test:12345/filter:abc:xyz'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/test/filter:test:12345'));
        $this->assertEquals('example.com', $helper->getHostname('https://example.com/test/filter:test:12345/filter:abc:xyz'));

        $this->assertEquals('example.com', $helper->getHostname('example.com'));

        $this->assertEquals(null, $helper->getHostname('Bilbo Baggins'));
    }

    /**
     * test getRootHostname
     */
    public function testGetRootHostname()
    {
        $helper = new UrlHelper();
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com/'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com/'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com/test'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com/test/'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com/test/abc'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com/test/abc/'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com/test/abc/123'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://example.com/test/abc/123/'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://www.app.example.com'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://www.app.example.com/'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/test'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/test/'));

        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com?test=12345'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/?test=12345'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/test?test=12345'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/test/?test=12345'));

        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/filter:test:12345'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/filter:test:12345/filter:abc:xyz'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/test/filter:test:12345'));
        $this->assertEquals('example.com', $helper->getRootHostname('https://app.example.com/test/filter:test:12345/filter:abc:xyz'));

        $this->assertEquals('example.com', $helper->getRootHostname('example.com'));

        $this->assertEquals(null, $helper->getRootHostname('Samwise Gamgee'));
    }

    /**
     * test getUrlWithoutScheme
     */
    public function testGetUrlWithoutScheme()
    {
        $helper = new UrlHelper();
        $this->assertEquals('example.com', $helper->getUrlWithoutScheme('https://example.com'));
        $this->assertEquals('example.com/', $helper->getUrlWithoutScheme('https://example.com/'));
        $this->assertEquals('example.com', $helper->getUrlWithoutScheme('https://example.com/', true));
        $this->assertEquals('example.com', $helper->getUrlWithoutScheme('https://example.com'));
        $this->assertEquals('www.example.com', $helper->getUrlWithoutScheme('https://www.example.com'));
        $this->assertEquals('app.example.com/', $helper->getUrlWithoutScheme('https://app.example.com/'));
        $this->assertEquals('example.com/test', $helper->getUrlWithoutScheme('https://example.com/test'));
        $this->assertEquals('example.com/test/', $helper->getUrlWithoutScheme('https://example.com/test/'));
        $this->assertEquals('example.com/test', $helper->getUrlWithoutScheme('https://example.com/test/', true));

        $this->assertEquals('example.com?test=12345', $helper->getUrlWithoutScheme('https://example.com?test=12345'));
        $this->assertEquals('example.com?test=12345', $helper->getUrlWithoutScheme('https://example.com/?test=12345', true));
        $this->assertEquals('app.example.com/?test=12345', $helper->getUrlWithoutScheme('https://app.example.com/?test=12345'));
        $this->assertEquals('example.com/test?test=12345', $helper->getUrlWithoutScheme('https://example.com/test?test=12345'));
        $this->assertEquals('example.com/test/?test=12345', $helper->getUrlWithoutScheme('https://example.com/test/?test=12345'));
        $this->assertEquals('example.com/test?test=12345', $helper->getUrlWithoutScheme('https://example.com/test/?test=12345', true));

        $this->assertEquals('example.com/filter:test:12345', $helper->getUrlWithoutScheme('https://example.com/filter:test:12345'));
        $this->assertEquals('example.com/filter:test:12345/filter:abc:xyz', $helper->getUrlWithoutScheme('https://example.com/filter:test:12345/filter:abc:xyz'));
        $this->assertEquals('example.com/test/filter:test:12345', $helper->getUrlWithoutScheme('https://example.com/test/filter:test:12345'));
        $this->assertEquals('example.com/test/filter:test:12345/filter:abc:xyz', $helper->getUrlWithoutScheme('https://example.com/test/filter:test:12345/filter:abc:xyz'));

        $this->assertEquals('example.com/test/filter:test:12345/filter:abc:xyz', $helper->getUrlWithoutScheme('example.com/test/filter:test:12345/filter:abc:xyz'));

        $this->assertEquals(null, $helper->getUrlWithoutScheme('Dark Lord Sauron'));
    }

    /**
     * test getValidURL
     */
    public function testGetValidURL()
    {
        $helper = new UrlHelper();
        $this->assertEquals(null, $helper->getValidURL('example.com'));
        $this->assertEquals(null, $helper->getValidURL('https://example'));
        $this->assertEquals('https://example.com', $helper->getValidURL('https://example.com'));
        $this->assertEquals('https://example.com/', $helper->getValidURL('https://example.com/'));
        $this->assertEquals('https://example.com/test', $helper->getValidURL('https://example.com/test'));
        $this->assertEquals('https://example.com/TEST', $helper->getValidURL('https://example.com/TEST'));
        $this->assertEquals('https://example.com/test/', $helper->getValidURL('https://example.com/test/'));
        $this->assertEquals('https://example.com/test/abc', $helper->getValidURL('https://example.com/test/abc'));
        $this->assertEquals('https://example.com/test/abc/', $helper->getValidURL('https://example.com/test/abc/'));
        $this->assertEquals('https://example.com/test/abc/123', $helper->getValidURL('https://example.com/test/abc/123'));
        $this->assertEquals('https://example.com/test/abc/123/', $helper->getValidURL('https://example.com/test/abc/123/'));
        $this->assertEquals('https://example.com/test/abc/123/456', $helper->getValidURL('https://example.com/test/abc/123/456'));
        $this->assertEquals('https://example.com/test/abc/123/456/', $helper->getValidURL('https://example.com/test/abc/123/456/'));
        $this->assertEquals('https://example.com/test/abc/123/456/789', $helper->getValidURL('https://example.com/test/abc/123/456/789'));
        $this->assertEquals('https://example.com/test/abc/123/456/789/', $helper->getValidURL('https://example.com/test/abc/123/456/789/'));

        $this->assertEquals('https://example.com?test=12345', $helper->getValidURL('https://example.com?test=12345'));
        $this->assertEquals('https://example.com/?test=12345', $helper->getValidURL('https://example.com/?test=12345'));
        $this->assertEquals('https://example.com/test?test=12345', $helper->getValidURL('https://example.com/test?test=12345'));
        $this->assertEquals('https://example.com/test/?test=12345', $helper->getValidURL('https://example.com/test/?test=12345'));

        $this->assertEquals('https://example.com/filter:test:12345', $helper->getValidURL('https://example.com/filter:test:12345'));
        $this->assertEquals('https://example.com/filter:test:12345/filter:abc:xyz', $helper->getValidURL('https://example.com/filter:test:12345/filter:abc:xyz'));
        $this->assertEquals('https://example.com/test/filter:test:12345', $helper->getValidURL('https://example.com/test/filter:test:12345'));
        $this->assertEquals('https://example.com/test/filter:test:12345/filter:abc:xyz', $helper->getValidURL('https://example.com/test/filter:test:12345/filter:abc:xyz'));

        $this->assertEquals(null, $helper->getValidURL('Peregrin Took'));
        $this->assertEquals(null, $helper->getValidURL('Merry Brandybuck'));
    }

    /**
     * test convertAndroidAppToHttps
     */
    public function testConvertAndroidAppToHttps()
    {
        $helper = new UrlHelper();
        $this->assertEquals('https://example.com', $helper->convertAndroidAppToHttps('android-app://example.com'));
        $this->assertEquals('https://example.com', $helper->convertAndroidAppToHttps('android-app://example.com/'));
        $this->assertEquals('https://app.example.com', $helper->convertAndroidAppToHttps('android-app://app.example.com'));
        $this->assertEquals('https://example.com', $helper->convertAndroidAppToHttps('android-app://com.example'));
        $this->assertEquals('https://app.example.com', $helper->convertAndroidAppToHttps('android-app://com.example.app'));

        $this->assertEquals(null, $helper->convertAndroidAppToHttps('Dark Lord Sauron'));
    }

    /**
     * test getPathname
     */
    public function testGetPathname()
    {
        $helper = new UrlHelper();
        $this->assertEquals('/', $helper->getPathname('https://example.com//'));
        $this->assertEquals('/', $helper->getPathname('https://example.com/'));
        $this->assertEquals('/', $helper->getPathname('https://example.com'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/#abc'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test#abc'));
        $this->assertEquals('/test#/abc', $helper->getPathname('https://example.com/test#/abc'));
        $this->assertEquals('/test/abc', $helper->getPathname('https://example.com/test/abc'));
        $this->assertEquals('/test/abc', $helper->getPathname('https://example.com/test/abc/'));
        $this->assertEquals('/test/abc', $helper->getPathname('https://example.com/test/abc//'));

        $this->assertEquals('/test', $helper->getPathname('https://example.com/#/test/#abc'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/#/test#abc'));
        $this->assertEquals('/test#/abc', $helper->getPathname('https://example.com/#/test#/abc'));
        $this->assertEquals('/test/abc', $helper->getPathname('https://example.com/#/test/abc'));

        $this->assertEquals('/test', $helper->getPathname('https://example.com/#!/test/#abc'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/#!/test#abc'));
        $this->assertEquals('/test#/abc', $helper->getPathname('https://example.com/#!/test#/abc'));
        $this->assertEquals('/test/abc', $helper->getPathname('https://example.com/#!/test/abc'));

        $this->assertEquals('/', $helper->getPathname('https://example.com?test=12345'));
        $this->assertEquals('/', $helper->getPathname('https://example.com/?test=12345'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/#abc?test=12345'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test#abc?test=12345'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/#/test/#abc?test=12345'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/#/test#abc?test=12345'));

        $this->assertEquals('/test', $helper->getPathname('https://example.com/#!/test/#abc/filter:test:12345'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/#!/test#abc/filter:test:12345/filter:abc:xyz'));
        $this->assertEquals('/', $helper->getPathname('https://example.com/filter:test:12345'));
        $this->assertEquals('/', $helper->getPathname('https://example.com/filter:test:12345/filter:abc:xyz'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/filter:test:12345'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/filter:$123_12:12345:123:test'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/filter:test:12345/filter:abc:xyz'));

        $this->assertEquals('/', $helper->getPathname('https://example.com//https://www.google.com/'));
        $this->assertEquals('/', $helper->getPathname('https://example.com/https://www.google.com/'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/#abc/https://www.google.com/https://www.google.com/'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/https://www.google.com/#abc'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/https://www.google.com/#/abc'));

        $this->assertEquals('/', $helper->getPathname('https://example.com//http://www.google.com/'));
        $this->assertEquals('/', $helper->getPathname('https://example.com/http://www.google.com/'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/#abc/http://www.google.com/https://www.google.com/'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/http://www.google.com/#abc'));
        $this->assertEquals('/test', $helper->getPathname('https://example.com/test/http://www.google.com/#/abc'));

        $this->assertEquals('/test', $helper->getPathname('https://example.com/#!/test/#!/#abc/filter:test:12345'));

        $this->assertEquals(null, $helper->getPathname('Dark Lord Sauron'));
    }

    /**
     * test getParameters
     */
    public function testGetParameters()
    {
        $helper = new UrlHelper();
        $this->assertEquals(null, $helper->getParameters('example.com/test/#abc'));
        $this->assertEquals(null, $helper->getParameters('https://example.com/test/#abc'));
        $this->assertEquals(['test' => 123], $helper->getParameters('https://example.com/test?test=123'));
        $this->assertEquals(null, $helper->getParameters('https://example.com/test#abc'));
        $this->assertEquals(['test' => 123], $helper->getParameters('https://example.com/test/?test=123'));
        $this->assertEquals(['test' => 123, 'abc' => 'xyz'], $helper->getParameters('https://example.com/test/?test=123&abc=xyz'));
        $this->assertEquals(['back' => 'https://www.google.com'], $helper->getParameters('https://example.com/test/abc?back=https%3A%2F%2Fwww.google.com'));
        $this->assertEquals(['test' => 123, 'abc' => 'xyz'], $helper->getParameters('https://example.com/test#/abc?test=123&abc=xyz'));

        $this->assertEquals(null, $helper->getParameters('https://example.com/#/test/#abc'));
        $this->assertEquals(['test' => 123], $helper->getParameters('https://example.com/#/test?test=123'));

        $this->assertEquals(null, $helper->getParameters('https://example.com/#!/test/#abc'));
        $this->assertEquals(['test' => 123], $helper->getParameters('https://example.com/#!/test?test=123'));
        $this->assertEquals(['s' => 'https://www.google.com/'], $helper->getParameters('https://example.com/?s=https://www.google.com/'));
        $this->assertEquals(['s' => 'http://www.google.com/'], $helper->getParameters('https://example.com/?s=http://www.google.com/'));

        $this->assertEquals(null, $helper->getParameters('Dark Lord Sauron'));
    }

    /**
     * test getScheme
     */
    public function testGetScheme()
    {
        $helper = new UrlHelper();

        $this->assertEquals('https', $helper->getScheme('https://example.com'));
        $this->assertEquals('https', $helper->getScheme('https://example.com/'));
        $this->assertEquals('https', $helper->getScheme('https://example.com/test'));
        $this->assertEquals('http', $helper->getScheme('http://example.com/test/'));
        $this->assertEquals(null, $helper->getScheme('example.com'));
        $this->assertEquals(null, $helper->getScheme('Dark Lord Sauron'));
    }
}
