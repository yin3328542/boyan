<?php
/**
 * Created by PhpStorm.
 * User: mxb
 * Date: 14-12-26
 * Time: 下午2:09
 */
class Admin_role extends API_Controller
{
    /**
     * 角色列表
     */
    public function roles_get()
    {

        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403));
        }
        $data = $this->model->select('*')->find_all();
        foreach($data as $k => $val) {
            $data[$k]['resource'] = json_decode($val['resource'], true);
        }

        $this->response(array('ret' => 0, 'data' => $data));
    }

    public function role_post()
    {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403));
        }
        $data['role_name'] = $this->post('role_name');
        $data['resource']  = $this->post('resource');
        if(is_array($data['resource'])) {
            $data['resource'] = json_encode($data['resource']);
        }

        //名称是否存在
        if($this->model->where(array('role_name' => $data['role_name']))->count()) {
            $this->response(array('ret' => 410, 'msg' => '角色名称已存在'));
        }
        if(!$this->model->add($data)) {
            $this->response(array('ret' => 500, 'msg' => '系统错误 '));
        }
        $this->response(array('ret' => 0, 'msg' => '添加成功'));
    }

    public function role_put()
    {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403));
        }
        $id = $this->put('id');
        $data['role_name'] = $this->put('role_name');
        $data['resource']  = $this->put('resource');
        if(is_array($data['resource'])) {
            $data['resource'] = json_encode($data['resource']);
        }

        //名称是否存在
        if($this->model->where(array('id !=' => $id, 'role_name' => $data['role_name']))->count()) {
            $this->response(array('ret' => 410, 'msg' => '角色名称已存在'));
        }
        if(!$this->model->where('id = '.$id)->edit($data)) {
            $this->response(array('ret' => 500, 'msg' => '系统错误 '));
        }
        $this->response(array('ret' => 0, 'msg' => '修改成功'));
    }

    public function role_delete()
    {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403));
        }
        $site_id = $this->_user['site_id'];
        $data['site_id'] = $site_id;
        $id = $this->post('id');

        //角色是否被使用
        $this->load->model('admin_model');
        $res = $this->admin_model->where(array('role_id' => $id))->find();
        if($res) {
            $this->response(array('ret' => 412, 'msg' => '角色已被使用，不能删除 '));
        }
        if(!$this->model->where(array('id' => $id))->delete()) {
            $this->response(array('ret' => 500, 'msg' => '系统错误 '));
        }
        $this->response(array('ret' => 0, 'msg' => '修改成功'));
    }

    public function resource_get()
    {
        $this->config->load('resource');
        $resource = $this->config->item('resource');
        if(!isset($resource['admin'])) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }
        $this->response(array('ret' => 0, 'data' => $resource['admin']));
    }
}