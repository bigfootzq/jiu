<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Shop\Model;
use Think\Model;
/**
 * 文档类型
 * @author jry <598821125@qq.com>
 */
class IndexModel extends Model {
    /**
     * 模块名称
     * @author jry <598821125@qq.com>
     */
    public $moduleName = 'Shop';

    /**
     * 数据库真实表名
     * 一般为了数据库的整洁，同时又不影响Model和Controller的名称
     * 我们约定每个模块的数据表都加上相同的前缀，比如微信模块用weixin作为数据表前缀
     * @author jry <598821125@qq.com>
     */
    protected $tableName = 'shop_goods';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(

    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(

    );
    
    /**
     * 根据金额 返消费奖励积分，自己22%，安置人20%
     * @param id  用户id
     * @param value 金额
     * @author bigfoot
     */
    public function addRewardScore($id,$value){
        if ( isset($id) && isset($value) ){
            //读取分销体系配置
            $marketing = C('marketing.1');
            $reward_score1 = $marketing['REWARD_SCORE1']/100;//本人返利消费奖励积分比例0.22
            $reward_score2 = $marketing['REWARD_SCORE2']/100;//接点人返利比例0.20
            //查出安置人
            $fid = D('User/Tree')->where("id = %d",$id)->getField('fid');
            // dump($position);
            if(!empty($fid)){
                $str = "(".$fid.",".round($value*$reward_score2/2,2)."),";
            }
            $reward_score = $str."(".$id.",".round($value*$reward_score1/2,2).")";
            
            // dump($reward_score);
            $sql = "insert into jiu_user_info (uid,reward_score) values".$reward_score."on duplicate key update reward_score = reward_score + values(reward_score);";
            $result = M()->execute($sql);
            // echo M()->_sql;
            // dump ($result);
            if($result !== false){
                // 写转账记录
                $data['uid'] = $id;
                $data['from_uid'] = $id;
                $data['source'] = "消费奖励积分入账，来自".get_user_name($id)."主动消费购物";
                $data['transfer_type'] = 4;
                $data['transfer_value'] = round($value*$reward_score1/2,2);
                $data['transfer_time'] = time();
                $data['detail'] = "消费奖励积分入账，来自".get_user_name($id)."主动消费购物";
                $data['status'] = 1;
                //安置人同样写一条
                $data2['uid'] = $fid;
                $data2['from_uid'] = $id;
                $data2['source'] = "消费奖励积分入账，来自".get_user_name($id)."主动消费购物";
                $data2['transfer_type'] = 4;
                $data2['transfer_value'] = round($value*$reward_score2/2,2);
                $data2['transfer_time'] = time();
                $data2['detail'] = "消费奖励积分入账，来自".get_user_name($id)."主动消费购物";
                $data2['status'] = 1;
                // dump($data);
                // dump($data2);
                D("Finance/Transfer")->add($data);
                D("Finance/Transfer")->add($data2);
                return $result;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 根据金额+日销售额+月销售额+累计销售额+积分
     * @param value
     * @param type 0积分折0.66,1不折扣，
     * @author bigfoot
     */
    public function addSales($id,$value,$type = 0){
        if ( isset($id) && isset($value) ){
            switch($type){
                case 1 :
                    $score = round($value ,2);//重消积分消费积分不打折
                    break;
                case 2 :
                    $score = 0;//激活会员无积分
                    break;
                default :
                    $score = round($value*0.66,2);//默认积分为销售额的66%
            }
            // dump($score);
            $value = round($value,2);
            $sql = "UPDATE jiu_user_info i
                    INNER JOIN jiu_user_tree t
                    ON i.uid = t.id
                    SET i.total_sales = i.total_sales+".$value.",
                    i.total_score = i.total_score+".$score.",
                    t.day_sales = t.day_sales+".$value.",
                    t.month_sales = t.month_sales+".$value."
                    WHERE  i.uid = ".$id;
            $result = M()->execute($sql);
            // echo M()->_sql;
            // dump ($result);
            return $result;
        }else{
            return false;
        }
    }
    
    /**
     * 根据金额执行店补
     * @param value
     * @param id 判断店铺级别1:5%,2:5%,3:8%
     * @author bigfoot
     */
    public function addShopReward($id,$value){
        if ( isset($id) && !empty($id) && isset($value) && !empty($value) ){
            //读取分销体系配置
            $marketing = C('marketing.1');
            $shop1_reward_percent = $marketing['SHOP1_REWARD_PERCENT']/100;//社区店经理店店补5%
            $shop3_reward_percent = $marketing['SHOP3_REWARD_PERCENT']/100;//中心店店店补8%
            $user_info = get_user_caiwu_info($id);
            switch($user_info['store_level']){
                case 1:
                case 2:
                    $shop_reward = round($value*$shop1_reward_percent,2);
                    break;
                case 3:
                    $shop_reward = round($value*$shop3_reward_percent,2);
                    break;
            }
            // dump($shop_reward);
            $sql = "UPDATE jiu_user_info
                    SET reward_coin = reward_coin +".$shop_reward." 
                    WHERE  uid = ".$id;
            $result = M()->execute($sql);
            // echo M()->_sql;
            // dump ($result);
            if($result !== false){
                //往奖金表里写一条记录
                $data['uid'] = $id;
                $data['reward_number'] = F('check_num')?F('check_num'):1;
                $data['reward_type'] = 5;
                $data['reward_value'] = $shop_reward;
                $data['reward_time'] = time();
                $data['detail'] = "店补，来自".get_user_name(is_login())."重复消费购物";
                $data['status'] = 1;
                D("Finance/Reward")->add($data);
                return $result;
            }
            
        }else{
            return false;
        }
    }
    
}
