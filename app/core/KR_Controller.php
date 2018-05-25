<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: river
 * Date: 13-10-18
 * Time: 下午2:38
 * 所有控制器基类
 */

class KR_Controller extends CI_Controller {

    protected $auto_load_model = FALSE; //是否自动加载模型
    protected $model_name = NULL; //模型名称
    protected $model = NULL; //模型对象
    protected $data = array(); //视图数据
    public $rest = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->dcm = $this->router->fetch_directory() . $this->router->fetch_class() . '/' . $this->router->fetch_method();
        $this->cm  = $this->router->fetch_class() . '/' . $this->router->fetch_method();
        $this->dc  = $this->router->fetch_directory() . $this->router->fetch_class();
        $this->dm  = $this->router->fetch_directory() . $this->router->fetch_method();
        $this->d  = $this->router->fetch_directory();
        $this->c  = $this->router->fetch_class();
        $this->m  = $this->router->fetch_method();

        //TODO
        //加载系统配置文件

        $this->load->model('admin_variable_model');
        $this->admin_config = $this->admin_variable_model->get_key_val();

        //自动加载相对应的数据模型
        if ($this->auto_load_model) {
            $model_name = $this->model_name ? $this->model_name . '_model' : $this->router->fetch_class() . '_model';
            $this->load->model($model_name);
            $this->model = $this->$model_name;
        }
        $this->_init_visitor(); //初始化访问者
        $this->_init_view(); //初始视图
    }

    protected function _init_visitor(){}

    protected function _init_view(){
        $this->load->library('layout');
        $this->data['_global'] = array(
            'module' => $this->router->fetch_module(),
            'controller' => $this->router->fetch_class(),
            'method' => $this->router->fetch_method(),
            'url' => array(
                'base' => base_url(),
                'api' => site_url('api').'/',
            )
        );
        //TODO 临时直接引用此文件，后期需要打包

        $this->data['app_assets_path'] = base_url() . APPPATH.'modules/'.$this->router->fetch_module().'/assets/';

        //调用底部版权信息

        $this->data = array_merge($this->admin_config,$this->data);
    }

    /**
     * @param $app_key
     * @param $secret_key
     */

    protected function _load_rest_client($app_key, $secret_key)
    {
        $this->load->library('rest', array(
            'server' => site_url('api'),
            'app_key' => $app_key,
            'secret_key' => $secret_key,
        ));
    }

    protected function _upload($field, $config = array())
    {
        $default_config['allowed_types'] = $this->setting['attachment']['allow_type'];
        $default_config['max_size'] = $this->setting['attachment']['max_size'];
        $config = array_merge($default_config, $config);
        $attachment_path = $this->setting['attachment']['path'];
        $config['upload_path'] = element('upload_path', $config) ? $attachment_path . $config['upload_path'] : $attachment_path;
        if (!is_dir($config['upload_path']) && !mkdir($config['upload_path'], 0777, TRUE)) {
            return FALSE;
        }
        $this->load->library('upload', $config);
        if ($this->upload->do_upload($field)) {
            return $this->upload->data();
        } else {
            return FALSE;
        }
    }

    /**
     * ajax方式返回数据
     */

    protected function ajax_return($data, $type='json')
    {
        if(strtoupper($type)=='JSON') {
            echo $this->output->set_content_type('application/json')->set_output(json_encode($data))->get_output();
        }else{
            // TODO 增加其它格式
        }
        exit;
    }

    /**
     * API返回数据
     * @param int $code
     * @param string $msg
     * @param array $result
     */
    protected function ajax_response($code = 200, $msg='', $result = array())
    {
        $this->ajax_return(array(
            'status' => array(
                'code' => $code,
                'msg'  => $msg,
            ),
            'result' => $result
        ));
    }

    public function captcha()
    {
        $this->load->helper('captcha');
        $cap = create_captcha(array(
            'img_path' => './data/captcha/',
            'img_url' => base_url().'data/captcha/',
            'img_width' => 80,
            'img_height' => 30
        ));
        $this->session->set_userdata('captcha', strtolower($cap['word']));
        echo $cap['image'];
    }

    public function check_captcha($input)
    {
        if ($this->session->userdata('captcha') == $input) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_captcha', '%s 不正确');
            return FALSE;
        }
    }

    /**
     * _remap实现前置后置操作
     * @param $method
     * @param array $params
     */

    public function _remap($method, $params = array())
    {
        if (method_exists($this, $method)) {
            if (method_exists($this, '_before_'.$method)) {
                if (call_user_func_array(array($this, '_before_'.$method), $params) === FALSE) {
                    return FALSE;
                }
            }
            call_user_func_array(array($this, $method), $params);
            if (method_exists($this, '_after_'.$method)) {
                if (call_user_func_array(array($this, '_after_'.$method), $params) === FALSE) {
                    return FALSE;
                }
            }
        } else {
            show_404();
        }
    }
}