<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-10-27
 * Time: 下午3:28
 */
class Auth extends API_Controller {

    protected $model_name = 'member';

    public function signin_post()
    {
        $member_id = intval($this->post('member_id'));
        $member = $this->model->where(array('id'=>$member_id))->find();
        if (!$member) {
            $valid = FALSE;
            $status = 401;
        } else {
            //发牌照
            $token = $this->_generate_token(APPTYPE_WAP, $member['id']);
            $valid = array('access_token'=>$token);
            $status = 200;
        }
        $this->response($valid, $status);
    }
}