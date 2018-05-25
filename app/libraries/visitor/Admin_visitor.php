<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-9
 * Time: 下午2:06
 */
require_once APPPATH . 'libraries/visitor/Base_visitor' . EXT;

class Admin_visitor extends Base_visitor {

    protected $_info_key = 'admin_info';
} 