<?php

/**
 * pc页面控制器基类
 *
 * 提供数据：
 *  1、站点配置信息
 *
 *
 *
 * 页面鉴权逻辑：
 *
 * 1.判断是否有访问鉴权标记 $author_request_session = $_GET['state']
 * 2.判断混存中是否有以author_request为key的缓存标记，
 *      如果有：
 *          说明是鉴权回调，通过返回的openid去读取关注用户信息（如果没有读取到，说明是没有关注的用户），更新session，标记用户登录
 *      如果没有：
 *          说明是非法或错误的调用，则执行步骤3
 * 3.读取session信息
 *      如果有：继续页面逻辑
 *      如果没有：进行鉴权操作
 *
 */

class Pc_Controller extends KR_Controller
{
    protected $_get_args = array();
    protected $_post_args = array();

    protected $data = array(); //视图级数据
    protected $tpl  = 'default';

    public function __construct()
    {
        parent::__construct();
        parent::_init_view();
        $this->layout->set_layout('pc');

        $this->_get_args = array_merge($this->_get_args, $this->uri->ruri_to_assoc());
        $this->_parse_get();
        $this->_parse_post();

        $this->load->helper('url');

        $this->_load_rest_client(REST_SERVER, APP_KEY, APP_SECRET);

        $this->data['_global']['url'] = array(
            'site_url' => base_url(),
            'api' => REST_SERVER,
        );

        //9.设置视图内暴露的数据
        $this->data['sys_controller'] = $this->c;
        $this->data['sys_view'] = $this->cm;
        $this->data['title'] = '';
        $this->data['_global']['app_key'] = APP_KEY;
        $this->columns_str();

    }

    protected function _load_rest_client($rest_server, $app_key, $secret_key)
    {
        $this->load->library('rest', array(
            'server' => $rest_server,
            'app_key' => $app_key,
            'secret_key' => $secret_key,
        ));
    }

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

    /**
     * 解析 GET
     */
    protected function _parse_get()
    {
        if ($this->input->is_cli_request()) {
            $args = $_SERVER['argv'];
            unset($args[0]);
            $_SERVER['QUERY_STRING'] =  $_SERVER['PATH_INFO'] = $_SERVER['REQUEST_URI'] = '/' . implode('/', $args) . '/';
        }
        parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $get);
        $this->_get_args = array_merge($this->_get_args, $get);
    }

    /**
     * 解析 POST
     */
    protected function _parse_post()
    {
        $this->_post_args = $_POST;
    }

    //获取get
    public function get($key = NULL, $xss_clean = TRUE)
    {
        if ($key === NULL) {
            return $this->_get_args;
        }
        return array_key_exists($key, $this->_get_args) ? $this->_xss_clean($this->_get_args[$key], $xss_clean) : FALSE;
    }

    //获取post
    public function post($key = NULL, $xss_clean = TRUE)
    {
        if ($key === NULL) {
            return $this->_post_args;
        }
        return array_key_exists($key, $this->_post_args) ? $this->_xss_clean($this->_post_args[$key], $xss_clean) : FALSE;
    }

    //参数过滤
    protected function _xss_clean($val, $process)
    {
        return $process ? $this->security->xss_clean($val) : $val;
    }

    /**
     * 导航控制器公用函数
     * @param $name_en 【别名】
     **/
    public function column_str($name_en='')
    {
        $p_column = $this->rest->get('column',array('name_en'=>$name_en));
        if(isset($p_column['ret']) && $p_column['ret']==0){
            $this->data['column'] = $p_column['data'];
        }else{
            $this->data['column'] = array();
        }
    }

    public function columns_str()
    {
        $p_columns = $this->rest->get('columns',array('pid'=>0,'status'=>1));
        if(isset($p_columns['ret']) && $p_columns['ret']==0){
            $columns = $p_columns['data'];
        }else{
            $columns = [];
        }
        foreach ($columns as $k=>$v){
            $v['child'] = $this->columns_str_by_id($v['id']);
            $columns[$k] = $v;
        }
        $this->data['columns'] = $columns;
    }

    public function columns_str_by_id($p_id)
    {
        if (!$p_id){$p_id = 0;}
        $p_columns = $this->rest->get('columns',array('pid'=>$p_id,'status'=>1));
        if(isset($p_columns['ret']) && $p_columns['ret']==0){
            $columns = $p_columns['data'];
        }else{
            $columns = [];
        }
        return $columns;

    }
}