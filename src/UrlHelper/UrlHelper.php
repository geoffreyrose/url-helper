<?php

namespace UrlHelper;

class UrlHelper
{
    public function isValidDomainName(string $domain): bool
    {
        return preg_match("/([a-z\d-]{1,63}\.[a-z]{1,63}(\.[a-z]{2})?)$/i", $domain)
            && preg_match('/.{1,255}/i', $domain);
    }

    public function getHostname(string $url): ?string
    {
        $d = $url;
        if (!str_starts_with($d, 'https://') && !str_starts_with($d, 'http://')) {
            $d = 'https://' . $d;
        }

        $host = parse_url($d)['host'] ?? null;
        if ($host && $this->isValidDomainName($host)) {
            return str_replace('www.', '', parse_url($d)['host']);
        } else {
            return null;
        }
    }

    public function getRootHostname(string $url): ?string
    {
        $d = $url;
        if (!str_starts_with($d, 'https://') && !str_starts_with($d, 'http://')) {
            $d = 'https://' . $d;
        }

        $host = parse_url($d)['host'] ?? null;
        if ($host && $this->isValidDomainName($host)) {
            preg_match("/([a-z\d-]{1,63}\.[a-z]{1,63}(\.[a-z]{2})?)$/i", $host, $matches);

            return $matches[0] ?? null;
        }

        return null;
    }

    public function getUrlWithoutProtocol(string $url): string
    {
        $d = $url;
        if (!str_starts_with($d, 'https://') && !str_starts_with($d, 'http://')) {
            $d = 'https://' . $d;
        }

        $url = parse_url($d)['host'];
        if (isset(parse_url($d)['path'])) {
            $url .= parse_url($d)['path'];
        }

        if (isset(parse_url($d)['query'])) {
            $url .= '?' . parse_url($d)['query'];
        }

        return rtrim($url, '/');
    }

    public function getValidURL(string $url): string|null
    {
        $host = $this->getHostname($url);
        if (!$host) {
            return null;
        }

        $slug = str_replace('http://', '', $url);
        $slug = str_replace('https://', '', $slug);
        $slug = str_replace($host, '', $slug);

        return 'https://' . $host . $slug;
    }

    public function convertAndroidAppToHttps(string $url): string
    {
        $new_url = '';
        if (isset($url) && str_starts_with($url, 'android-app://')) {
            $url = $this->stringReplaceFirst('android-app://', '', $url);
            if ($this->strStartsWithInArray($url, ['org.', 'com.', 'net.', 'io.'])) {
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

        return 'https://' . $new_url;
    }

    public function getPathname(string $url): string
    {
        if (str_contains($url, '/#!/')) {
            $url = str_replace('/#!/', '/', $url);
        } elseif (str_contains($url, '/#/')) {
            $url = str_replace('/#/', '/', $url);
        }

        $pathname = parse_url($url)['path'] ?? '';
        $fragment = parse_url($url)['fragment'] ?? '';

        if ($fragment) {
            if (str_contains($url, '#/')) {
                $pathname = $pathname . '#' . $fragment;
            } else {
                $pathname = str_replace('#' . $fragment, '', $pathname);
            }
        }

        if (str_contains($pathname, 'http://')) {
            $pathname = explode('http://', $pathname)[0];
        }

        if (str_contains($pathname, 'https://')) {
            $pathname = explode('https://', $pathname)[0];
        }

        if ($pathname) {
            $pathname = rtrim($pathname, '/');
        }

        if ($pathname) {
            if (str_starts_with($pathname, '/#!/')) {
                $pathname = str_replace('/#!/', '/', $pathname);
            }
        }

        if ($pathname) {
            if (str_starts_with($pathname, '/#/')) {
                $pathname = str_replace('/#/', '/', $pathname);
            }
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

    public function getParameters(string $url): array|null
    {
        try {
            $parse = parse_url($url);
            $url = str_replace($parse['scheme'] . '://' . $parse['host'], '', $url);
            if (str_starts_with($url, '/#!/')) {
                $url = str_replace('/#!/', '/', $url);
            }

            if (str_starts_with($url, '/#/')) {
                $url = str_replace('/#/', '/', $url);
            }

            if (str_contains($url, '#/')) {
                $url = str_replace('#/', '', $url);
            }

            $query = parse_url($url)['query'] ?? null;
            $parameters = null;
            if ($query) {
                parse_str($query, $request_query);
                $parameters = $request_query;
            }

            // at some point deal with url paths  like /text:123:abc
            // add in /text:123:abc ['text' => ['123', 'abc']]
            // and /text:123 to $parameters array as ['text' => 123]

        } catch (\Throwable $th) {
            $parameters = null;
        }

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

    static private function strStartsWithInArray(string $string, array $startStrings): bool
    {
        foreach ($startStrings as $startString) {
            if (str_starts_with($string, $startString)) {
                return true;
            }
        }
        return false;
    }
}
