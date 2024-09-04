<div style="text-align: center;"> 

[![Latest Stable Version](https://img.shields.io/packagist/v/geoffreyrose/url-helper?style=flat-square)](https://packagist.org/packages/geoffreyrose/url-helper)
[![Total Downloads](https://img.shields.io/packagist/dt/geoffreyrose/url-helper?style=flat-square)](https://packagist.org/packages/geoffreyrose/url-helper/stats)
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

```
public function isValidDomainName(string $domain): bool
```

```
public function getHostname(string $url): ?string
```

```
public function getScheme(string $url): ?string
```

```
public function getRootHostname(string $url): ?string
```

```
public function getUrlWithoutScheme(string $url, bool $trimTrailingSlash=false): string
```

```
public function getValidURL(string $url): ?string
```

```
public function convertAndroidAppToHttps(string $url): string
```

```
public function getPathname(string $url): string
```

```
public function getParameters(string $url): ?array
```


### Run Tests

```
$ ./vendor/bin/phpunit

// or with coverage 

$ XDEBUG_MODE=coverage ./vendor/bin/phpunit
```
