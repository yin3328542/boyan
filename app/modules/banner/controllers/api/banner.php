<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-10-23
 * Time: 上午9:53
 */

class Banner extends API_Controller
{
    public function banners_get()
    {
        $where = array();

        if(!$this->app['type'] == APPTYPE_ADMIN && !$this->app['type'] == APPTYPE_PC) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $limit = $this->get('limit') ? $this->get('limit') : 10;
        $offset = $this->get('offset') ? $this->get('offset') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'listorder';
        $order = $this->get('order') ? $this->get('order') : 'asc';
        $status = $this->get('status') ? intval($this->get('status')) : 0;
        $type = $this->get('type') ? $this->get('type') : 'index';
        if($status !== false && $status > 0) {
            $where['status'] = $status;
        }
        $where['banner.type'] = $type;
        $response['_count'] = $this->model
            ->join('attachment', 'banner.img = attachment.id', 'left')
            ->where($where)
            ->count();

        $response['data'] = $this
            ->model
            ->select('banner.*,attachment.filepath as filepath,attachment.id attachmen_id')
            ->join('attachment', 'banner.img = attachment.id', 'left')
            ->where($where)
            ->order_by('status' , 'desc')
            ->order_by($sort, $order)
            ->order_by('ad_time' , 'desc')
            ->limit($limit, $offset)
            ->find_all();
        //echo($this->db->last_query());

        foreach($response['data'] as $k => $val) {
            $val['img_file'] = image_url($val['filepath']);
            $val['ad_time'] = date('Y-m-d H:i:s',$val['ad_time']);
            $response['data'][$k]  = $val;
        }

        $response['ret'] = 0;
        $this->response($response);
    }

    public function banner_get()
    {
        $id = $this->get('id');

        if(!$id){
            $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        }

        $banner = $this->model
            ->select('banner.*,attachment.filepath as img_file,attachment.id attachmen_id')
            ->join('attachment', 'banner.img = attachment.id', 'left')
            ->where('banner.id',$id)
            ->find();
        !$banner && $this->response(array('ret' => 403, 'msg' => '记录不存在'));

        $banner['img'] = image_url($banner['img_file']);
        $banner['alt'] = $banner['alt'] ? $banner['alt'] : '';
        $banner['ad_time'] = $banner['ad_time']>0 ? date('Y-m-d H:i:s',$banner['ad_time']) : '';

        $this->response(array('ret' => 0, 'data' => $banner));
    }

    public function banner_post()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $data['name'] = $this->post('name');
        $data['alt'] = $this->post('alt');
        $data['img'] = $this->post('img');
        $data['url'] = $this->post('url');
        $data['type'] = $this->post('type');
        $data['listorder'] = $this->post('listorder') ? intval($this->post('listorder')) : 255;
        $data['status'] = $this->post('status') ? intval($this->post('status')) : 0;
        $data['ad_time'] = time();


        if($this->model->add($data)){
            $this->response(array('ret' => 0,'msg'=>'添加成功'));
        }else{
            $this->response(array('ret' => 401,'msg'=>'系统错误'));
        }
    }

    public function banner_put()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = intval($this->get('id'));
        //更新内容

        $data['name'] = $this->post('name');
        $data['alt'] = $this->post('alt');
        $data['img'] = $this->post('img');
        $data['url'] = $this->post('url');
        $data['listorder'] = $this->post('listorder') ? intval($this->post('listorder')) : 255;
        $data['status'] = $this->post('status') ? intval($this->post('status')) : 0;
        $data['ed_time'] = time();


        if(!$this->model->where(array('id' => $id))->edit($data)) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }else{
            $this->response(array('ret' => 0, 'data' => '修改成功'));
        }
    }

    /**
     * banner显示或隐藏
     */
    public function banner_status_get()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = $this->get('id');
        $status = $this->get('status');

        if(!$id){
            $this->response(array('ret' => 501, 'msg' => '参数不足'));
        }

        if($status==0){
            $count_N = $this->model
                ->where('banner.status','1')
                ->where('banner.id !=',$id)
                ->count();
            if($count_N==0){
                $this->response(array('ret' => 401,'msg'=>'至少显示一张幻灯片'));
            }
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

    public function del_banner_delete()
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

    public function order_put()
    {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $id = intval($this->put('id'));

        if($id){
            $where['id'] = $id;
            $data['listorder'] = $this->put('listorder') ? $this->put('listorder') : 0;
            if($this->model->where($where)->edit($data)) {
                $this->response(array('ret' => 0));
            }
            $this->response(array('ret' => 500, 'msg' => '修改失败'));
        }
        $this->response(array('ret' => 500, 'msg' => '参数错误'));
    }

    public function img_delete() {
        $id = intval($this->get('id'));
        $this->load->model('attachment_model');
        $this->attachment_model->where(array(
            'id' => $id
        ))->delete();

        $this->response(array('ret' => 0));
    }

    public function app_banners_get()
    {
        $where = array();
        $sort = $this->get('sort') ? $this->get('sort') : 'listorder';
        $order = $this->get('order') ? $this->get('order') : 'asc';
        $type = 'appindex';
        $where['status'] = 1;

        $where['banner.type'] = $type;
        $response['data'] = $this
            ->model
            ->select('banner.*,attachment.filepath as filepath,attachment.id attachmen_id')
            ->join('attachment', 'banner.img = attachment.id', 'left')
            ->where($where)
            ->order_by('status' , 'desc')
            ->order_by($sort, $order)
            ->order_by('ad_time' , 'desc')
            ->find_all();
        foreach($response['data'] as $k => $val) {
            $val['img_file'] = image_url($val['filepath']);
            $val['alt'] = $val['alt'] ? $val['alt'] : '';
            $val['ad_time'] = date('Y-m-d H:i:s',$val['ad_time']);
            if(strpos($val['url'],'javascript')!==false){
                $val['url'] = '[不进行页面跳转]';
            }
            $response['data'][$k]  = $val;
        }

        $response['ret'] = 0;
        $this->response($response);
    }
}