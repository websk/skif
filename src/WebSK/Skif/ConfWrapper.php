<?php
namespace WebSK\Skif;

/**
 * Class ConfWrapper
 * @package WebSK\Skif
 */
class ConfWrapper
{
 
    /**
     * Get value an array by using "root.branch.leaf" notation
     *
     * @param string $path   Path to a specific option to extract
     * @param mixed $default Value to use if the path was not found
     * @return mixed
     */
    public static function value($path, $default = ''){
    	
    	if (empty($path)) {
    		return '';
    	}

        $container = Container::self();

    	$value = $container['settings'] ?? [];

        $parts = explode(".", $path);
 
        foreach ($parts as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                // key doesn't exist, fail
                return $default;
            }
        }
 
        return $value;
    }
}