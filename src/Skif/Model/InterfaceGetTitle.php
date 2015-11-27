<?php

namespace Skif\Model;

/**
 * Interface InterfaceGetTitle
 * @package Skif\Model
 * Возвращает экранное, человекочитаемое имя модели, которое можно выводить в админке и т.п.
 */
interface InterfaceGetTitle {
    public function getTitle();
} 