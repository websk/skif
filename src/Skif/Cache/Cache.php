<?php

namespace Skif\Cache;

class Cache
{
    public $keyPrefix = 'prefix';

    /**
     * @var boolean whether to use memcached or memcache as the underlying caching extension.
     * If true {@link http://pecl.php.net/package/memcached memcached} will be used.
     * If false {@link http://pecl.php.net/package/memcache memcache}. will be used.
     * Defaults to false.
     */

    /**
     * @var Memcache the Memcache instance
     */
    public $_cache = null;
    public $connected = false;

    /**
     * @var array list of memcache server configurations
     */
    //private $_servers = array();

    public function __construct()
    {
        $this->connected = false;

        $this->init();
    }

    /**
     * Initializes this application component.
     * This method is required by the {@link IApplicationComponent} interface.
     * It creates the memcache instance and adds memcache servers.
     * @throws CException if memcache extension is not loaded
     */
    public function init()
    {
        $servers = \Skif\Conf\ConfWrapper::value('cache.servers');
        if (!$servers) {
            return false;
        }

        $this->createCache();

        foreach ($servers as $server) {
            $server['weight'] = 1;

            if (!$this->_cache->addServer($server['host'], $server['port'])){
                throw new \Exception('Server add failed');
            }

            $this->_cache->setCompressThreshold(5000, 0.2);

            $this->connected = true;
        }
    }

    /**
     * @return mixed the memcache instance (or memcached if {@link useMemcached} is true) used by this component.
     */
    public function createCache()
    {
        $this->_cache = new \Memcache;
    }

    /**
     * @return \Memcached|\Memcache|null the memcache instance (or memcached if {@link useMemcached} is true) used by this component.
     */
    public function getConnectionObj()
    {
        return $this->_cache;
    }

    public function get($key)
    {
        $unique_key = $this->generateUniqueKey($key);
        $value = $this->getValue($unique_key);

        if ($value !== false) {
            $data = unserialize($value);
            return $data;
        }

        return false;
    }

    public function delete($key)
    {
        $unique_key = $this->generateUniqueKey($key);
        return $this->deleteValue($unique_key);
    }

    /**
     * Retrieves a value from cache with a specified key.
     * This is the implementation of the method declared in the parent class.
     * @param string $key a unique key identifying the cached value
     * @return string the value stored in cache, false if the value is not in the cache or expired.
     */
    protected function getValue($key)
    {
        return $this->_cache->get($key);
    }

    /**
     * Retrieves multiple values from cache with the specified keys.
     * @param array $keys a list of keys identifying the cached values
     * @return array a list of cached values indexed by the keys
     */
    /*
    protected function getValues($keys)
    {
        return $this->useMemcached ? $this->_cache->getMulti($keys) : $this->_cache->get($keys);
    }
    */

    protected function generateUniqueKey($key)
    {
        return md5($this->keyPrefix . $key);
    }

    public function set($key, $value, $expire = -1)
    {
        $unique_key = $this->generateUniqueKey($key);

        return $this->setValue($unique_key, serialize($value), $expire);
    }

    /**
     * Stores a value identified by a key in cache.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string $key the key identifying the value to be cached
     * @param string $value the value to be cached
     * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    protected function setValue($key, $value, $expire = -1)
    {
        if ($expire == -1) {
            $expire = \Skif\Conf\ConfWrapper::value('cache.expire');
        }

        if ($expire > 0) {
            $expire += time();
        }
        else {
            $expire = 0;
        }

        return $this->_cache->set($key, $value, 0, $expire);
    }

    /**
     * Deletes a value with the specified key from cache
     * This is the implementation of the method declared in the parent class.
     * @param string $key the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    protected function deleteValue($key)
    {
        $result = $this->_cache->delete($key, 0);

        return $result;
    }
}

/**
 * CMemCacheServerConfiguration represents the configuration data for a single memcache server.
 *
 * See {@link http://www.php.net/manual/en/function.Memcache-addServer.php}
 * for detailed explanation of each configuration property.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CMemCache.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.caching
 * @since 1.0
 */
class CMemCacheServerConfiguration
{
    /**
     * @var string memcache server hostname or IP address
     */
    public $host;
    /**
     * @var integer memcache server port
     */
    public $port = 11211;
    /**
     * @var boolean whether to use a persistent connection
     */
    public $persistent = true;
    /**
     * @var integer probability of using this server among all servers.
     */
    public $weight = 1;
    /**
     * @var integer value in seconds which will be used for connecting to the server
     */
    public $timeout = 15;
    /**
     * @var integer how often a failed server will be retried (in seconds)
     */
    public $retryInterval = 15;
    /**
     * @var boolean if the server should be flagged as online upon a failure
     */
    public $status = true;

    /**
     * Constructor.
     * @param array $config list of memcache server configurations.
     * @throws CException if the configuration is not an array
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            foreach ($config as $key => $value)
                $this->$key = $value;
            if ($this->host === null)
                throw new CException(Yii::t('yii', 'CMemCache server configuration must have "host" value.'));
        } else
            throw new CException(Yii::t('yii', 'CMemCache server configuration must be an array.'));
    }
}