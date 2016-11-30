<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
// 模块信息配置
return array(
    // 模块信息
    'info' => array(
        'name'        => 'Sms',
        'title'       => 'SMS',
        'icon'        => 'fa fa-newspaper-o',
        'icon_color'  => '#9933FF',
        'description' => '消息模块',
        'developer'   => 'bigfoot',
        'website'     => '',
        'version'     => '0.1.0',
        'dependences' => array(
            'Admin'   => '0.1.0',
        )
    ),

    // 用户中心导航
    'user_nav' => array(
        'title' => array(
            'center' => '消息管理',
        ),
        'center' => array(
            '0' => array(
                'title' => '发送私信',
                'icon'  => 'fa fa-edit',
                'url'   => 'User/Message/send',
                'color' => '#F68A3A',
            ),
            '1' => array(
                'title' => '消息中心',
                'icon'  => 'fa fa-envelope-o',
                'url'   => 'User/Message/index',
                'badge' => array('User/Message', 'newMessageCount'),
                'badge_class' => 'badge-danger',
                'color' => '#80C243',
            ),
            '2' => array(
                'title' => '查看公告',
                'icon'  => 'fa fa-list',
                'url'   => 'Sms/Notice/index',
                'color' => '#398CD2',
            ),
            
        ),
        
    ),

    // 模块配置
    'config' => array(
        'need_check' => array(
            'title'   => '1',
            'type'    => 'radio',
            'options' => array(
                '1'   => '需要',
                '0'   => '不需要',
            ),
            'value'   => '0',
        ),
        
    ),

    // 后台菜单及权限节点配置
    'admin_menu' => array(
        '1' => array(
            'id'    => '1',
            'pid'   => '0',
            'title' => '消息',
            'icon'  => 'fa fa-newspaper-o',
        ),        
        '2' => array(
            'pid'   => '1',
            'title' => '内容管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '3' => array(
            'pid'   => '2',
            'title' => '通知公告',
            'icon'  => 'fa fa-bullhorn',
            'url'   => 'Sms/Notice/index',
        ),
        '4' => array(
            'pid'   => '3',
            'title' => '新增',
            'url'   => 'Sms/Notice/add',
        ),
        '5' => array(
            'pid'   => '3',
            'title' => '编辑',
            'url'   => 'Sms/Notice/edit',
        ),
        '6' => array(
            'pid'   => '3',
            'title' => '设置状态',
            'url'   => 'Sms/Notice/setStatus',
        ),
        '7' => array(
            'pid'   => '2',
            'title' => '文化宣传',
            'icon'  => 'fa fa-bullhorn',
            'url'   => 'Sms/Advice/index',
        ),
        '8' => array(
            'pid'   => '3',
            'title' => '新增',
            'url'   => 'Sms/Advice/add',
        ),
        '9' => array(
            'pid'   => '3',
            'title' => '编辑',
            'url'   => 'Sms/Advice/edit',
        ),
        '10' => array(
            'pid'   => '3',
            'title' => '设置状态',
            'url'   => 'Sms/Advice/setStatus',
        ),
        
    )
);
