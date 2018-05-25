<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-11-12
 * Time: 下午4:08
 */
class Clear_cache extends Api_Controller {

    public function index() {
        $this->session->sess_destroy();
        $this->memcached();
        echo 'ok';
    }

    public function memcached() {
        //清缓存
        $host = parse_url(current_url(), PHP_URL_HOST);
        $host_items = explode('.', $host);
        $site_id = $host_items[0];
        $this->load->model('cache_model');
        $this->cache_model->remove($site_id, 'SITE_INFO');
        $this->cache_model->remove($site_id, 'SITE_WEIXIN');
        return true;
    }
}