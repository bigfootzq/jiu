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
        'name'        => 'Service',
        'title'       => 'SERVICE',
        'icon'        => 'fa fa-newspaper-o',
        'icon_color'  => '#9933FF',
        'description' => '服务中心模块',
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
            'center' => '服务中心',
        ),
        'center' => array(
            '0' => array(
                'title' => '填写汇款通知',
                'icon'  => 'fa fa-edit',
                'url'   => 'Service/index/remittance_advice',
                'color' => '#F68A3A',
            ),
            '1' => array(
                'title' => '注册新会员',
                'icon'  => 'fa fa-registered',
                'url'   => 'User/User/register4',
                'color' => '#398CD2',
            ),
            '2' => array(
                'title' => '我的报单',
                'icon'  => 'fa fa-table',
                'url'   => 'Service/index/declaration',
                'color' => '#398CD2',
            ),
        ),
        
    ),

    // 模块配置
    'config' => array(
        'status' => array(
            'title'   => '是否开启',
            'type'    => 'radio',
            'options' => array(
                '1' => '开启',
                '0' => '关闭',
            ),
            'value' => '1',
        ),
        'reg_toggle' => array(
            'title'   => '注册开关',
            'type'    => 'radio',
            'options' => array(
                '1'   => '开启',
                '0'   => '关闭',
            ),
            'value'   => '1',
        ),
        'allow_reg_type' => array(
            'title'   => '允许注册类型',
            'type'    =>'checkbox',
            'options' => array(
                'username' => '用户名注册',
                'email'    => '邮箱注册',
                'mobile'   => '手机注册',
            ),
            'value'=> array(
                '0' => 'username',
            ),
        ),
        'deny_username' => array(
            'title'   => '禁止注册的用户名',
            'type'    =>'textarea',
            'value'   => '',
        ),
        'user_protocol' => array(
            'title'   => '用户协议',
            'type'    =>'kindeditor',
            'value'=>'请在“后台——用户——用户设置”中设置',
        ),
        'behavior' => array(
            'title'   => '行为扩展',
            'type'   =>'checkbox',
            'options'=> array(
                'User' => 'User',
            ),
            'value'  => array(
                '0'  => 'User',
            ),
        ),
    ),

    // 后台菜单及权限节点配置
    'admin_menu' => array(
        '1' => array(
            'pid'   => '0',
            'title' => '分销',
            'icon'  => 'fa fa-user',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '分销管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        // '3' => array(
            // 'pid'   => '2',
            // 'title' => '模块设置',
            // 'icon'  => 'fa fa-wrench',
            // 'url'   => 'Service/Index/module_config',
        // ),
        '4' => array(
            'pid'   => '2',
            'title' => '分销体系配置',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Service/Index/marketing_config',
        ),
        '5' => array(
            'pid'   => '2',
            'title' => '分销清算模拟',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Service/Index/check',
        ),
        
        '6' => array(
            'pid'   => '4',
            'title' => '新增',
            'url'   => 'Service/Index/add',
        ),
        '7' => array(
            'pid'   => '4',
            'title' => '编辑',
            'url'   => 'Service/Index/edit',
        ),
        '8' => array(
            'pid'   => '4',
            'title' => '设置状态',
            'url'   => 'Service/Index/setStatus',
        ),
        
    )
);
