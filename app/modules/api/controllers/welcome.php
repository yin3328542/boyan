<?php
class Welcome extends API_Controller
{
    var $model_name='admin_variable';
    public function base_info_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $this->load->model('admin_variable_model');
        $variableList = $this->admin_variable_model->find_all();
        $variable = [];
        foreach ($variableList as $v){
            $variable[$v['name']] = $v['value'];
        }
        $variable['by_location_longitude'] = 0;
        $variable['by_location_latitude'] = 0;
        if(!empty($variable['by_location'])){
            $location =  explode(',',$variable['by_location']);
            $variable['by_location_longitude'] = $location[1];
            $variable['by_location_latitude'] = $location[0];
        }
        unset($variable['by_location']);
        unset($variable['by_app_id']);
        unset($variable['by_app_secret']);

        $response['ret'] = 0;
        $response['data'] = $variable;
        $this->response($response);
    }

    public function authorization_get()
    {
        $code = $this->get('code');
        if(empty($code)){
            $this->response(array('ret' => 403, 'msg' => '参数不正确'));
        }
        $this->load->model('admin_variable_model');
        $variableList = $this->admin_variable_model->find_all();
        $variable = [];
        foreach ($variableList as $v){
            $variable[$v['name']] = $v['value'];
        }
//        $wxapplet = new Wxapplet(['app_id'=>$variable['by_app_id'],'app_secret'=>$variable['by_app_secret']]);
//        $open_id_info =$wxapplet->getUserOpenID($code);
//        if(isset($open_id_info['errcode'])){
//            return KRHelper::errorResponse($open_id_info['errcode'],$open_id_info['errmsg']);
//        }
//        if(!is_array($open_id_info)){
//            return KRHelper::errorResponse(ErrorCode::INVALID_PARAM);
//        }
//        if(empty($open_id_info)){
//            return KRHelper::errorResponse(ErrorCode::GETTING_OPEN_ID_FAILURE);
//        }
        //$open_id = $open_id_info['openid'];
        $open_id = '222';
        $data = [];
        $this->load->model('member_xcx_model');
        $memberInfo=$this->member_xcx_model->where(['open_id'=>$open_id])->find();
        if(empty($memberInfo)){
            $data['open_id'] = $open_id;
            $data['public_id'] = md5($open_id);
            $data['dt_add'] = time();
            $this->member_xcx_model->add($data);
        }
        $memberInfo=$this->member_xcx_model
            ->select('id,nickname,open_id')
            ->where(['open_id'=>$open_id])->find();
        empty($memberInfo['nickname'])&&$memberInfo['nickname'] ='游客';
        //发牌照
        $token = $this->_generate_token(APPTYPE_XCX, $memberInfo['id']);
        $memberInfo['access_token'] = $token;
        $response['ret'] = 0;
        $response['data'] = $memberInfo;
        $this->response($response);
    }

    public function homepage_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $this->load->model('banner_model');
        $bannerList = $this->banner_model
            ->select('id,name,img')
            ->where(['type'=>'index','status'=>1])->find_all();
        $this->load->model('attachment_model');
        foreach ($bannerList as $k=>$v){
            $attachment = $this->attachment_model->where(['id'=>$v['img']])->find();
            if(empty($attachment)){
                unset($bannerList[$k]);
                continue;
            }
            $v['img'] = image_url($attachment['filepath']);
            $bannerList[$k] = $v;
        }
        $result['banner_list'] =array_values($bannerList);

        $this->load->model('column_model');
        $columnList = $this->column_model
            ->select('id,name,img,model')
            ->where(['status'=>1])->find_all();
        foreach ($columnList as $k=>$v){
            $attachment = $this->attachment_model->where(['id'=>$v['img']])->find();
            if(empty($attachment)){
                unset($columnList[$k]);
                continue;
            }
            $v['img'] = image_url($attachment['filepath']);
            $v['type'] = $v['model'];
            $columnList[$k] = $v;
        }
        $result['column_list'] = array_values($columnList);
        $response['ret'] = 0;
        $response['data'] = $result;
        $this->response($response);

    }

    public function company_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $this->load->model('admin_model');
        $adminInfo = $this->admin_model->select('description')->find();
        $result['description'] = $adminInfo['description'];
        $result['ad_img'] = '';
        $this->load->model('adsense_model');
        $adsenseInfo = $this->adsense_model
            ->join('attachment','attachment.id = adsense.img')
            ->where(['adsense.status'=>1,'adsense.type'=>'index'])->find();
        if(!empty($adsenseInfo)){
            $result['ad_img'] = image_url($adsenseInfo['filepath']);
        }

        $response['ret'] = 0;
        $response['data'] = $result;
        $this->response($response);

    }

    public function services_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $this->load->model('service_model');
        $serviceList = $this->service_model
            ->select('service.id,title,img,description,attachment.filepath')
            ->join('attachment','attachment.id = service.img')
            ->find_all();
        foreach ($serviceList as $k=>$v){
            $v['img']  =image_url($v['filepath']);
            $serviceList[$k] = $v;
        }
        $result['service_list'] = $serviceList;
        $result['ad_img'] = '';
        $this->load->model('adsense_model');
        $adsenseInfo = $this->adsense_model
            ->join('attachment','attachment.id = adsense.img')
            ->where(['adsense.status'=>1,'adsense.type'=>'service'])->find();
        if(!empty($adsenseInfo)){
            $result['ad_img'] = image_url($adsenseInfo['filepath']);
        }

        $response['ret'] = 0;
        $response['data'] = $result;
        $this->response($response);

    }


    public function cases_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $limit                  = $this->get('limit') ? $this->get('limit') : 10;
        $offset                 = $this->get('offset') ? $this->get('offset') : 0;
        $this->load->model('news_model');

        $where['status'] = 1;
        $response['total'] = $this->news_model->where($where)->count();
        $casesList = $this->news_model
            ->select('news.id,title,img,intro,attachment.filepath')
            ->join('attachment','attachment.id = news.img')
            ->where($where)
            ->order_by('news.id', 'desc')
            ->limit($limit, $offset)
            ->find_all();
        foreach ($casesList as $k=>$v){
            $v['img']  =image_url($v['filepath']);
            $casesList[$k] = $v;
        }
        $result['cases_list'] = $casesList;
        $result['ad_img'] = '';
        $this->load->model('adsense_model');
        $adsenseInfo = $this->adsense_model
            ->join('attachment','attachment.id = adsense.img')
            ->where(['adsense.status'=>1,'adsense.type'=>'case'])->find();
        if(!empty($adsenseInfo)){
            $result['ad_img'] = image_url($adsenseInfo['filepath']);
        }

        $response['ret'] = 0;
        $response['data'] = $result;
        $this->response($response);

    }

    public function case_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $id                  = $this->get('id');
        $this->load->model('news_model');

        $where['status'] = 1;
        $where['id'] = $id;
        $caseInfo = $this->news_model
            ->select('news.id,title,img,intro')
            ->where($where)
            ->find();
        $response['ret'] = 0;
        $response['data'] = $caseInfo;
        $this->response($response);

    }

    public function contact_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $result['ad_img'] = '';
        $this->load->model('adsense_model');
        $adsenseInfo = $this->adsense_model
            ->join('attachment','attachment.id = adsense.img')
            ->where(['adsense.status'=>1,'adsense.type'=>'contact'])->find();
        if(!empty($adsenseInfo)){
            $result['ad_img'] = image_url($adsenseInfo['filepath']);
        }

        $response['ret'] = 0;
        $response['data'] = $result;
        $this->response($response);

    }

    public function banners_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $type = $this->get('type')?$this->get('type'):'index';
        $where['banner.type'] = $type;
        $this->load->model('banner_model');
        $bannerList = $this->banner_model
            ->select('banner.id,name,img,attachment.filepath')
            ->where($where)
            ->join('attachment','attachment.id = banner.img')
            ->order_by('listorder','asc')
            ->find_all();
        foreach ($bannerList as $k=>$v){
            $v['img']  =image_url($v['filepath']);
            $bannerList[$k] = $v;
        }

        $response['ret'] = 0;
        $response['data']['banner_list'] = $bannerList;
        $this->response($response);

    }


    public function adsense_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $type = $this->get('type')?$this->get('type'):'index';
        $where['adsense.type'] = $type;
        $where['adsense.status'] = 1;
        $this->load->model('adsense_model');
        $adsenseInfo = $this->adsense_model
            ->select('adsense.id,name,filepath')
            ->where($where)
            ->join('attachment','attachment.id = adsense.img')
            ->find();
        !empty($adsenseInfo)&&$adsenseInfo['img'] = image_url($adsenseInfo['filepath']);
        unset($adsenseInfo['filepath']);
        $response['ret'] = 0;
        $response['data'] = $adsenseInfo;
        $this->response($response);

    }

    public function join_contact_get()
    {
        if($this->app['type'] != APPTYPE_XCX ) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $name = trim($this->post('name'));
        $mobile = trim($this->post('mobile'));
        $message = trim($this->post('message'));
        $time = trim($this->post('time'));
        $member_id = $this->_user['id'];
        $dt_add = time();

        $this->load->model('contact_model');

        if(!preg_match("/^1[34578]\d{9}$/", $mobile)){
            $this->response(array('ret' => 401, 'msg' => '请填写正确的手机号码!'));
        }


        $data = array();

        $data['name'] = $name;
        $data['mobile'] = $mobile;
        $data['message'] = $message;
        $data['time'] = $time;
        $data['member_id'] = $member_id;
        $data['dt_add'] = $dt_add;

        if($this->contact_model->add($data)){
            $this->response(array('ret' => 0,'msg'=>'添加成功'));
        }else{
            $this->response(array('ret' => 401,'msg'=>'系统错误'));
        }
    }

}