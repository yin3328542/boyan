<?php

/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-4-17
 * Time: 下午4:03
 */
class Banner extends Admin_Controller {

    protected $top_active = 'system';
    protected $aside_active = 'banner';

    public function index($type='index')
    {
        $this->load->model('banner_config_model');
        $bannerConfigList = $this->banner_config_model->find_all();
        $this->data['type'] = $type;
        $this->data['_global']['type'] = $type;
        $this->data['banner_config_list']= $bannerConfigList;
        $this->data['js_file'] = 'banner_list';
        $this->layout->view($this->m, $this->data);
    }

    public function add($type='index')
    {
        $this->data['js_file'] = 'banner_add';
        $this->data['_global']['type'] = $type;
        $this->layout->view($this->m, $this->data);
    }

    public function edit($id)
    {
        $this->data['id'] = $id;
        $this->data['js_file'] = 'banner_edit';
        $this->layout->view($this->m, $this->data);
    }
} 