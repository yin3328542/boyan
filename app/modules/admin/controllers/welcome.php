<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-3
 * Time: 下午9:37
 * 总管理后台
 */

class Welcome extends Admin_Controller {
    public function index()
    {
        $this->data['js_file'] = 'index';
        $this->layout->view($this->m, $this->data);
    }

    public function admin_password()
    {
        $this->data['js_file'] = 'admin_password';
        $this->layout->view($this->m, $this->data);
    }
}