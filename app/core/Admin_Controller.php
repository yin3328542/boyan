<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: river
 * Date: 13-10-23
 * Time: 下午4:30
 * 总管理员后台基类
 */

class Admin_Controller extends KR_Controller {

    protected $top_active = 'admin';
    protected $aside_active = 'index';

    protected $app_key = '5318162aac6f0086';
    protected $app_secret = '0c28536510e0b0b429750f478222d549';

    public function __construct()
    {
        parent::__construct();

        //总后台APP

        $this->_load_rest_client($this->app_key, $this->app_secret);

        $this->_check_priv();
        $this->_check_resource();//判断菜单权限
    }

    protected function _init_view()
    {
        parent::_init_view();
        $this->layout->set_layout('admin');

        $this->load->model('admin_node_model');

        $this->data['top_nav'] = $this->admin_node_model->where(array('parent_id'=>0))->order_by('listorder')->find_all();

        foreach ($this->data['top_nav'] as $k => $_nav) {
            $this->data['top_nav'][$k]['sub_nav'] = $this->admin_node_model->where(array('parent_id'=>$_nav['id']))->order_by('listorder')->find_all();
            if ($_nav['alias'] == $this->top_active) {
                $this->data['top_nav'][$k]['current'] = 1;
                //$top_active_id = $_nav['id'];
                //break;
            }
        }

        $this->data['top_active'] = $this->top_active;
        //$this->data['left_aside'] = $this->admin_node_model->where(array('parent_id'=>$top_active_id))->order_by('listorder')->find_all();
        $this->data['aside_active'] = $this->aside_active;
        $this->data['_global']['url']['signout'] = site_url('admin/auth/signout');
        $this->data['style_file'] = 'style';
        $this->data['js_file'] = 'main';
    }

    public function _init_visitor()
    {
        $this->load->library('visitor/Admin_visitor', array(), 'visitor');
    }

    public function _check_priv()
    {
        if (!$this->visitor->is_signin && !in_array($this->router->fetch_method(), array('signin'))) {
            redirect(site_url('admin/auth/signin'));
        } else {
            $this->data['admin_info'] = $this->visitor->info;
            $this->data['_global']['app_key'] = $this->app_key;
            $this->rest->access_token = $this->session->userdata('admin_access_token');
            $this->data['_global']['access_token'] = $this->session->userdata('admin_access_token');
        }
    }

    /**
     * 验证菜单访问权限
     */
    public function _check_resource()
    {
        //用户的resource设置
        $admin_info = $this->visitor->info;
        if($admin_info['role_id'] == 0) {
            return true;
        }
        //当前资源路径
        $curr_res = $this->router->fetch_module().'/'.$this->c.'/'.$this->m;
        if($this->d) {
            $curr_res = $this->router->fetch_module().'/'.$this->d.$this->c.'/'.$this->m;
        }
        //查找资源宿主
        $this->config->load('resource');
        $all_resource = $this->config->item('resource');
        if(!$all_resource OR !isset($all_resource['admin'])) {
            return true;
        }
        $resource = $all_resource['admin'];
        $res_key = '';
        foreach($resource as $key => $res) {
            if($res['resource'] == $curr_res) {
                $res_key = $key;
                break;
            }
        }
        if(!$res_key) {
            return true;
        }

        $user_res = $admin_info['resource'];
        if(!$user_res) {
            //无权限
            redirect(site_url('admin/noaccess'));
        }
        if(!in_array($res_key, $user_res)) {
            //无权限
            redirect(site_url('admin/noaccess'));
        }

        return true;
    }

}