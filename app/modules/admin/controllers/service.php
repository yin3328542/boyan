<?php

/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-4-17
 * Time: 下午4:03
 */
class Service extends Admin_Controller {

    protected $top_active = 'service';
    protected $aside_active = 'service';

    public function index()
    {
        $this->data['js_file'] = 'service/service_list';
        $this->layout->view($this->cm, $this->data);
    }

    public function add()
    {
        $this->data['js_file'] = 'service/service_add';
        $this->layout->view($this->cm, $this->data);
    }

    public function edit($id)
    {
        $this->data['id'] = $id;
        $this->data['js_file'] = 'service/service_edit';
        $this->layout->view($this->cm, $this->data);
    }
} 