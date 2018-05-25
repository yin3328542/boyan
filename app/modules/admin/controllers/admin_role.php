<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-12-26
 * Time: 上午11:52
 */
class Admin_role extends Admin_Controller {

    protected $top_active = 'system';
    protected $aside_active = 'role';

    public function index()
    {
        $this->data['js_file'] = 'admin_role';
        $this->layout->view($this->c, $this->data);
    }

}