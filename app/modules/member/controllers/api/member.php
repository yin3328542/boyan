<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-10-27
 * Time: 下午3:40
 */
class Member extends API_Controller {
    var $model_name='member_xcx';
    public function __construct()
    {
        parent::__construct();
    }

    public function members_get()
    {
        $site_id = isset($this->_user['site_id']) ? $this->_user['site_id'] : $this->get('site_id');
        if($this->app['type'] == APPTYPE_SITE && !$site_id) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $limit = $this->get('limit') ? $this->get('limit') : 10;
        $offset = $this->get('offset') ? $this->get('offset') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'dt_add';
        $order = $this->get('order') ? $this->get('order') : 'asc';
        $keyword = $this->get('keyword') ? $this->get('keyword') : '';
        $where['site_id'] = $site_id;
        $where['public_id !='] = '';
        $where['uid >'] = 0;

        $or_where = '(1 = 1)';

        if($keyword) {
            $or_where = "(name like '%$keyword%' or nickname like '%$keyword%' or mobile like '%$keyword%')";
        }

        $response['_count'] = $this->model->where($where)->where($or_where)->count();
        $response['data'] = $this->model
            ->where($where)
            ->where($or_where)
            ->order_by($sort, $order)
            ->limit($limit, $offset)
            ->find_all();
        $this->load->model("user_model");
        foreach($response['data'] as $k=>$item) {
            $userInfo = $this->user_model->find($item['uid']);
            $response['data'][$k]['in_blacklist'] = $userInfo['in_blacklist'];
            if(!empty($userInfo['in_blacklist'])){
                $response['data'][$k]['name'] = $item['name']."[已加入黑名单]";
            }

        }

        $response['ret'] = 0;
        $this->response($response);
    }


    public function member_put()
    {
        $site_id = isset($this->_user['site_id']) ? $this->_user['site_id'] : $this->get('site_id');
        if($this->app['type'] == APPTYPE_SITE && !$site_id) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }
        $id = $this->put('id');
        $action = $this->put('action');

        $memberInfo = $this->model->find($id);
        if(empty($memberInfo)){
            $this->response(array('ret' => 403, 'msg' => '用户不存在'));
        }
        switch ($action){
            case 'in': $in_blacklist=1;break;
            case 'out': $in_blacklist=0;break;
        }
        $this->load->model('user_model');
        $this->user_model->where(['id'=>$memberInfo['uid']])->edit(['in_blacklist'=>$in_blacklist]);
        $response['ret'] = 0;
        $this->response($response);
    }
    /**
     * 金币日志记录列表
     * api接口：api/gold_logs
     * 方式：get
     * 参数：
     *      open_id
     *      limit   查询数量
     *      offset  偏移位置
     * 返回：
     *      {ret, data{}}
     *          id        记录ID(惟一标识)
     *          gold        金币
     *          status_str   格式化的状态
     *          type_str   格式化的类型
     *          dt_add_str   格式化的增加时间
     *
     *      错误 {ret: >0 , msg: xxx }
     */
    public function gold_logs_get()
    {
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';


        $teller_type = $this->get('type') !== false ? $this->get('type') : 'all';

        if(!$open_id){
            $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        }
        if($this->app['type'] == APPTYPE_WAP){
            if(empty($open_id)){
                $this->response(array('ret' => 400, 'msg' => '缺少参数'));
            }
            $teller_type == 'all' && $teller_type = 1;
        }
        if($open_id){
            $member = $this->model->member_get(array('open_id'=>$open_id));
            if(empty($member)){
                $this->response(array('ret' => 404, 'msg' => '不存在此用户'));
            }
        }
        //处理分页等参数
        $limit = $this->get('limit') ? $this->get('limit') : 10;
        $offset = $this->get('offset') ? $this->get('offset') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'dt_add';
        $order = $this->get('order') ? $this->get('order') : 'desc';

        $status = $this->get('status') !== false ? $this->get('status') : 'all';

        $where = array();
        $where['open_id'] = $open_id;
        if($status == 'all'){
            $where['status !='] = 2;
        }else{
            $where['status'] = $status;
        }
        $where['type'] = 1;//暂时只显示消费记录

        $this->load->model('gold_log_model');

        $response['_count'] = $this->gold_log_model->where($where)->count();

        $response['data'] = $this->gold_log_model
            ->where($where)
            ->order_by($sort, $order)
            ->limit($limit, $offset)
            ->find_all();

        //0 申请中，1 已处理，2 已拒绝
        $status_arr = array(
            '0'=>'进行中',
            '1'=>'已完成',
            '2'=>'未知',
        );
        $type_arr = array(
            '1'=>'购物消费',
        );

        foreach($response['data'] as $k=>$v){
            $response['data'][$k]['dt_add_str'] = date('Y-m-d H:i:s', $v['dt_add']);
            $status = $v['status'];
            $response['data'][$k]['status_str'] = $status_arr[$status];
            $type = $v['type'];
            $response['data'][$k]['type_str'] = $type_arr[$type];
            if($type == 1 && $status == 2){
                $response['data'][$k]['status_str'] = '已取消';
            }
        }

        $response['ret'] = 0;
        $this->response($response);
    }

    /**
     * 会员金币
     * 接口：api/member_gold
     * 参数：
     *      open_id
     * 返回：
     *     can_tx_amount      可提现
     *     gold               总金币
     *     use_gold           已使用金币
     *     exchange_gold      已兑换金币
     */
    public function member_gold_get()
    {
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';

        if(!$open_id){
            $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        }
        if($this->app['type'] == APPTYPE_WAP){
            if(empty($open_id)){
                $this->response(array('ret' => 400, 'msg' => '缺少参数'));
            }
            $goods_arr =  $this->get('goods') ? (array) json_decode( $this->get('goods'), true) : array();
        }
        if($open_id){
            $this->load->model('shop_model');
            $shop = $this->shop_model->shop_get(array('open_id'=>$open_id));
            if(empty($shop)){
                #$this->response(array('ret' => 404, 'msg' => '不存在此分享家'));
            }
        }
        $response['can_tx_amount'] =  0;


        if(isset($shop['id'])){
            //计划七天内的收入金额总额（这一部分不能提现）
            #$time = strtotime(date('Y-m-d 00:00:00', strtotime('-7day')));
            $this->load->model('shop_fencheng_model');
            $this->load->model('shop_teller_model');
            #$freeze_amount = max(0,$this->shop_fencheng_model->where(array('shop_id'=>$shop['id'], 'dt_add >'=>$time))->sum('amount'));
            $unfinished_amount = $freeze_amount = max(0,$this->shop_fencheng_model->where(array('shop_id'=>$shop['id'], 'status'=>0))->sum('amount'));

            $response['total_amount'] = $shop['amount'];
            $sq_tx_amount = max(0,$this->shop_teller_model->where(array('status'=>0, 'shop_id'=>$shop['id']))->sum('amount'));
            $response['freeze_amount'] = $freeze_amount + $sq_tx_amount;
            $response['can_tx_amount'] = max($response['total_amount'] - $response['freeze_amount'], 0);
        }

        $response['can_tx_amount'] = sprintf("%.2f", $response['can_tx_amount']);

        $member_arr = $this->model->member_get(array('open_id'=>$open_id));

        if($member_arr['gold'] > 0 && $goods_arr){
            $max_gold = 0;
            $goods_max_gold_arr = $goods_num_size_arr = $goods_id_arr = array();

            foreach($goods_arr as $goods){
                $_goods_id = $goods['goods_id'];

                $goods_id_arr[] = $goods['goods_id'];
                $goods_num_size_arr[$_goods_id] += $goods['goods_num'];
                $goods_max_gold_arr[$_goods_id] += $goods['goods_price'] * $goods['goods_num'] * 100;
            }
            //查询限制金币
            if($goods_id_arr){
                $this->load->model('goods_model');
                $db_goods_arr = $this->goods_model->where_in('id', $goods_id_arr)->find_all();
                foreach($db_goods_arr as $goods){
                    $_goods_id = $goods['id'];
                    $_max_gold = $goods['max_gold'];
                    if($_max_gold == -1){
                        $max_gold += $goods_max_gold_arr[$_goods_id];
                        continue;
                    }
                    if(isset($goods_num_size_arr[$_goods_id])){
                        //计算最大使用值
                        $max_gold += $goods_num_size_arr[$_goods_id] * $_max_gold;
                    }
                }
            }
            if($max_gold != 0){
                $member_arr['gold'] =  $member_arr['gold'] < $max_gold ? $member_arr['gold'] : $max_gold;//最大使用金币
            }else{
                $member_arr['gold'] =  0;
            }
        }

        $response['gold'] = $member_arr['gold'];
        $response['gold2amount'] = kr_format_amount($member_arr['gold'] / 100);
        $response['use_gold'] = $member_arr['use_gold'];
        $response['exchange_gold'] = $member_arr['exchange_gold'];

        $response['ret'] = 0;
        $this->response($response);
    }
    /**
     * 收藏小店
     * 接口：api/member_collect_shop
     * 方式：get
     * 参数：
     *      open_id
     *      shop_id
     * 返回：
     *      正确 {ret: 0 , msg: xxx }
     *      错误 {ret: >0 , msg: xxx }
     */
    public function member_collect_shop_get()
    {
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';
        $shop_id = $this->get('shop_id');
        if(!$open_id || !$shop_id){
            $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        }

        $member = $this->model->member_get(array('open_id'=>$open_id));
        if(!$member){
            $this->response(array('ret' => 401, 'msg' => '用户不存在'));
        }
        $site_id = $member['site_id'];
        $uid     = $member['uid'];

        $this->load->model('collect_shop_model');
        $s = $this->collect_shop_model->collect_add($uid, $site_id, $shop_id, $open_id);

        if($s > 0 ){
            $this->response(array('ret' => 0, 'msg' => '收藏成功'));
        }elseif($s == -1){
            $this->response(array('ret' => 4, 'msg' => '已经收藏'));
        }else{
            $this->response(array('ret' => 5, 'msg' => '收藏失败'));
        }
    }
    /**
     * 收藏小店列表
     * api接口：api/member_collect_shop_list
     * 方式：get
     * 参数：
     *      open_id
     *      limit   查询数量
     *      offset  偏移位置
     * 返回：
     *      {ret, data:[]}
     *      错误 {ret: >0 , msg: xxx }
     */
    public function member_collect_shop_list_get()
    {
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';

        if(!$open_id){
            $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        }
        $is_shop = 0;
        if($open_id){
            $member = $this->model->member_get(array('open_id'=>$open_id));
            if(empty($member)){
                $this->response(array('ret' => 404, 'msg' => '不存在此用户'));
            }
            $this->load->model('shop_model');
            $shop = $this->shop_model->shop_get(array('open_id'=>$open_id));
            if($shop['status'] == 1){
                $is_shop = 1;
            }
        }
        //处理分页等参数
        $limit = $this->get('limit') ? $this->get('limit') : 10;
        $offset = $this->get('offset') ? $this->get('offset') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'dt_add';
        $order = $this->get('order') ? $this->get('order') : 'desc';

        $where = array();
        if($member['uid']){
            $where['uid'] = $member['uid'];
        }else{
            $where['open_id'] = $member['open_id'];
        }

        $this->load->model('collect_shop_model');
        $this->load->model('shop_model');
        $this->load->model('shop_config_model');

        $response['_count'] = $this->collect_shop_model->where($where)->count();

        $data = $this->collect_shop_model
            ->select('collect_shop.*, site.name')
            ->join('site', 'site.id = collect_shop.site_id')
            ->where($where)
            ->order_by($sort, $order)
            ->limit($limit, $offset)
            ->find_all();

        $response['data'] = array();

        foreach($data as $v){
            $site_id = $v['site_id'];
            if($is_shop && $site_id == $shop['site_id']){
                unset($v);
                continue;
            }
            $shop_info = $this->shop_model->shop_get(array('id'=>$v['shop_id']));
            if(!isset($shop_config[$site_id])){
                $shop_config[$site_id] = $this->shop_config_model->where(array('site_id'=>$v['site_id']))->find();
            }
            $v['shop_name'] = $shop_info['nickname'].'的'.$shop_config[$site_id]['suffix'];
            $v['avatar']    = $shop_info['avatar'];

            $v['url'] = 'http://'.wap_url($v['site_id']).'/shop_id/'.$v['shop_id'];

            $response['data'][] = $v;
        }

        $response['ret'] = 0;
        $this->response($response);
    }

    /**
     * 商品浏览记录列表
     * api接口：api/goods_history
     * 方式：get
     * 参数：
     *      limit   查询数量
     *      offset  偏移位置
     * 返回：
     *      错误 {ret: >0 , msg: xxx }
     *      成功 {ret: 0 , data: xxx }
     *                      goods_url   商品url
     *                      img   商品图片
     *                      price   当前价格
     *                      market_price    市场价格
     */
    public function goods_history_get()
    {
        $public_id =  isset($this->_user['public_id']) ? $this->_user['public_id'] : '';
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';

        if(!$open_id && !$public_id){
            $this->response(array('ret' => 400, 'msg' => '缺少参数'));
        }

        //处理分页等参数
        $limit = $this->get('limit') ? $this->get('limit') : 10;
        $offset = $this->get('offset') ? $this->get('offset') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'dt_add';
        $order = $this->get('order') ? $this->get('order') : 'desc';


        $where = array();

        if($open_id){
            $member = $this->model->member_get(array('open_id'=>$open_id));
            if(empty($member)){
                $this->response(array('ret' => 404, 'msg' => '不存在此用户'));
            }
            $where['uid'] = $member['uid'];
        }
        if($public_id){
            $where['uid'] = $this->_user['id'];
        }

        $this->load->model('member_goods_history_model');

        $response['_count'] = $this->member_goods_history_model->where($where)->count();

        $response['data'] = $this->member_goods_history_model
            ->select('member_goods_history.*,goods.img,goods.name,goods.price,goods.market_price')
            ->join('goods', 'goods.id = member_goods_history.goods_id')
            ->where($where)
            ->order_by($sort, $order)
            ->limit($limit, $offset)
            ->find_all();

        foreach($response['data'] as &$v){
            $v['img'] = image_url($v['img']);
            $v['goods_url'] = 'http://'.wap_url($v['site_id']).'/goods/detail/id/'.$v['goods_id'];
        }

        $response['ret'] = 0;
        $this->response($response);
    }
    /**
     * 访问过的小店列表
     * api接口：api/my_site
     * 方式：get
     * 参数：
     *    无
     * 返回：
     *      错误 {ret: >0 , msg: xxx }
     *      成功 {ret: 0 , data: xxx }
     *                      name   商家店铺名称
     *                      logo_file   logo
     *                      site_url    店铺链接
     */
    public function my_site_get()
    {
        $public_id =  isset($this->_user['public_id']) ? $this->_user['public_id'] : '';
        if(!$public_id) {
            $this->response(array('ret' => 400, 'msg' => 'public_id error.'));
        }
        $arr = $this->model->where('public_id', $public_id)->find_all();
        $this->load->model('site_model');

        $data = array();

        foreach($arr as $item){
            $site_info = $this->site_model->get_site_info($item['site_id']);
            $data[] = array(
                'name' => $site_info['name'],
                'site_url' => $site_info['site_url'],
                'logo_file' => $site_info['logo_file'],
            );
        }

        $response['ret'] = 0;
        $response['data'] = $data;
        $this->response($response);
    }

    /**
     *   商品收藏列表接口   api/collect_goods_list
     *   请求方式：get
     *   参数:
     *        open_id
     *         limit  查询数量   默认是10
     *         offset  开始位置  默认是0
     *   成功返回：{ret: 0, _count: 总数量, data: []}
     *                             goods_id
     *                             goods_name: 商品名称
     *                             goods_price 售价
     *                             goods_img 商品主图
     *                             fav_num  收藏数量
     *   失败返回：{ret: > 0 , msg: xxx}
     */
    public function collect_goods_list_get()
    {
        $this->load->model('collect_goods_model');
        $where = array();
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';
        !$open_id && $this->response(array('ret' => 400, 'msg' => '参数错误'));

        $member_info = $this->model->member_get(array('open_id'=>$open_id));

        if(!$member_info){
            $this->response(array('ret' => 404, 'msg' => '用户不存在'));
        }

        $where['open_id'] = $open_id;

        $limit = $this->get('limit') ? $this->get('limit') : 10;
        $offset = $this->get('offset') ? $this->get('offset') : 0;
        $sort = $this->get('sort') ? $this->get('sort') : 'id';
        $order = $this->get('order') ? $this->get('order') : 'desc';

        $response['_count'] = $this->collect_goods_model->where($where)->count();
        $response['data'] = $this->collect_goods_model
            ->where($where)
            ->order_by($sort, $order)
            ->limit($limit, $offset)
            ->find_all();

        $this->load->model('collect_goods_num_model');

        foreach($response['data'] as $k => $val) {
            $response['data'][$k]['goods_img'] = image_url($val['goods_img']);
            $response['data'][$k]['fav_num'] = $this->collect_goods_num_model->get_num($val['goods_id']);
            $response['data'][$k]['goods_url'] = 'http://'.wap_url($val['site_id']).'/goods/detail/id/'.$val['goods_id'];
        }

        $response['ret'] = 0;
        $this->response($response);

    }
    /**
     * 说明：收藏/取消 商品
     * 接口：api/collect_goods
     * 方式：get
     * 参数：
     *      open_id
     *      goods_id
     *      action   add 收藏 ,  del 取消
     * 返回：
     *      正确 {ret: 0, msg: xxx }
     *      错误 {ret: >0 , msg: xxx }
     */
    public function collect_goods_get()
    {
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';
        $goods_id = $this->post('goods_id');
        $action = $this->post('action');

        if(!$open_id || !$goods_id || !in_array($action, array('add','del'))){
            $this->response(array('ret' => 400, 'msg' => '参数错误'));
        }

        $this->load->model('collect_goods_model');
        $is_collect = $this->collect_goods_model->where(array('open_id'=>$open_id, 'goods_id'=>$goods_id))->find();


        $this->load->model('collect_goods_num_model');


        if($action == 'add'){
            if($is_collect){
                $this->response(array('ret' => 400, 'msg' => '已经收藏过了'));
            }
            $data = array();
            $this->load->model('goods_model');
            $data = $this->goods_model->select('id as goods_id,name as goods_name,img as goods_img,price as goods_price')->find($goods_id);

            if(empty($data)) $this->response(array('ret' => 400, 'msg' => '不存在此商品'));



            $member_info = $this->model->member_get(array('open_id'=>$open_id));
            if(!$member_info){
                $this->response(array('ret' => 404, 'msg' => '用户不存在'));
            }

            $data['open_id'] = $open_id;
            $data['site_id'] = $member_info['site_id'];
            $data['uid'] = $member_info['uid'];
            $data['dt_add'] = time();

            if($this->collect_goods_model->add($data)){
                $this->collect_goods_num_model->change_num($goods_id);
                $this->response(array('ret' => 0, 'msg' => '收藏成功'));
            }else{
                $this->response(array('ret' => 400, 'msg' => '收藏失败'));
            }
        }
        if($action == 'del'){
            if(!$is_collect){
                $this->response(array('ret' => 400, 'msg' => '还没有收藏'));
            }
            if($this->collect_goods_model->where(array('open_id'=>$open_id, 'goods_id'=>$goods_id))->delete()){
                $this->collect_goods_num_model->change_num($goods_id, -1);
                $this->response(array('ret' => 0, 'msg' => '取消成功'));
            }else{
                $this->response(array('ret' => 400, 'msg' => '取消失败'));
            }
        }
    }
    /**
     * 说明：用户反馈
     * 接口：api/user_feedback
     * 方式：get
     * 参数：
     *      type    类型:1表示商品质量，2表示物流发展，3表示售后服务，4表示操作意见，5表示其他
     *      mobile   联系电话(可填)
     *      content  反馈内容
     * 返回：
     *      错误 {ret: >0, msg: xxx }
     *      正确 {ret: 0 , msg: xxx }
     */
    public function user_feedback_get()
    {
        $uid = $this->_user['uid'];
        if(!$uid){
            $this->response(array('ret'=>1,'msg'=>'用户错误'));
        };
        $this->load->model('user_model');
        $userInfo = $this->user_model->find($uid);
        if(!$userInfo){
            $this->response(array('ret'=>401,'msg'=>'用户不存在'));
        }
        $data = array();
        $data['type'] = $this->get('type');
        $data['mobile'] = $this->get('mobile');
        $data['content'] = $this->get('content');
        $data['dt_add'] = time();
        $data['status'] = 0;
        $data['site_id'] = $userInfo['site_id'];
        $data['uid'] = $userInfo['id'];
        $data['reply_content'] = '';
        $data['dt_reply'] = 0;

        if(!in_array($data['type'], array(1,2,3,4,5)) || empty($data['content'])){
            $this->response(array('ret'=>401,'msg'=>'参数错误'));
        }

        $this->load->model('user_feedback_model');
        if($this->user_feedback_model->add($data)){
            $this->response(array('ret'=>0,'msg'=>'感谢您提出的宝贵意见,客服将尽快与您取得联系'));
        }else{
            $this->response(array('ret'=>502,'msg'=>'系统繁忙，请重新提交！'));
        }
    }

    /**
     * 说明：任务列表
     * 接口：api/task_list
     * 方式：get
     * 参数：
     *      open_id
     *      site_id
     * 返回：
     *      错误 {ret: >0, msg: xxx }
     *      正确 {ret: 0 , data: [] }
     */
    public function task_list_get()
    {
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';
        $site_id = $this->get('site_id');

        if(!$open_id || !$site_id){
            $this->response(array('ret' => 400, 'msg' => '参数错误'));
        }

        $member_info = $this->model->member_get(array('open_id'=>$open_id));
        if(!$member_info){
            $this->response(array('ret' => 404, 'msg' => '用户不存在'));
        }
        if($site_id != $member_info['site_id']){
            $this->response(array('ret' => 404, 'msg' => '数据有误'));
        }
        //查询有效的任务
        $this->load->model('task_model');
        $this->load->model('task_receive_model');
        $task_data = $this->task_model->where(array('site_id'=>$site_id,'status'=>1))->find_all();
        //var_dump($task_data);
        $other_data = $data = $data_checkin = $data_share = $data_subscribe = array();
        foreach($task_data as $item){
            $code = $item['code'];
            if(in_array($code, array('checkin','share','reg','buy','sell','subscribe','team','visit'))){
                $fun = $code.'_opt';
                $item['icon'] = image_url("icon/{$code}.png");

                if($code == 'checkin'){
                    $data_checkin = $this->task_receive_model->$fun($item, $open_id);
                }elseif($code == 'share'){
                    $data_share = $this->task_receive_model->$fun($item, $open_id);
                }elseif($code == 'subscribe'){
                    $data_subscribe = $this->task_receive_model->$fun($item, $open_id);
                }else{
                    $other_data[] = $this->task_receive_model->$fun($item, $open_id);
                }
            }
        }

        $size = sizeof($other_data);

        for($i = 1; $i < $size; $i++)
        {
            for($j = $size - 1; $j >= $i; $j--){
                if($other_data[$j]['percentage'] < $other_data[$j-1]['percentage'])
                {
                    $old_item = $other_data[$j];
                    $other_data[$j] = $other_data[$j-1];
                    $other_data[$j-1] = $old_item;
                }
            }
        }
        if($data_share) array_unshift($other_data, $data_share);//将分享排列到最前面
        if($data_checkin) array_unshift($other_data, $data_checkin);//将签到排列到最前面

        if($data_subscribe){
            if($data_subscribe['is_opt'] == 0 ) array_unshift($other_data, $data_subscribe);//将分享排列到最前面
            else $other_data[] = $data_subscribe;
        }
        $data = $other_data;
        foreach($data as &$item){
            $item[percentage] .= '%';//方便前端显示
        }
        $this->response(array('ret' => 0, 'data' => $data));
    }
    /**
     * 说明：每日签到
     * 接口：api/task_checkin
     * 方式：get
     * 参数：
     *      open_id
     *      site_id
     * 返回：
     *      错误 {ret: >0, msg: xxx }
     *      正确 {ret: 0 , msg: xxx }
     */
    public function task_checkin_get()
    {
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';
        $site_id = $this->get('site_id');

        if(!$open_id || !$site_id){
            $this->response(array('ret' => 400, 'msg' => '参数错误'));
        }
        if(phpLock('task_checkin' , $open_id)){
            $this->response(array('ret' => 400, 'msg' => '正在处理'));
        }
        $member_info = $this->model->member_get(array('open_id'=>$open_id));
        if(!$member_info){
            $this->response(array('ret' => 404, 'msg' => '用户不存在'));
        }
        if($site_id != $member_info['site_id']){
            $this->response(array('ret' => 404, 'msg' => '数据有误'));
        }
        //查询有效的任务
        $this->load->model('task_model');
        $this->load->model('task_receive_model');
        $task_data = $this->task_model->where(array('site_id'=>$site_id,'code'=>'checkin','status'=>1))->find();
        if(!$task_data){
            $this->response(array('ret' => 404, 'msg' => '任务关闭状态'));
        }
        $item = $this->task_receive_model->checkin_opt($task_data, $open_id);
        if($item['is_opt'] == 1){
            $this->response(array('ret' => 404, 'msg' => '今天已经签到'));
        }else{
            $add_data = array();
            $add_data['open_id'] = $open_id;
            $add_data['task_id'] = $item['id'];
            $add_data['task_name'] = $item['name'];
            $add_data['level'] = 0;
            $add_data['process'] = 100;
            $add_data['stat'] = 1;
            $add_data['task_gift_type'] = $item['gift_type'];
            $add_data['is_receive'] = 1;
            $add_data['gift'] = $item['config'];
            $add_data['dt_add'] = time();
            if($this->task_receive_model->_add($add_data)){
                //奖励  gold奖励金币 cash奖励现金
                if($item['gift_type']=='gold'){
                    $this->model->add_gold($open_id, $add_data['gift'],$item['name']);
                }
                if($item['gift_type']=='cash'){
                    $this->load->model('shop_model');
                    $this->shop_model->add_amount($open_id, $add_data['gift'],$item['name']);
                }
            }
            phpUnLock('task_checkin' , $open_id);
            $this->response(array('ret' => 0, 'msg' => '签到成功'));
        }
    }
    /**
     * 说明：每日分享
     * 接口：api/task_share
     * 方式：get
     * 参数：
     *      open_id
     *      site_id
     * 返回：
     *      错误 {ret: >0, msg: xxx }
     *      正确 {ret: 0 , msg: xxx }
     */
    public function task_share_get()
    {
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';
        $site_id = $this->get('site_id');

        if(!$open_id || !$site_id){
            $this->response(array('ret' => 400, 'msg' => '参数错误'));
        }
        if(phpLock('task_share' , $open_id)){
            $this->response(array('ret' => 400, 'msg' => '正在处理'));
        }
        $member_info = $this->model->member_get(array('open_id'=>$open_id));
        if(!$member_info){
            $this->response(array('ret' => 404, 'msg' => '用户不存在'));
        }
        if($site_id != $member_info['site_id']){
            $this->response(array('ret' => 404, 'msg' => '数据有误'));
        }
        //查询有效的任务
        $this->load->model('task_model');
        $this->load->model('task_receive_model');
        $task_data = $this->task_model->where(array('site_id'=>$site_id,'code'=>'share','status'=>1))->find();
        if(!$task_data){
            $this->response(array('ret' => 404, 'msg' => '任务关闭状态'));
        }
        $item = $this->task_receive_model->share_opt($task_data, $open_id);
        if($item['is_opt'] == 1){
            $this->response(array('ret' => 404, 'msg' => '今天已经分享'));
        }else{
            $add_data = array();
            $add_data['open_id'] = $open_id;
            $add_data['task_id'] = $item['id'];
            $add_data['task_name'] = $item['name'];
            $add_data['level'] = 0;
            $add_data['process'] = 100;
            $add_data['stat'] = 1;
            $add_data['task_gift_type'] = $item['gift_type'];
            $add_data['is_receive'] = 1;
            $add_data['gift'] = $item['config'];
            $add_data['dt_add'] = time();
            if($this->task_receive_model->_add($add_data)){
                //奖励  gold奖励金币 cash奖励现金
                if($item['gift_type']=='gold'){
                    $this->model->add_gold($open_id, $add_data['gift'],$item['name']);
                }
                if($item['gift_type']=='cash'){
                    $this->load->model('shop_model');
                    $this->shop_model->add_amount($open_id, $add_data['gift'],$item['name']);
                }
            }
            phpUnLock('task_checkin' , $open_id);
            $this->response(array('ret' => 0, 'msg' => '分享成功'));
        }
    }

    /**
     * 说明：删除地址
     * 接口：api/member_address_select
     * add by ysj 2017-07-26
     * 方式：post
     * 参数：
     *      id        地址ID  必填  id如果是-1的话代表是微信的地址
     *      name      收货人地址
     *      zipcode	  邮编
     *      province	省份
     *      city	城市
     *      district	地区
     *      address	详细地址
     *      mobile	手机号码
     * 返回：
     *      正确 {ret: 0 , msg: xxx  }
     *      错误 {ret: >0 , msg: xxx }
     */
    public function member_address_select_post()
    {
        $open_id =  isset($this->_user['open_id']) ? $this->_user['open_id'] : '';
        $site_id =  isset($this->_user['site_id']) ? $this->_user['site_id'] : '';
        $public_id =  isset($this->_user['public_id']) ? $this->_user['public_id'] : '';
        $uid =  isset($this->_user['uid']) ? $this->_user['uid'] : '';
        $this->checkIsRegister();
        $address['id'] =  $this->post('id');
        $address['uid'] =  $uid;
        $address['public_id'] =  $public_id;
        $address['site_id'] =  $site_id;
        $address['open_id'] =  $open_id;
        $address['name'] =  $this->post('name');
        $address['zipcode'] =  $this->post('zipcode');
        $address['province'] =  $this->post('province');
        $address['city'] =  $this->post('city');
        $address['district'] =  $this->post('district');
        $address['address'] =  $this->post('address');
        $address['mobile'] =  $this->post('mobile');

        $this->load->library('kr_redis');
        $this->kr_redis->select(15);
        $redisKey='address:'.$uid.":";
        $this->kr_redis->hMSet($redisKey,$address);
        $this->kr_redis->expire($redisKey,600);

        $response['ret'] = 0;
        $response['msg'] = '保存成功';
        $this->response($response);
    }


    /**
     * 说明：获取用户消费金额，当日成交，累计成交
     * 接口：api/member_trade
     * add by ysj 2017-08-11
     * 方式：post
     * 参数：
     *      today_trade   今日成交
     *      total_trade   累计成交
     *      pay_amount	  消费金额
     * 返回：
     *      正确 {ret: 0 , msg: xxx  }
     */
    public function member_trade_get()
    {
        $uid =  isset($this->_user['uid']) ? $this->_user['uid'] : '';
        if(empty($uid)){
            $this->response(array('ret' => 400, 'msg' => '参数错误'));
        }

        $date = date('Y-m-d',time());
        $this->load->model('shop_tj_fencheng_model');
        $this->shop_tj_fencheng_model->db->select_sum('amount');
        $tj_fencheng_today=$this->shop_tj_fencheng_model->where('dt_tj',$date)->where('uid',$uid)->find();
        $member['today_trade'] = empty($tj_fencheng_today['amount'])?0:$tj_fencheng_today['amount'];

        $this->shop_tj_fencheng_model->db->select_sum('amount');
        $tj_fencheng_total=$this->shop_tj_fencheng_model->where('uid',$uid)->find();
        $member['total_trade'] = empty($tj_fencheng_total['amount'])?0:$tj_fencheng_total['amount'];

        $this->load->model('order_model');
        $this->order_model->db->select_sum('pay_amount');
        $order_total=$this->order_model->where('uid',$uid)->where('paid',1)->find();
        $member['pay_amount'] = empty($order_total['pay_amount'])?0:$order_total['pay_amount'];


        $response['ret'] = 0;
        $response['msg'] = '获取成功';
        $response['data'] =$member;
        $this->response($response);
    }
}