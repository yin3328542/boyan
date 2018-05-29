<?php
class Wxapplet
{
    protected $_api_server  = 'https://api.weixin.qq.com/sns/';
    protected $_ci;
    protected $_app_id='';
    protected $_app_secret='';

    public function __construct($options = array())
    {
        isset($options['app_id'])&&$this->_app_id  = $options['app_id'];
        isset($options['app_secret'])&&$this->_app_secret     = $options['app_secret'];
    }

    public function getAppID(){
        return $this->_app_id;
    }

    private function httpRequest($url,$data = null){
        $curl = curl_init();
        curl_setopt ( $curl, CURLOPT_URL, $url );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, false );
        if(isset($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function getUserOpenID($code){
        $url = $this->_api_server.'jscode2session?appid='.$this->_app_id.'&secret='.$this->_app_secret.'&js_code='.$code.'&grant_type=authorization_code';
        $result = $this->httpRequest($url);
        $user_openid = json_decode($result, true );
        return $user_openid;
    }


    public function getAccessToken(){
        $app_secret = $this->_app_secret;
        $app_id = $this->_app_id;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$app_id}&secret={$app_secret}";
        $ret = $this->httpRequest($url);
        $ret = json_decode($ret,true);
        $access_token = $ret['access_token'];
        return $access_token;

    }

    public function getQrcode($scene=''){

        if(empty($scene)){
            $scene = $this->_app_id;
        }
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token={$access_token}";
        //var_dump($url);
        $data['scene'] = $scene;
        $data['path'] = 'pages/article/article';
        $response = $this->httpRequest($url,json_encode($data));
        $save_name = md5($this->_app_id).'.png';
        $save_path = rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/data/qrcode/'.$save_name;
        file_put_contents($save_path,$response);
        return $save_name;
    }


}