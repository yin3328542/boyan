<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-3-28
 * Time: 下午6:14
 */

/**
 * 过滤html标签
 **/
function str_del_html($str_del_html)
{
    return preg_replace ("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is",'',$str_del_html);
}



function arrange_html_img($matches)
{
    if (false === strpos($matches[2], 'http://')) {
        return '<img '.$matches[1].'src="'.base_url('data/attachment/'.$matches[2]).'" '.$matches[3].'/>';
    } else {
        return $matches[0];
    }
}

/**
 * 二维数组转换成一维数组
 * @param $input
 * @param $columnKey
 * @param null $indexKey
 * @return array
 */
function kr_array_column($input, $columnKey, $indexKey=null)
{
    if(!function_exists('array_column')){
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false;
        $indexKeyIsNull            = (is_null($indexKey))?true :false;
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false;
        $result                         = array();
        foreach((array)$input as $key=>$row){
            if($columnKeyIsNumber){
                $tmp= array_slice($row, $columnKey, 1);
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
            }else{
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null;
            }
            if(!$indexKeyIsNull){
                if($indexKeyIsNumber){
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key))?current($key):null;
                    $key = is_null($key)?0:$key;
                }else{
                    $key = isset($row[$indexKey])?$row[$indexKey]:0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}

/**
 *  计算两组经纬度坐标 之间的距离
 *   params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km);
 *   return m or km
 */
function get_distance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2)
{
    $earth_radius = 6378.137;
    //$pi = 3.1415926;
    $radLat1 = $lat1 * PI()/ 180.0;   //PI()圆周率
    $radLat2 = $lat2 * PI() / 180.0;
    $a = $radLat1 - $radLat2;
    $b = ($lng1 * PI() / 180.0) - ($lng2 * PI() / 180.0);
    $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
    $s = $s * $earth_radius;
    $s = round($s * 1000);
    if ($len_type > 1)
    {
        $s /= 1000;
    }
    return round($s, $decimal);
}


/**
 * @param $url
 * @return bool
 * 检查图片是否存在
 */
function img_exits($url) {
    $ch = curl_init();
    $timeout = 10;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $contents = curl_exec($ch);
    if (preg_match("/404/", $contents)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 返回指定长度、格式的随机字符串
 * @param $length  (长度)
 * @param string $type  (number:只返回数字列, letter:只返回字母, all:返回字母数字混合)
 * @return string
 */
function kr_rand($length,$type = 'all') {
    $str = str_split("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
    $rand = '';
    $rand_min = 0;
    $rand_max = 61;
    if($type == 'number') {
        $rand_min = 52;
        $rand_max = 61;
    }
    if($type == 'letter') {
        $rand_min = 0;
        $rand_max = 51;
    }
    for($i = 0; $i < $length; $i ++) {
        $rand .= $str[rand($rand_min, $rand_max)];
    }
    return $rand;
}

/**
 * 格式化金额
 * @param $str     需要格式化的字符串
 * @param int $ws  保留几位小数
 * @return string  返回格式化的数据
 */
function kr_format_amount($str, $ws=2){
    return sprintf("%.{$ws}f", $str);
}

/**
  +----------------------------------------------------------
 * 功能：字符串截取指定长度
  +----------------------------------------------------------
 * @param string    $string      待截取的字符串
 * @param int       $len         截取的长度
 * @param int       $start       从第几个字符开始截取
 * @param boolean   $suffix      是否在截取后的字符串后跟上省略号
  +----------------------------------------------------------
 * @return string               返回截取后的字符串
  +----------------------------------------------------------
 */
function substr_cut($str, $len = 100, $start = 0, $suffix = 1)
{
	$array = array();
    $str = strip_tags(trim(strip_tags($str)));
    $str = str_replace(array("\n", "\t"), "", $str);
    $strlen = mb_strlen($str);
    while ($strlen) {
        $array[] = mb_substr($str, 0, 1, "utf8");
        $str = mb_substr($str, 1, $strlen, "utf8");
        $strlen = mb_strlen($str);
    }
    $end = $len + $start;
    $str = '';
    for ($i = $start; $i < $end; $i++) {
        isset($array[$i]) && $str.=$array[$i];
    }
    return count($array) > $len ? ($suffix == 1 ? $str . "..." : $str) : $str;
}

/**二维码增加logo
 * @param bool $qr
 * @param bool $logo
 * @param bool $border
 * @param int $px
 * @return bool|resource
 */
function qrcode_logo($qr = false, $logo = false, $border = false, $px = 10)
{
    if($logo !== FALSE && $qr !== false)
    {
        $QR = imagecreatefromstring(file_get_contents($qr));
        $logo_img = imagecreatefromstring(file_get_contents($logo));
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);

        $logo_width = imagesx($logo_img);
        $logo_height = imagesy($logo_img);

        $logo_qr_width = $px * 11;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;

        //先加边框
        if($border !== false) {
            $border_img = imagecreatefromstring(file_get_contents($border));
            $boder_h = imagesx($border_img);
            $border_w = imagesy($border_img);
            imagecopyresampled($logo_img, $border_img, 0, 0, 0, 0, $logo_width, $logo_height, $boder_h, $border_w);
        }

        imagecopyresampled($QR, $logo_img, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        return $QR;
    }
    return false;
}


function kr_curlGet($url)
{
    if(empty($url))return '';
    $url_ch = curl_init();
    curl_setopt($url_ch, CURLOPT_URL, $url);
    curl_setopt($url_ch, CURLOPT_USERAGENT, kr_randUseragent());
    curl_setopt($url_ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($url_ch, CURLOPT_FOLLOWLOCATION, 1);//是否抓取跳转后的页面

    curl_setopt($url_ch, CURLOPT_REFERER, $url);

    curl_setopt($url_ch, CURLOPT_TIMEOUT, 15);
    $url_output = trim(curl_exec($url_ch));
    curl_close($url_ch);
    if($url_output)
    {
        return $url_output;
    }
    return '';
}
function kr_curlPost($url, $postFields = array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, kr_randUseragent());
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (is_array($postFields) && 0 < count($postFields))
    {
        $postBodyString = "";
        foreach ($postFields as $k => $v)
        {
            $postBodyString .= "$k=" . urlencode($v) . "&";
        }
        unset($k, $v);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
    }
    $reponse = curl_exec($ch);
    if (curl_errno($ch)){
        //throw new Exception(curl_error($ch),0);
        //var_dump( curl_error($ch) );
    }
    else{
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 !== $httpStatusCode){
            //throw new Exception($reponse,$httpStatusCode);
            //var_dump($reponse,$httpStatusCode);
        }
    }
    curl_close($ch);
    return $reponse;
}
function kr_randUseragent()
{
    $user_agent = array(
        'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)',
        'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.96 Safari/537.4',
        'Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:15.0) Gecko/20120830 Firefox/15.0 FirePHP/0.7.1',
        'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) )',
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) )',
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.79 Safari/535.11',
        'Opera/9.80 (Windows NT 5.1; U; Edition IBIS; zh-cn) Presto/2.10.229 Version/11.62',
        'Mozilla/5.0 (Windows NT 5.1; rv:11.0) Gecko/20100101 Firefox/11.0',
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; 360SE)',
        'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ;  QIHU 360EE)',
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; Maxthon/3.0)',
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; TencentTraveler 4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) )',
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3'
    );
    return $user_agent[array_rand($user_agent)];
}

