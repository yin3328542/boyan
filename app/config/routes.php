<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_module'] = 'home';
$route['default_controller'] = "welcome";
$route['404_override'] = '';

$http_host = explode('.', $_SERVER['HTTP_HOST']);

if ($http_host[0] == 'baoyan') {     //平台超级管理员：限制访问ip  http://pcadmin.121dian.com/
    $route['default_module'] = 'admin';
    $route['default_controller'] = 'welcome';
}

//api
/*$route['api/add_goods'] = 'api/checkout/add_goods';
$route['api/cart_is_exist_goods'] = 'api/checkout/cart_is_exist_goods';
$route['api/checkout_info'] = 'api/checkout/checkout_info';
$route['api/order'] = 'api/checkout/order';

$route['api/order_opt'] = 'api/order/order_opt';

$route['clear_cache'] = 'api/clear_cache/index';
$route['article/(:num)'] = 'article/index/$1';
$route['member_check/(:num)'] = 'member_check/index/$1';*/

/* End of file routes.php */
/* Location: ./application/config/routes.php */
