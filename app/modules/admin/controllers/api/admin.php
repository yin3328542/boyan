<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-1-24
 * Time: 下午10:31
 *
 * @property Admin_model $model
 */

class Admin extends API_Controller {

    public function admins_get()
    {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403));
        }
        $data = $this->model->select('admin.*, admin_role.role_name')
            ->join('admin_role', 'admin_role.id = admin.role_id', 'left')
            ->find_all();
        foreach($data as $k => $val) {
            if($data[$k]['role_id'] == 0) {
                $data[$k]['role_name'] = '超级管理员';
            }
            $data[$k]['dt_login'] = $val['dt_login'] > 0 ? date('Y-m-d H:i:s', $val['dt_login']) : '';
            $data[$k]['dt_add'] = date('Y-m-d H:i:s', $val['dt_add']);
        }

        $this->response(array('ret' => 0, 'data' => $data));
    }

    public function admin_get()
    {
        if ($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(NULL, 403);
        }
        if (!$id = intval($this->get('id')) && $this->app['type'] == APPTYPE_ADMIN ) {
            $id = $this->_user['id'];
        }
        !$id && $this->response(array('status' => false, 'error' => 'Parameter Error.'), 403);
        $admin = $this->model->select('admin.*, admin_role.resource')
            ->join('admin_role', 'admin.role_id = admin_role.id', 'left')
            ->where(array('admin.id' => $id))
            ->find();
        if($admin['resource']) {
            $admin['resource'] = json_decode($admin['resource'], true);
        }
        $this->response($admin);
    }

    public function admin_post()
    {
        if ($this->app['type'] != APPTYPE_ADMIN ) {
            $this->response(array('ret' => 403, 'msg' => '没有权限'), 403);
        }

        $role_id = trim($this->post('role_id'));
        $password = md5($this->post('password'));
        $confirm_password = md5($this->post('confirm_password'));
        $username = $this->post('username');
        $real_name = $this->post('real_name');
        $email = $this->post('email');
        $ip_login = $this->input->ip_address();

        if($role_id<1)
        {
            $this->response(array('ret' => 405, 'msg' => '请选择管理员角色或先去添加管理员角色'));
        }

        if($password !== $confirm_password)
        {
            $this->response(array('ret' => 400, 'msg' => '两次密码不一致'));
        }

        $results = $this->model->select('id')
            ->where('username',$username)
            ->find();

        if($results)
        {
            $this->response(array('ret' => 501, 'msg' => '该邮箱已被使用'));
        }

        $current_time=time();

        $data = array(
            'role_id' => $role_id,
            'username' => $username,
            'real_name' => $real_name,
            'password' => $password,
            'email' => $email,
            'ip_add' => $ip_login,
            'dt_add' => $current_time,
            'status' => 1,
        );

        $result=$this->model->add($data);

        if (!$result) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        } else {
            $this->response(array('ret' => 0, 'data' => $this->post()), 201);
        }
    }

    public function admin_put()
    {

    }

    public function admin_delete()
    {
        $id = $this->get('id');
        $this->model->_delete($id);

        $this->response(array('ret' => 0, 'msg' => '删除成功'));
    }

    /**
     * admin密码修改
     */
    public function password_post()
    {
        switch ($this->app['type']) {
            case APPTYPE_ADMIN :
                $id = intval($this->_user['id']);
                break;
            case APPTYPE_SITE :
                $id = intval($this->_user['id']);
                break;
            default :
                $this->response(array('ret' => 403, 'msg' => '没有权限'), 403);
                break;
        }

        !isset($id) && $id = $this->_args['id'];
        $old_pwd = md5($this->_args['old_pwd']);
        $new_pwd = md5($this->_args['new_pwd']);
        $pwd = md5($this->_args['pwd']);

        if($new_pwd != $pwd){
            $this->response(array('ret' => 403, 'msg' => '两次输入的密码不一致'));
        }
        //$this->load->model('admin_model');
        $arr = $this->model->where(array('id'=>$id))->find();
        if($arr['password'] != $old_pwd){
            $this->response(array('ret' => 403, 'msg' => '输入的旧密码不正确'));
        }else{
            if($this->model->where(array('id'=>$id))->edit(array('password'=>$pwd))){
                $this->response(array('ret' => 0, 'msg' => '修改成功，下次请使用新密码登录'));
            }else{
                $this->response(array('ret' => 403, 'msg' => '修改失败'));
            }
        }

    }

    public function company_put()
    {
        if ($this->app['type'] != APPTYPE_ADMIN ) {
            $this->response(array('ret' => 403, 'msg' => '没有权限'), 403);
        }

        $description = $this->put('description');


        $data = array(
            'description' => $description,
        );

        $result=$this->model->where(['id'=>$this->_user['id']])->edit($data);

        if (!$result) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        } else {
            $this->response(array('ret' => 0,'msg'=>'保存成功'));
        }
    }

    public function company_get()
    {
        if ($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(NULL, 403);
        }
        if (!$id = intval($this->get('id')) && $this->app['type'] == APPTYPE_ADMIN ) {
            $id = $this->_user['id'];
        }
        !$id && $this->response(array('status' => false, 'error' => 'Parameter Error.'), 403);
        $admin = $this->model->select('admin.*')
            ->where(array('admin.id' => $id))
            ->find();
        $response['ret'] = 0;
        $response['data'] = $admin;
        $this->response($response);
    }


} 