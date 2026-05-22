<?php

namespace UrlHelper\Facades;

use Illuminate\Support\Facades\Facade;

class URLHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \UrlHelper\UrlHelper::class;
    }
}
