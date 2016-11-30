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
        'name'        => 'Shop',
        'title'       => 'SHOP',
        'icon'        => 'fa fa-newspaper-o',
        'icon_color'  => '#9933FF',
        'description' => '购物模块',
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
            'center' => '购物中心',
        ),
        'center' => array(
            '0' => array(
                'title' => '主动消费购物',
                'icon'  => 'fa fa-shopping-cart',
                'url'   => 'Shop/Buy/coinbuy',
                'color' => '#F68A3A',
            ),
            '1' => array(
                'title' => '奖励积分购物',
                'icon'  => 'fa  fa-shopping-cart',
                'url'   => 'Shop/Buy/rewardScoreBuy',
                'color' => '#398CD2',
            ),
            '2' => array(
                'title' => '重消积分购物',
                'icon'  => 'fa  fa-shopping-cart',
                'url'   => 'Shop/Buy/repeatScoreBuy',
                'color' => '#398CD2',
            ),
            '3' => array(
                'title' => '购买订单查看',
                'icon'  => 'fa  fa-shopping-cart',
                'url'   => 'Shop/Order/my',
                'color' => '#398CD2',
            ),
            '4' => array(
                'title' => '销售订单查看',
                'icon'  => 'fa  fa-shopping-cart',
                'url'   => 'Shop/Order/mySale',
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
            'title' => '购物中心',
            'icon'  => 'fa fa-newspaper-o',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '购物管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        // '3' => array(
            // 'pid'   => '1',
            // 'title' => '购物统计',
            // 'icon'  => 'fa fa-folder-open-o',
        // ),
        '4' => array(
            'pid'   => '2',
            'title' => '商品列表',
            'icon'  => 'fa fa-area-chart',
            'url'   => 'Shop/Index/index',
        ),
        '5' => array(
            'pid'   => '4',
            'title' => '新增',
            'url'   => 'Shop/Index/add',
        ),
        '6' => array(
            'pid'   => '4',
            'title' => '编辑',
            'url'   => 'Shop/Index/edit',
        ),
        '7' => array(
            'pid'   => '4',
            'title' => '设置状态',
            'url'   => 'Shop/Index/setStatus',
        ),
        '8' => array(
            'pid'   => '2',
            'title' => '回收站',
            'icon'  => 'fa fa-area-chart',
            'url'   => 'Shop/Index/recycle',
        ),
        '9' => array(
            'pid'   => '8',
            'title' => '设置状态',
            'url'   => 'Shop/Index/setStatus',
        ),
        '10' => array(
            'pid'   => '1',
            'title' => '订单管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '11' => array(
            'pid'   => '10',
            'title' => '订单列表',
            'icon'  => 'fa fa-area-chart',
            'url'   => 'Shop/Order/index',
        ),      
        '12' => array(
            'pid'   => '11',
            'title' => '详情',
            'url'   => 'Shop/order/detail',
        ),
        '13' => array(
            'pid'   => '11',
            'title' => '发货',
            'url'   => 'Shop/order/delivery',
        ),
        '14' => array(
            'pid'   => '11',
            'title' => '设置状态',
            'url'   => 'Shop/Index/setStatus',
        ),
        '15' => array(
            'pid'   => '1',
            'title' => '购物统计',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '16' => array(
            'pid'   => '15',
            'title' => '订单统计',
            'icon'  => 'fa fa-area-chart',
            'url'   => 'Shop/Statistics/order',
        ),
        '17' => array(
            'pid'   => '15',
            'title' => '销售统计',
            'icon'  => 'fa fa-area-chart',
            'url'   => 'Shop/Statistics/value',
        ),
        
    )
);
