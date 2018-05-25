<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-7
 * Time: 下午8:48
 */

class Base_visitor {

    // 登陆状态
    public $is_signin = FALSE;
    // 访问者信息
    public $info = NULL;
    //session键
    protected $_info_key = '';
    // 授权角色
    protected $role_allow = NULL;
    //site是否初始化信息
    public $is_init = FALSE;

    public function __construct()
    {
        $info = $this->session->userdata($this->_info_key);
        if ($info) {
            $this->info = $info;
            $this->is_signin = TRUE;
            (isset($info['is_init']) && $info['is_init'] == 1) && $this->is_init = TRUE;
        }
    }

    public function assign($info, $remember = FALSE)
    {
        $remember && $this->session->sess_expiration = 3600*24*14;
        $this->session->set_userdata($this->_info_key, $info);
        $this->is_signin = TRUE;
    }

    public function logout()
    {
        $this->session->unset_userdata($this->_info_key);
    }

    function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }
} 