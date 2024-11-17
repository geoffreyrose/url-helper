<?php

namespace UrlHelper\Facades;

class URLHelper extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \UrlHelper\UrlHelper::class;
    }
}
