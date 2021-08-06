<?php

namespace WebSK\Skif\Redirect;

use WebSK\Entity\Entity;
use WebSK\Utils\HTTP;

/**
 * Class Redirect
 * @package WebSK\Skif\Redirect
 */
class Redirect extends Entity
{
    const DB_TABLE_NAME = 'redirect_rewrites';

    const REDIRECT_KIND_STRING = 1;
    const REDIRECT_KIND_REGEXP = 2;

    const REDIRECT_KINDS_ARR = [
        self::REDIRECT_KIND_STRING => 'строка',
        self::REDIRECT_KIND_REGEXP => 'регексп'
    ];

    const _SRC = 'src';
    protected string $src;

    const _DST = 'dst';
    protected string $dst;

    const _CODE = 'code';
    protected int $code = HTTP::STATUS_MOVED_PERMANENTLY;

    const _KIND = 'kind';
    protected int $kind = self::REDIRECT_KIND_STRING;

    /**
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }

    /**
     * @param string $src
     */
    public function setSrc(string $src): void
    {
        $this->src = $src;
    }

    /**
     * @return string
     */
    public function getDst(): string
    {
        return $this->dst;
    }

    /**
     * @param string $dst
     */
    public function setDst(string $dst): void
    {
        $this->dst = $dst;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getKind(): int
    {
        return $this->kind;
    }

    /**
     * @param int $kind
     */
    public function setKind(int $kind): void
    {
        $this->kind = $kind;
    }
}
