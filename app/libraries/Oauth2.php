<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-11-12
 * Time: 上午11:44
 */
class Oauth2 {

    protected $open_id;     //open_id
    protected $access_token;
    protected $user_info;   //用户信息
    protected $app_id;
    protected $app_secret;
    protected $cache_key;

    public function __construct($options = array())
    {
        $this->load->library('curl');
        $this->load->model('cache_model');
        $this->app_id = $options['app_id'];
        $this->app_secret = $options['app_secret'];
        $this->cache_key = $this->session->userdata('session_id');
    }

    /**
     * 跳转到登录授权页面
     */
    public function goto_oauth()
    {
        if($this->input->get('code')) {

        } else {
            //跳到授权页
            $this->load->helper('url');
            $redirect_uri = urlencode(current_url());
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->app_id&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";

            redirect($url);
        }
    }

    /**
     * 根据code拉取access_token和open_id
     * @return mixed
     */
    public function get_access_token()
    {
        //有缓存直接返回
        if($access_token = $this->cache_model->read($this->cache_key, 'access_token')) {
            $this->open_id = $access_token['openid'];
            $this->access_token = $access_token['access_token'];
            return $access_token;
        }

        if(!$this->input->get('code')) {
            $this->goto_oauth();
        }
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token";
        $params = array(
            'appid' => $this->app_id,
            'secret' => $this->app_secret,
            'code'  => $this->input->get('code'),
            'grant_type' => 'authorization_code'
        );
        $response = $this->curl->simple_get($url, $params);
        $response = json_decode($response, TRUE);
        if(isset($response['errcode'])) {
            exit('ERROR: GET ACCESS_TOKEN FAILED'.$response['errcode']);
        }
        $this->open_id = $response['openid'];
        $this->access_token = $response['access_token'];
        //缓存起来
        $this->cache_model->update($this->cache_key, 'access_token', $response, 3600 * 24); //24小时
        return $response;
    }

    /**
     * 拉取用户详情(头像、呢称、地区、性别 ...)
     * @return mixed
     */
    public function get_user_info()
    {
        //有缓存直接返回
        if($this->user_info = $this->cache_model->read($this->cache_key, 'user_info')) {
            return $this->user_info;
        }

        if(!$this->access_token) {
            $this->get_access_token();
        }
        $url = "https://api.weixin.qq.com/sns/userinfo";
        $params = array(
            'access_token' => $this->access_token,
            'openid'      => $this->open_id,
            'lang'         => 'zh_CN',
        );
        $response = $this->curl->simple_get($url, $params);
        $response = json_decode($response, TRUE);
        if(isset($response['errcode'])) {
            exit('error:'.$response['errcode']);
        }

        //缓存用户详情
        $this->cache_model->update($this->cache_key, 'user_info', $response, 3600 * 24); //24小时
        return $response;
    }

    function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }
}