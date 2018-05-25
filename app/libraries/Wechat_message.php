<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: suolan
 * Date: 14-8-26
 * Time: 下午7:31
 */

/**
 * Class Wechat_message
 * 微信消息处理器
 *
 * 可选的初始化配置：token
 *
 *
 */
class Wechat_message{
    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_LOCATION = 'location';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_EVENT = 'event';
    const MSGTYPE_MUSIC = 'music';
    const MSGTYPE_NEWS = 'news';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';

    private $_token; //token
    private $_receive;


    public function __construct($options)
    {
        //获取微信服务器信息
        $postStr = file_get_contents('php://input');
        //token
        $this->_token = ($options && isset($options['token']) )? $options['token'] : '';
        //如果有信息则暂存信息
        if (!empty($postStr)) {
            $this->_receive = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        }else{
            //exit('没有接收到微信服务器消息.');
        }
    }


    /*------------------------------------------------------------------------------------------------
                                        验证是否为微信服务器消息
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 验证是否为微信服务器消息
     * @param null $token，默认使用初始化提供的token
     * @return bool
     */
    public function valid($token = NULL)
    {
        if(!$token){
            $token = $this->_token;
        }

        $echoStr = isset($_GET['echostr']) ? $_GET['echostr'] : '';
        if ($echoStr) {//用于开发者验证
            if ($this->_checkSignature($token)){
                exit($echoStr);
            }
            else{
                exit('No Access');//错误的请求，肯定不是来自微信
            }
        } else {
            return $this->_checkSignature($token);
        }
    }

    /*------------------------------------------------------------------------------------------------
                                        从微信消息中获取指定字段值
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 从微信消息中获取指定字段值
     * @param $key
     * @return bool
     */
    public function getMsgField($key){
        if(isset($this->_receive[$key])){
            return $this->_receive[$key];
        }else{
            return false;
        }
    }
    /*------------------------------------------------------------------------------------------------
                                        获取微信消息中MsgType字段
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_MsgType(){
        return $this->getMsgField('MsgType');
    }

    /*------------------------------------------------------------------------------------------------
                                    获取微信消息中的FromUserName字段
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_FromUserName(){
        return $this->getMsgField('FromUserName');
    }

    /*------------------------------------------------------------------------------------------------
                                      获取微信消息中ToUserName字段
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_ToUserName(){
        return $this->getMsgField('ToUserName');
    }

    /*------------------------------------------------------------------------------------------------
                                        获取微信消息中MsgId字段
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_MsgId(){
        return $this->getMsgField('MsgId');
    }

    /*------------------------------------------------------------------------------------------------
                                    获取微信群发消息回报的群发消息ID
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_SendAll_MsgId(){
        return $this->getMsgField('MsgID');
    }

    /*------------------------------------------------------------------------------------------------
                                     获取微信消息中的CreateTime字段
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_CreateTime(){
        return $this->getMsgField('CreateTime');
    }

    /*------------------------------------------------------------------------------------------------
                        获取微信消息中Content字段（如果是语音识别，则返回Recognition字段）
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_Content(){
        if($this->getMsgField('Content')==false){
            return $this->getMsgField('Recognition');
        }
        else{
            return $this->getMsgField('Content');
        }
    }

    /*------------------------------------------------------------------------------------------------
                                        获取微信消息中PicUrl字段
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_PicUrl(){
        return $this->getMsgField('PicUrl');
    }

    /*------------------------------------------------------------------------------------------------
                              获取微信消息中链接信息{Url,Title,Description}
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_Link(){
        if ($this->getMsgField('Url')!==false) {
            return array(
                'Url' => $this->getMsgField('Url'),
                'Title' => $this->getMsgField('Title'),
                'Description' => $this->getMsgField('Description')
            );
        } else{
            return false;
        }
    }

    /*------------------------------------------------------------------------------------------------
                        获取微信消息中地理位置{Label,Location_X,Location_Y,Scale}
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_Geo(){
        if ($this->getMsgField('Location_X')!==false) {
            return array(
                'Location_X' => $this->getMsgField('Location_X'),
                'Location_Y' => $this->getMsgField('Location_Y'),
                'Scale' => $this->getMsgField('Scale'),
                'Label' => $this->getMsgField('Label')
            );
        } else{
            return false;
        }
    }

    /*------------------------------------------------------------------------------------------------
                                   获取微信消息中事件信息{Event,EventKey}
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_Event(){
        if ($this->getMsgField('Event')!==false) {
            return array(
                'Event' => $this->getMsgField('Event'),
                'EventKey' => $this->getMsgField('EventKey')
            );
        } else{
            return false;
        }
    }

    /*------------------------------------------------------------------------------------------------
                                   获取微信消息中语音信息{MediaId,Format}
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_Voice(){
        if ($this->getMsgField('MediaId')!==false) {
            return array(
                'MediaId' => $this->getMsgField('MediaId'),
                'Format' => $this->getMsgField('Format')
            );
        } else{
            return false;
        }
    }

    /*------------------------------------------------------------------------------------------------
                                 获取微信消息中视频信息信息{MediaId,ThumbMediaId}
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_Video(){
        if ($this->getMsgField('MediaId')!==false) {
            return array(
                'MediaId' => $this->getMsgField('MediaId'),
                'ThumbMediaId' => $this->getMsgField('ThumbMediaId')
            );
        } else{
            return false;
        }    }

    /*------------------------------------------------------------------------------------------------
                                 获取微信消息中的Ticket字段(用户通过扫描二维码关注时)
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_Ticket(){
        return $this->getMsgField('Ticket');
    }

    /*------------------------------------------------------------------------------------------------
                                 获取微信消息中发送成功数量(高级群发结果推送)
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_SentCount(){
        return $this->getMsgField('SentCount');
    }

    /*------------------------------------------------------------------------------------------------
                                 获取微信消息中发送失败数量(高级群发结果推送)
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_ErrorCount(){
        return $this->getMsgField('ErrorCount');
    }

    /*------------------------------------------------------------------------------------------------
                                 获取微信消息中发送结果(高级群发结果推送)
    /*------------------------------------------------------------------------------------------------*/
    public function getMsg_Status(){
        return $this->getMsgField('Status');
    }

    /*------------------------------------------------------------------------------------------------
                                            是否为文本消息
    /*------------------------------------------------------------------------------------------------*/
    public function isText(){
        return $this->getMsg_MsgType()==self::MSGTYPE_TEXT;
    }

    /*------------------------------------------------------------------------------------------------
                                            是否为图片消息
    /*------------------------------------------------------------------------------------------------*/
    public function isImage(){
        return $this->getMsg_MsgType()==self::MSGTYPE_IMAGE;
    }

   /*------------------------------------------------------------------------------------------------
                                            是否为链接消息
   /*------------------------------------------------------------------------------------------------*/
    public function isLink(){
        return $this->getMsg_MsgType()==self::MSGTYPE_LINK;
    }

    /*------------------------------------------------------------------------------------------------
                                             是否地理位置消息
    /*------------------------------------------------------------------------------------------------*/
    public function isLocation(){
        return $this->getMsg_MsgType()==self::MSGTYPE_LOCATION;
    }

    /*------------------------------------------------------------------------------------------------
                                             是否为语音消息
    /*------------------------------------------------------------------------------------------------*/
    public function isVoice(){
        return $this->getMsg_MsgType()==self::MSGTYPE_VOICE;
    }

    /*------------------------------------------------------------------------------------------------
                                             是否为视频消息
    /*------------------------------------------------------------------------------------------------*/
    public function isVideo(){
        return $this->getMsg_MsgType()==self::MSGTYPE_VIDEO;
    }

    /*------------------------------------------------------------------------------------------------
                                             是否为事件类型消息
    /*------------------------------------------------------------------------------------------------*/
    public function isEvent(){
        return $this->getMsg_MsgType()==self::MSGTYPE_EVENT;
    }

    /*------------------------------------------------------------------------------------------------
                                             是否是关注事件（包括扫码关注）
    /*------------------------------------------------------------------------------------------------*/
    public function isSubscribe(){
        return ($this->getMsg_MsgType() == self::MSGTYPE_EVENT && $this->getMsgField('Event') == 'subscribe');
    }

    /*------------------------------------------------------------------------------------------------
                                             是否是取消关注事件
    /*------------------------------------------------------------------------------------------------*/
    public function isUnSubscribe(){
        return ($this->getMsg_MsgType() == self::MSGTYPE_EVENT && $this->getMsgField('Event') == 'unsubscribe');
    }

    /*------------------------------------------------------------------------------------------------
                                             是否是关注后扫码
    /*------------------------------------------------------------------------------------------------*/
    public function isScancode(){
        return ($this->getMsg_MsgType() == self::MSGTYPE_EVENT && $this->getMsgField('Event') == 'SCAN');
    }

    /*------------------------------------------------------------------------------------------------
                                            是否是上报地理位置
    /*------------------------------------------------------------------------------------------------*/
    public function isReportLocation(){
        return ($this->getMsg_MsgType() == self::MSGTYPE_EVENT && $this->getMsgField('Event') == 'LOCATION');
    }

    /*------------------------------------------------------------------------------------------------
                                          是否点击自定义菜单获取信息
    /*------------------------------------------------------------------------------------------------*/
    public function isMenuClick(){
        return ($this->getMsg_MsgType() == self::MSGTYPE_EVENT && $this->getMsgField('Event') == 'CLICK');
    }

    /*------------------------------------------------------------------------------------------------
                                             是否点击自定义菜单跳转链接
    /*------------------------------------------------------------------------------------------------*/
    public function isMenuNavigate(){
        return ($this->getMsg_MsgType() == self::MSGTYPE_EVENT && $this->getMsgField('Event') == 'VIEW');
    }

    /*------------------------------------------------------------------------------------------------
                                             是否高级群发结果推送
    /*------------------------------------------------------------------------------------------------*/
    public function isSendJobFinish(){
        return ($this->getMsg_MsgType() == self::MSGTYPE_EVENT && $this->getMsgField('Event') == 'MASSSENDJOBFINISH');
    }


    /*------------------------------------------------------------------------------------------------
                                            通用的回复消息方法
    /*------------------------------------------------------------------------------------------------*/
    /**
     *
     * @param string $msg 消息文本
     * @param bool $return 是否返回消息文本，否则使用echo输出
     * @return mixed
     */
    protected function reply($msg = '', $return = false){
        if($return){
            return $msg;
        }else{
            echo($msg);
        }
    }

    /*------------------------------------------------------------------------------------------------
                               静默回复（不需要给用户在聊天窗口返回消息时使用）
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 静默回复（不需要给用户在聊天窗口返回消息时使用）
     * @return bool
     */
    public function reply_quiet(){
        return true;
    }


    /*------------------------------------------------------------------------------------------------
                                         回复文本消息
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 回复文本消息
     * @param string $content 文本消息内容
     * @param bool $return 是否返回消息xml文本，否则使用echo输出
     * @return mixed
     */
    public function reply_text($content, $return = false){
        $template = <<<MSG
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>
MSG;
        $xml_str = sprintf($template,
            $this->getMsg_FromUserName(),
            $this->getMsg_ToUserName(),
            time(),
            $content
        );
        return $this->reply($xml_str,$return);
    }

    /*------------------------------------------------------------------------------------------------
                                            回复音乐消息
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 回复音乐消息
     * @param string $title 音乐标题
     * @param string $desc 音乐摘要
     * @param string $musicurl 音乐文件地址
     * @param string $hgmusicurl 高品质音乐文件地址
     * @param string $thumbmediaid 缩略图文件媒体id（上传到微信服务器后的媒体ID）
     * @param bool $return
     * @return mixed
     */
    public function reply_music($title, $desc, $musicurl, $hgmusicurl = '',$thumbmediaid='', $return = false){
        $template = <<<MSG
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
<Music>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<MusicUrl><![CDATA[%s]]></MusicUrl>
<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
</Music>
</xml>
MSG;
        $xml_str = sprintf($template,
            $this->getMsg_FromUserName(),
            $this->getMsg_ToUserName(),
            time(),
            $title,
            $desc,
            $musicurl,
            $hgmusicurl,
            $thumbmediaid
        );
        return $this->reply($xml_str,$return);
    }

    /*------------------------------------------------------------------------------------------------
                                            回复图片信息
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 回复图片信息
     * @param string $mediaId 媒体id（上传到微信服务器后的媒体ID）
     * @param bool $return
     * @return mixed
     */
    public function reply_image($mediaId, $return = false){
        $template = <<<MSG
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA[%s]]></MediaId>
</Image>
</xml>
MSG;
        $xml_str = sprintf($template,
            $this->getMsg_FromUserName(),
            $this->getMsg_ToUserName(),
            time(),
            $mediaId
        );
        return $this->reply($xml_str,$return);
    }

    /*------------------------------------------------------------------------------------------------
                                            回复语音信息
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 回复语音信息
     * @param string $mediaId 媒体id（上传到微信服务器后的媒体ID）
     * @param bool $return
     * @return mixed
     */
    public function reply_voice($mediaId, $return = false){
        $template = <<<MSG
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
<Voice>
<MediaId><![CDATA[%s]]></MediaId>
</Voice>
</xml>
MSG;
        $xml_str = sprintf($template,
            $this->getMsg_FromUserName(),
            $this->getMsg_ToUserName(),
            time(),
            $mediaId
        );
        return $this->reply($xml_str,$return);
    }

    /*------------------------------------------------------------------------------------------------
                                            回复视频信息
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 回复视频信息
     * @param string $mediaId 媒体id（上传到微信服务器后的媒体ID）
     * @param string $title 视频标题
     * @param $description 视频摘要
     * @param bool $return
     * @return mixed
     */
    public function reply_video($mediaId, $title,$description,$return = false){
        $template = <<<MSG
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
<Video>
<MediaId><![CDATA[%s]]></MediaId>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
</Video>
</xml>
MSG;
        $xml_str = sprintf($template,
            $this->getMsg_FromUserName(),
            $this->getMsg_ToUserName(),
            time(),
            $mediaId,
            $title,
            $description
        );

        echo($xml_str);

        return $this->reply($xml_str,$return);
    }

    /*------------------------------------------------------------------------------------------------
                                            回复图文信息
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 回复图文信息
     * @param array $data_news
     * 数组结构:
     *  array(
     *    [0]=>array(
     *        'Title'=>'msg title',
     *        'Description'=>'summary text',
     *        'PicUrl'=>'http://www.domain.com/1.jpg',
     *        'Url'=>'http://www.domain.com/1.html'
     *    ),
     *    [1]=>....
     *  )
     * @param bool $return
     * @return mixed
     */
    public function reply_news($data_news,$return = false){
        $count = count($data_news);

        $template = <<<MSG
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>
%s
</Articles>
</xml>
MSG;
        $xml_str = sprintf($template,
            $this->getMsg_FromUserName(),
            $this->getMsg_ToUserName(),
            time(),
            $count,
            $this->parseNewsArray2Xml($data_news)
        );
        return $this->reply($xml_str,$return);


    }

    /*------------------------------------------------------------------------------------------------
                                            创建图文信息节点，用于快速使用reply_news方法
    /*------------------------------------------------------------------------------------------------*/
    /**
     * 创建图文信息节点用于快速使用reply_news方法
     * @param string $title 标题
     * @param string $url 链接地址
     * @param string $description 摘要
     * @param string $picurl 标题图片地址
     * @return array
     */
    public  function createNewsItem($title,$url,$description = '',$picurl = ''){
        return array(
            'Title'=>$title,
            'Description'=>$description,
            'PicUrl'=>$picurl,
            'Url'=>$url
        );
    }

    /**
     * 转换图文数组成xml字符串结构
     * @param $news_array
     * @return string
     */
    private function parseNewsArray2Xml($news_array){
        $xml = '';
        foreach($news_array as $news_item){
            $item = '<item>';
            if(isset($news_item['Title'])){
                $item .= ('<Title><![CDATA[' . $news_item['Title'] . ']]></Title>');
                if(isset($news_item['Description'])){
                    $item .= ('<Description><![CDATA[' . $news_item['Description'] . ']]></Description>');

                }
                if(isset($news_item['PicUrl'])){
                    $item .= ('<PicUrl><![CDATA[' . $news_item['PicUrl'] . ']]></PicUrl>');

                }
                if(isset($news_item['Url'])){
                    $item .= ('<Url><![CDATA[' . $news_item['Url'] . ']]></Url>');

                }
            }
            $item .= '</item>';
            $xml .= $item;
        }
        return $xml;
    }

    /**
     * 通过token验证微信消息签名
     * @param $token
     * @return bool
     */
    private function _checkSignature($token)
    {
        $signature = isset($_GET['signature'])?$_GET['signature']:'';
        $timestamp = isset($_GET['timestamp'])?$_GET['timestamp']:'';
        $nonce = isset($_GET['nonce'])?$_GET['nonce']:'';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
}
// END Wechat_message Class

/* End of file Wechat_message.php */
/* Location: ./libraries/kunrou/Wechat_message.php */
