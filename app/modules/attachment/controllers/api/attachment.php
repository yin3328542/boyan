<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-3-11
 * Time: 下午8:07
 */

class Attachment extends API_Controller {
    public function upload_post()
    {
        $site_id = $this->_user['site_id'];
        if (!$site_id) {
            $site_id = 0;
        }
        $folder = $this->post('folder') ? $this->post('folder') : 'goods';
        $local_save = $this->get('local_save') ? $this->get('local_save') : 0;
        $attachment_path = 'data/attachment/';
        $file_path = $folder.'/' . date('ym/d/');
        $upload_path = $attachment_path . $file_path;
        $imgInfo = getimagesize($_FILES['filedata']['tmp_name']);
        if($imgInfo[0]>2000) {
            $this->response(array('success' => false, 'msg' => '上传图片的宽度不能大于2000'));
        }
//        if($folder=='banner'){
//            $imgInfo = getimagesize($_FILES['filedata']['tmp_name']);
//            if($imgInfo[0]!=750 ||$imgInfo[1]!=300){
//                $this->response(array('success' => false, 'msg' => '请上传750*300宽高的图片'));
//            }
//            if($_SERVER['CONTENT_LENGTH']>300*1024){
//                $this->response(array('success' => false, 'msg' => '图片大小请限制在300KB以内'));
//            }
//        }


        if (!is_dir($upload_path) && !mkdir($upload_path, 0777, TRUE)) {
            $this->response(array('success' => false, 'msg' => '创建目录失败'));
        }
        $this->load->library('upload', array(
            'allowed_types' => 'gif|jpg|png|mp3|amr|mp4|jpeg',
            'upload_path' => $upload_path,
            'max_size'    => 2048,  //最大2MB
            'file_name' => uniqid(),
        ));
        if ($this->upload->do_upload('filedata')) {
            $result = $this->upload->data();
            //下行代码用于上传到图片服务器，本地时注销即可
            //$res = $this->rest_upload($result['full_path'], $file_path, $result['file_name'], array(), TRUE, $local_save);
            $attachment_data = array(
                'site_id' => $site_id,
                'filename' => $result['file_name'],
                'filepath' => $file_path . $result['file_name'],
                'filesize' => $result['file_size'], //kb
                'dt_upload' => time(),
                'ip_upload' => $this->input->ip_address()
            );
            $attachment_data['id'] = $this->model->add($attachment_data);
            $attachment_data['file_url'] = image_url($file_path.$result['file_name']);
            $this->response(array('success'=>TRUE, 'data'=>$attachment_data));
        } else {
            $this->response(array('success'=>FALSE, 'msg'=>$this->upload->display_errors()));
        }
    }

    public function note_upload_post()
    {
        $site_id = $this->_user['site_id'];
        if (!$site_id) {
            $site_id = 0;
        }
        $folder = $this->post('folder') ? $this->post('folder') : 'notes';
        $local_save = $this->get('local_save') ? $this->get('local_save') : 0;
        $attachment_path = 'data/attachment/';
        $file_path = $folder.'/' . date('ym/d/');
        $upload_path = $attachment_path . $file_path;
        if (!is_dir($upload_path) && !mkdir($upload_path, 0777, TRUE)) {
            $this->response(array('ret' => 114, 'msg' => '创建目录失败'));
        }
        $this->load->library('upload', array(
            'allowed_types' => 'gif|jpg|png|mp3|amr|mp4',
            'upload_path' => $upload_path,
            'max_size'    => 2048,  //最大2MB
            'file_name' => uniqid(),
        ));
        if ($this->upload->do_upload('filedata')) {
            $result = $this->upload->data();
            //下行代码用于上传到图片服务器，本地时注销即可
            $attachment_data = array(
                'site_id' => $site_id,
                'uid' => empty($this->_user['uid'])?0:$this->_user['uid'],
                'filename' => $result['file_name'],
                'filepath' => $file_path . $result['file_name'],
                'filesize' => $result['file_size'], //kb
                'dt_upload' => time(),
                'ip_upload' => $this->input->ip_address()
            );
            $this->load->model("user_notes_attachment_model","attachment_model");
            $attachment_data['id'] = $this->attachment_model->add($attachment_data);
            $attachment_data['file_url'] = image_url($file_path.$result['file_name']);
            $this->response(['ret'=>0,'data'=>$attachment_data]);
        } else {
            $this->response(array('ret'=>115,'msg'=>$this->upload->display_errors()));
        }
    }


    public function editor_upload_post()
    {
        $this->_user = $this->session->userdata('siter_info');
        $site_id = $this->_user['site_id'];
        if (!$site_id) {
            //$this->response(array('success' => false, 'msg' => '验证失败，请刷新重试'));
            $site_id = 0;
        }
        $attachment_path = 'data/attachment/';
        $file_path = 'editor/' . date('ym/d/');
        $upload_path = $attachment_path . $file_path;

        if (!is_dir($upload_path) && !mkdir($upload_path, 0777, TRUE)) {
            return FALSE;
        }
        $this->load->library('upload', array(
            'allowed_types' => 'gif|jpg|png',
            'upload_path' => $upload_path,
            'max_size'    => 2048,  //最大2MB
            'file_name' => uniqid(),
        ));
        if ($this->upload->do_upload('upload')) {
            $result = $this->upload->data();
            //下行代码用于上传到图片服务器，本地时注销即可
            //$this->rest_upload($result['full_path'], $file_path, $result['file_name']);
            $attachment_data = array(
                'site_id' => $site_id,
                'filename' => $result['file_name'],
                'filepath' => $file_path . $result['file_name'],
                'filesize' => $result['file_size'], //kb
                'dt_upload' => time(),
                'ip_upload' => $this->input->ip_address()
            );
            $this->model->add($attachment_data);
            $fn = $this->input->get('CKEditorFuncNum');
            $file =  image_url($file_path.$result['file_name']);//$file_path . $result['file_name'];
            $str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$file.'\', \'\');</script>';
            exit($str);
        }
    }

    public function ueditor_upload_get()
    {
        $limit = $this->get('size') ? $this->get('size') : 10;
        $start = $offset = $this->get('start') ? $this->get('start') : 0;
        $sort = 'id';
        $order = 'desc';

        $total = $this->model->count();
        if($total > 0){
            $dlist = $this->model
                ->order_by($sort, $order)
                ->limit($limit, $offset)
                ->find_all();

            $list = array();

            foreach($dlist as $item) {
                $_url = image_url($item['filepath']);
                $list[] = array(
                    'url'=>$_url,
                    'mtime'=>$item['dt_upload'],

                    'id'=>$item['id'],
                    'filepath'=>$item['filepath'],
                    'file_url'=>$_url,
                );
            }

            $data = array(
                "state" => "SUCCESS",
                "list" => $list,
                "start" => $start,
                "total" => $total
            );
        }else{

            $data = array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => 0
            );

        }

        $this->response($data);
    }

    public function ueditor_upload_post()
    {
        $attachment_path = 'data/attachment/';
        $file_path = 'editor/' . date('ym/d/');
        $upload_path = $attachment_path . $file_path;

        if (!is_dir($upload_path) && !mkdir($upload_path, 0777, TRUE)) {
            return FALSE;
        }
        $this->load->library('upload', array(
            'allowed_types' => 'gif|jpg|png',
            'upload_path' =>$upload_path,
            'max_size'    => 2048,  //最大2MB
            'file_name' => uniqid(),
        ));
        if ($this->upload->do_upload('upfile')) {
            $result = $this->upload->data();
            //下行代码用于上传到图片服务器，本地时注销即可
            //$this->rest_upload($result['full_path'], $file_path, $result['file_name']);
            $attachment_data = array(
                'filename' => $result['file_name'],
                'filepath' => $file_path . $result['file_name'],
                'filesize' => $result['file_size'], //kb
                'dt_upload' => time(),
                'ip_upload' => $this->input->ip_address()
            );
            $this->model->add($attachment_data);
            $file =  image_url($file_path.$result['file_name']);//$file_path . $result['file_name'];
            $this->response(array(
                "state" => 'SUCCESS',
                "url" => $file,
                "title" => $result['file_name'],
                "original" => $result['file_name'],
                "type" => 'gif|jpg|png',
                "size" => $result['file_size']
            ));
        }else{

            $this->response(array(
                "state" => $this->upload->display_errors(),
            ));

        }
    }

    /**
     * 上传excel文件
     */
    public function upload_excel_post()
    {
        $folder = $this->post('folder') ? $this->post('folder') : 'excel';
        $upload_path = 'data/attachment/'.$folder.'/'. date('ym/d/');
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        $this->load->library('upload', array(
            'allowed_types' => 'xls|xlsx',
            'upload_path' => $upload_path,
            'max_size'    => 2048,  //最大2MB
            'file_name' => uniqid(),
        ));
        if ($this->upload->do_upload('filedata')) {
            $result = $this->upload->data();
            $result['relative_path'] = $folder.'/'. date('ym/d/').$result['orig_name'];
            $this->response(array('ret' => 0, 'data' => $result));
        } else {
            $this->response(array('ret' => 500, 'msg' => $this->upload->display_errors()));
        }
    }

    /**
     * 上传微信支付pem文件文件
     */
    public function upload_cert_post()
    {
        $site_id = $this->_user['site_id'];
        $folder = $this->post('folder') ? $this->post('folder') : 'excel';
        $upload_path = 'data/cacert/'.$site_id;
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        $this->load->library('upload', array(
            'allowed_types' => '*',
            'upload_path' => $upload_path,
            'max_size'    => 1024,  //最大1MB
            'overwrite' => true,
        ));
        if ($this->upload->do_upload('filedata')) {
            $result = $this->upload->data();
            $this->response(array('ret' => 0, 'data' => $result));
        } else {
            $this->response(array('ret' => 500, 'msg' => $this->upload->display_errors()));
        }
    }

    /**
     * 上传pdf文件
     */
    public function upload_pdf_post()
    {
        $folder = $this->post('folder') ? $this->post('folder') : 'pdf';
        $upload_path = 'data/attachment/'.$folder.'/'. date('ym/d/');
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        $this->load->library('upload', array(
            'allowed_types' => 'pdf',
            'upload_path' => $upload_path,
            'max_size'    => 4096,  //最大4MB
            'file_name' => uniqid(),
        ));
        if ($this->upload->do_upload('filedata')) {
            $result = $this->upload->data();
            $result['relative_path'] = $folder.'/'. date('ym/d/').$result['orig_name'];
            $this->response(array('ret' => 0, 'data' => $result));
        } else {
            $this->response(array('ret' => 500, 'msg' => $this->upload->display_errors()));
        }
    }

    /**
     * 上传压缩包文件
     */
    public function upload_rar_post()
    {
        $folder = $this->post('folder') ? $this->post('folder') : 'rar';
        $upload_path = 'data/attachment/'.$folder.'/'. date('ym/d/');
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        $this->load->library('upload', array(
            'allowed_types' => 'rar|zip',
            'upload_path' => $upload_path,
            'max_size'    => 4096,  //最大4MB
            'file_name' => uniqid(),
        ));
        if ($this->upload->do_upload('filedata')) {
            $result = $this->upload->data();
            $result['relative_path'] = $folder.'/'. date('ym/d/').$result['orig_name'];
            $this->response(array('ret' => 0, 'data' => $result));
        } else {
            $this->response(array('ret' => 500, 'msg' => $this->upload->display_errors()));
        }
    }


    public function attachment_delete()
    {
        $id = intval($this->get('id'));
        $attachment = $this->model->where(array('id'=>$id))->find();
        //var_dump($attachment);
        if(!$attachment) {
            $this->response(array('success'=>FALSE, 'msg'=>'该文件不存在'));
        }
        if($result = $this->model->delete($id)) {
            $this->rest_remove($attachment['filepath']);
        }
        $this->response($result);
    }

    public function attachments_get() {
        $site_id = $this->_user['site_id'];
        if (!$site_id) {
            $this->response(array('ret' => 403, 'msg' => '验证失败，请刷新重试'));
        }
        $folder = $this->get('folder');
        $limit = $this->get('limit') ? : 8;
        $offset = $this->get('offset') ? : 0;

        $options = array(
            'where' => array('site_id' => $site_id)
        );

        $_count = $this->model->count($options);
        $data = $this->model->limit($limit, $offset)->order_by('id', 'desc')->find_all($options);

        foreach($data as &$item) {
            $item['img'] = image_url($item['filepath']);
        }

        $this->response(array('ret' => 0, 'data' => $data, '_count' => $_count));
    }


    /**
     * 上传微信授权验证文件
     */
    public function upload_check_file_post()
    {
        $site_id = $this->_user['site_id'];
        $name = $_FILES['checkFile']['name'];
        if(empty($name)){
            $this->response(array('ret' => -1, 'msg' =>'请上传文件MP_verify_(*).txt'));
        }
        if(strstr($name,"MP_verify_")===false){
            $this->response(array('ret' => -1, 'msg' =>'请上传文件MP_verify_(*).txt'));
        }
        $this->load->library('rest', [
            'server'     => 'http://'.wap_url($site_id).'/api',
            'app_key'    => $this->app_key,
            'secret_key' => $this->app_secret,
        ], 'api_rest');

        $res = $this->api_rest->post('create_check_file', [
            'name' =>$name,
            'content' =>file_get_contents($_FILES['checkFile']['tmp_name'])
        ]);
        echo $res;exit();

    }
} 