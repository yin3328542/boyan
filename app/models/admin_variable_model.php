<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-9
 * Time: 下午4:35
 */

class Admin_variable_model extends KR_Model {
    public function get_key_val($name=''){
        $data = $name ? $this->where(array('name' => $name))->find() : $this->find_all();
        if(empty($name)){
            foreach($data as $item){
                $key = $item['name'];
                $_data[$key] = $item['value'];
            }
            return (array) $_data;
        }
        return $data;
    }

} 