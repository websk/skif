<?php

namespace WebSK\Skif;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 * @package WebSK\Skif
 * @method static UriInterface getUri()
 * @method static mixed getParam($key, $default = null)
 * @method static mixed getAttribute($key, $default = null)
 * @method static mixed getQueryParam($key, $default = null)
 * @see RequestInterface
 */
class Request extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'request';
    }
}
