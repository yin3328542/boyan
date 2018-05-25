<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-11-13
 * Time: 下午7:37
 */

class Uploader extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $site_info = $this->session->userdata('siter_info');
        if($site_info && isset($site_info['site_id'])) {
            $site_id = $site_info['site_id'];
        } else {
            show_error('no sign', 403);
        }

        $img_path = $this->input->get('path') ? $this->input->get('path') : 'image';

        $this->data['js_file'] = 'uploader';
        $this->data['img_path'] = $img_path;
        $this->load->view($this->dc, $this->data);
    }
}