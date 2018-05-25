<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-2-10
 * Time: 下午3:33
 */

class Admin extends Admin_Controller {
    protected  $top_active = 'system';
    protected $aside_active = 'admin_list';

    public function index()
    {
        $this->data['js_file'] = 'admin_list';
        $this->layout->view($this->c, $this->data);
    }

    public function add()
    {
        $this->layout->view($this->cm, $this->data);
    }

    public function admin_message()
    {
        $this->data['js_file'] = 'admin_message';
        //var_dump($this->m);die;
        $this->data['receive_id'] = $this->input->get('receive_id');
        $this->data['type'] = $this->input->get('type');
        $this->data['name'] = $this->input->get('name');
        $this->layout->view($this->m, $this->data);
    }
    public function admin_message_list()
    {
        $this->data['js_file'] = 'admin_message_list';
        $this->layout->view($this->m, $this->data);
    }

    public function company()
    {
        $this->data['js_file'] = 'company';
        $this->data['_global']['admin_id'] = $this->data['admin_info']['id'];
        $this->layout->view($this->m, $this->data);
    }
}