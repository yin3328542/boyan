<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-1-24
 * Time: 下午5:23
 * @property Admin_model $model
 */

class Auth extends API_Controller {

    protected $model_name = 'admin';

    public function signin_post()
    {
        //判断签名
        if (!$this->_sign_veryfy($this->_args, $this->_args['sign'])) {
            $this->response(array('status' => false, 'error' => 'Parameter Error.'), 403);
        }

        $username = trim($this->post('username'));
        $password = trim($this->post('password'));
        $admin = $this->model->where(array('username'=>$username, 'password'=>$password))->find();
        if (!$admin) {
            $valid = FALSE;
            $status = 401;
        } else {
            //更新登录信息
            $last_ip = trim($this->post('client_ip'));
            $dt_login = time();
            $this->model->where(array('id' => $admin['id']))->edit(array('last_ip' => $last_ip, 'dt_login' => $dt_login));
            //发牌照
            $token = $this->_generate_token(APPTYPE_ADMIN, $admin['id']);
            $valid = array('access_token'=>$token);
            $status = 200;
        }

        $this->response($valid, $status);
    }
}