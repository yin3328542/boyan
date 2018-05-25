<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-10-27
 * Time: 下午3:29
 */

//page
$route['siter/member'] = 'member/welcome/index';

//API
$route['api/member/signin']  = 'member/api/auth/signin';
$route['api/refresh_openid']  = 'member/api/member/refresh_openid';
$route['api/members'] = 'member/api/member/members';
$route['api/gold_logs'] = 'member/api/member/gold_logs';
$route['api/member_gold'] = 'member/api/member/member_gold';

$route['api/user/user_feedback'] = 'member/api/member/user_feedback';

$route['api/my_site'] = 'member/api/member/my_site';

$route['api/task_list'] = 'member/api/member/task_list';
$route['api/task_checkin'] = 'member/api/member/task_checkin';
$route['api/task_share'] = 'member/api/member/task_share';

$route['api/goods_history'] = 'member/api/member/goods_history';

$route['api/member_collect_shop'] = 'member/api/member/member_collect_shop';
$route['api/member_collect_shop_list'] = 'member/api/member/member_collect_shop_list';

$route['api/collect_goods_list'] = 'member/api/member/collect_goods_list';
$route['api/collect_goods'] = 'member/api/member/collect_goods';

$route['api/member_address'] = 'member/api/member/member_address';
$route['api/member_address_list'] = 'member/api/member/member_address_list';
$route['api/member_address_save'] = 'member/api/member/member_address_save';
$route['api/member_address_del'] = 'member/api/member/member_address_del';
$route['api/member_set_first_address'] = 'member/api/member/member_set_first_address';

$route['api/refresh_member']  = 'member/api/member/refresh_member';

$route['api/wx_transfer'] = 'member/api/member/wx_transfer';

//新增将用户选择的地址保存起来 by ysj 20170726
$route['api/member_address_select'] = 'member/api/member/member_address_select';

$route['api/member_trade'] = 'member/api/member/member_trade';

$route['api/member'] = 'member/api/member/member';//单个用户操作

