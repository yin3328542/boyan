<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-10-23
 * Time: 上午9:53
 */

class Adsense extends API_Controller
{
    public function adsenses_get()
    {
        $where = array();

        if(!$this->app['type'] == APPTYPE_ADMIN && !$this->app['type'] == APPTYPE_PC) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $response['data'] = $this
            ->model
            ->select('adsense.*,attachment.filepath as filepath,attachment.id attachmen_id')
            ->join('attachment', 'adsense.img = attachment.id', 'left')
            ->where($where)
            ->order_by('ad_time' , 'desc')
            ->find_all();
        foreach($response['data'] as $k => $val) {
            $val['img_file'] = image_url($val['filepath']);
            $val['ad_time'] = date('Y-m-d H:i:s',$val['ad_time']);
            $response['data'][$k]  = $val;
        }

        $response['ret'] = 0;
        $this->response($response);
    }

    public function adsense_get()
    {
        $id = $this->get('id');

        if(!$id){
            $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        }

        $banner = $this->model
            ->select('adsense.*,attachment.filepath as img_file,attachment.id attachmen_id')
            ->join('attachment', 'adsense.img = attachment.id', 'left')
            ->where('adsense.id',$id)
            ->find();
        !$banner && $this->response(array('ret' => 403, 'msg' => '记录不存在'));

        $banner['img'] = image_url($banner['img_file']);
        $banner['ad_time'] = $banner['ad_time']>0 ? date('Y-m-d H:i:s',$banner['ad_time']) : '';

        $this->response(array('ret' => 0, 'data' => $banner));
    }

    public function adsense_put()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = intval($this->put('id'));
        //更新内容
        $data['img'] = $this->put('img');
        $data['status'] = $this->put('status') ? intval($this->put('status')) : 0;
        $data['ed_time'] = time();

        if(!$this->model->where(array('id' => $id))->edit($data)) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }else{
            $this->response(array('ret' => 0, 'data' => '修改成功'));
        }
    }

    /**
     * 显示或隐藏
     */
    public function adsense_status_get()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = $this->get('id');
        $status = $this->get('status');

        if(!$id){
            $this->response(array('ret' => 501, 'msg' => '参数不足'));
        }

        if($status !== false) {
            if(!in_array($status, array(1,0))){
                $this->response(array('ret' => 500, 'msg' => '参数有误'));
            }

            if($this->model->where(array('id' => $id))->edit(array('status'=>$status))){
                $this->response(array('ret' => 0), 201);
            }else{
                $this->response(array('ret' => 500, 'msg' => '系统错误'), 500);
            }
        }
    }

    public function adsense_delete()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $id = intval($this->get('id'));

        if($this->model->where(array('id' => $id))->delete()) {
            $this->response(array('ret' => 0));
        } else {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }
    }

    public function img_delete() {
        $id = intval($this->get('id'));
        $this->load->model('attachment_model');
        $this->attachment_model->where(array(
            'id' => $id
        ))->delete();

        $this->response(array('ret' => 0));
    }

}