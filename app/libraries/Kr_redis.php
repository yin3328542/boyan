<?php
/**
 * Created by PhpStorm.
 * User: zhanglihe
 * Date: 15/9/25
 * Time: 下午4:21
 */
class Kr_redis {

    /**
     * default redis server config
     * @var array
     */
    protected static $_default_config = array(
        'socket_type' => 'tcp',
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => 6379,
        'timeout' => 0
    );

    /**
     * Redis connection
     *
     * @var	Redis
     */
    protected $_redis;

    public function __construct() {
        $config = array();
        $CI =& get_instance();
        if ($CI->config->load('redis'))
        {
            $config = $CI->config->item('redis');
        }
        $config = array_merge(self::$_default_config, $config);
        $this->_redis = new Redis();
        try
        {
            if ($config['socket_type'] === 'unix')
            {
                $success = $this->_redis->connect($config['socket']);
            }
            else // tcp socket
            {
                $success = $this->_redis->connect($config['host'], $config['port'], $config['timeout']);
            }
            if ( ! $success)
            {
                throw new RuntimeException('Cache: Redis connection failed. Check your configuration.');
            }
        }
        catch (RedisException $e)
        {
            throw new RuntimeException('Cache: Redis connection refused ('.$e->getMessage().')');
        }
        if (isset($config['password']) && ! $this->_redis->auth($config['password']))
        {
            throw new RuntimeException('Cache: Redis authentication failed.');
        }
    }

    /**
     * 指定当前数据库(0-9)
     * @param $db int
     */
    public function select($db) {
        $this->_redis->select(9);
    }

    /**
     * 查找符合给定模式的key。
     *   --KEYS *命中数据库中所有key。
     *   --KEYS h?llo命中hello， hallo and hxllo等。
     *   --KEYS h*llo命中hllo和heeeeello等。
     *   --KEYS h[ae]llo命中hello和hallo，但不命中hillo。
     * @param $param
     * @return mixed
     */
    public function keys($param) {
        return $this->_redis->keys($param);
    }

    /**
     * Get cache
     *
     * @param	string	Cache ID
     * @return	mixed
     */
    public function get($key)
    {
        return $this->_redis->get($key);
    }
    // ------------------------------------------------------------------------
    /**
     * Save cache
     *
     * @param	string	$id	Cache ID
     * @param	mixed	$data	Data to save
     * @param	int	$ttl	Time to live in seconds
     * @return	bool	TRUE on success, FALSE on failure
     */
    public function save($id, $data, $ttl = 60)
    {
        return $this->_redis->set($id, $data, $ttl);
    }
    // ------------------------------------------------------------------------

    /**
     * 描述：验证指定的键是否存在
     * @param $key
     * @return bool 成功返回：TRUE;失败返回：FALSE
     */
    public function has($key) {
        return $this->_redis->exists($key);
    }
    // ------------------------------------------------------------------------
    /**
     * Delete from cache
     *
     * @param	string	Cache key
     * @return	bool
     */
    public function delete($key)
    {
        return $this->_redis->del($key);
    }
    // ------------------------------------------------------------------------
    /**
     * Increment a raw value
     *
     * @param	string	$id	Cache ID
     * @param	int	$offset	Step/value to add
     * @return	mixed	New value on success or FALSE on failure
     */
    public function increment($id, $offset = 1)
    {
        return $this->_redis->incr($id, $offset);
    }
    // ------------------------------------------------------------------------
    /**
     * Decrement a raw value
     *
     * @param	string	$id	Cache ID
     * @param	int	$offset	Step/value to reduce by
     * @return	mixed	New value on success or FALSE on failure
     */
    public function decrement($id, $offset = 1)
    {
        return $this->_redis->decr($id, $offset);
    }
    // ------------------------------------------------------------------------
    /**
     * Clean cache
     *
     * @return	bool
     * @see		Redis::flushDB()
     */
    public function clean()
    {
        return $this->_redis->flushDB();
    }
    // ------------------------------------------------------------------------
    /**
     * Get cache driver info
     *
     * @param	string	Not supported in Redis.
     *			Only included in order to offer a
     *			consistent cache API.
     * @return	array
     * @see		Redis::info()
     */
    public function cache_info($type = NULL)
    {
        return $this->_redis->info();
    }
    // ------------------------------------------------------------------------
    /**
     * Get cache metadata
     *
     * @param	string	Cache key
     * @return	array
     */
    public function get_metadata($key)
    {
        $value = $this->get($key);
        if ($value !== FALSE)
        {
            return array(
                'expire' => time() + $this->_redis->ttl($key),
                'data' => $value
            );
        }
        return FALSE;
    }

    // ------------------------------------------------------------------------
    /**
     * Class destructor
     *
     * Closes the connection to Redis if present.
     *
     * @return	void
     */
    public function __destruct()
    {
        if ($this->_redis)
        {
            $this->_redis->close();
        }
    }

    /**
     * @param $method
     * @param $args
     * @return $this
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_redis, $method), $args);
    }

}