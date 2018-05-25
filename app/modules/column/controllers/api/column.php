<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-10-23
 * Time: 上午9:53
 */

class Column extends API_Controller
{
    public function admin_columns_get()
    {
        $where = array();

        $sort = $this->get('sort') ? $this->get('sort') : 'id';
        $order = $this->get('order') ? $this->get('order') : 'asc';
        $status = $this->get('status') ? $this->get('status') : '0';

        if($status > 0) {
            $where['status'] = $status;
        }

        $response['data'] = $this
            ->model
            ->select('column.*,attachment.filepath as filepath,attachment.id attachmen_id')
            ->join('attachment', 'column.img = attachment.id', 'left')
            ->where($where)
            ->order_by($sort, $order)
            ->find_all();

        foreach($response['data'] as $k => $val) {
            $val['img_file'] = image_url($val['filepath']);
            $val['dt_time'] = date('Y-m-d H:i:s',$val['dt_time']);
            $response['data'][$k]  = $val;
        }

        $response['ret'] = 0;
        $this->response($response);
    }

    public function columns_get()
    {
        $where = array();

        if(!$this->app['type'] == APPTYPE_ADMIN && !$this->app['type'] == APPTYPE_PC) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $sort = $this->get('sort') ? $this->get('sort') : 'listorder';
        $order = $this->get('order') ? $this->get('order') : 'asc';
        $pid = $this->get('pid') >=0 ? $this->get('pid') : '';
        //上级ID
        if($pid !=='') {
            $where['pid'] = $pid;
        }
        $where['status'] = 1;
        $response['data'] = $this
            ->model
            ->select('column.*,attachment.filepath as filepath,attachment.id attachmen_id')
            ->join('attachment', 'column.img = attachment.id', 'left')
            ->where($where)
            ->order_by('pid' , 'asc')
            ->order_by($sort, $order)
            ->find_all();
        //echo($this->db->last_query());

        foreach($response['data'] as $k => $val) {
            $response['data'][$k]['img_file'] = image_url($val['filepath']);
            $response['data'][$k]['dt_time'] = date('Y-m-d H:i:s',$val['dt_time']);
        }

        $response['ret'] = 0;
        $this->response($response);
    }

    public function column_get()
    {
        $id = $this->get('id');
        $name_en = $this->get('name_en');

        if(!$id && !$name_en){
            $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        }
        $where = [];
        if(!empty($id)){
            $where['column.id'] = $id;
        }
        if(!empty($name_en)){
            $where['column.name_en'] = $name_en;
        }


        $column = $this->model
            ->select('column.*,attachment.filepath as img_file,attachment.id attachmen_id')
            ->join('attachment', 'column.img = attachment.id', 'left')
            ->where($where)
            ->find();

        !$column && $this->response(array('ret' => 403, 'msg' => '记录不存在'));

        $column['img'] = image_url($column['img_file']);

        $column['dt_time'] = $column['dt_time']>0 ? date('Y-m-d H:i:s',$column['dt_time']) : '';

        if(strpos($column['url'],'javascript')!==false){
            $column['url'] = '';
        }

        $this->response(array('ret' => 0, 'data' => $column));
    }

    public function column_post()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $data['pid'] = $this->post('pid') ? intval($this->post('pid')) : 0;
        $data['name'] = $this->post('name');
        $data['name_en'] = $this->post('name_en');
        $data['img'] = $this->post('img');
        $data['url'] = $this->post('url');
        $data['title'] = $this->post('title');
        $data['keywords'] = $this->post('keywords');
        $data['description'] = $this->post('description');
        $data['listorder'] = $this->post('listorder') ? intval($this->post('listorder')) : 255;
        $data['status'] = $this->post('status') ? intval($this->post('status')) : 0;
        $data['sb_time'] = time();

        if(!$data['name']){
            $this->response(array('ret' => 401,'msg'=>'栏目名称不能为空'));
        }

        if(!$data['url']){
            $data['url'] = 'javascript:void(0)';
        }



        if($this->model->add($data)){
            $this->response(array('ret' => 0,'msg'=>'添加成功'));
        }else{
            $this->response(array('ret' => 401,'msg'=>'系统错误'));
        }
    }

    public function column_put()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = intval($this->get('id'));
        //更新内容

        $data['pid'] = $this->post('pid') ? intval($this->post('pid')) : 0;
        $data['name'] = $this->post('name');
        $data['name_en'] = $this->post('name_en');
        $data['img'] = $this->post('img');
        $data['url'] = $this->post('url');
        $data['title'] = $this->post('title');
        $data['keywords'] = $this->post('keywords');
        $data['description'] = $this->post('description');
        $data['listorder'] = $this->post('listorder') ? intval($this->post('listorder')) : 255;
        $data['status'] = $this->post('status') ? intval($this->post('status')) : 0;
        $data['edit_time'] = time();

        if(!$data['name']){
            $this->response(array('ret' => 401,'msg'=>'栏目名称不能为空'));
        }

        if(!$data['url']){
            $data['url'] = 'javascript:void(0)';
        }

        if(!$this->model->where(array('id' => $id))->edit($data)) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }else{
            $this->response(array('ret' => 0, 'data' => '修改成功'));
        }
    }

    public function del_column_delete()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $id = intval($this->get('id'));

        //判断是否有下级栏目
        $column = $this->model->where(array('pid' => $id))->count();
        if($column > 0) {
            $this->response(array('ret' => 417, 'msg' => '对不起，当前栏目存在下级栏目，无法删除!'));
        }

        if($this->model->where(array('id' => $id))->delete()) {
            $this->response(array('ret' => 0));
        } else {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }
    }

    public function edit_listorder_put()
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
}