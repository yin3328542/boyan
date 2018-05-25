<?php

/**
 * Created by PhpStorm.
 * User: mxb
 * Date: 15-2-12
 * Time: 上午9:14
 */

$route['admin/adsense'] = 'adsense/adsense/index';

$route['admin/adsense/edit/(:num)'] = 'adsense/adsense/edit/$1';

$route['api/admin/adsenses'] = 'adsense/api/adsense/adsenses';
$route['api/admin/adsense'] = 'adsense/api/adsense/adsense';
$route['api/admin/adsense_status'] = 'adsense/api/adsense/adsense_status';
$route['api/admin/adsense/order'] = 'adsense/api/adsense/order';
$route['api/admin/adsense/(:num)'] = 'adsense/api/adsense/adsense/id/$1';
$route['api/admin/adsense/img/(:num)'] = 'adsense/api/adsense/img/id/$1';
