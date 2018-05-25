<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-10-23
 * Time: 上午9:53
 */

class Service extends API_Controller
{
    public function services_get()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN && !$this->app['type'] == APPTYPE_PC) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $limit = $this->get('limit') ? $this->get('limit') : 10;
        $offset = $this->get('offset') ? $this->get('offset') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'listorder';
        $order = $this->get('order') ? $this->get('order') : 'asc';
        $keyword = $this->get('keyword') ? $this->get('keyword') : '';

        $where = array();

        $or_where = "(1 = 1)";

        if($keyword) {
            $or_where .= " and zl_service.title like '%$keyword%' ";
        }


        $response['_count'] = $this->model
            ->join('attachment', 'service.img = attachment.id', 'left')
            ->where($where)
            ->where($or_where)
            ->count();

        $response['data'] = $this
            ->model
            ->select('service.*,attachment.filepath as filepath,attachment.id attachmen_id')
            ->join('attachment', 'service.img = attachment.id', 'left')
            ->where($where)
            ->where($or_where)
            ->order_by($sort, $order)
            ->order_by('dt_add' , 'desc')
            ->limit($limit, $offset)
            ->find_all();
        foreach($response['data'] as $k => $val) {
            $response['data'][$k]['img_file'] = image_url($val['filepath']);
            $response['data'][$k]['dt_add'] = $val['dt_add']>0 ? date('Y-m-d H:i:s',$val['dt_add']) : '';
        }

        $response['ret'] = 0;
        $this->response($response);
    }

    public function service_post()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $data['title'] = $this->post('title') ? $this->post('title') : '';
        $data['img'] = $this->post('img') ? intval($this->post('img')) : 0;
        $data['description'] = $this->post('description') ? intval($this->post('description')) : 0;
        $data['listorder'] = $this->post('listorder') ? intval($this->post('listorder')) : 255;
        $data['dt_add'] = time();

        if($data['title']==''){
            $this->response(array('ret' => 400, 'msg' => '标题不能为空'));
        }
        if ($data['img'] == 0){
            $this->response(array('ret' => 400, 'msg' => '请至少上传一张图片'));
        }

        if($this->model->add($data)){
            $this->response(array('ret' => 0,'msg'=>'添加成功'));
        }else{
            $this->response(array('ret' => 401,'msg'=>'系统错误'));
        }
    }

    public function service_get()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = $this->get('id');
        if(!$id){
            $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        }

        $service = $this->model
            ->select('service.*,attachment.filepath as img_file,attachment.id attachmen_id')
            ->join('attachment', 'service.img = attachment.id', 'left')
            ->where('service.id',$id)
            ->find();
        !$service && $this->response(array('ret' => 403, 'msg' => '记录不存在'));

        $service['img'] = image_url($service['img_file']);
        $service['dt_add'] = $service['dt_add']>0 ? date('Y-m-d H:i:s',$service['dt_add']) : '';

        $this->response(array('ret' => 0, 'data' => $service));
    }

    public function service_put()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = intval($this->get('id'));
        if(!$id){
            $this->response(array('ret' => 500, 'msg' => '参数错误'));
        }
        //更新内容

        $data['title'] = $this->put('title') ? $this->put('title') : '';
        $data['img'] = $this->put('img') ? intval($this->put('img')) : 0;
        $data['listorder'] = $this->put('listorder') ? intval($this->put('listorder')) : 255;
        $data['description'] = $this->put('description') ? intval($this->put('description')) :'';
        $data['dt_update'] = time();

        if($data['title']==''){
            $this->response(array('ret' => 400, 'msg' => '标题不能为空'));
        }
        if ($data['img'] == 0){
            $this->response(array('ret' => 400, 'msg' => '请至少上传一张图片'));
        }

        if(!$this->model->where(array('id' => $id))->edit($data)) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }else{
            $this->response(array('ret' => 0, 'data' => '修改成功'));
        }
    }

    public function service_delete()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $id = intval($this->get('id'));
        if(!$id){
            $this->response(array('ret' => 500, 'msg' => '参数错误'));
        }

        if($this->model->where(array('id' => $id))->delete()) {
            $this->response(array('ret' => 0));
        } else {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }
    }

    public function img_delete() {
        $id = intval($this->get('id'));
        if(!$id){
            $this->response(array('ret' => 500, 'msg' => '参数错误'));
        }
        $this->load->model('attachment_model');
        $this->attachment_model->where(array(
            'id' => $id
        ))->delete();

        $this->response(array('ret' => 0));
    }
}