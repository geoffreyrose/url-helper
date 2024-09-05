<div style="text-align: center;"> 

[![Latest Stable Version](https://img.shields.io/packagist/v/geoffreyrose/url-helper?style=flat-square)](https://packagist.org/packages/geoffreyrose/url-helper)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/geoffreyrose/url-helper/main.yml?branch=main&style=flat-square)](https://github.com/geoffreyrose/url-helper/actions?query=branch%3Amain)
[![Codecov branch](https://img.shields.io/codecov/c/gh/geoffreyrose/url-helper/main?style=flat-square)](https://app.codecov.io/gh/geoffreyrose/url-helper/branch/main)
[![License](https://img.shields.io/github/license/geoffreyrose/url-helper?style=flat-square)](https://github.com/geoffreyrose/url-helper/blob/main/LICENSE)
</div>

# PHP URL Helper
An easy-to-use PHP helper to parse out different parts of a URL


### Requirements
* PHP 8.0+

### Usage

```
$ composer require geoffreyrose/url-helper
```

```php
<?php

require 'vendor/autoload.php';

use UrlHelper\UrlHelper;

...

$urlHelper = new UrlHelper();
```

### Methods

#### isValidDomainName()
```
isValidDomainName(string $domain): bool

$urlHelper = new UrlHelper();

$urlHelper->isValidDomainName('https://example.com'); // false
$urlHelper->isValidDomainName('example.com'); // true
$urlHelper->isValidDomainName('Frodo Baggins'); // false
```

#### getHostname()
```
getHostname(string $url): ?string

$urlHelper = new UrlHelper();

$urlHelper->getHostname('https://example.com'); // example.com
$urlHelper->getHostname('https://www.example.com')); // www.example.com
$urlHelper->getHostname('Bilbo Baggins'); // null
```

#### getScheme()
```
getScheme(string $url): ?string

$urlHelper = new UrlHelper();

$urlHelper->getScheme('https://example.com'); // https
$urlHelper->getScheme('example.com'); // null
$urlHelper->getScheme('ftp://example.com'); // ftp
$urlHelper->getScheme('Dark Lord Sauron'); // null
```

#### getRootHostname()
```
getRootHostname(string $url): ?string

$urlHelper = new UrlHelper();

$urlHelper->getRootHostname('https://example.com'); // example.com
$urlHelper->getRootHostname('https://www.example.com')); // example.com
$urlHelper->getRootHostname('Samwise Gamge'); // null
```

#### getUrlWithoutScheme()
```
getUrlWithoutScheme(string $url, bool $trimTrailingSlash=false): ?string

$urlHelper = new UrlHelper();

$urlHelper->getUrlWithoutScheme('https://example.com'); // example.com
$urlHelper->getUrlWithoutScheme('https://example.com/', true); // example.com
$urlHelper->getUrlWithoutScheme('https://example.com/test/?abc=123', true); // example.com/test?abc=123
$urlHelper->getUrlWithoutScheme('https://www.example.com')); // www.example.com
$urlHelper->getUrlWithoutScheme('Peregrin Took'); // null
```

#### getValidURL()
```
getValidURL(string $url): ?string

$urlHelper = new UrlHelper();

$urlHelper->getValidURL('https://example.com'); // https://example.com
$urlHelper->getValidURL('https://www.example.com'); // https://www.example.com
$urlHelper->getValidURL('https://example.com/test'); // https://example.com/test
$urlHelper->getValidURL('example.com'); // null
$urlHelper->getValidURL('Merry Brandybuck')); // null
```

#### convertAndroidAppToHttps()
```
convertAndroidAppToHttps(string $url): ?string

$urlHelper = new UrlHelper();

$urlHelper->convertAndroidAppToHttps('android-app://com.example'); // https://example.com
$urlHelper->convertAndroidAppToHttps('android-app://example.com'); // https://example.com
$urlHelper->convertAndroidAppToHttps('Dark Lord Sauron'); // null
```

#### getPathname()
```
getPathname(string $url): ?string

$urlHelper = new UrlHelper();

$urlHelper->getPathname('https://example.com/path/to/somewhere'); // /path/to/somewhere
$urlHelper->getPathname('https://example.com'); // /
$urlHelper->getPathname('https://example.com/test/abc?test=12345')); // /test/abc
```

#### getParameters()
```
getParameters(string $url): ?array

$urlHelper = new UrlHelper();
 
$urlHelper->getParameters('https://example.com/path/to/somewhere?test=12345&test2=abc'); // ['test' => '12345', 'test2' => 'abc']
$urlHelper->getParameters('https://example.com'); // null
$urlHelper->getPathname('Dark Lord Sauron'); // null
```


### Run Tests

```
$ ./vendor/bin/phpunit

// or with coverage 

$ XDEBUG_MODE=coverage ./vendor/bin/phpunit
```
