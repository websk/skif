<?php

namespace Skif\EditorTabs;

/**
 * Interface InterfaceEditorTabs
 * Если реализован - модель умеет возвращать массив табов для админки.
 * @package Skif\Model
 */
interface InterfaceEditorTabs {
    public function getEditorTabsArr();
}
