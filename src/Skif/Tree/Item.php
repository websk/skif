<?php

namespace Skif\Tree;

/**
 * Class Item
 * Добавляет к классу функционал дерева.
 * Класс должен:
 * - поддерживать InterfaceLoad и InterfaceFactory
 * - иметь константы DB_TABLE_NAME и DB_ID
 * - если класс использует activeRecord - поле children_item_ids_arr (его добавляет этот трейт) должно быть включено в $active_record_ignore_fields_arr
 * - вызывать loadChildrenIdsArr при load
 * - вызывать resetTreeRootAndParentCache после удаления
 * @package Skif\Tree
 */
trait Item {

    protected  $parent_id = 0;
    protected $weight = 0;
    protected $children_item_ids_arr = array();

    public function getParentId(){
        return $this->parent_id;
    }

    /**
     * Принимает как глобальные, так и неглобальные имена классов (глобализует сама).
     * @param $class_name
     */
    static public function exceptionIfClassNotCompatibleWithTree($class_name){
        $global_class_name = \Skif\Model\Helper::globalizeClassName($class_name);

        if (!defined($global_class_name . '::DB_ID')) {
            throw new \Exception('class must provide DB_ID constant to use Tree');
        }

        if (!defined($global_class_name . '::DB_TABLE_NAME')) {
            throw new \Exception('class must provide DB_TABLE_NAME constant to use Tree');
        }

        \Skif\Model\Helper::exceptionIfClassNotImplementsInterface($global_class_name, 'Skif\Model\InterfaceLoad');
        \Skif\Model\Helper::exceptionIfClassNotImplementsInterface($global_class_name, 'Skif\Model\InterfaceFactory');
    }

    /**
     * Сразу обновляет данные в базе.
     * @param $new_parent_id
     * @throws \Exception
     */
    public function setParentId($new_parent_id){
        self::exceptionIfClassNotCompatibleWithTree(__CLASS__);

        if (!$this->getId()) {
            // Для объекта, которого ещё нет в базе, только устанавливаем значение поля
            // - чтобы при сохранении поле сразу записалось в базу
            $this->parent_id = $new_parent_id;
            return;
        }
        // CHECKS FIRST
        if ($new_parent_id == $this->getId()) {
            throw new \Exception("Объект не может быть сам себе родителем");
        }

        $all_children_ids_arr = self::getAllChildrenIdsArr($this->getId());

        if (in_array($new_parent_id, $all_children_ids_arr)) {
            throw new \Exception("Объект уже является потомком");
        }

        // UPDATE PARENT_ID

        $old_parent_id = $this->getParentId(); // сохраняем ид старого родителя

        // теперь обновляем данные в объекте и в базе
        $this->parent_id = $new_parent_id;
        \Skif\DB\DBWrapper::query(self::DB_ID, 'update ' . self::DB_TABLE_NAME . ' set parent_id = ? where id = ?',
            array($this->parent_id, $this->getId()));

        // RESET CACHES
        // кэши всегда сбрасываем после изменения данных в БД

        if ($old_parent_id) {
            self::afterUpdate($old_parent_id); // сбрасываем кэш старого родителя
        }

        $this->resetTreeRootAndParentCache();

        self::afterUpdate($this->getId());
    }

    public function getWeight(){
        return $this->weight;
    }

    /**
     * Сразу обновляет данные в базе.
     * @param $weight
     * @throws \Exception
     */
    public function setWeight($weight){
        self::exceptionIfClassNotCompatibleWithTree(__CLASS__);

        if (!$this->getId()) {
            // Для объекта, которого ещё нет в базе, только устанавливаем значение поля
            // - чтобы при сохранении поле сразу записалось в базу
            $this->weight = $weight;
            return;
        }

        $this->weight = $weight;
        \Skif\DB\DBWrapper::query(self::DB_ID, 'update ' . self::DB_TABLE_NAME . ' set weight = ? where id = ?',
            array($this->weight, $this->getId()));

        // кэши всегда сбрасываем после изменения данных в БД

        $this->resetTreeRootAndParentCache();

        self::afterUpdate($this->getId());
    }

    public function getChildrenIdsArr()
    {
        return $this->children_item_ids_arr;
    }

    /**
     * Загружает список потомков и сохраняет в поле модели. Должно вызываться из метода load модели.
     */
    protected function loadChildrenIdsArr(){
        self::exceptionIfClassNotCompatibleWithTree(__CLASS__);

        $parent_id = $this->getId();

        $sql = "SELECT id FROM " . self::DB_TABLE_NAME . " WHERE parent_id = ? ORDER BY weight ASC";

        $this->children_item_ids_arr = \Skif\DB\DBWrapper::readColumn(self::DB_ID, $sql, array($parent_id));
    }

    /**
     * Собирает список всех потомков на всех уровнях.
     * @param $menu_id
     * @param int $depth
     * @return array
     * @throws \Exception
     */
    protected static function getAllChildrenIdsArr($menu_id, $depth = 0)
    {
        self::exceptionIfClassNotCompatibleWithTree(__CLASS__);

        if ($depth > 100){
            throw new \Exception("too deep");
        }

        $menu_obj = self::factory($menu_id);

        $total_children = $menu_obj->getChildrenIdsArr();

        if (!$total_children){
            return array();
        }

        foreach ($total_children as $child_id) {
            $child_obj = self::factory($child_id);

            if (!$child_obj->getChildrenIdsArr()) {
                continue;
            }

            $childrens_children = self::getAllChildrenIdsArr($child_id, $depth + 1);

            $total_children = array_merge($total_children, $childrens_children);
        }

        return $total_children;
    }

    public static function getRootItemsIdsArr()
    {
        self::exceptionIfClassNotCompatibleWithTree(__CLASS__);

        $cache_key = self::treeGetRootItemsIdsArrCacheKey();

        $item_id_arr = \Skif\Cache\CacheWrapper::get($cache_key);

        if ($item_id_arr !== false) {
            return $item_id_arr;
        }

        $sql = "SELECT id FROM " . self::DB_TABLE_NAME . " WHERE parent_id = 0 ORDER BY weight ASC";

        $item_id_arr = \Skif\DB\DBWrapper::readColumn(self::DB_ID, $sql);

        \Skif\Cache\CacheWrapper::set($cache_key, $item_id_arr, 3600);

        return $item_id_arr;
    }

    static protected function treeGetRootItemsIdsArrCacheKey(){
        return 'tree_root_items_' . __CLASS__; // actual class name will be used (not trait name)
    }

    static public function resetTreeRootCache(){
        $cache_key = self::treeGetRootItemsIdsArrCacheKey();
        \Skif\Cache\CacheWrapper::delete($cache_key);
    }

    /**
     * Может вызываться для удаленного объекта, поэтому не должна обращаться к БД для него. (т.е. ограничения те же, что и для afterDelete)
     */
    public function resetTreeRootAndParentCache(){
        self::exceptionIfClassNotCompatibleWithTree(__CLASS__);

        self::resetTreeRootCache();

        if ($this->parent_id) {
            self::afterUpdate($this->parent_id); // сбрасываем кэш нового родителя
        }
    }

}