<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 应用资源列表-用于权限控制
 * Created by PhpStorm.
 * User: river
 * Date: 14-11-25
 * Time: 上午11:57
 */
$config['resource'] = array(
    /*------------------------------------------------------
        |   后台权限
        ------------------------------------------------------*/
    'admin' => array(
        //修改密码
        'password' => array(
            'name'     => '修改密码',
            'resource' => 'admin/welcome/admin_password',
            'api'      => array(),
            'parent'   => 'self',
        ),

        //文章管理
        'news' => array(
            'name'     => '文章管理',
            'resource' => 'news/welcome/index',
            'api'      => array(),
            'parent'   => 'self',
        ),
        'news_list' => array(
            'name'     => '文章列表',
            'resource' => 'news/welcome/index',
            'api'      => array(),
            'parent'   => 'news',
        ),
        'service_list' => array(
            'name'     => '服务政策',
            'resource' => 'service/service/service_list',
            'api'      => array(),
            'parent'   => 'news',
        ),
        'banner' => array(
            'name'     => '首页幻灯片',
            'resource' => 'banner/banner/index',
            'api'      => array(),
            'parent'   => 'news',
        ),
        'category' => array(
            'name'     => '分类管理',
            'resource' => 'admin/admin_category/index',
            'api'      => array(),
            'parent'   => 'news',
        ),

        //图文管理
        'imgtext'     => array(
            'name'     => '图文管理',
            'resource' => 'imgtext/imgtext/index',
            'api'      => array(),
            'parent'   => 'self',
        ),
        'imgtext_list' => array(
            'name'     => '图文列表',
            'resource' => 'imgtext/imgtext/index',
            'api'      => array(),
            'parent'   => 'imgtext',
        ),

        //服务商管理
        'agency'     => array(
            'name'     => '服务商管理',
            'resource' => 'agency/welcome/index',
            'api'      => array(),
            'parent'   => 'self',
        ),
        'activity_list' => array(
            'name'     => '服务商列表',
            'resource' => 'agency/welcome/index',
            'api'      => array(),
            'parent'   => 'agency',
        ),

        //品牌商管理
        'brands' => array(
            'name'     => '品牌商管理',
            'resource' => 'agency/admin/welcome/index',
            'api'      => array('agency/api/agency/agency_post','agency/api/agency/agency_put' ,
                'agency/api/agency/agency_status_get'),
            'parent'   => 'self',
        ),
        'brands_list' => array(
            'name'     => '品牌商列表',
            'resource' => 'brands/welcome/index',
            'api'      => array(),
            'parent'   => 'brands',
        ),

        //战队管理
        'team' => array(
            'name'     => '战队管理',
            'resource' => 'team/welcome/index',
            'api'      => array(),
            'parent'   => 'self',
        ),
        'team_list' => array(
            'name'     => '战队列表',
            'resource' => 'team/welcome/index',
            'api'      => array(),
            'parent'   => 'team',
        ),

        //会员管理
        'member' => array(
            'name'     => '会员中心',
            'resource' => 'member/welcome/index',
            'api'      => array(),
            'parent'   => 'self',
        ),
        'member_list' => array(
            'name'     => '会员列表',
            'resource' => 'member/welcome/index',
            'api'      => array(),
            'parent'   => 'member',
        ),

        //系统设置
        'system' => array(
            'name'     => '系统设置',
            'resource' => 'column/column/index',
            'api'      => array(),
            'parent'   => 'self',
        ),
        'column_list' => array(
            'name'     => '前台导航',
            'resource' => 'column/column/index',
            'api'      => array(),
            'parent'   => 'system',
        ),
        'variable' => array(
            'name'     => '基本参数',
            'resource' => 'admin/variable/admin_variable',
            'api'      => array(),
            'parent'   => 'system',
        ),
        'menu' => array(
            'name'     => '后台菜单',
            'resource' => 'admin/menu/admin_menu',
            'api'      => array(),
            'parent'   => 'system',
        ),
        'admin_list' => array(
            'name'     => '账号管理',
            'resource' => 'admin/admin/index',
            'api'      => array(),
            'parent'   => 'system',
        ),
        'role' => array(
            'name'     => '角色管理',
            'resource' => 'admin/admin_role/index',
            'api'      => array(),
            'parent'   => 'system',
        )
    )
);