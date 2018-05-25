<?php
/**
 * Created by PhpStorm.
 * User: mxb
 * Date: 14-11-18
 * Time: 下午2:03
 */
class Admin_variable extends API_Controller
{
    public function admin_variable_get() {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $options = array();
        $response['data'] = $this->model->find_all($options);
        $response['ret'] = 0;
        $this->response($response);
    }

    public function key_variable_get() {
        $name = $this->get('name');
        $data = $this->model->get_key_val($name);
        !$data && $this->response(array('ret' => 404, 'msg' => '记录不存在'));
        $this->response(array('ret' => 0, 'data' => $data));
    }

    public function add_variable_post(){

        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        if(!$this->post('remark')){
            $this->response(array('ret' => 400, 'msg' => '参数说明不能为空'));
        }
        if(!$this->_args['value']){
            $this->response(array('ret' => 400, 'msg' => '参数值不能为空'));
        }
        if(!$this->post('name')){
            $this->response(array('ret' => 400, 'msg' => '变量名不能为空'));
        }

        $name = $this->post('name');
        $type = $this->post('text_type');
        if ($type==1){
            $value = $this->post('value');
        }else{
            $value = $this->_args['value'];
        }

        $remark = $this->post('remark');

        $results = $this->model->select('name')->where('name',$name)->find_all();
        if(count($results)>0)
        {
            $this->response(array('ret' => 501, 'msg' => '该变量名已被使用'));
        }

        $data = array(
            'name' => $name,
            'type' => $type,
            'value' => $value,
            'remark' => $remark
        );

        $result=$this->model->add($data);

        if (!$result) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        } else {
            $this->response(array('ret' => 0, 'data' => $this->post()), 201);
        }
    }

    public function edit_variable_post()
    {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $name = $this->post('name');
        $type = $this->post('type');
        //var_dump($type);die;
        if($name){
            $where['name'] = $name;
            if ($type==1){
                $data['value'] = $this -> post('value');
            }else{
                $data['value'] = $this -> _args['value'];
            }

            if($this->model->where($where)->edit($data)) {
                $this->response(array('ret' => 0));
            }
            $this->response(array('ret' => 500, 'msg' => '修改失败'));
        }
        $this->response(array('ret' => 500, 'msg' => '参数错误'));
    }
}