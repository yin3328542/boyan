<?php

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-16
 * Time: 上午11:55
 */

$route['api/admin/signin']  = 'admin/api/auth/signin';
$route['api/admin']  = 'admin/api/admin/admin';
$route['api/admin/(:num)'] = 'admin/api/admin/admin/id/$1';

$route['admin/admin_password']  = 'admin/welcome/admin_password';
$route['api/admin_password']  = 'admin/api/admin/password';

$route['admin/message']  = 'admin/admin/admin_message';
$route['admin/message_list']  = 'admin/admin/admin_message_list';
$route['admin/category'] = 'admin/admin_category/index';

$route['api/settings']  = 'admin/api/setting/settings';
$route['api/setting']  = 'admin/api/setting/setting';

$route['admin/menu']  = 'admin/menu/admin_menu';

$route['api/menu_list']  = 'admin/api/admin_menu/menu_list';
$route['api/add_menu']  = 'admin/api/admin_menu/add_menu';
$route['api/edit_menu']  = 'admin/api/admin_menu/edit_menu';
$route['api/del_menu']  = 'admin/api/admin_menu/del_menu';
$route['api/edit_listorder']  = 'admin/api/admin_menu/edit_listorder';

$route['admin/variable']  = 'admin/variable/admin_variable';

$route['api/admin_variable']  = 'admin/api/admin_variable/admin_variable';
$route['api/add_variable']  = 'admin/api/admin_variable/add_variable';
$route['api/edit_variable']  = 'admin/api/admin_variable/edit_variable';

$route['api/categorys'] = 'admin/api/category/categorys';
$route['api/category'] = 'admin/api/category/category';
$route['api/category/(:num)'] = 'admin/api/category/category/id/$1';

$route['api/del_category'] = 'admin/api/category/del_category';

//账号角色管理
$route['admin/admin'] = 'admin/admin/index';
$route['admin/role'] = 'admin/admin_role/index';
$route['api/admins'] = 'admin/api/admin/admins';
$route['api/admin/role'] = 'admin/api/admin_role/role';
$route['api/admin/roles'] = 'admin/api/admin_role/roles';
$route['api/admin/resource'] = 'admin/api/admin_role/resource';
$route['admin/join'] = 'admin/join/index';
$route['api/admin/contact'] = 'admin/api/contact/contact';
$route['admin/company'] = 'admin/admin/company';
$route['api/admin/company'] = 'admin/api/admin/company';

//服务类目
$route['admin/service'] = 'admin/service/index';
$route['admin/service/add'] = 'admin/service/add';
$route['admin/service/edit/(:num)'] = 'admin/service/edit/$1';

$route['api/admin/services'] = 'admin/api/service/services';
$route['api/admin/service'] = 'admin/api/service/service';
$route['api/admin/service/(:num)'] = 'admin/api/service/service/id/$1';
$route['api/admin/service/img/(:num)'] = 'admin/api/service/img/id/$1';

