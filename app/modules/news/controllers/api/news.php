<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-10-23
 * Time: 上午9:53
 */

class News extends API_Controller
{
    public function newses_get()
    {
        $where = array();
        if($this->app['type'] != APPTYPE_ADMIN ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $limit = $this->get('limit') ? $this->get('limit') : 10;
        $offset = $this->get('offset') ? $this->get('offset') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'dt_add';
        $order = $this->get('order') ? $this->get('order') : 'desc';
        $status = $this->get('status') ? intval($this->get('status')) : -1;

        switch ($status){
            case  1:$where['status'] = 1;break;
            case  0:$where['status'] = 0;break;
            default :break;
        }
        $response['_count'] = $this->model
            ->where($where)
            ->count();

        $response['data'] = $this->model
            ->select('news.*,attachment.filepath as icon_file,attachment.id attachmen_id')
            ->join('attachment', 'news.img = attachment.id', 'left')
            ->where($where)
            ->order_by($sort, $order)
            ->order_by('dt_add', 'desc')
            ->limit($limit, $offset)
            ->find_all();

        foreach($response['data'] as $k => $val) {
            $response['data'][$k]['img_file'] = image_url($val['icon_file']);
            $response['data'][$k]['dt_add'] = date('Y-m-d',$val['dt_add']);
        }

        $response['ret'] = 0;
        $this->response($response);
    }

    public function news_get()
    {
        $id = $this->get('id');
        if($this->app['type'] != APPTYPE_ADMIN ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        !$id && $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        $news = $this->model->select('news.*,attachment.filepath as icon_file,attachment.id attachmen_id')
            ->join('attachment', 'news.img = attachment.id', 'left')
            ->where('news.id',$id)->find();

        !$news && $this->response(array('ret' => 403, 'msg' => '记录不存在'));

        $news['img'] = image_url($news['icon_file']);
        $news['dt_add'] = $news['dt_add']>0 ? date('Y-m-d',$news['dt_add']) : '';
        $this->response(array('ret' => 0, 'data' => $news));
    }

    public function news_post()
    {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $data['title'] = $this->post('title');
        $data['img'] = $this->post('img');
        $data['intro'] = $this->post('intro',false);
        $data['status'] = intval($this->post('status'));
        $data['dt_add'] = time();

        if(!$data['title']){
            $this->response(array('ret' => 401,'msg'=>'请填写标题'));
        }
        if(!$data['intro']){
            $this->response(array('ret' => 401,'msg'=>'请填写内容'));
        }

        if($this->model->add($data)){
            $this->response(array('ret' => 0,'msg'=>'添加成功'));
        }else{
            $this->response(array('ret' => 401,'msg'=>'系统错误'));
        }
    }

    public function news_put()
    {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = intval($this->get('id'));
        //更新内容

        $data['title'] = $this->put('title');
        $data['img'] = $this->put('img');
        $data['intro'] = $this->put('intro',false);
        $data['status'] = intval($this->put('status'));

        if(!$data['title']){
            $this->response(array('ret' => 401,'msg'=>'请填写标题'));
        }

        if(!$data['intro']){
            $this->response(array('ret' => 401,'msg'=>'请填写内容'));
        }

        if(!$this->model->where(array('id' => $id))->edit($data)) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }else{
            $this->response(array('ret' => 0, 'data' => '修改成功'));
        }
    }

    /**
     * 显示或隐藏
     */
    public function news_status_get()
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

    public function news_delete()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $id = $this->get('id');

        if($this->model->where(array('id' => $id))->delete()) {
            $this->response(array('ret' => 0));
        } else {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }
    }

    /*
        * 前端获取新闻列表
        */
    public function wap_news_get()
    {
        $where = array();

        $limit = $this->get('limit') ? $this->get('limit') : 9;
        $page = $this->get('page') ? $this->get('page') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'stick_level';
        $order = $this->get('order') ? $this->get('order') : 'desc';
        $cate_id   = $this->get('cate_id') ? intval($this->get('cate_id')) : 0;

        $offset = ($page-1)*$limit;
        $where['news.status'] =1;

        //分类
        if($cate_id > 0) {
            $where['cate_id'] = $cate_id;
        }

        $response['_count'] = $this->model
            ->join('category','news.cate_id = category.id','left')
            ->where($where)
            ->count();

        $response['data'] = $this->model
            ->select('news.id,news.title,news.head_desc,news.dt_add,attachment.filepath as icon_file')
            ->join('attachment', 'news.img = attachment.id', 'left')
            ->where($where)
            ->order_by($sort, $order)
            ->order_by('dt_add', 'desc')
            ->limit($limit, $offset)
            ->find_all();

        foreach($response['data'] as $k => $val) {
            $response['data'][$k]['img_file'] = image_url($val['icon_file']);
            $response['data'][$k]['dt_add'] = date('Y-m-d',$val['dt_add']);
        }

        $this->load->library('page');
        $config['base_url'] = 'javascript:void(0);';    //URL 路径
        $config['use_page_numbers'] = TRUE;                  //默认分页URL中是显示每页记录数,启用后显示当前页码
        $config['total_rows'] = $response['_count'];             //总信息条数
        $config['per_page'] = $limit;                        //每页显示条数
        $config['cur_page'] = $offset;                       //当前页数
        $config['cur_page_new'] = $page;                     //当前页(自己新增参数)
        $this->page->initialize($config);
        $response['pagelist'] = $this->page->create_links();

        $response['ret'] = 0;
        $this->response($response);
    }

    public function wap_new_get()
    {
        $id = $this->get('id');

        //新闻流量更新-开始
        $options=array();
        $options['where'] = array('id'=>$id);
        $this->model->set_inc('reality_pvs',$options,1);
        //新闻流量更新-结束

        !$id && $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        $news = $this->model->select('news.id,news.title,news.intro,news.dt_add,news.keywords,category.id cate_id,category.name cate_name')
            ->join('category','news.cate_id = category.id','left')
            ->where('news.id',$id)->find();

        !$news && $this->response(array('ret' => 403, 'msg' => '记录不存在'));
        $news['dt_add'] = $news['dt_add']>0 ? date('Y-m-d H:i:s',$news['dt_add']) : '';
        $news['keywords'] = empty($news['keywords'])?[]:explode(',',$news['keywords']);

        $news['next_info'] = $this->model->select('news.id as next_id,title')->where('id >'.$id)->order_by('id', 'asc')->find();

        $this->response(array('ret' => 0, 'data' => $news));
    }


    /*
    * 前端获取新闻分类
    */
    public function news_category_get()
    {
        $where = array();
        $this->load->model('category_model');
        $where['status'] =1;
        $response['data'] = $this->category_model
            ->select('id,name')
            ->where($where)
            ->find_all();
        $response['ret'] = 0;
        $this->response($response);
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