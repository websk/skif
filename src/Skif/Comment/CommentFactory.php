<?php

namespace Skif\Comment;


class CommentFactory extends \Skif\Factory
{
    /**
     * @param $id
     * @return null|\Skif\Comment\Comment
     */
    public static function loadComment($id)
    {
        $class_name = '\Skif\Comments\Comment';

        return self::createAndLoadObject($class_name, $id);
    }
} 