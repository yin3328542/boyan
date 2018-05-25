<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-11-5
 * Time: 下午12:50
 */

class Order extends Api_Controller
{
    /**
     * 说明：操作订单状态(前端接口)
     * 接口：api/order_opt
     * add by lxp 2014-11-11
     * 方式：get
     * 参数：
     *      open_id
     *      id   订单ID
     *		action  操作类型，  shouhuo  设置为已收货  tuihuo  退货款
     * 返回：
     *      正确 {ret: 0, msg: xxx }
     *      错误 {ret: >0 , msg: xxx }
     */
    public function order_opt()
    {
	    $data = array();
	    
        $open_id = $data['open_id'] = $this->input->get('open_id');
        $data['id'] = $this->input->get('id');
        $data['action'] = $this->input->get('action');
        $data['tuihuo_cause'] = $this->input->get('cause');

        if(!$open_id || !$data['id'] || !in_array($data['action'], array( 'shouhuo', 'tuihuo'))){//'paid', 'shipped',
	        $this->ajax_return(array('ret' => 500, 'msg' => '参数错误'));
        }

        $res = $this->rest->put('order', $data);
        //var_dump($res,$data);die;
        if($this->rest->status() == 200 && isset($res['ret'])) {
            $this->ajax_return($res);
        } else {
            $this->ajax_return(array('ret' => 500, 'msg' => '系统错误'));
        }
    }

}