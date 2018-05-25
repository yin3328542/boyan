<?php

/**
 * Created by PhpStorm.
 * User: 111
 * Date: 14-4-17
 * Time: 下午4:14
 */

//api

$route['api/admin/newses'] = 'news/api/news/newses';
$route['api/admin/news'] = 'news/api/news/news';
$route['api/admin/news_status'] = 'news/api/news/news_status';
$route['api/admin/news/(:num)'] = 'news/api/news/news/id/$1';
$route['api/admin/news/img'] = 'news/api/news/img';
$route['api/admin/news/img/(:num)'] = 'news/api/news/img/id/$1';

$route['admin/news'] = 'news/welcome/index';
$route['admin/news/add'] = 'news/welcome/add';
$route['admin/news/edit/(:num)'] = 'news/welcome/edit/$1';


$route['api/wap_news'] = 'news/api/news/wap_news';
$route['api/news_category'] = 'news/api/news/news_category';
$route['api/wap_new'] = 'news/api/news/wap_new';