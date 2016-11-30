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
        'name'        => 'EShop',
        'title'       => 'ESHOP',
        'icon'        => 'fa fa-newspaper-o',
        'icon_color'  => '#9933FF',
        'description' => '购物商城模块',
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
            'center' => '购物商城',
        ),
        'center' => array(
            '0' => array(
                'title' => '购物商城',
                'icon'  => 'fa fa-shopping-cart',
                'url'   => 'User/Blank/index',
                'color' => '#F68A3A',
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
            'title' => '购物商城',
            'icon'  => 'fa fa-shopping-cart',
        ),
        
    )
);
