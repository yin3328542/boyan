<?php
/**
 * Created by PhpStorm.
 * User: maxubin
 * Date: 14-2-10
 * Time: 下午3:33
 */

class Variable extends Admin_Controller {
    protected $top_active = 'system';
    protected $aside_active = 'variable';

    public function admin_variable()
    {
        $this->data['js_file'] = 'admin_variable';
        $this->layout->view($this->m, $this->data);
    }


}