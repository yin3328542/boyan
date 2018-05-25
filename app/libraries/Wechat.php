<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-4-21
 * Time: 下午7:14
 * 微信PHP-SDK
 */

class Wechat {

    protected $_api_server = 'https://api.weixin.qq.com/cgi-bin/';
    protected $_ci;
    protected $_app_id;
    protected $_app_secret;
    protected $_access_token;

    public function __construct($options = array())
    {
        $this->load->library('curl');
        $this->app_id = $options['app_id'];
        $this->app_secret = $options['app_secret'];
    }

    public function access_token()
    {
        $params = array(
            'grant_type' => 'client_credential',
            'appid' => $this->app_id,
            'secret' => $this->app_secret,
        );
        $response = $this->curl->simple_get($this->_api_server . 'token', $params);
        $response = json_decode($response, TRUE);
        if (isset($response['access_token'])) {
            $this->_access_token = $response['access_token'];
            return $response;
        } else {
            return false;
        }
    }

    /**
     * 获取关注者列表
     * @param string $next_openid
     * @param string $access_token
     * @return mixed
     */
    public function user_get($next_openid = '', $access_token = '')
    {
        $params = array();
        $params['access_token'] = $access_token ? $access_token : $this->_access_token;
        $next_openid && $params['next_openid'] = $next_openid;
        $response = $this->curl->simple_get($this->_api_server . 'user/get', $params);
        $response = json_decode($response, TRUE);
        if (!isset($response['errcode'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 获取用户信息
     * @param $openid
     * @param string $access_token
     * @return mixed
     */
    public function get_user_info($openid, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $params = array(
            'access_token' => $access_token,
            'openid' => $openid,
            'lang' => 'zh_CN'
        );
        $response = $this->curl->simple_get($this->_api_server . 'user/info', $params);
        $response = json_decode($response, TRUE);
        if (!isset($response['errcode'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 获取用户分组
     * @param string $access_token
     * @return mixed
     */
    public function groups_get($access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $response = $this->curl->simple_get($this->_api_server . 'groups/get?access_token='.$access_token);
        $response = json_decode($response, TRUE);
        if (!isset($response['errcode'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 创建分组
     * @param $group_json
     * @param string $access_token
     * @return mixed
     */
    public function groups_create($group_json, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $response = $this->curl->simple_post($this->_api_server . 'groups/create?access_token='.$access_token, $group_json);
        $response = json_decode($response, TRUE);
        return $response;
    }

    /**
     * 获取用户所属分组
     * @param $openid
     * @param string $access_token
     * @return mixed
     */
    public function groups_getid($openid, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $post_json = json_encode(array('openid'=>$openid));
        $response = $this->curl->simple_post($this->_api_server . 'groups/getid?access_token='.$access_token, $post_json);
        $response = json_decode($response, TRUE);
        if (!isset($response['errcode'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 修改分组
     * @param $groups
     * @param string $access_token
     * @return mixed
     */
    public function groups_update($groups, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $post_json = json_encode(array('group'=>$groups));
        $response = $this->curl->simple_post($this->_api_server . 'groups/update?access_token='.$access_token, $post_json);
        $response = json_decode($response, TRUE);
        if ($response['errcode'] == 0) {
            return TRUE;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 移动用户分组
     * @param $openid
     * @param $to_groupid
     * @param string $access_token
     * @return bool
     */
    public function groups_members_update($openid, $to_groupid, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $post_json = json_encode(array('openid'=>$openid, 'to_groupid'=>$to_groupid));
        $response = $this->curl->simple_post($this->_api_server . 'groups/members/update?access_token='.$access_token, $post_json);
        $response = json_decode($response, TRUE);
        if ($response['errcode'] == 0) {
            return TRUE;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 获取自定义菜单
     * @param string $access_token
     * @return mixed
     */
    public function menu_get($access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $response = $this->curl->simple_get($this->_api_server . 'menu/get?access_token='.$access_token);
        $response = json_decode($response, TRUE);
        return $response;
    }

    /**
     * 创建自定义菜单
     * @param array $menus
     * @param string $access_token
     * @return bool
     */
    public function menu_create($menus, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $post_json = urldecode(json_encode(array('button'=>$menus)));
        $post_json = str_replace("\\", "", $post_json);
        $response = $this->curl->simple_post($this->_api_server . 'menu/create?access_token='.$access_token, $post_json);
        //$this->curl->debug();
        $response = json_decode($response, TRUE);
        if ($response['errcode'] == 0) {
            return TRUE;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 删除自定义菜单
     * @param string $access_token
     * @return bool
     */
    public function menu_delete($access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $response = $this->curl->simple_get($this->_api_server . 'menu/delete?access_token='.$access_token);
        $response = json_decode($response, TRUE);
        //return $access_token;
        if ($response['errcode'] == 0) {
            return TRUE;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 生成二维码
     * @param $qrcode_json
     * @param string $access_token
     * @return mixed
     */
    public function qrcode_create($qrcode_json, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $response = $this->curl->simple_post($this->_api_server . 'qrcode/create?access_token='.$access_token, $qrcode_json);
        $response = json_decode($response, TRUE);
        if (!isset($response['errcode'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

    function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    /**
     * 媒体文件上传
     * @param $url
     * @param $type
     * @param string $access_token
     * @return mixed
     */
    public function media_post($url, $type, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $api_url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type='.$type;
        $param['media'] = '@'.$url;
        $response = $this->curl->simple_post($api_url, $param);

        $response = json_decode($response, TRUE);
        if (!isset($response['errcode'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 图文群发信息上传
     * @param $arr_json
     * @param string $access_token
     * @return mixed
     */
    public function news_post($arr_json, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $api_url = $this->_api_server.'media/uploadnews?access_token='.$access_token;

        $response = $this->curl->simple_post($api_url, $arr_json);
        $response = json_decode($response, TRUE);
        if (!isset($response['errcode'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

    public function sendall_post($arr_json, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $api_url = $this->_api_server.'message/mass/sendall?access_token='.$access_token;

        $response = $this->curl->simple_post($api_url, $arr_json);
        $response = json_decode($response, TRUE);
        if (isset($response['msg_id'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 发送客服信息（点对点发送）
     * @param $post_json
     * @param string $access_token
     * @return mixed
     */
    public function message_custom_post($post_json, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $api_url = $this->_api_server.'message/custom/send?access_token='.$access_token;

        $response = $this->curl->simple_post($api_url, $post_json);
        $response = json_decode($response, TRUE);
        if (!isset($response['errcode'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

    /**
     * 发送模板信息（点对点发送）
     * @param $post_json
     * @param string $access_token
     * @return mixed
     */
    public function message_template_post($post_json, $access_token = '')
    {
        $access_token = $access_token ? $access_token : $this->_access_token;
        $api_url = $this->_api_server.'message/template/send?access_token='.$access_token;

        $response = $this->curl->simple_post($api_url, $post_json);
        $response = json_decode($response, TRUE);
        if (!isset($response['errcode'])) {
            return $response;
        } else {
            return $response['errcode'];
        }
    }

} 