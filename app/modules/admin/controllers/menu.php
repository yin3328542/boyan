<?php
/**
 * Created by PhpStorm.
 * User: maxubin
 * Date: 14-2-10
 * Time: 下午3:33
 */

class Menu extends Admin_Controller {
    protected $top_active = 'system';
    protected $aside_active = 'menu';

    public function admin_menu()
    {
        $this->data['js_file'] = 'admin_menu';
        $this->layout->view($this->m, $this->data);
    }
}