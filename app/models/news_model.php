<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-9
 * Time: 下午4:35
 */

class News_model extends KR_Model {
    public function get_category_name($news_id){
        $this->load->model('category_model', 'category');
        $cate = $this->category->select('category.name')
            ->where(array('id' => $news_id))
            ->find();

        if($cate){
            $category_name = $cate['name'];
        }else{
            $category_name = '';
        }

        return $category_name;
    }
} 