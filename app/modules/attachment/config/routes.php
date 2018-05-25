<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-16
 * Time: 上午11:55
 */

$route['api/attachment/upload']  = 'attachment/api/attachment/upload';
$route['api/attachment/editor_upload']  = 'attachment/api/attachment/editor_upload';
$route['api/attachment/ueditor_upload']  = 'attachment/api/attachment/ueditor_upload';
$route['api/attachment/ueditor_list']  = 'attachment/api/attachment/ueditor_upload';
$route['api/attachment/(:num)']  = 'attachment/api/attachment/attachment/id/$1';

$route['api/upload_excel'] = 'attachment/api/attachment/upload_excel';

$route['siter/uploader']  = 'attachment/uploader/index';
