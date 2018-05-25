<?php

/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-4-17
 * Time: 下午4:03
 */
class Column extends Admin_Controller {

    protected $top_active = 'system';
    protected $aside_active = 'column_list';

    public function index()
    {
        $this->data['js_file'] = 'column_list';
        $this->layout->view($this->m, $this->data);
    }

    public function add($id)
    {
        //获取当前栏目名称
        $p_column = $this->rest->get('column',array('id'=>$id));
        if(isset($p_column['ret']) && $p_column['ret']==0){
            $this->data['column'] = $p_column['data'];
        }else{
            $this->data['column'] = array();
        }
        $this->data['id'] = $id;
        //var_dump($p_column);die;


        $this->data['js_file'] = 'column_add';
        $this->layout->view($this->m, $this->data);
    }

    public function edit($id)
    {
        $this->data['column'] = array();
        $this->data['pid'] = 0;
        $this->data['id'] = $id;
        $this->data['js_file'] = 'column_edit';
        $this->layout->view($this->m, $this->data);
    }
} 