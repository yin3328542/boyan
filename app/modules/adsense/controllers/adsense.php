<?php

/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-4-17
 * Time: 下午4:03
 */
class Adsense extends Admin_Controller {

    protected $top_active = 'system';
    protected $aside_active = 'adsense';

    public function index()
    {
        $this->data['js_file'] = 'adsense_list';
        $this->layout->view($this->m, $this->data);
    }
    public function edit($id)
    {
        $this->data['id'] = $id;
        $this->data['js_file'] = 'adsense_edit';
        $this->layout->view($this->m, $this->data);
    }
} 