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
        'name'        => 'Finance',
        'title'       => 'FINANCE',
        'icon'        => 'fa fa-newspaper-o',
        'icon_color'  => '#9933FF',
        'description' => '财务模块',
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
            'center' => '财务管理',
        ),
        'center' => array(
            '0' => array(
                'title' => '财务流水',
                'icon'  => 'fa fa-rmb',
                'url'   => 'Finance/Index/myAccount',
                'color' => '#F68A3A',
            ),
            '1' => array(
                'title' => '奖金查询',
                'icon'  => 'fa fa-rmb',
                'url'   => 'Finance/Index/myLiquidate',
                'color' => '#398CD2',
            ),
            '2' => array(
                'title' => '奖金明细',
                'icon'  => 'fa fa-rmb',
                'url'   => 'Finance/Index/myReward',
                'color' => '#398CD2',
            ),
            '3' => array(
                'title' => '转换',
                'icon'  => 'fa fa-rmb',
                'url'   => 'Finance/Index/transfer',
                'color' => '#398CD2',
            ),
            '4' => array(
                'title' => '提现',
                'icon'  => 'fa fa-rmb',
                'url'   => 'Finance/Index/getMoney',
                'color' => '#398CD2',
            ),
            '5' => array(
                'title' => '中心店转账',
                'icon'  => 'fa fa-rmb',
                'url'   => 'Finance/Index/coinTransfer',
                'color' => '#398CD2',
            ),
            '6' => array(
                'title' => '转账',
                'icon'  => 'fa fa-rmb',
                'url'   => 'Finance/Index/rewardTransfer',
                'color' => '#398CD2',
            ),
            '7' => array(
                'title' => '消费奖励积分转账',
                'icon'  => 'fa fa-rmb',
                'url'   => 'Finance/Index/rewardScoreTransfer',
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
            'title' => '财务',
            'icon'  => 'fa fa-newspaper-o',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '财务管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '3' => array(
            'pid'   => '1',
            'title' => '财务报表',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '4' => array(
            'pid'   => '2',
            'title' => '用户财务列表',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Caiwu/index',
        ),
        '5' => array(
            'pid'   => '2',
            'title' => '汇款信息列表',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Index/remit',
        ),
        '6' => array(
            'pid'   => '1',
            'title' => '财务统计',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '7' => array(
            'pid'   => '4',
            'title' => '编辑',
            'url'   => 'Finance/Caiwu/edit',
        ),
        '8' => array(
            'pid'   => '4',
            'title' => '设置状态',
            'url'   => 'Finance/Caiwu/setStatus',
        ),
        '9' => array(
            'pid'   => '5',
            'title' => '删除',
            'url'   => 'Finance/Index/setStatus',
        ),
        '10' => array(
            'pid'   => '5',
            'title' => '处理',
            'url'   => 'Finance/Index/setStatus',
        ),
        '11' => array(
            'pid'   => '2',
            'title' => '提现申请列表',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Index/getmoney',
        ),
        '12' => array(
            'pid'   => '11',
            'title' => '删除',
            'url'   => 'Finance/Index/setStatus',
        ),
        '13' => array(
            'pid'   => '11',
            'title' => '处理',
            'url'   => 'Finance/Index/setStatus',
        ),
        '14' => array(
            'pid'   => '6',
            'title' => '奖金发放统计',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Statistics/reward',
        ),
        '15' => array(
            'pid'   => '2',
            'title' => '电子币管理',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Caiwu/coin',
        ),
        '16' => array(
            'pid'   => '15',
            'title' => '修改',
            'url'   => 'Finance/Caiwu/coinedit',
        ),
        '17' => array(
            'pid'   => '2',
            'title' => '店铺管理',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Caiwu/shop',
        ),
        '18' => array(
            'pid'   => '17',
            'title' => '修改',
            'url'   => 'Finance/Caiwu/shopedit',
        ),
        '19' => array(
            'pid'   => '3',
            'title' => '奖励发放列表',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Reward/index',
        ),
        '20' => array(
            'pid'   => '3',
            'title' => '转账信息列表',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Transfer/index',
        ),
        '21' => array(
            'pid'   => '3',
            'title' => '清算信息列表',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Check/index',
        ),
        '22' => array(
            'pid'   => '3',
            'title' => '导出清算报表',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Finance/Check/export',
        ),
    )
);
