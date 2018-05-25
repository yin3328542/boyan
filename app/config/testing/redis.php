<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: zhanglihe
 * Date: 15/9/2
 * Time: 下午2:51
 */
$config['redis'] = array(
    'socket_type' => 'tcp',
    'host' => '127.0.0.1',
    'password'=>'123456',
    'port'     => 6379,
    'timeout' => 0
);