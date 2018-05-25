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
            $variable['by_location_longitude'] = $location[0];
            $variable['by_location_latitude'] = $location[1];
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
}