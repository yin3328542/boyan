<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-1-24
 * Time: 下午10:31
 *
 * @property Admin_model $model
 */

class Contact extends API_Controller {

    public function contact_get()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $limit = $this->get('limit') ? $this->get('limit') : 10;
        $offset = $this->get('offset') ? $this->get('offset') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'status';
        $order = $this->get('order') ? $this->get('order') : 'asc';
        $response['_count'] = $this->model
            ->count();

        $response['data'] = $this->model
            ->order_by($sort, $order)
            ->order_by('id', 'desc')
            ->limit($limit, $offset)
            ->find_all();
        foreach ( $response['data'] as $k=>$v){
            switch ($v['status']){
                case  "0":$response['data'][$k]['status_str']='未处理';break;
                case  "1":$response['data'][$k]['status_str']='已处理';break;
            }
        }
        $response['ret'] = 0;
        $this->response($response);
    }

    public function contact_post()
    {
        $name =$this->post('name');
        $mobile =$this->post('mobile');
        if(empty($name)){
            $this->response(['ret'=>-1,'msg'=>'联系人姓名不能为空']);
        }
        if($this->checkMobileValidity($mobile)){
            $this->response(['ret'=>-1,'msg'=>'联系人手机号码不合法']);
        }
        $province =$this->post('province');//省
        $city =$this->post('city');//市
        $district =$this->post('district');//区
        $data =[] ;
        $data['name'] = $name;
        $data['mobile'] = $mobile;
        $data['address'] = $province.$city.$district;
        $data['dt_add'] = time();
        $this->model->add($data);
        $this->response(['ret'=>0,'msg'=>'提交成功']);
    }

    public function contact_put()
    {
        if(!$this->app['type'] == APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $id =$this->put('id');
        $this->model->where(['id'=>$id])->edit(['status'=>1]);
        $this->response(['ret'=>0,'msg'=>'标识成功']);
    }


    public function region_get()
    {
        $this->load->model('region_model');
        $pid = $this->get('pid');
        $response['data'] = $this
            ->region_model
            ->where(['parent_id'=>$pid])
            ->find_all();
        $response['ret'] = 0;
        $this->response($response);
    }

} 