<?php

namespace UrlHelper;

class UrlHelper
{
    public function isValidDomainName(string $domain): bool
    {
        // domains must be less than or equal to 253 characters in total
        // each subdomain (or subdomain.subdomain, etc) must each be less than or equal to 63 characters
        // country codes must be 2 characters
        // this technically allows for invalid domain names like 'example.com.usa', assuming 'usa' is the country code
        // but that is technically a valid domain name if the tld is 'usa' and the domain is 'com.usa' with a subdomain of 'example'
        return preg_match("/^(?:[a-z\d-]{1,63}\.)*[a-z\d-]{1,63}\.[a-z]{2,63}$/i", $domain)
            && strlen($domain) <= 253;
    }

    public function getHostname(string $url): ?string
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if(!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        $host = parse_url($privateUrl)['host'] ?? null;
        if ($host && $this->isValidDomainName($host)) {
            return $host;
        } else {
            return null;
        }
    }

    public function getScheme(string $url): ?string
    {
        $scheme = null;
        if(str_contains($url, '://')) {
            $scheme = explode('://', $url)[0];
        }

        return $scheme;
    }

    public function getRootHostname(string $url): ?string
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if(!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        $host = parse_url($privateUrl)['host'] ?? null;
        if ($host && $this->isValidDomainName($host)) {
            preg_match("/([a-z\d-]{1,63}\.[a-z]{1,63}(\.[a-z]{2})?)$/i", $host, $matches);

            return $matches[0] ?? null;
        }

        return null;
    }

    public function getUrlWithoutScheme(string $url, bool $trimTrailingSlash=false): ?string
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if(!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        if(!$this->getValidURL($privateUrl)) {
            return null;
        }

        $urlWithoutScheme = parse_url($privateUrl)['host'];
        if (isset(parse_url($privateUrl)['path'])) {
            $urlWithoutScheme .= parse_url($privateUrl)['path'];

            if ($trimTrailingSlash) {
                $urlWithoutScheme = rtrim($urlWithoutScheme, '/');
            }
        }

        if (isset(parse_url($privateUrl)['query'])) {
            $urlWithoutScheme .= '?' . parse_url($privateUrl)['query'];
        }

        return $urlWithoutScheme;
    }

    public function getValidURL(string $url): ?string
    {
        $scheme = $this->getScheme($url);
        if(!$scheme) {
            return null;
        }

        $host = $this->getHostname($url);
        if(!$host) {
            return null;
        }

        $slug = $this->stringReplaceFirst($scheme . '://', '', $url);
        $slug = $this->stringReplaceFirst($host, '', $slug);

        return $scheme . '://' . $host . $slug;
    }

    public function convertAndroidAppToHttps(string $url): ?string
    {
        $new_url = null;
        if (str_starts_with($url, 'android-app://')) {
            $url = $this->stringReplaceFirst('android-app://', '', $url);
            if ($this->stringStartsWithInArray($url, ['org.', 'com.', 'net.', 'io.'])) {
                $parts = array_reverse(explode('.', $url));
                foreach ($parts as $part) {
                    $part = $this->stringReplaceFirst('/', '', $part);

                    $new_url .= $part . '.';
                }

                $new_url = rtrim($new_url, '.');
            } else {
                $new_url = $this->stringReplaceFirst('/', '', $url);
            }
        }

        if($new_url) {
            $new_url = 'https://' . $new_url;
        }

        return $new_url;
    }

    public function getPathname(string $url): ?string
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if(!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        if(!$this->getValidURL($privateUrl)) {
            return null;
        }

        if (str_contains($privateUrl, '/#!/')) {
            $privateUrl = str_replace('/#!/', '/', $privateUrl);
        } elseif (str_contains($privateUrl, '/#/')) {
            $privateUrl = str_replace('/#/', '/', $privateUrl);
        }

        $pathname = parse_url($privateUrl)['path'] ?? '';
        $fragment = parse_url($privateUrl)['fragment'] ?? '';

        if ($fragment) {
            if (str_contains($privateUrl, '#/')) {
                $pathname = $pathname . '#' . $fragment;
            } else {
                $pathname = str_replace('#' . $fragment, '', $pathname);
            }
        }

        $scheme = $this->getScheme($url);
        if ($scheme && str_contains($pathname, $scheme)) {
            $pathname = explode($scheme, $pathname)[0];
        }

        if ($pathname) {
            $pathname = rtrim($pathname, '/');
        }

        if ($pathname) {
            // look for /text:123:abc and remove anything after text
            $pathname = preg_replace('/\/[a-z-0-9]+:\S+/i', '', $pathname);
        }

        if ($pathname === '' || $pathname === null) {
            $pathname = '/';
        }

        return $pathname;
    }

    public function getParameters(string $url): ?array
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if(!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        if(!$this->getValidURL($privateUrl)) {
            return null;
        }
        
        $privateUrl = $this->getValidURL($privateUrl);
        $parse = parse_url($privateUrl);
        $privateUrl = str_replace( $parse['scheme'] . '://' . $parse['host'], '', $privateUrl);
        if (str_starts_with($privateUrl, '/#!/')) {
            $privateUrl = str_replace('/#!/', '/', $privateUrl);
        }

        if (str_starts_with($privateUrl, '/#/')) {
            $privateUrl = str_replace('/#/', '/', $privateUrl);
        }

        if (str_contains($privateUrl, '#/')) {
            $privateUrl = str_replace('#/', '', $privateUrl);
        }

        $query = parse_url($privateUrl)['query'] ?? null;
        $parameters = null;
        if ($query) {
            parse_str($query, $request_query);
            $parameters = $request_query;
        }

        // at some point deal with url paths  like /text:123:abc
        // add in /text:123:abc ['text' => ['123', 'abc']]
        // and /text:123 to $parameters array as ['text' => 123]

        return $parameters;
    }

    static private function stringReplaceFirst(string $search, string $replace, string $subject): string
    {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }

    static private function stringStartsWithInArray(string $string, array $startStrings): bool
    {
        foreach ($startStrings as $startString) {
            if (str_starts_with($string, $startString)) {
                return true;
            }
        }
        return false;
    }
}
