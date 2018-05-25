<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-2-10
 * Time: 下午3:33
 */

class Join extends Admin_Controller {
    protected  $top_active = 'join';
    protected $aside_active = 'join';

    public function index()
    {
        $this->data['js_file'] = 'join';
        $this->layout->view($this->c, $this->data);
    }
}