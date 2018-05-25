<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-2-11
 * Time: 下午10:44
 */

class Layout {

    public $layout_path = '_layouts/';
    public $layout = 'main';

    public function set_layout($layout)
    {
        $this->layout = $layout;
    }

    public function view($view, $data=array(), $return=FALSE)
    {
        if (strpos($view, ':') !== FALSE) {
            $view_arr = explode(':', $view);
            $this->layout = $view_arr[0];
            $view = $view_arr[1];
        }
        $layout_file = $this->layout_path . '/' . $this->layout;
        $loaded_data = array();
        $loaded_data['block_content'] = $this->load->view($view, $data, TRUE);
        if($return) {
            $output = $this->load->view($layout_file, $loaded_data, TRUE);
            return $output;
        } else {
            $this->load->view($layout_file, $loaded_data);
        }
    }

    function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }
} 