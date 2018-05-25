<?php

/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-4-17
 * Time: 下午4:03
 */
class Welcome extends Admin_Controller {

    protected $top_active = 'news';
    protected $aside_active = 'news_list';

    public function index()
    {
        $this->data['js_file'] = 'news_list';
        $this->layout->view($this->m, $this->data);
    }

    public function add()
    {
        $this->data['js_file'] = 'news_add';
        $this->layout->view($this->m, $this->data);
    }

    public function edit($id)
    {
        $this->data['id'] = $id;
        $this->data['js_file'] = 'news_edit';
        $this->layout->view($this->m, $this->data);
    }
} 