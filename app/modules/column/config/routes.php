<?php

/**
 * Created by PhpStorm.
 * User: mxb
 * Date: 15-2-12
 * Time: 上午9:14
 */

//api

$route['api/columns'] = 'column/api/column/columns';
$route['api/column'] = 'column/api/column/column';
$route['api/del_column']  = 'column/api/column/del_column';
$route['api/listorder']  = 'column/api/column/edit_listorder';
$route['api/column/(:num)'] = 'column/api/column/column/id/$1';
$route['api/column/img/(:num)'] = 'column/api/column/img/id/$1';

$route['admin/column'] = 'column/column/index';
$route['admin/column/add/(:num)'] = 'column/column/add/$1';
$route['admin/column/edit/(:num)'] = 'column/column/edit/$1';

$route['api/admin/columns'] = 'column/api/column/admin_columns';


