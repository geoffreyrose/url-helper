<?php

namespace UrlHelper;

class UrlHelper
{
    /**
     * Validates whether a string is a valid domain name.
     *
     * @param  string  $domain  The domain name to validate.
     * @return bool True if the domain is valid, false otherwise.
     */
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

    /**
     * Extracts the hostname from a URL, returning null if the host is not a valid domain.
     *
     * @param  string  $url  The URL to parse.
     * @return string|null The hostname, or null if not found or invalid.
     */
    public function getHostname(string $url): ?string
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if (!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        $host = parse_url($privateUrl)['host'] ?? null;
        if ($host && $this->isValidDomainName($host)) {
            return $host;
        } else {
            return null;
        }
    }

    /**
     * Extracts the scheme (e.g. "https", "http") from a URL.
     *
     * @param  string  $url  The URL to parse.
     * @return string|null The scheme, or null if none is present.
     */
    public function getScheme(string $url): ?string
    {
        $scheme = null;
        if (str_contains($url, '://')) {
            $scheme = explode('://', $url)[0];
        }

        return $scheme;
    }

    /**
     * Extracts the root hostname (registrable domain) from a URL, stripping subdomains.
     *
     * @param  string  $url  The URL to parse.
     * @return string|null The root hostname, or null if not found or invalid.
     */
    public function getRootHostname(string $url): ?string
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if (!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        $host = parse_url($privateUrl)['host'] ?? null;
        if ($host && $this->isValidDomainName($host)) {
            preg_match("/([a-z\d-]{1,63}\.[a-z]{1,63}(\.[a-z]{2})?)$/i", $host, $matches);

            return $matches[0] ?? null;
        }

        return null;
    }

    /**
     * Returns the URL with the scheme removed, optionally trimming a trailing slash.
     *
     * @param  string  $url  The URL to process.
     * @param  bool  $trimTrailingSlash  Whether to remove a trailing slash from the path.
     * @return string|null The URL without scheme, or null if the URL is invalid.
     */
    public function getUrlWithoutScheme(string $url, bool $trimTrailingSlash = false): ?string
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if (!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        if (!$this->getValidURL($privateUrl)) {
            return null;
        }

        $urlWithoutScheme = parse_url($privateUrl)['host'] ?? null;
        if ($urlWithoutScheme === null) {
            return null; // @codeCoverageIgnore
        }

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

    /**
     * Returns a normalized, valid URL string, or null if the URL is invalid.
     *
     * @param  string  $url  The URL to validate and normalize.
     * @return string|null The normalized URL, or null if invalid.
     */
    public function getValidURL(string $url): ?string
    {
        $scheme = $this->getScheme($url);
        if (!$scheme) {
            return null;
        }

        $host = $this->getHostname($url);
        if (!$host) {
            return null;
        }

        $slug = $this->stringReplaceFirst($scheme . '://', '', $url);
        $slug = $this->stringReplaceFirst($host, '', $slug);

        return $scheme . '://' . $host . $slug;
    }

    /**
     * Converts an android-app:// URL to its https:// equivalent.
     *
     * @param  string  $url  The android-app URL to convert.
     * @return string|null The converted https URL, or null if input is not an android-app URL.
     */
    public function convertAndroidAppToHttps(string $url): ?string
    {
        if (!str_starts_with($url, 'android-app://')) {
            return null;
        }

        $url = $this->stringReplaceFirst('android-app://', '', $url);

        [$authority, $path] = array_pad(explode('/', $url, 2), 2, '');

        if ($authority === '') {
            return null;
        }

        if ($this->stringStartsWithInArray($authority, ['org.', 'com.', 'net.', 'io.'])) {
            $authority = implode('.', array_reverse(explode('.', $authority)));
        }

        $newUrl = 'https://' . $authority;
        if ($path !== '') {
            $newUrl .= '/' . $path;
        }

        return $newUrl;
    }

    /**
     * Extracts the pathname from a URL, normalizing hash-bang fragments and stripping colon-delimited path segments.
     *
     * @param  string  $url  The URL to parse.
     * @return string|null The pathname, or null if the URL is invalid.
     */
    public function getPathname(string $url): ?string
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if (!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        if (!$this->getValidURL($privateUrl)) {
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

    /**
     * Parses and returns query parameters from a URL as an associative array.
     *
     * @param  string  $url  The URL to parse.
     * @return array<mixed, mixed>|null The query parameters, or null if the URL is invalid or has no query string.
     */
    public function getParameters(string $url): ?array
    {
        $privateUrl = $url;
        $scheme = $this->getScheme($url);
        if (!$scheme) {
            $privateUrl = 'https://' . $privateUrl;
        }

        if (!$this->getValidURL($privateUrl)) {
            return null;
        }

        $privateUrl = $this->getValidURL($privateUrl);
        $parse = parse_url($privateUrl);
        if (!is_array($parse) || !isset($parse['scheme']) || !isset($parse['host'])) {
            return null; // @codeCoverageIgnore
        }
        $privateUrl = str_replace($parse['scheme'] . '://' . $parse['host'], '', $privateUrl);

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

    /**
     * Replaces only the first occurrence of a substring within a string.
     *
     * @param  string  $search  The substring to find.
     * @param  string  $replace  The replacement string.
     * @param  string  $subject  The string to search within.
     * @return string The resulting string after the replacement.
     */
    private static function stringReplaceFirst(string $search, string $replace, string $subject): string
    {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject; // @codeCoverageIgnore
    }

    /**
     * Checks whether a string starts with any of the provided prefixes.
     *
     * @param  string  $string  The string to test.
     * @param  string[]  $startStrings  The list of prefixes to check against.
     * @return bool True if the string starts with any of the given prefixes.
     */
    private static function stringStartsWithInArray(string $string, array $startStrings): bool
    {
        foreach ($startStrings as $startString) {
            if (str_starts_with($string, $startString)) {
                return true;
            }
        }

        return false;
    }
}
