<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 微信author2认证接口相关地址
 */
define('WC_URL_AUTHORIZE','https://open.weixin.qq.com/connect/oauth2/authorize');
define('WC_URL_ACCESS_TOKEN','https://api.weixin.qq.com/sns/oauth2/access_token');
define('WC_URL_REFRESH_TOKEN','https://api.weixin.qq.com/sns/oauth2/refresh_token');
define('WC_URL_USERINFO','https://api.weixin.qq.com/sns/userinfo');

/**
 * 默认公共账号体系公众账号的基本参数
 * 严重警告：该参数在系统运行后不能进行更改，否则会造成用户体系混乱
 */
$config['default_wechat_appid'] = 'wxd9b0e4ae8a8ed9c5';
$config['default_wechat_appsecret'] = 'ca45fc98596f3a8ff0ef71c6865c9658';


