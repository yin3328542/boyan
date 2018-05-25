<?php

/**
 * Created by PhpStorm.
 * User: mxb
 * Date: 15-2-12
 * Time: 上午9:14
 */

$route['admin/banner'] = 'banner/banner/index';

$route['admin/banner/add'] = 'banner/banner/addee/$1';
$route['admin/banner/add/(:any)'] = 'banner/banner/add/$1';
$route['admin/banner/edit/(:num)'] = 'banner/banner/edit/$1';
$route['admin/banner/(:any)'] = 'banner/banner/index/$1';

$route['api/banners'] = 'banner/api/banner/banners';
$route['api/banner'] = 'banner/api/banner/banner';
$route['api/banner_status'] = 'banner/api/banner/banner_status';
$route['api/del_banner']  = 'banner/api/banner/del_banner';
$route['api/order'] = 'banner/api/banner/order';
$route['api/banner/(:num)'] = 'banner/api/banner/banner/id/$1';
$route['api/banner/img/(:num)'] = 'banner/api/banner/img/id/$1';
