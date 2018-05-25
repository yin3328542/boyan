<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-2-19
 * Time: 下午3:27
 */


class API_Controller extends REST_Controller {

    protected $auto_load_model = TRUE;
    protected $model_name = NUll;
    protected $app = NULL;
    protected $_user = NULL;

    protected $app_key = '121o8d6f83js749m';
    protected $app_secret = '34tvf467tty68o66t4v36nsdf678mut7';

    protected $site_api_url = 'http://shop.121dian.com/api/';
    protected $site_app_key = '12179lk3co09a571';
    protected $site_access_token = '29cba4d097b94f33e8a13b13b38eea5f';

    /*
    protected $site_api_url = 'http://shop.121dian.com/api/';
    protected $site_app_key = '12179lk3co09a571';
    protected $site_access_token = '29cba4d097b94f33e8a13b13b38eea5f';
    */
    public function __construct()
    {
        parent::__construct();
        //自动加载相对应的数据模型
        if ($this->auto_load_model) {
            $model_name = $this->model_name ? $this->model_name . '_model' : $this->router->fetch_class() . '_model';
            $this->load->model($model_name, 'model');
        }
        $this->_set_user();
        $this->_check_resource();
    }

        /**
         * 验证资源访问权限
         */
    public function _check_resource()
    {
        if($this->app['type'] == APPTYPE_SITE) {
            if($this->_user['role_id'] == 0) {
                return true;
            }
            //当前资源路径
            $curr_res = $this->router->fetch_module().'/'.$this->router->fetch_class().'/'.$this->router->fetch_method();
            if($this->router->fetch_directory()) {
                $curr_res = $this->router->fetch_module().'/'.$this->router->fetch_directory().$this->router->fetch_class().'/'.$this->router->fetch_method();
            }
            $curr_res = $curr_res.'_'. $this->request->method;
            //查找资源宿主
            $this->config->load('resource');
            $all_resource = $this->config->item('resource');
            if(!$all_resource OR !isset($all_resource['siter'])) {
                return true;
            }
            $resource = $all_resource['siter'];
            $resource_api = $all_resource['api'];
            $res_key = '';
            foreach($resource as $key => $res) {
                if(in_array($curr_res, $res['api'])) {
                    $res_key = $key;
                }
            }
            if(!$res_key) {
                return true;
            }
            //无需验证权限时退出
            if(!isset($resource_api[$curr_res]) OR $resource_api[$curr_res] < 2) {
                return true;
            }

            //用户的resource设置
            $siter_info = $this->visitor->info;
            $user_res = $siter_info['resource'];
            if(!$user_res) {
                //无权限
                $this->response(array('ret' => 403, 'msg' => '对不起，您的访问权限不足'));
            }
            if(!in_array($res_key, $user_res)) {
                //无权限
                $this->response(array('ret' => 403, 'msg' => '对不起，您的访问权限不足'));
            }

            return true;
        }
        return true;
    }

    private function _set_user()
    {
        if (!$access_token = isset($this->_args['access_token']) ? $this->_args['access_token'] : $this->input->server('HTTP_ACCESS_TOKEN')) {
            return FALSE;
        }
        $this->load->model('rest_token_model');
        if (!$access = $this->rest_token_model->where('token', $access_token)->find()) {
            return FALSE;
        }

        switch ($access['type']) {
            case APPTYPE_ADMIN :
                $this->load->model('admin_model', 'user_model');
                break;
            case APPTYPE_PC :
                $this->load->model('member_model', 'user_model');
                break;
        }

        $this->_user = $this->user_model->find($access['uid']);
    }

    /**
     * 为了兼容多点登录，增加判断，如果存在token，则返回已经存在的，如果不存在，则生成一个(**正式环境应去掉该功能**)
     * @param $type
     * @param $uid
     * @param int $ctr
     * @return string
     */
    protected function _generate_token($type, $uid, $ctr = 0)
    {
        $this->load->model('rest_token_model');
        //查询token是否存在
        $token = $this->rest_token_model->where(array("type" => $type, "uid" => $uid))->get_field("token");
        if (!$token) {
            $token = md5($ctr . $this->config->item('encryption_key') . $uid . time());

            //清理历史session_key
            $this->rest_token_model->where(array(
                'type' => $type,
                'uid' => $uid,
            ))->delete();

            if ($this->rest_token_model->where('token', $token)->count()) {
                $token = $this->_generate_token($uid, ++$ctr);
            }

            $this->rest_token_model->add(array(
                'type' => $type,
                'uid' => $uid,
                'token' => $token,
                'dt_add' => time()
            ));
        }
        return $token;
    }

    /**
     * rest client
     */
    protected function _load_rest_client()
    {
        $this->load->library('rest', array(
            'server' => site_url('api'),
            'app_key' => $this->app_key,
            'secret_key' => $this->app_secret,
        ), 'api_rest');
    }

    /**
     * @param $to_email
     * @param $subject
     * @param $message
     * @param string $from_email
     * @param string $from_name
     * @return mixed
     * 发送邮件
     */

    protected function _send_email($to_email, $subject, $message, $from_email='', $from_name='121店通知')
    {
        $this->load->config('email');
        $this->load->library('email');

        $from_email = $from_email ? $from_email : $this->config->item('smtp_user');
        $this->email->from($from_email, $from_name);
        $this->email->to($to_email);
        $this->email->subject($subject);
        $this->email->message($message);
        return $this->email->send();
    }

    protected $img_app_key = '5318162aac6f0086';
    protected $img_app_secret = '0c28536510e0b0b429750f478222d549';
    /*------------------------------------------------------------
    | @name 上传到图片服务器,并返回图片相对路径
    |------------------------------------------------------------
    | @param $image
    | @param array $size example: array(array('width'=>80, 'height'=>80), array('width'=>640, 'height'=>320), array('width'=>640, 'height'=>640))
    | @return mixed
    */
    public function rest_upload($full_path, $upload_file_path='', $img_name='', $size=array(), $compress=TRUE , $local_save=FALSE)
    {
        $this->load->library('rest', array(
            'server' => IMAGE_SERVER."api",
            'app_key' => $this->img_app_key,
            'secret_key' => $this->img_app_secret,
        ), 'image_rest');

        $data = array(
            'img' => '@'.$full_path
        );
        $upload_file_path && $data['upload_path'] = IMG_UPLOAD_FOLDER.'/'.$upload_file_path;
        $img_name && $data['img_name'] = $img_name;
        $size && $data['size'] = json_encode($size);
        $compress && $data['compress'] = $compress;
        $result = $this->image_rest->post('upload/img', $data);
        !$local_save && @unlink($full_path);
        //$this->image_rest->debug();exit;
        if(isset($result['error'])) {
            $this->response(array('success'=>FALSE, 'msg'=>$result['error']));
        } else {
            return $result['img'];
        }
    }

    public function rest_remove($img_path)
    {
        $this->load->library('rest', array(
            'server' => IMAGE_SERVER."api",
            'app_key' => $this->img_app_key,
            'secret_key' => $this->img_app_secret,
        ));

        $data = array(
            'img_path' => rel_image_url($img_path)
        );

        $this->rest->delete('upload/img', $data);
    }


    /**
     * 通过用户open_id 获取 网站用户数据
     * @param $open_id
     * @return array
     */
    public function get_site_member_info($open_id)
    {
        if(!$open_id) return array();

        $this->load->model('member_model');

        $member = $this->member_model->where(array('open_id' => $open_id))->find();

        return $member;
    }

    /**
     * 返回一个订单编号
     * @return string
     */
    public function get_order_sn()
    {
        $order_sn = date('ymdHis').rand(1000,9999);

        $this->load->model('order_model');

        $order = $this->order_model->where(array('order_sn' => $order_sn))->find();

        if(!$order){
            return $order_sn;
        }else{
            return $this->get_order_sn();
        }
    }

    /**
     * 通过商品id,规格 判断库存  及   获取商品价格库存等数据
     * @param $goods_id
     * @param $pro_id
     * @param $goods_num
     * @return array
     */
    public function get_goods_info($goods_id, $pro_id = 0, $goods_num = 1,$open_id='')
    {
        //读取商品库存信息
        $data = array();
        if(!$goods_id){
            $this->response(array('ret' => 501, 'msg' => '参数不正确'));
        }
        $site_id = 0;

        $this->load->model('goods_model');
        $goods = $this->goods_model->where(array('id' => $goods_id))->find();
        if($goods['status'] != 1){
            $this->response(array('ret' => 404, 'msg' => '商品已经下架或删除'));
        }
        $time = time();
        $open_id || $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : false;
        $open_id || $open_id = $this->input->get_post('open_id');
        if($goods['max_buy_num'] > 0 && $goods['max_buy_start_time'] < $time && $goods['max_buy_end_time'] > $time){
            if($goods['max_buy_num'] < $goods_num){
                $this->response(array('ret' => 404, 'msg' => '本商品限购数量:'.$goods['max_buy_num']));
            }elseif($open_id){
                //判断用户的订单（限制时间段）商品数量
                $this->load->model('order_model');
                $order_arr = $this->order_model->select('order_sn')->where(array('open_id'=>$open_id,'dt_add >'=>$goods['max_buy_start_time']))->find_all();
                $order_sn_arr = array();
                foreach($order_arr as $order){
                    $order_sn_arr[] = $order['order_sn'];
                }
                if($order_sn_arr){
                    $this->load->model('order_goods_model');
                    $_goods_num = $this->order_goods_model->select_sum('goods_num')->where(array('goods_id'=>$goods_id))->where_in('order_sn',$order_sn_arr)->get_field('goods_num');
                    if($goods['max_buy_num'] <= ($_goods_num+$goods_num)){
                        $this->response(array('ret' => 404, 'msg' => '限购数量:'.$goods['max_buy_num'].',已购 '.$_goods_num));
                    }
                }
            }
        }

        if($pro_id > 0) {
            $this->load->model('goods_pro_model');
            $goods = $this->goods_pro_model->select('goods.name,goods.price,goods.img, goods.total,goods.status, goods.stock, goods.weight, goods.site_id, goods.fanli, goods_pro.*')
                ->join('goods', 'goods_pro.goods_id = goods.id')
                ->where(array('goods_pro.id' => $pro_id))->find();

            if($goods_id != $goods['goods_id']){
                $this->response(array('ret' => 403, 'msg' => '参数不匹配'));
            }
            if($goods['status'] != 1){
                $this->response(array('ret' => 404, 'msg' => '商品已经下架或删除'));
            }

            //库存控制
            if($goods['pro_total'] > 0 && ($goods['pro_stock'] <= 0 OR $goods['pro_stock'] < $goods_num) ) {
            //if($goods['pro_total'] > 0 && $goods['pro_stock'] <= 0) {
                $this->load->model('goods_model');
                $this->goods_model->update_status($goods_id, 'outstock');
                $this->response(array('ret' => 501, 'msg' => '对不起，库存不足无法立即购买'));
            }else{
                $data['goods_stock'] = $goods['pro_stock'] ? $goods['pro_stock'] : 0;//不限库存
                $data['goods_total'] = $goods['pro_total'];
                $data['goods_id'] = $goods['goods_id'];
                $data['goods_price'] = $goods['pro_price'] > 0 ? $goods['pro_price'] : $goods['price'];
                $data['goods_weight'] = $goods['pro_weight'] > 0 ? $goods['pro_weight'] : $goods['weight'];
                if($goods['attr']){
                    $_arr = json_decode($goods['attr'],true);
                    //var_dump($goods['attr'],$_arr);die;
                    $arr = array();
                    foreach($_arr as $_ar)
                    {
                        $arr[] = $_ar['key'].':'.$_ar['value'];
                    }
                    $data['goods_attr'] = implode(',', $arr);
                }
                $data['img_file'] = image_url($goods['img']);
                $data['img'] = $goods['img'];
                $data['name'] = $goods['name'];
                $site_id = $goods['site_id'];
                $data['fencheng_scale'] =  $goods['fanli'];
            }
        } else {
            //是否存在规格货品
            $this->load->model('goods_pro_model');
            $goods_pro = $this->goods_pro_model->where(array('goods_id' => $goods_id))->count();
            if($goods['goods_attr'] > 0 && $goods_pro) {
                $this->response(array('ret' => 511, 'msg' => '请选择商品规格'));
            }
            //库存控制
            if($goods['total'] > 0 && ($goods['stock'] <= 0 OR $goods['stock'] < $goods_num)) {
            //if($goods['total'] > 0 && $goods['stock'] <= 0) {
                $this->goods_model->update_status($goods_id, 'outstock');
                $this->response(array('ret' => 501, 'msg' => '对不起，库存不足无法立即购买'));
            }else{
                $data['goods_stock'] = $goods['stock'] ? $goods['stock'] : 0;//不限库存
                $data['goods_total'] = $goods['total'];
                $data['goods_id'] = $goods['id'];
                $data['goods_price'] = $goods['price'];
                $data['goods_weight'] = $goods['weight'];
                $data['img_file'] = image_url($goods['img']);
                $data['img'] = $goods['img'];
                $data['name'] = $goods['name'];
                $data['goods_attr'] = '';
                $site_id = $goods['site_id'];
                $data['fencheng_scale'] =  $goods['fanli'];
            }
        }
        $data['site_id'] = $site_id;
        /*
        if($data['goods_total'] > 0 && $data['goods_stock'] < $goods_num)
        {
            $this->response(array('ret' => 501, 'msg' => '对不起，库存不足无法立即购买'));
        }
        */
        //计算分成
        if( $site_id && ( $data['fencheng_scale'] == -1 || empty($data['fencheng_scale']) )){
            //获取店铺的分成
            $this->load->model('site_model');
            $site = $this->site_model->get_site_info($site_id);

            $data['fencheng_scale'] = $site['fencheng'];
        }
        $data['fencheng_scale'] = floatval($data['fencheng_scale'] ? $data['fencheng_scale']/100 : 0);
        $data['fencheng_amount'] = kr_format_amount($data['goods_price'] * $goods_num * $data['fencheng_scale']);

        $data['goods_num'] = $goods_num;
        $data['pro_id'] = $pro_id;
        return $data;
    }

    /**
     * 查询商品的运费（可以考虑缓存起来）
     * @param $site_id  网站ID
     * @param $shipping_keyword  运送方式key
     * @param $province  城市名称
     * @param $goods_arr
     *            goods_id 商品ID
     *            goods_num  数量
     *            pro_id  商品规格    可选
     *            goods_weight 重量      可选
     */
    public function get_goods_shipping_fee($site_id, $shipping_keyword, $province, $goods_arr)
    {
        //不填写以下参数，直接返回0
        if(!$site_id || !$shipping_keyword || !$province || !is_array($goods_arr)){
            return 0;
        }
        //判断是否全场免费
        $this->load->model('site_model');
        $site_arr = $this->site_model->get_site_info($site_id);
        if($site_arr && $site_arr['free_shipping'] == 1){
            return 0;
        }
        //计算商品总重量
        $goods_weight = 0;
        foreach($goods_arr as $goods){
            $goods_id = $goods['goods_id'];
            $goods_num = $goods['goods_num'];
            $pro_id = isset($goods['pro_id']) ? $goods['pro_id'] : 0;
            $weight = -1;//isset($goods['goods_weight']) ? $goods['goods_weight'] :
            if($weight == -1){
                $goods_info = $this->get_goods_info($goods_id, $pro_id, $goods_num);
                $goods_weight += ($goods_info['goods_weight'] * $goods_num);
            }else{
                $goods_weight += $weight;
            }
        }
        if($goods_weight <= 0){
            return 0;//没有重量，直接免费
        }
        //查询运送方式
        $this->load->model('shipping_model');
        $shipping_arr =  $this->shipping_model->where(array('keyword' => $shipping_keyword, 'site_id' => $site_id))->find();
        //判断是否免费
        if($shipping_arr['is_free']==1){
            return 0;
        }
        //查询该运送方式的费用列表
        $this->load->model('shipping_fee_model');
        //按首重费用从大到小排列
        $shipping_fee_arr =  $this->shipping_fee_model
            ->where(array('sid' => $shipping_arr['id']))
            ->order_by('fee1','desc')
            ->find_all();
        //查询城市id
        $this->load->model('region_model');
        $region_arr =  $this->region_model
            ->where(array('parent_id' => 1))
            ->like('name', str_replace(array('省','市'),array(),$province), 'after')
            ->find();
        //计算重量
        //var_dump($region_arr,$province);exit();

        if( isset($region_arr['id']) && $region_arr['id']  ){
            $shipping_info = array();
            foreach($shipping_fee_arr as $shipping_fee){
                $area_arr = json_decode($shipping_fee['area'],true);//解析配送区域
                //var_dump($area_arr);die;
                if(is_array($area_arr)){
                    //查找对应的配送区域
                    foreach($area_arr as $key=>$area){
                        if($key == $region_arr['id']){
                            $shipping_info =  $shipping_fee;
                            break;//查找到，马上跳出
                        }
                    }
                }else{
                    //$this->response(array('ret' => 501, 'msg' => '配送区域有误'));//没有配送区域
                    return 0;
                }
                if($shipping_info){
                    break;//查找到配送费用信息
                }
            }
        }else{
            //$this->response(array('ret' => 501, 'msg' => '无法找到此城市'));//不存在的城市
            return 0;
        }
        //计算费用
        if($shipping_info){
            //判断首重
            if($goods_weight > $shipping_info['weight1']){
                //大于首重计算方式
                $weight = $goods_weight - $shipping_info['weight1'];//超出的重量
                $fee = $shipping_info['fee2'] / $shipping_info['weight2'];//续重单价
                return $shipping_info['fee1'] + $weight * $fee;
            }else{
                return $shipping_info['fee1'];
            }
        }else{
            return 0; //免费
        }
    }

    //检测邮箱
    protected function check_email($email_address){
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        return preg_match($pattern, $email_address);
    }

    //检测手机

    protected function checkMobileValidity($mobile){
        $exp = "/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[012356789]{1}[0-9]{8}$|14[57]{1}[0-9]$/";
        if(preg_match($exp,$mobile)){
               return true;
        }else{
               return false;
        }
    }
} 