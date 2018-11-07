<?php

namespace WebSK\Skif;

class Request extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'request';
    }
}
