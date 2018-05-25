<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-12
 * Time: 下午6:41
 *
 * REST服务端
 */
abstract class REST_Controller extends CI_Controller
{
    protected $rest_format = NULL;
    // 允许的HTTP请求方式
    protected $allowed_http_methods = array('get', 'delete', 'post', 'put', 'options', 'patch', 'head');
    protected $request = NULL;
    protected $response = NULL;
    protected $rest = NULL;

    protected $_get_args = array();
    protected $_post_args = array();
    protected $_insert_id = '';
    protected $_put_args = array();
    protected $_delete_args = array();
    protected $_patch_args = array();
    protected $_head_args = array();
    protected $_options_args = array();
    protected $_args = array();
    protected $_allow = TRUE;
    protected $_zlib_oc = FALSE;
    protected $_start_rtime = '';
    protected $_end_rtime = '';
    protected $_supported_formats = array(
        'xml' => 'application/xml',
        'json' => 'application/json',
        'jsonp' => 'application/javascript',
        'serialized' => 'application/vnd.php.serialized',
        'php' => 'text/plain',
        'html' => 'text/html',
        'csv' => 'application/csv'
    );

    public function __construct()
    {
        parent::__construct();

        $this->_start_rtime = microtime(TRUE);

        // 初始对象
        $this->request = new stdClass();
        $this->response = new stdClass();
        $this->rest = new stdClass();

        $this->_zlib_oc = @ini_get('zlib.output_compression');
        $this->load->config('rest');

        // 黑名单
        if ($this->config->item('rest_ip_blacklist_enabled')) {
            $this->_check_blacklist_auth();
        }
        // 是否 SSL?
        $this->request->ssl = $this->_detect_ssl();
        // 请求方式 POST, DELETE, GET, PUT?
        $this->request->method = $this->_detect_method();
        // Create argument container, if nonexistent
        if ( ! isset($this->{'_'.$this->request->method.'_args'})) {
            $this->{'_'.$this->request->method.'_args'} = array();
        }
        $this->_get_args = array_merge($this->_get_args, $this->uri->ruri_to_assoc());

        $this->load->library('format');
        // 请求数据格式
        $this->request->format = $this->_detect_input_format();
        $this->request->body = NULL;
        //参数赋值
        $this->{'_parse_' . $this->request->method}();
        if ($this->request->format and $this->request->body) {
            $this->request->body = $this->format->factory($this->request->body, $this->request->format)->to_array();
            $this->{'_'.$this->request->method.'_args'} = $this->request->body;
        }
        $this->_args = array_merge($this->_get_args, $this->_options_args, $this->_patch_args, $this->_head_args , $this->_put_args, $this->_post_args, $this->_delete_args, $this->{'_'.$this->request->method.'_args'});

        //临时解决跨域问题，以后取值需要直接用 $this->_args
        $this->_get_args = $this->_options_args = $this->_patch_args = $this->_head_args = $this->_put_args = $this->_post_args = $this->_delete_args = $this->{'_'.$this->request->method.'_args'} = $this->_args;

        $this->response->format = $this->_detect_output_format();
        $this->response->lang = $this->_detect_lang();

        //应用验证
        $this->_detect_app();

        if ( ! $this->input->is_ajax_request() AND config_item('rest_ajax_only')) {
            $this->response(array('status' => false, 'error' => 'Only AJAX requests are accepted.'), 505);
        }
    }

    public function __destruct()
    {
        $this->_end_rtime = microtime(TRUE);
        if (config_item('rest_enable_logging')) {
            //$this->_log_access_time();
        }
    }

    public function _remap($object_called, $arguments)
    {
        if (config_item('force_https') AND !$this->_detect_ssl()) {
            $this->response(array('status' => false, 'error' => 'Unsupported protocol'), 403);
        }

        $pattern = '/^(.*)\.('.implode('|', array_keys($this->_supported_formats)).')$/';
        if (preg_match($pattern, $object_called, $matches)) {
            $object_called = $matches[1];
        }

        $controller_method = $object_called.'_'.$this->request->method;

        if ( ! method_exists($this, $controller_method)) {
            $this->response(array('status' => false, 'error' => 'Unknown method.'), 404);
        }

        //检测调用次数
        if ($this->_check_limit()) {
            $this->response(array('status' => false, 'error' => 'This API key has reached the hourly limit.'), 401);
        }

        call_user_func_array(array($this, $controller_method), $arguments);

    }

    /**
     * 响应
     */
    public function response($data = null, $http_code = null)
    {
        global $CFG;

        if ($data === NULL && $http_code === null) {
            $http_code = 404;
            $output = NULL;
        } else if ($data === NULL && is_numeric($http_code)) {
            $output = NULL;
        } else {
            if ($CFG->item('compress_output') === TRUE && $this->_zlib_oc == FALSE) {
                if (extension_loaded('zlib')) {
                    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
                        ob_start('ob_gzhandler');
                    }
                }
            }

            is_numeric($http_code) || $http_code = 200;

            if (method_exists($this, '_format_'.$this->response->format)) {
                header('Content-Type: '.$this->_supported_formats[$this->response->format]);
                $output = $this->{'_format_'.$this->response->format}($data);
            } elseif (method_exists($this->format, 'to_'.$this->response->format)) {
                header('Content-Type: '.$this->_supported_formats[$this->response->format]);
                $output = $this->format->factory($data)->{'to_'.$this->response->format}();
            } else {
                $output = $data;
            }
        }

        header('HTTP/1.1: ' . $http_code);
        header('Status: ' . $http_code);

        if (!$this->_zlib_oc && !$CFG->item('compress_output')) {
            header('Content-Length: ' . strlen($output));
        }
        exit($output);
    }

    /**
     * 检测应用
     */
    private function _detect_app() {
        if (!$key = $this->input->server('HTTP_APP_KEY')) {
            if(!$key = $this->input->get_post('app_key')){
                $key = $this->_args['app_key'];
            }
        }
        !$key && $this->response(array('status' => false, 'error' => 'Missing App Key.'), 403);
        $this->load->model('app_model');
        if (!$this->app = $this->app_model->where('key', $key)->find()) {
            $this->response(array('status' => false, 'error' => 'Invalid App Key.'), 403);
        }
        //判断请求的IP或者域名 TODO
    }

    private function _check_limit()
    {

    }

    protected function _sign_veryfy($params, $sign)
    {
        if (isset($params['sign'])) {
            unset($params['sign']);
        }
        ksort($params);
        reset($params);
        $params_signed = '';
        foreach ($params as $k => $v) {
            $params_signed .= "$k$v";
        }
        unset($k, $v);
        $params_signed .= $this->app['secret'];
        if ($sign == strtoupper(md5($params_signed))) {
            return TRUE;
        }
        return FALSE;
    }

    /*
     * 检测SSL
     */
    protected function _detect_ssl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on");
    }

    /*
     * 检测输入格式
     */
    protected function _detect_input_format()
    {
        if ($this->input->server('CONTENT_TYPE')) {
            foreach ($this->_supported_formats as $format => $mime) {
                if (strpos($match = $this->input->server('CONTENT_TYPE'), ';')) {
                    $match = current(explode(';', $match));
                }
                if ($match == $mime) {
                    return $format;
                }
            }
        }
        return NULL;
    }

    /**
     * 检测输出格式
     */
    protected function _detect_output_format()
    {
        if (isset($this->_get_args['callback'])){
            return 'jsonp';
        }

        $pattern = '/\.('.implode('|', array_keys($this->_supported_formats)).')$/';

        if (preg_match($pattern, $this->uri->uri_string(), $matches)) {
            return $matches[1];
        } elseif ($this->_get_args AND !is_array(end($this->_get_args)) AND preg_match($pattern, end($this->_get_args), $matches)) {
            $last_key = end(array_keys($this->_get_args));
            $this->_get_args[$last_key] = preg_replace($pattern, '', $this->_get_args[$last_key]);
            $this->_args[$last_key] = preg_replace($pattern, '', $this->_args[$last_key]);
            return $matches[1];
        }

        if (isset($this->_get_args['format']) AND array_key_exists($this->_get_args['format'], $this->_supported_formats)) {
            return $this->_get_args['format'];
        }

        if ($this->config->item('rest_ignore_http_accept') === FALSE AND $this->input->server('HTTP_ACCEPT')) {
            foreach (array_keys($this->_supported_formats) as $format) {
                if (strpos($this->input->server('HTTP_ACCEPT'), $format) !== FALSE) {
                    if ($format != 'html' AND $format != 'xml') {
                        return $format;
                    } else {
                        if ($format == 'html' AND strpos($this->input->server('HTTP_ACCEPT'), 'xml') === FALSE) {
                            return $format;
                        } elseif ($format == 'xml' AND strpos($this->input->server('HTTP_ACCEPT'), 'html') === FALSE) {
                            return $format;
                        }
                    }
                }
            }
        }

        if (!empty($this->rest_format)) {
            return $this->rest_format;
        }

        return config_item('rest_default_format');
    }

    /**
     * 检测语言
     */
    protected function _detect_lang()
    {
        if (!$lang = $this->input->server('HTTP_ACCEPT_LANGUAGE')) {
            return NULL;
        }

        if (strpos($lang, ',') !== FALSE) {
            $langs = explode(',', $lang);
            $return_langs = array();
            foreach ($langs as $lang) {
                list($lang) = explode(';', $lang);
                $return_langs[] = trim($lang);
            }
            return $return_langs;
        }

        return $lang;
    }

    /**
     * 检测请求方式
     */
    protected function _detect_method()
    {
        $method = strtolower($this->input->server('REQUEST_METHOD'));

        if ($this->config->item('enable_emulate_request')) {
            if ($this->input->get_post('_method')) {
                $method = strtolower($this->input->get_post('_method'));
            } elseif ($this->input->server('HTTP_X_HTTP_METHOD_OVERRIDE')) {
                $method = strtolower($this->input->server('HTTP_X_HTTP_METHOD_OVERRIDE'));
            }
        }

        if (in_array($method, $this->allowed_http_methods) && method_exists($this, '_parse_' . $method)) {
            return $method;
        }

        return 'get';
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
        $this->request->format and $this->request->body = file_get_contents('php://input');
    }

    /**
     * 解析 PUT
     */
    protected function _parse_put()
    {
        if ($this->request->format) {
            $this->request->body = file_get_contents('php://input');
        } else {
            parse_str(file_get_contents('php://input'), $this->_put_args);
        }
    }

    /**
     * 解析 HEAD
     */
    protected function _parse_head()
    {
        parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $head);
        $this->_head_args = array_merge($this->_head_args, $head);
    }

    /**
     * 解析 OPTIONS
     */
    protected function _parse_options()
    {
        parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $options);
        $this->_options_args = array_merge($this->_options_args, $options);
    }

    /**
     * 解析 PATCH
     */
    protected function _parse_patch()
    {
        if ($this->request->format) {
            $this->request->body = file_get_contents('php://input');
        } else {
            parse_str(file_get_contents('php://input'), $this->_patch_args);
        }
    }

    /**
     * 解析 DELETE
     */
    protected function _parse_delete()
    {
        parse_str(file_get_contents('php://input'), $this->_delete_args);
    }

    // 输入 --------------------------------------------------------------

    public function get($key = NULL, $xss_clean = TRUE)
    {
        if ($key === NULL) {
            return $this->_get_args;
        }
        return array_key_exists($key, $this->_get_args) ? $this->_xss_clean($this->_get_args[$key], $xss_clean) : FALSE;
    }

    public function options($key = NULL, $xss_clean = TRUE)
    {
        if ($key === NULL) {
            return $this->_options_args;
        }
        return array_key_exists($key, $this->_options_args) ? $this->_xss_clean($this->_options_args[$key], $xss_clean) : FALSE;
    }

    public function head($key = NULL, $xss_clean = TRUE)
    {
        if ($key === NULL) {
            return $this->head_args;
        }
        return array_key_exists($key, $this->head_args) ? $this->_xss_clean($this->head_args[$key], $xss_clean) : FALSE;
    }

    public function post($key = NULL, $xss_clean = TRUE)
    {
        if ($key === NULL) {
            return $this->_post_args;
        }
        return array_key_exists($key, $this->_post_args) ? $this->_xss_clean($this->_post_args[$key], $xss_clean) : FALSE;
    }

    public function put($key = NULL, $xss_clean = TRUE)
    {
        if ($key === NULL) {
            return $this->_put_args;
        }
        return array_key_exists($key, $this->_put_args) ? $this->_xss_clean($this->_put_args[$key], $xss_clean) : FALSE;
    }

    public function delete($key = NULL, $xss_clean = TRUE)
    {
        if ($key === NULL) {
            return $this->_delete_args;
        }
        return array_key_exists($key, $this->_delete_args) ? $this->_xss_clean($this->_delete_args[$key], $xss_clean) : FALSE;
    }

    public function patch($key = NULL, $xss_clean = TRUE)
    {
        if ($key === NULL) {
            return $this->_patch_args;
        }
        return array_key_exists($key, $this->_patch_args) ? $this->_xss_clean($this->_patch_args[$key], $xss_clean) : FALSE;
    }

    protected function _xss_clean($val, $process)
    {
        return $process ? $this->security->xss_clean($val) : $val;
    }

    public function validation_errors()
    {
        $string = strip_tags($this->form_validation->error_string());
        return explode("\n", trim($string, "\n"));
    }

    // 安全 ---------------------------------------------------------

    protected function _check_blacklist_auth()
    {
        $blacklist = explode(',', config_item('rest_ip_blacklist'));
        foreach ($blacklist AS &$ip) {
            $ip = trim($ip);
        }
        if (!in_array($this->input->ip_address(), $blacklist)) {
            $this->response(array('status' => false, 'error' => 'IP Denied'), 401);
        }
    }

    protected function _force_loopable($data)
    {
        if ( ! is_array($data) AND !is_object($data)) {
            $data = (array) $data;
        }
        return $data;
    }

    // 格式化 ---------------------------------------------------------

    protected function _format_jsonp($data = array())
    {
        return $this->get('callback').'('.json_encode($data).')';
    }

}
