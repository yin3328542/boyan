<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-3
 * Time: 下午9:37
 * 总管理后台
 */

class Auth extends Admin_Controller {

    /**
     * 登陆
     */

    public function signin()
    {
        if ($this->visitor->is_signin) {
            redirect(module_url());
        }

        //var_dump($this->input->post());

        if ($post = $this->input->post()) {
            $username = $this->input->post('username');
            $password = md5($this->input->post('password'));
            $client_ip = $this->input->ip_address();//获取客户端IP

            $resp = $this->rest->post('admin/signin', array('username' => $username, 'password' => $password, 'client_ip' => $client_ip));
            if ($this->rest->status() == 200) {
                //保存在session
                $this->session->set_userdata('admin_access_token', $resp['access_token']);
                $this->rest->access_token = $resp['access_token'];
                $admin = $this->rest->get('admin');
                $this->visitor->assign(array(
                    'id' => $admin['id'],
                    'username' => $admin['username'],
                    'role_id' => $admin['role_id'],
                    'resource' => $admin['resource']
                ));
            }
            $this->ajax_response($this->rest->status());
        } else {
            $this->data['style_file'] = 'signin';
            $this->data['js_file'] = 'signin';
            $this->load->view($this->cm, $this->data);
        }
    }

    /**
     * 退出
     */

    public function signout()
    {
        $this->visitor->logout();
        redirect(module_url('auth/signin'));
    }
}