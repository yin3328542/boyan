<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-12-26
 * Time: 下午6:17
*/
class Noaccess extends Admin_Controller
{
    protected $top_active = '';
    protected $aside_active = '';

    public function index()
    {
        $this->data['js_file'] = 'noaccess';
        $this->layout->view($this->c, $this->data);
    }
}