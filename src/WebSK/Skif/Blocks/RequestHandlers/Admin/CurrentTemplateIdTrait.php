<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

trait CurrentTemplateIdTrait
{
    public const string COOKIE_CURRENT_TEMPLATE_ID = 'skif_blocks_current_template_id';

    /**
     * Тема
     * @return string
     */
    public function getCurrentTemplateId(): int
    {
        if (array_key_exists(self::COOKIE_CURRENT_TEMPLATE_ID, $_COOKIE)) {
            return $_COOKIE[self::COOKIE_CURRENT_TEMPLATE_ID];
        }

        return 1;
    }

    public function setCurrentTemplateId(int $template_id): void
    {
        $delta = null;
        setcookie(self::COOKIE_CURRENT_TEMPLATE_ID, $template_id, $delta, '/');
    }
}