<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-5
 * Time: 下午9:02
 */

class KR_Loader extends CI_Loader {

    protected $_ci_module_path;

    public function initialize()
    {
        parent::initialize();
        $router =& $this->_ci_get_component('router');
        $this->_ci_module_path = APPPATH.'modules/'.$router->fetch_module().'/';
        array_unshift($this->_ci_library_paths, $this->_ci_module_path);
        array_unshift($this->_ci_helper_paths, $this->_ci_module_path);
        array_unshift($this->_ci_model_paths, $this->_ci_module_path);
        $this->_ci_view_paths = array($this->_ci_module_path.'views/' => TRUE) + $this->_ci_view_paths;
        $config =& $this->_ci_get_component('config');
        array_unshift($config->_config_paths, $this->_ci_module_path);
    }

    public function widget($widget, $args = array())
    {
        if (is_file(APPPATH . 'widget/'.$widget.'.php')) {
            require_once(APPPATH.'widget/'.$widget.'.php');
            $widget = ucfirst($widget) . '_Widget';
            $widget_obj = new $widget($args);
            $widget_obj->render();
        } else {
            show_error('Unable to locate the widget you have specified: '.$widget);
        }
    }

}