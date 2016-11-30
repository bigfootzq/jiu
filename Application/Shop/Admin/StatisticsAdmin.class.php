<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace shop\Admin;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;
/**
 * 默认控制器
 * @author jry <598821125@qq.com>
 */
class StatisticsAdmin extends AdminController {
    /**
     * 默认方法
     * @author bigfoot
     */
    public function index(){
        
    }
    
    public function order() {
        //计算统计图日期
        $today = strtotime(date('Y-m-d', time())); //今天
        $start_date = I('get.start_date') ? strtotime(I('get.start_date')) : $today-14*86400;
        $end_date   = I('get.end_date') ? (strtotime(I('get.end_date'))+1) : $today+86400;
        $count_day  = ($end_date-$start_date)/86400; //查询最近n天
        $shop_username = I('get.shop_username');
        if ( !empty($shop_username) ){
            $map['shop_id'] = get_user_id( $shop_username );
        }
        $user_object = D('Shop/Order');
        for($i = 0; $i < $count_day; $i++){
            $day = $start_date + $i*86400; //第n天日期
            $day_after = $start_date + ($i+1)*86400; //第n+1天日期
            $map['create_time'] = array(
                array('egt', $day),
                array('lt', $day_after)
            );
            $order_create_date[] = date('m月d日', $day);
            $order_create_count[] = (int)$user_object->where($map)->count();
        }

        $this->assign('start_date', date('Y-m-d', $start_date));
        $this->assign('end_date', date('Y-m-d', $end_date-1));
        $this->assign('count_day', $count_day);
        $this->assign('shop_username', $shop_username);
        $this->assign('order_create_date', json_encode($order_create_date));
        $this->assign('order_create_count', json_encode($order_create_count));
        $this->assign('meta_title', "订单统计");
        $this->display();
    }
    
    public function value() {
        //计算统计图日期
        $today = strtotime(date('Y-m-d', time())); //今天
        $start_date = I('get.start_date') ? strtotime(I('get.start_date')) : $today-14*86400;
        $end_date   = I('get.end_date') ? (strtotime(I('get.end_date'))+1) : $today+86400;
        $count_day  = ($end_date-$start_date)/86400; //查询最近n天
        $shop_username = I('get.shop_username');
        if ( !empty($shop_username) ){
            $map['shop_id'] = get_user_id( $shop_username );
        }
        $user_object = D('Shop/Order');
        for($i = 0; $i < $count_day; $i++){
            $day = $start_date + $i*86400; //第n天日期
            $day_after = $start_date + ($i+1)*86400; //第n+1天日期
            $map['create_time'] = array(
                array('egt', $day),
                array('lt', $day_after)
            );
            
            $order_create_date[] = date('m月d日', $day);
            $order_date_value[] = (int)$user_object->where($map)->sum('value');
        }
        // dump($map);
        // dump($order_date_value);
        $this->assign('start_date', date('Y-m-d', $start_date));
        $this->assign('end_date', date('Y-m-d', $end_date-1));
        $this->assign('count_day', $count_day);
        $this->assign('shop_username', $shop_username);
        $this->assign('order_create_date', json_encode($order_create_date));
        $this->assign('order_date_value', json_encode($order_date_value));
        $this->assign('meta_title', "订单金额统计");
        $this->display();
    }
}
