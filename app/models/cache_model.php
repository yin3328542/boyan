<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Cache_model
 */
class Cache_model extends CI_Model {
    private $_prefix = PREFIX_CACHE_KEY;
    function __construct()
    {
        parent::__construct();
        $this->load->driver('cache',array('adapter' => 'memcached'));
    }

    /**
     * 删除指定站点对应缓存
     * @param $site_id
     * @param string $key
     * @return mixed
     */
    function remove($site_id,$key=''){
        return $this->cache->memcached->delete($this->_getCacheKey($site_id,$key));
    }

    /**
     * 读取指定站点对应缓存
     * @param $site_id
     * @param string $key
     * @return mixed
     */
    function read($site_id,$key=''){
        return $this->cache->memcached->get($this->_getCacheKey($site_id,$key));
    }

    /**
     * 更新指定站点对应缓存
     * @param $site_id
     * @param string $key
     * @param $site_info
     * @param int $ttl
     * @return mixed
     */
    function update($site_id,$key = '',$site_info,$ttl = 600){
        return $this->cache->memcached->save($this->_getCacheKey($site_id,$key),$site_info,$ttl);
    }

    //返回对应的缓存key
    private function _getCacheKey($site_id,$key=''){
        return $this->_prefix . $site_id . '_' . $key;
    }
}