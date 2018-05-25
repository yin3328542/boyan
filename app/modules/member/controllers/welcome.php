<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-10-30
 * Time: 下午1:54
 */
class Welcome extends Siter_Controller {

    protected $top_active = 'member';
    protected $aside_active = 'member_list';

    public function index()
    {
        $this->data['js_file'] = 'member_list';
        $this->layout->view($this->c, $this->data);
    }
}