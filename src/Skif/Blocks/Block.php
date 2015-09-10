<?php

namespace Skif\Blocks;


class Block
{
    // default values for new block
    //public $_original_obj; // what was loaded from database
    public $id;
    public $theme; // think over!!! theme is not attribute of block
    public $status = 0;
    public $weight = 1;
    public $region = '';
    public $custom = 0;
    public $throttle = 0;
    public $visibility = 3;
    public $pages = '+ ^';
    public $title = '';
    public $cache = 8; // default for new blocks
    public $body = '';
    public $info = '';
    public $format = 3;
    public $is_loaded = false;

    public function __construct()
    {

    }

    /**
     * @param $block_id
     * @throws \Exception
     * @return bool|null|object|\stdClass
     */
    public function load($block_id)
    {
        if (!$block_id) {
            return false;
        }

        $original_obj= \Skif\DB\DBWrapper::readObject(
            "SELECT * FROM blocks WHERE id = ?",
            array($block_id)
        );

        foreach ($original_obj as $field => $value) {
            $this->$field = $value;
        }

        // some drupal magic: 'no region' is stored as empty string, but processed as BLOCK_REGION_NONE (-1)

        if ($this->region == '') {
            $this->region = \Skif\Constants::BLOCK_REGION_NONE;
        }

        $this->is_loaded = true;

        return true;
    }

    /**
     * Был ли загружен блок
     * @return bool
     */
    public function isLoaded()
    {
        return $this->is_loaded;
    }

    public function save()
    {
        if (($this->id == '')) { // new block

            try {
                $theme = $this->getTheme();

                \Skif\DB\DBWrapper::query(
                    'INSERT INTO blocks (theme, status, weight, region, custom, throttle, visibility, pages, title, cache, body, info, format)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    array($theme, 0, 0, '', 0, 0, 0, $this->pages, '', 8, $this->body, $this->info, $this->format)
                );
            }
            catch (\PDOException $e) {
                $duplicate_block_pattern = "/Duplicate entry '.*' for key 'info'/";
                if (preg_match($duplicate_block_pattern, $e->getMessage())) {
                    return false;
                } else {
                    throw new \PDOException("\r\nUrl: ".$_SERVER['REQUEST_URI']."\r\n".$e->getMessage());
                }
            }
            $this->id = \Skif\DB\DBWrapper::lastInsertId();
            \Skif\Blocks\BlockFactory::removeFromCacheById($this->id);

            \Skif\Logger\Logger::logObjectEvent($this, 'создание');
        } else { // existing block
            \Skif\DB\DBWrapper::query(
                "UPDATE blocks
                SET visibility = ?, pages = ?, weight = ?, status = ?, region = ?, cache = ?, body = ?, info = ?, format = ?
                WHERE id = ?",
                array($this->visibility, $this->pages, $this->weight, $this->status, $this->region, $this->cache,
                    $this->body, $this->info, $this->format, $this->id)
            );

            \Skif\Logger\Logger::logObjectEvent($this, 'изменение');

            \Skif\Blocks\BlockFactory::removeFromCacheById($this->id);
        }

        return true;
    }

    /**
     * ID блока
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Регион блока
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Вес блока
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Заголовок блока
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Содержимое блока
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Формат блока
     * @return int
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Условия видимости для блока
     * @return string
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Тема
     * @return mixed
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * Контекст кэширования
     * @return int
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Вывод содержимого блока с учетом PHP - кода
     * @return string
     */
    public function renderBlockContent()
    {
        if ($this->getFormat() == \Skif\Constants::BLOCK_FORMAT_TYPE_PHP) {
            return $this->evalContentPHPBlock();
        }

        return $this->getBody();
    }

    /**
     * Выполняет PHP код в блоке и возвращает результат
     * @return string
     */
    public function evalContentPHPBlock()
    {
        ob_start();
        print eval('?>'. $this->getBody());
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    public function delete()
    {
        $sql = "DELETE FROM blocks WHERE id=?";
        \Skif\DB\DBWrapper::query( $sql, array($this->getId()));

        $sql = "DELETE FROM blocks_roles WHERE block_id=?";
        \Skif\DB\DBWrapper::query( $sql, array($this->getId()));

        \Skif\Blocks\BlockFactory::removeFromCacheById($this->getId());

        \Skif\Logger\Logger::logObjectEvent($this, 'удаление');

        return true;
    }
}