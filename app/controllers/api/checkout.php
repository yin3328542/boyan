<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-11-5
 * Time: 下午12:50
 */

class Checkout extends Api_Controller
{
	public function cart_is_exist_goods()
	{
        $open_id = $this->input->post('open_id');
        $res_goods = $this->rest->get('shopping_cart', array('open_id' => $open_id));
        if($this->rest->status() == 200 && isset($res_goods['ret']) && $res_goods['ret'] == 0) {
            $data['goods'] = $res_goods['data'];
            $data['total_fee'] = $res_goods['total_fee'];
        }
        if(!$data['goods']) {
            $this->ajax_return(array('ret' => 601, 'msg' => '不存在购物商品'));
        }else{
	        $this->ajax_return(array('ret' => 0, 'msg' => '存在购物商品'));
        }	
	}

    /**
     * 立即购买临时保存商品
     * method post
     */
    public function add_goods()
    {
        $open_id = $this->input->post('open_id');
        $goods_id = $this->input->post('goods_id');
        $pro_id = $this->input->post('pro_id');
        $goods_num = intval($this->input->post('goods_num')) ? intval($this->input->post('goods_num')) : 1;

        if(!$goods_id) {
            $this->ajax_return(array('ret' => 400, 'msg' => '参数错误'));
        }
        $res = $this->rest->get('shopping_go', array('goods_id' => $goods_id, 'pro_id' => $pro_id, 'goods_num' => $goods_num));
        if($this->rest->status() == 200 && isset($res['ret'])) {
            if($res['ret'] == 0) {
                //写入缓存
                $this->load->model('cache_model');
                $this->cache_model->update($open_id, 'TEMP_GOODS', $res['data'], 60 * 24); //缓存20分钟
            }
            $this->ajax_return($res);
        } else {
            $this->ajax_return(array('ret' => 500, 'msg' => '系统错误'));
        }
    }

    /**
     * 结算信息
     * method get
     */
    public function checkout_info()
    {

        //参数：
        $open_id = $this->input->get('open_id');
        $site_id = $this->input->get('site_id');
        $type    = $this->input->get('type') ? $this->input->get('type') : 'cart';

        //获取商品
        if($type == 'cart') {
            $res_goods = $this->rest->get('shopping_cart', array('open_id' => $open_id));
            if($this->rest->status() == 200 && isset($res_goods['ret']) && $res_goods['ret'] == 0) {
                $data['goods'] = $res_goods['data'];
                $data['total_fee'] = $res_goods['total_fee'];
            }
            if(!$data['goods']) {
                //$this->ajax_return(array('ret' => 601, 'msg' => '您还没有选择购物的商品'));
            }
        } elseif($type == 'buy') {
            $this->load->model('cache_model');
            $data['goods'] = array($this->cache_model->read($open_id, 'TEMP_GOODS'));
            if(!$data['goods']) {
                //$this->ajax_return(array('ret' => 601, 'msg' => '您还没有选择购物的商品'));
            }
            $data['total_fee'] = $data['goods'][0]['goods_price'] * $data['goods'][0]['goods_num'];
        } else {
        	//$this->ajax_return(array('ret' => 601, 'msg' => '您还没有选择购物的商品'));
        }

        //获取收货地址
        $res_address = $this->rest->get('member_address', array('open_id' => $open_id));
        if($this->rest->status() == 200 && isset($res_address['ret']) && $res_address['ret'] == 0) {
            $data['address'] = $res_address['data'];
        } else {
            $data['address'] = '';
        }

        //获取支付方式、配送方式
        $res_payment = $this->rest->get('payment_method', array('site_id' => $site_id));
        if($this->rest->status() == 200 && isset($res_payment['ret']) && $res_payment['ret'] == 0) {
            $data['payment'] = $res_payment['data'];
        }
        if( !isset($data['payment']) || empty($data['payment']) ) {
            //$this->ajax_return(array('ret' => 611, 'msg' => '店铺没有开通支付功能'));
        }
        $res_shipping = $this->rest->get('shipping_method', array('site_id' => $site_id, 'province'=>$res_address['data']['province'] ,
            'goods'=>json_encode($data['goods'], true)));
        //var_dump($res_shipping);die;
        if($this->rest->status() == 200 && isset($res_shipping['ret']) && $res_shipping['ret'] == 0) {
            $data['shipping'] = $res_shipping['data'];
        } else {
            $data['shipping'] = '';
        }
        if( !isset($data['shipping']) || empty($data['shipping']) ) {
            //$this->ajax_return(array('ret' => 611, 'msg' => '店铺没有设置配送方式'));
        }

        //是否定义过配送时间段选择
        $res_time = $this->rest->get('shipping_time', array('site_id' => $site_id, 'hour'=> date('H')));
        if($this->rest->status() == 200 && isset($res_time['ret']) && $res_time['ret'] == 0) {
            $data['shipping_time'] = $res_time['data'];
        } else {
            $data['shipping_time'] = '';
        }

        //把商品总价加上运费
        if($data['shipping']) {
            $data['order_fee'] = $data['total_fee'] + $data['shipping'][0]['fee'];
        } else {
            $data['order_fee'] = $data['total_fee'];
        }

        $this->ajax_return(array('ret' => 0, 'data' => $data));

    }

    /**保存订单数据(前端接口)
     * 接口：api/order
     * add by lxp 2014-11-06
     * 方式：post
     * 参数：
     *      open_id
     *      site_id
     *		type  (buy立即购物, cart购物车)
     *      shop_id
     *      address_id 用户地址ID
     *      payment_code 支付code
     *      shipping_keyword 快递keyword
     *		shipping_note 备注
     * 返回：
     *      正确 {ret: 0, data: {} }
     *                      付款支付跳转传值等信息
     *      错误 {ret: >0 , msg: xxx }
     */
    public function order()
    {
	    $data = array();
	    
        $open_id = $data['open_id'] = $this->input->post('open_id');
        $data['site_id'] = $this->input->post('site_id');
        //$data['goods'] = $this->input->post('goods');
        $data['shop_id'] = $this->input->post('shop_id') ? $this->input->post('shop_id') : 0;
        $data['address_id'] = $this->input->post('address_id');
        $data['payment_code'] = $this->input->post('payment_code');
        $data['shipping_keyword'] = $this->input->post('shipping_keyword');
        $data['shipping_note'] = $this->input->post('shipping_note');

        
        $type    = $this->input->post('type') ? $this->input->post('type') : 'cart';
        //获取商品
        if($type == 'cart') {
            $res_goods = $this->rest->get('shopping_cart', array('open_id' => $open_id));
            if($this->rest->status() == 200 && isset($res_goods['ret']) && $res_goods['ret'] == 0) {
                $data['goods'] = $res_goods['data'];
            }
            if(!$data['goods']) {
                $this->ajax_return(array('ret' => 601, 'msg' => '您还没有选择购物的商品'));
            }
        } else {
            $this->load->model('cache_model');
            $data['goods'] = array($this->cache_model->read($open_id, 'TEMP_GOODS'));
            if(!$data['goods']) {
                $this->ajax_return(array('ret' => 601, 'msg' => '您还没有选择购物的商品1'));
            }
        }
        if(!$open_id) {
            $this->ajax_return(array('ret' => 400, 'msg' => '参数错误'));
        }
        $data['goods'] = json_encode($data['goods']);
        $res = $this->rest->post('order_save', $data);
        //var_dump($res,$data);die;
        if($this->rest->status() == 200 && isset($res['ret'])) {
            $this->ajax_return($res);
        } else {
            $this->ajax_return(array('ret' => 500, 'msg' => '系统错误'));
        }
    }

}