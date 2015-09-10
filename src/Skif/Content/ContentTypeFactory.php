<?php

namespace Skif\Content;


class ContentTypeFactory extends \Skif\Factory
{
    /**
     * @param $content_type
     * @return null|\Skif\Content\ContentType
     */
    public static function loadContentTypeByType($content_type) {
        $class_name = '\Skif\Content\ContentType';

        $id = \Skif\Content\ContentTypeFactory::getIdByContentType($content_type);
        return self::createAndLoadObject($class_name, $id);
    }

    public static function getIdByContentType($content_type)
    {
        $query = "SELECT id FROM content_types WHERE type=?";
        return \Skif\DB\DBWrapper::readField($query, array($content_type));
    }

}