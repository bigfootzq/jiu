<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Finance\Admin;
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
    
    public function reward() {
        //计算统计图日期
        $today = strtotime(date('Y-m-d', time())); //今天
        $start_date = I('get.start_date') ? strtotime(I('get.start_date')) : $today-14*86400;
        $end_date   = I('get.end_date') ? (strtotime(I('get.end_date'))+1) : $today+86400;
        $count_day  = ($end_date-$start_date)/86400; //查询最近n天
        $user_object = D('Finance/Reward');
        for($i = 0; $i < $count_day; $i++){
            $day = $start_date + $i*86400; //第n天日期
            $day_after = $start_date + ($i+1)*86400; //第n+1天日期
            $map['reward_time'] = array(
                array('egt', $day),
                array('lt', $day_after)
            );
            $map['reward_type']  = array('neq',6);
            $reward_create_date[] = date('m月d日', $day);
            $reward_date_value[] = (int)$user_object->where($map)->sum('reward_value');
        }

        $this->assign('start_date', date('Y-m-d', $start_date));
        $this->assign('end_date', date('Y-m-d', $end_date-1));
        $this->assign('count_day', $count_day);
        $this->assign('reward_create_date', json_encode($reward_create_date));
        $this->assign('reward_date_value', json_encode($reward_date_value));
        $this->assign('meta_title', "奖励发放统计");
        $this->display();
    }
    
    
}
