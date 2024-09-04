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
