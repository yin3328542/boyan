<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-1-22
 * Time: 上午1:27
 */

function module_url($uri = '') {
    $CI =& get_instance();
    return site_url($CI->router->fetch_module() . '/' . $uri);
}


function domain_url($sub_domain = '', $uri = '') {
    $CI =& get_instance();
    $url = $CI->config->base_url($uri);
    $sub_domain && $url = str_replace($_SERVER['HTTP_HOST'], $sub_domain . strstr($_SERVER['HTTP_HOST'], '.'), $url);
    return $url;
}

function interface_url($uri) {
    $CI = & get_instance();
    $interface_url = rtrim($CI->config->item('interface_url'), "/");
    return $interface_url . "/" . $uri;
}

function wap_url($site_id = "") {
    if ($site_id == "") {
        return "site_id can not be empty";
    }
    $CI = & get_instance();
    $wap_url = rtrim($CI->config->item('wap_url'), "/");
    return $site_id . "." . $wap_url;
}

function image_url($url)
{
    return base_url('data/attachment/'.$url);
    //return IMAGE_SERVER.IMG_SERVER_FOLDER.'/'.IMG_UPLOAD_FOLDER.'/'.$url;
}

function rel_image_url($url)
{
    return IMG_UPLOAD_FOLDER.$url;
}

/**
 * 商家地址二维码
 * @param $site_id
 * @param int $shop_id
 * @return mixed
 */
function site_qrcode_url($site_id, $shop_id = 0)
{
    $CI =& get_instance();
    if($shop_id > 0 ) {
        $url = $CI->config->base_url('shop/shop_qrcode/index/'.$site_id.'/'.$shop_id);
    } else {
        $url = $CI->config->base_url('shop/shop_qrcode/index/'.$site_id);
    }

    return $url;
}