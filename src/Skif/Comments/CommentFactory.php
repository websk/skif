<?php

namespace Skif\Comments;


class CommentFactory extends \Skif\Factory
{
    /**
     * @param $id
     * @return null|\Skif\Comments\Comment
     */
    public static function loadComment($id)
    {
        $class_name = '\Skif\Comments\Comment';

        return self::createAndLoadObject($class_name, $id);
    }
} 