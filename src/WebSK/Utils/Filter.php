<?php

namespace WebSK\Utils;

/**
 * Class Filter
 * @package Skif\Util
 */
class Filter
{
    /** @var bool */
    public $is_positive;

    /** @var bool */
    public $is_negative;

    /** @var string */
    public $mask;

    /** @var string */
    public $sign;

    /** @var string */
    public $target_url;

    /**
     * Filter constructor.
     * @param string $filter_str
     */
    public function __construct(string $filter_str)
    {
        $this->is_positive = false;
        $this->is_negative = false;
        $this->mask = '';
        $this->sign = '';
        $this->target_url = '';

        // TODO: check filter format

        // process sign

        $this->sign = substr($filter_str, 0, 1);

        if ($this->sign == '+') {
            $this->is_positive = true;
        }

        if ($this->sign == '-') {
            $this->is_negative = true;
        }

        // process mask and url

        $mask_source = substr($filter_str, 2);
        $mask_source_arr = explode('=>', $mask_source);

        $this->mask = $mask_source_arr[0];

        if (array_key_exists(1, $mask_source_arr)) {
            $this->target_url = $mask_source_arr[1];
        }
    }

    /**
     * @param string $real_url
     * @return bool
     */
    public function matchesPage(string $real_url = '')
    {
        $page_url = Url::getUriNoQueryString();

        if ($real_url != '') {
            $page_url = $real_url;
        }

        $mask = '@' . $this->mask . '@';
        if (preg_match($mask, $page_url)) {
            return true;
        }

        return false;
    }
}
