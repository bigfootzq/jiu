<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Finance\Controller;
use Home\Controller\HomeController;
use Common\Util\BTNode;
/**
 * 清算控制器
 * @author jry <598821125@qq.com>
 */
class CheckController extends HomeController {
       //根结点
        public $mRoot;
        
       //后序访问栈
        public $mPostordreStack;

        /**
         *构造方法,初始化建立二叉树
         *
         *@param array $btdata 根据后序遍历录入的二叉树的数据，一维数组，每一个元素代表二叉树一个结点值,扩充结点值为''[长度为0的字符串]
         *@return void
         */
        public function __construct($btdata=array()){
            parent::__construct();
            $this->mRoot= $this->sqlnode(2); //查询返回根节点
            // dump($this->mRoot);
            $this->getPostorderTraversal($this->mRoot);
            // $this->preorder($this->mRoot);
        }
        
        /*
        * 默认函数
        *
        */
        public function index(){
            // echo 'test';
            $this->display();
        }
        
        /**
         *日清算，利用前面建立的访问栈 mPostordreStack
         *
         *@return void
         */
        public function daycheck(){
            $visitstack = $this->mPostordreStack;
            $secondstack = $this->mPostordreStack;
            // dump($visitstack);
            // 日清算一次遍历，计算下面AB的当日销售额，取较小的10%为销售提成，有封顶
                while (!empty($visitstack)) {
                    $center_node = array_pop($visitstack);
                    
                    $values = $this->dayupdate1($center_node);
                    if ( !empty($values) ){
                        $day_reward1 .= $values['day_reward1'];
                        $reward_coin .= $values['reward_coin'];
                        $reward_record .= $values['reward_record'];
                        $transfer_record1 .= $values['transfer_record1'];
                        // dump($values);   
                    }
                }
                $day_reward1  = rtrim($day_reward1 , ",");
                $reward_coin  = rtrim($reward_coin , ",");
                $reward_record  = rtrim($reward_record , ",");
                $transfer_record1 = rtrim($transfer_record1,",");
                // dump($day_reward1);
                // dump($reward_coin);
                // dump($reward_record);
                // dump($transfer_record1);
                if(!empty($day_reward1)){
                    $sql1 = "insert into `jiu_user_tree` (id,`day_reward1`) values".$day_reward1."on duplicate key update day_reward1 = values(day_reward1)";
                    //每日第一项收入
                    M()->execute($sql1);
                    // echo M()->_sql();
                }
                if(!empty($reward_coin)){
                    $sql2 = "insert into `jiu_user_info` (uid,`reward_coin`) values".$reward_coin."on duplicate key update reward_coin = reward_coin + values(reward_coin)";
                    //奖金币+
                    M()->execute($sql2);
                    // echo M()->_sql();
                }
                if(!empty($reward_record)){
                    $sql8 = "insert into `jiu_finance_reward` (uid,`reward_number`,`reward_type`,`reward_value`,`reward_time`,`detail`,`status`) values".$reward_record;
                    //奖金入账，写奖金表
                    M()->execute($sql8);
                    // echo M()->_sql();
                }
                if ($transfer_record1){
                    $sql9 = "insert into `jiu_finance_transfer` (uid,from_uid,source,transfer_time,transfer_type,transfer_value,detail,status) values ".$transfer_record1;
                    M()->execute($sql9);
                    // echo M()->_sql();
                }


            //日清算二次遍历，获得所有下线的第一项收入的50%，有封顶
                while (!empty($secondstack)) {
                    $center_node = array_pop($secondstack);
                    $values2 = $this->dayupdate2($center_node);
                    if ( !empty($values2) ){
                        $day_reward2 .= $values2['day_reward2'];
                        $day2_reward_coin .= $values2['day2_reward_coin'];
                        $reward_record2 .= $values2['reward_record2'];
                        $repeat_record .= $values2['repeat_record'];
                        $transfer_record2 .= $values2['transfer_record2'];
                        $transfer_record3 .= $values2['transfer_record3'];
                        $day_check .= $values2['day_check'];
                        // dump($values2);
                    }
                }
                $day_reward2  = rtrim($day_reward2 , ",");
                $day2_reward_coin  = rtrim($day2_reward_coin , ",");
                $reward_record2  = rtrim($reward_record2 , ",");
                $repeat_record  = rtrim($repeat_record , ",");
                $transfer_record2 .=  rtrim($transfer_record2, ",");
                $transfer_record3 .=  rtrim($transfer_record3, ",");
                $day_check = rtrim($day_check,",");
                // dump($day_reward2);
                // dump($reward_coin);
                // dump($reward_record2);
                // dump($repeat_record2);
                // dump($transfer_record2);
                // dump($transfer_record3);
                // dump($day_check);
                
                if(!empty($day_reward2)){
                    $sql3 = "insert into `jiu_user_tree` (id,`day_reward2`) values".$day_reward2."on duplicate key update day_reward2 = values(day_reward2)";
                    //每日第二项收入
                    M()->execute($sql3);
                    // echo M()->_sql();
                }
                if(!empty($day2_reward_coin)){
                    $sql4 = "insert into `jiu_user_info` (uid,`reward_coin`,`repeat_score`,`month_repeat_score`) values ".$day2_reward_coin."on duplicate key update reward_coin = reward_coin + values(reward_coin),repeat_score = repeat_score + values(repeat_score),month_repeat_score = month_repeat_score + values(month_repeat_score)";
                    //奖金币+,重复消费积分+,月度累计重复消费积分+
                     M()->execute($sql4);
                    // echo M()->_sql();
                }
                if(!empty($reward_record2)){
                    $sql5 = "insert into `jiu_finance_reward` (uid,`reward_number`,`reward_type`,`reward_value`,`reward_time`,`detail`,`status`) values".$reward_record2;
                    //写奖金表
                    M()->execute($sql5);
                    // echo M()->_sql();
                }
                if(!empty($repeat_record)){
                    $sql6 = "insert into `jiu_finance_reward` (uid,`reward_number`,`reward_type`,`reward_value`,`reward_time`,`detail`,`status`) values".$repeat_record;
                    //写奖金表，转为重复消费积分记录
                    M()->execute($sql6);
                    // echo M()->_sql();
                }
                
                if ($transfer_record2){
                    $sql10 = "insert into `jiu_finance_transfer` (uid,from_uid,source,transfer_time,transfer_type,transfer_value,detail,status) values ".$transfer_record2;
                    M()->execute($sql10);
                    // echo M()->_sql();
                }
                if ($transfer_record3){
                    $sql11 = "insert into `jiu_finance_transfer` (uid,from_uid,source,transfer_time,transfer_type,transfer_value,detail,status) values ".$transfer_record3;
                    M()->execute($sql11);
                    // echo M()->_sql();
                }
                if ($day_check){
                    $sql7 = "insert into `jiu_finance_check` (uid,check_no,day_reward1,day_reward2,month_rebate,month_bonus,toplimit,repeat_score,total_reward,check_time,status) values".$day_check;
                    //写清算表，日清算记录
                    M()->execute($sql7);
                    // echo M()->_sql();
                }
                $check_num = F('check_num')?F('check_num'):1;
                $check_num = $check_num +1;
                F('check_num',$check_num);//期号+1
                // dump($check_num);
                
                // 最后清空day_reward1,day_reward2,day_sales
                $clearsql = "update jiu_user_tree set day_reward1 = 0 , day_reward2 = 0,day_sales = 0";
                M()->execute($clearsql);
                $birthday = A('User/Birthday');
                $birthday->index();
        }
        
        /**
         *月清算，利用前面建立的访问栈 mPostordreStack
         *
         *@return void
         */
        public function monthcheck(){
            $visitstack = $this->mPostordreStack;
            $secondstack=$this->mPostordreStack;
            // dump($secondstack);
            //月清算一次遍历，更新会员级别，计算月度返利
                while (!empty($visitstack)) {
                    $center_node = array_pop($visitstack);
                    $values = $this->monthupdate1($center_node); //更新该会员级别
                    if (!empty($values)){
                        $month_rebate .= $values['month_rebate'];
                        $reward_coin .= $values['reward_coin'];
                        $referral_bonus .= $values['referral_bonus'];
                        $month_reward_record .= $values['month_reward_record'];
                        $month_repeat_record .= $values['month_repeat_record'];
                        $transfer_record1 .= $values['transfer_record1'];
                        $transfer_record2 .= $values['transfer_record2'];
                        $month_check1 .= $values['month_check1'];
                        // dump($values);
                    }
                }
                
                $month_rebate  = rtrim($month_rebate , ",");
                $reward_coin  = rtrim($reward_coin , ",");
                $referral_bonus  = rtrim($referral_bonus , ",");
                $month_reward_record  = rtrim($month_reward_record , ",");
                $month_repeat_record  = rtrim($month_repeat_record , ",");
                $transfer_record1 = rtrim($transfer_record1,",");
                $transfer_record2 = rtrim($transfer_record2,",");
                $month_check1 = rtrim($month_check1,",");
                // dump($month_rebate);
                // dump($reward_coin);
                // dump($referral_bonus);
                // dump($month_reward_record);
                // dump($month_repeat_record);
                // print_r($transfer_record1);
                // dump($transfer_record2);
                if(!empty($month_rebate)){
                    $sql1 = "insert into `jiu_user_tree` (id,`month_rebate`) values".$month_rebate."on duplicate key update month_rebate = values(month_rebate)";
                    //月返利，
                    M()->execute($sql1);
                    // echo M()->_sql();
                }
                if(!empty($reward_coin)){
                    $sql2 = "insert into `jiu_user_info` (uid,`user_level`,`reward_coin`,`repeat_score`,`month_repeat_score`) values ".$reward_coin."on duplicate key update user_level = values(user_level), reward_coin = reward_coin + values(reward_coin),repeat_score = repeat_score + values(repeat_score),month_repeat_score = month_repeat_score + values(month_repeat_score)";
                    M()->execute($sql2);
                    // echo M()->_sql();
                }
                if(!empty($referral_bonus)){
                    $sql3  = "insert into `jiu_user_info` (uid,`reward_coin`) values".$referral_bonus."on duplicate key update reward_coin = reward_coin + values(reward_coin)";
                    M()->execute($sql3);
                    // echo M()->_sql();
                }
                if(!empty($month_reward_record)){
                    $sql4 = "insert into `jiu_finance_reward` (uid,`reward_number`,`reward_type`,`reward_value`,`reward_time`,`detail`,`status`) values".$month_reward_record;
                    //写奖金表
                    M()->execute($sql4);
                    // echo M()->_sql();
                }
                if(!empty($month_repeat_record)){
                    $sql5 = "insert into `jiu_finance_reward` (uid,`reward_number`,`reward_type`,`reward_value`,`reward_time`,`detail`,`status`) values".$month_repeat_record;
                    //写奖金表，转为重复消费积分记录
                    M()->execute($sql5);
                    // echo M()->_sql();
                }
                if ($transfer_record1){
                    $sql12 = "insert into `jiu_finance_transfer` (uid,from_uid,source,transfer_time,transfer_type,transfer_value,detail,status) values ".$transfer_record1;
                    M()->execute($sql12);
                    // echo M()->_sql();
                }
                if ($transfer_record2){
                    $sql13 = "insert into `jiu_finance_transfer` (uid,from_uid,source,transfer_time,transfer_type,transfer_value,detail,status) values ".$transfer_record2;
                    M()->execute($sql13);
                    // echo M()->_sql();
                }
            
            // 月清算二次遍历，根据会员级别计算分红
            while (!empty($secondstack)) {
                $center_node = array_pop($secondstack);
                $values2 = $this->monthupdate2($center_node); //计算分红
                if ( !empty($values2) ){
                    $month_bonus .= $values2['month_bonus'];
                    $bonus_reward_coin .= $values2['bonus_reward_coin'];
                    $month_reward_record2 .= $values2['month_reward_record2'];
                    $month_repeat_record2 .= $values2['month_repeat_record2'];
                    $month_check2.= $values2['month_check2'];
                    // dump($values2);     
                }
            }
            
            // dump($month_bonus);
            // dump($bonus_reward_coin);
            $month_bonus   = rtrim($month_bonus  , ",");
            $bonus_reward_coin  = rtrim($bonus_reward_coin , ",");
            $month_reward_record2  = rtrim($month_reward_record2 , ",");
            $month_repeat_record2  = rtrim($month_repeat_record2 , ",");
            $month_check2 = rtrim($month_check2,",");
            // dump($month_reward_record2);
            // dump($month_check1);
            // dump($month_check2);
            if(!empty($month_bonus)){
                $sql6 = "insert into `jiu_user_tree` (id,`month_bonus`) values".$month_bonus." on duplicate key update month_bonus = values(month_bonus)";
                M()->execute($sql6);
                // echo M()->_sql();
            }
            if(!empty($bonus_reward_coin)){
                $sql7 = "insert into `jiu_user_info` (uid,`reward_coin`,`repeat_score`,`month_repeat_score`) values ".$bonus_reward_coin."on duplicate key update  reward_coin = reward_coin + values(reward_coin),repeat_score = repeat_score + values(repeat_score),month_repeat_score = month_repeat_score + values(month_repeat_score)";
                M()->execute($sql7);
                // echo M()->_sql();
            }
            
            
            if(!empty($month_reward_record2)){
                $sql8 = "insert into `jiu_finance_reward` (uid,`reward_number`,`reward_type`,`reward_value`,`reward_time`,`detail`,`status`) values".$month_reward_record2;
                //写奖金表
                M()->execute($sql8);
                // echo M()->_sql();
            }
            if(!empty($month_repeat_record2)){
                $sql9 = "insert into `jiu_finance_reward` (uid,`reward_number`,`reward_type`,`reward_value`,`reward_time`,`detail`,`status`) values".$month_repeat_record2;
                //写奖金表，转为重复消费积分记录
                M()->execute($sql9);
                // echo M()->_sql();
            }
            //写清算表，月清算记录
             if(!empty($month_check1)){
                $sql10 = "insert into `jiu_finance_check` (uid,check_no,day_reward1,day_reward2,month_rebate,month_bonus,toplimit,repeat_score,total_reward,check_time,status) values".$month_check1;
            
                
                M()->execute($sql10);
                // echo M()->_sql();
             }
             if(!empty($month_check2)){
                 $sql11 = "insert into `jiu_finance_check` (uid,check_no,day_reward1,day_reward2,month_rebate,month_bonus,toplimit,repeat_score,total_reward,check_time,status) values".$month_check2;
                M()->execute($sql11);
                // echo M()->_sql();
             }
             
             //最后清空month_sales,month_repeat_score
                $clearsql = "update jiu_user_tree set month_sales = 0";
                $clearsql2 = "update jiu_user_info set month_repeat_score = 0";
                M()->execute($clearsql);
                M()->execute($clearsql2);
        }
       
        /**
         *日清算一次遍历，计算下面AB的当日销售额（后序遍历，AB已算出销售额），取较小的10%为销售提成
         *
         *@param int 数据库中节点的id，如果0则取根节点
         *@return BTNode
         */
        public function dayupdate1($node){
            $info_object = D('User/Caiwu');
            $reward1=min($node->mLchild->mData['day_sales'], $node->mRchild->mData['day_sales'])*0.1;
            $node->mData['day_reward1'] = min($reward1, 800.00);
            $dr1 = $node->mData['day_reward1'];
            // dump($dr1);
            //待补充，根据$node->mData['.day_reward1']进行返利,加奖金币
            if($dr1){
                $sql['reward_coin'] .= '('.$node->mData['id'].','.$dr1.'),';// 这里拼接好sql字符串，(id,reward_coin)
                $sql['day_reward1'] .= '('.$node->mData['id'].','.$dr1.'),';// 这里拼接好sql字符串，(id,dayreward1)
                $check_num = F('check_num')?F('check_num'):1;//读取期号，如果期号不存在，则是第一期
                $detail = "'组织奖来自日清算第".$check_num."期'";
                
                $sql['reward_record'] .= '('.$node->mData['id'].','.$check_num.',1,'.$dr1.','.time().','.$detail.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                
                $source = "'组织奖来自日清算第".$check_num."期'";
                
                //转账记录
                $sql['transfer_record1'] = '('.$node->mData['id'].',0,'.$source.','.time().',2,'.$dr1.','.$detail.',1),';
                // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                // 1电子币转出账|2奖金币转入账|3奖金币转换为电子币|4消费奖励积分入账|5消费积分出账6|电子币入账|7奖金币出账';
            }

            //更新销售额，自己的销售额加下面AB的销售额
            $node->mData['day_sales'] += $node->mLchild->mData['day_sales']+$node->mRchild->mData['day_sales'];
            $ds = $node->mData['day_sales'];
            if ($ds){
                $id3 = D('User/Tree')->where('id = %d',$node->mData['id'] )->setField('day_sales',$ds);
                $id4 = D('User/Tree')->where('id = %d',$node->mData['id'] )->setInc('total_market_sales',$ds);
            }
            return $sql;
        }
        
        // /**
         // *日清算二次遍历，获得直接下线的第一项收入的50%
         // *
         // *@param int 数据库中节点的id，如果0则取根节点
         // *@return BTNode
         // */
        public function dayupdate2($node){
            $reward1=$node->mData['day_reward1'];
            $reward2=0;
            if($node->mRchild != null) {
                $reward2 += $node->mRchild->mData['day_reward1']*0.5;
            }
            if($node->mLchild != null) { 
                $reward2 += $node->mLchild->mData['day_reward1']*0.5;
            }
            $node->mData['day_reward2']=min($reward2, 800.00-$reward1);
            $reward2 = $node->mData['day_reward2'];
            if ($reward2){
                $sql['day_reward2'] = '('.$node->mData['id'].','.$reward2.'),';// 这里拼接好sql字符串，(id,day_reward2)
            }
            //待补充，根据$node->mData.day_reward2进行返利
            $reward = $reward1 + $reward2;
           
            // dump ($reward1);
            // dump ($reward2);
            
            if($reward){
                $data = $this->getRepeatScore($node,$reward);//返回reward_coin,repeat_score
                $sql['day2_reward_coin'] = '('.$node->mData['id'].','.$data['reward_coin'].','.$data['repeat_score'].','.$data['month_repeat_score'].'),';
                // 这里拼接好sql字符串，(reward_coin,repeat_score,month_repeat_score)
                
                $check_num = F('check_num')?F('check_num'):1;//读取期号，如果期号不存在，则是第一期
                $detail = "'领导奖来自第".$check_num."期'";
                $sql['reward_record2'] = '('.$node->mData['id'].','.$check_num.',2,'.$data['reward_coin'].','.time().','.$detail.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                $source = $detail;       
                 //转账记录
                $sql['transfer_record2'] = '('.$node->mData['id'].',0,'.$source.','.time().',2,'.$data['reward_coin'].','.$detail.',1),';
                // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                
                if( !empty($data['repeat_score']) ){
                    $detail2 = "'转为重复消费积分来自第".$check_num."期'";
                    $sql['repeat_record'] = '('.$node->mData['id'].','.$check_num.',6,'.$data['repeat_score'].','.time().','.$detail2.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                    $source = "'重复消费积分入账来自第".$check_num."期'";    
                    //转账记录
                    $sql['transfer_record3'] = '('.$node->mData['id'].',0,'.$source.','.time().',8,'.$data['repeat_score'].','.$source.',1),';
                    // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                }
                
                //清算记录
                $total_reward = $reward - $data['repeat_score'];
                $sql['day_check'] = '('.$node->mData['id'].','.$check_num.','.$reward1.','.$reward2.',0,0,0,'.$data['repeat_score'].','.$total_reward.','.time().','.'1),';
                //(uid,check_no,day_reward1,day_reward2,month_rebate,month_bonus,toplimit,repeat_score,total_reward,check_time,status)
                //userid，期号，组织奖，领导奖，消费业绩奖，服务费，封顶，重复消费，奖金总数，来源时间
            }
           
            return $sql;
        }
        
        /**
         *月清算一次遍历，更新节点对应会员积分，级别，确定返利级别并计算月度返利，是否更新回数据库中？
         *
         *@param int 数据库中节点的id，如果0则取根节点
         *@return BTNode
         */
        public function monthupdate1($node){
            // dump($node->mData['id']);
            $info_object = D('User/Caiwu');
            $tree_object = D('User/Tree');
            $score = $node->mData['total_score'];
            // $node->mData['director_num'] = 0;
            // $node->mData['manager_num'] = 0;
            if($node->mLchild != null) {
                $node->mData['director_num'] += $node->mLchild->mData['director_num'];
                $node->mData['manager_num'] += $node->mLchild->mData['manager_num'];
            }
            if($node->mRchild != null) {
                $node->mData['director_num'] += $node->mRchild->mData['director_num'];
                $node->mData['manager_num'] += $node->mRchild->mData['manager_num'];
            }
            
            // dump($node->mData['director_num']);
            // dump($node->mData['manager_num']);
             
            if ( ( $node->mData['manager_num'] >= 3) && ( $node->mData['user_level'] <7) ) { //下面有3个经理为总监，已经是总监级别不减
                // dump($node->mData['id']);
                $node->mData['user_level'] = 7; 
                $node->mData['month_rebate'] = $node->mData['month_sales']*0.66*0.255;
                $node->mData['manager_num']=0; //自己是总监，为独立市场，经理数清零，自己算一个经理
                $info_object->where('uid = %d',$node->mData['id'])->setField('user_level',7);
                $tree_object->where('id = %d',$node->mData['id'])->setField('manager_num',0);
            } else if ( ($node->mData['director_num'] >= 2) && ($node->mData['user_level']<6) ) { //下面有2个主任为经理，已经是经理级别不减
                // dump($node->mData['id']);
                // dump($node->mData['user_level']);
                // dump($node->mData['store_level']);
                $node->mData['user_level'] = 6; 
                $node->mData['month_rebate'] = $node->mData['month_sales']*0.66*0.25;
                $node->mData['manager_num']++; //自己是经理，经理数＋1
                $node->mData['director_num'] = 0; //自己是经理，为独立市场，主任数清零
                $tree_object->where('id = %d',$node->mData['id'])->setInc('manager_num');
                $tree_object->where('id = %d',$node->mData['id'])->setField('director_num',0);
                if ($node->mData['store_level'] == '1'){// 如果已经开店且店铺为社区店，提升为经理店。
                    $info_object->where('uid = %d',$node->mData['id'])->setField('user_level',6);
                    $info_object->where('uid = %d',$node->mData['id'])->setField('store_level',2);
                }
            } else if ($score >= 15000) { //主任
                
                // dump($node->mData['id']);
                $node->mData['user_level'] = 5; 
                $node->mData['month_rebate'] = $node->mData['month_sales']*0.66*0.2;
                $node->mData['director_num']++; //加主任数
                $tree_object->where('id = %d',$node->mData['id'])->setInc('director_num');
            } else if ($score >= 7001) {
                $node->mData['user_level'] = 4; 
                $node->mData['month_rebate'] = $node->mData['month_sales']*0.66*0.15;
            } else if ($score >= 2001) { 
                $node->mData['user_level'] = 3; 
                $node->mData['month_rebate'] = $node->mData['month_sales']*0.66*0.12;
            } else if ($score >= 401) { 
                $node->mData['user_level'] = 2; 
                $node->mData['month_rebate'] = $node->mData['month_sales']*0.66*0.09;
            } else if ($score >= 1) { 
                $node->mData['user_level'] = 1; 
                $node->mData['month_rebate'] = $node->mData['month_sales']*0.66*0.06;
            }
            
                // dump($node->mData['user_level']);
                // dump($node->mData['month_rebate']);
            
            if($node->mData['month_rebate'] > 0 ){
                // dump($node->mData['month_rebate']);
                //查找推荐人，按照推荐人给予极差返利
                $referral_bonus = $this->getReferralBonus($node,$node->mData['month_rebate']);
                // dump($referral_bonus);
                if ( $referral_bonus > 0){
                    $sql['referral_bonus']  = '('.$node->mData['pid'].','.$referral_bonus.'),';
                     //这里拼接好sql字符串，(id,reward_coin)pid,referralbonus
                }
                $sql['month_rebate'] = '('.$node->mData['id'].','.$node->mData['month_rebate'].'),';
                 //这里拼接好sql字符串，(id,month_rebate)
                $data = $this->getRepeatScore($node, $node->mData['month_rebate']);
                // 这里拼接好sql字符串，(id,user_level,reward_coin,repeat_score,month_repeat_score)
                $sql['reward_coin'] = '('.$node->mData['id'].','.$node->mData['user_level'].','.$data['reward_coin'].','.$data['repeat_score'].','.$data['month_repeat_score'].'),';
                // dump($sql['reward_coin']);
                //根据$node->mData['month_rebate']进行返利
                //确定级别完毕，按照级别开始根据本月销售额来计算返利，+奖励币（月度返利），
                //强制消费 -奖励币 +重复消费积分
                $check_num = F('check_num')?F('check_num'):1;//读取期号，如果期号不存在，则是第一期
                $detail = "'月度返利来自第".$check_num."期'";
                $sql['month_reward_record'] .= '('.$node->mData['id'].','.$check_num.',3,'.$data['reward_coin'].','.time().','.$detail.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                
                //转账记录
                $source = "'奖金币入账,月度返利来自第".$check_num."期'";       
                 
                $sql['transfer_record1'] = '('.$node->mData['id'].',0,'.$source.','.time().',2,'.$data['reward_coin'].','.$source.',1),';
                // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                
                if( !empty($data['repeat_score']) ){
                    $detail2 = "'月度返利转为重复消费积分来自第".$check_num."期'";
                    $sql['month_repeat_record'] .= '('.$node->mData['id'].','.$check_num.',6,'.$data['repeat_score'].','.time().','.$detail2.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                    
                    $source2 = "'重复消费积分入账，月度返利转为重复消费积分来自第".$check_num."期'";    
                    
                    $sql['transfer_record2'] = '('.$node->mData['id'].',0,'.$source2.','.time().',8,'.   $data['repeat_score'].','.$source2.',1),';
                    // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                
                }
                //清算记录
                $total_reward = $node->mData['month_rebate'] -$data['repeat_score'];
                $sql['month_check1'] = '('.$node->mData['id'].','.$check_num.',0,0,'.$node->mData['month_rebate'].',0,0,'.$data['repeat_score'].','.$total_reward.','.time().','.'1),';
                //(uid,check_no,day_reward1,day_reward2,month_rebate,month_bonus,toplimit,repeat_score,total_reward,check_time,status)
                //userid，期号，组织奖，领导奖，消费业绩奖，服务费，封顶，重复消费，奖金总数，来源时间
            } 
             
            return $sql;
        }
        /**
         *月清算二次遍历，计算分红，是否更新回数据库中？
         *
         *@param int 
         *@return null
         */
        public function monthupdate2($node){
            $info_object = D('User/Caiwu');
            $tree_object = D('User/Tree');
            $check_num = F('check_num')?F('check_num'):1;//读取期号，如果期号不存在，则是第一期
            // dump($node->mData['id']);
            // dump($node->mData['user_level']);
            if (empty($node) || $node->mData['user_level'] < 6){
                // dump($node->mData['id']);
                // dump($node->mData['user_level']);
                return;
            }
                

            $number1=4; //找4代经理
            $number2=10; //总监级别找10代
            $bonus=0;
            $count1=0;
            $rate1 = array(0.09, 0.07, 0.05, 0.05);
            $rate2 = array(0.095, 0.075, 0.055, 0.055,0.005,0.005,0.005,0.005,0.005,0.005);

            $stack = array();
            if($node->mRchild != null) {
                array_push($stack, $node->mRchild);
            }
            if($node->mLchild != null) {
                array_push($stack, $node->mLchild);
            }
            // dump($stack);
            if ($node->mData['user_level'] == 6){ //计算经理的分红
                // dump($node->mData['id']);
                while(!empty($stack)){
                    $center_node = array_pop($stack);
                    // dump($center_node->mData['id']);
                    // dump($center_node->mData['user_level']);
                    // dump($center_node->mData['month_rebate']);
                    if ($center_node->mData['user_level'] == 6) {
                        $bonus += $center_node->mData['month_rebate']*$rate1[$count1];
                        // dump($center_node->mData['month_rebate']);
                        // dump($bonus);
                        $count1++;
                        if ($count1==$number1) { //找齐了4代
                            $bonus = round($bonus,2);
                            $node->mData['month_bonus'] = $bonus;
                            if($bonus){
                                //待补充，根据$node->mData.month_bonu执行返利
                                $month_bonus = $this->getRepeatScore($node,$bonus);
                                $repeat_score = $month_bonus['repeat_score'];
                                // dump($month_bonus);
                                $sql['month_bonus'] = '('.$node->mData['id'].','.$bonus.'),';
                                 //这里拼接好sql字符串，(id,month_bonus)
                                    
                                $sql['bonus_reward_coin'] = '('.$node->mData['id'].','.$month_bonus['reward_coin'].','.$month_bonus['repeat_score'].','.$month_bonus['month_repeat_score'].'),';
                                // 这里拼接好sql字符串，(id,reward_coin,repeat_score,month_repeat_score)
                                
                                //清算记录
                                $total_reward = $bonus - $repeat_score;
                                $sql['month_check2'] = '('.$node->mData['id'].','.$check_num.',0,0,0,'.$bonus.',0,'.$repeat_score.','.$total_reward.','.time().','.'1),';
                                //(uid,check_no,day_reward1,day_reward2,month_rebate,month_bonus,toplimit,repeat_score,total_reward,check_time,status)
                                //userid，期号，组织奖，领导奖，消费业绩奖，服务费，封顶，重复消费，奖金总数，来源时间
                                $detail = "'月度分红来自第".$check_num."期'";
                                $sql['month_reward_record2'] .= '('.$node->mData['id'].','.$check_num.',4,'.$month_bonus['reward_coin'].','.time().','.$detail.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                               
                                //转账记录
                                $source = "'奖金币入账，月度分红来自第".$check_num."期'";     
                                 
                                $sql['transfer_record3'] = '('.$node->mData['id'].',0,'.$source.','.time().',2,'.$month_bonus['reward_coin'].','.$source.',1),';
                                // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                                if( !empty($data['repeat_score']) ){
                                    $detail2 = "'月度分红转为重复消费积分来自第".$check_num."期'";
                                    $sql['month_repeat_record2'] .= '('.$node->mData['id'].','.$check_num.',6,'.$month_bonus['repeat_score'].','.time().','.$detail2.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                                    
                                    $source2 = "'重复消费积分入账，月度分红转为重复消费积分来自第".$check_num."期'";     
                                    
                                    $sql['transfer_record4'] = '('.$node->mData['id'].',0,'.$source2.','.time().',8,'.   $month_bonus['repeat_score'].','.$source2.',1),';
                                    // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                                }
                            }   
                            return $sql;
                        }
                        if($center_node->mRchild != null) array_push($stack, $center_node->mRchild);
                        if($center_node->mLchild != null) array_push($stack, $center_node->mLchild);
                    }

                    
                }//endwhile
            }//endif
            if ($node->mData['user_level'] == 7){ //计算总监的分红
                // dump($node->mData['id']);
                // dump($node->mData['month_rebate']);
                while(!empty($stack)){
                    $center_node = array_pop($stack);
                    // $reward2 += $node->mData['day_reward1']*0.5;
                    if (($count1<$number1) and ($center_node->mData['user_level']>=6)) { //前4代经理总监都算
                        $bonus += $center_node->mData['month_rebate']*$rate2[$count1];
                        $count1++;
                        
                        // dump($bonus);
                    } else if  (($count1>=$number1) and ($center_node->mData['user_level']>=6)) //找后6代中，经理总监的分红都算
                        $bonus += $center_node->mData['month_rebate']*$rate2[$count1]; 
                        if ($center_node->mData['user_level'] == 7) { //后6代只算总监
                            $count1++;
                            if ($count1==$number2) {//找齐了10代
                                $bonus = round($bonus,2);
                                $node->mData['month_bonus'] = $bonus;
                                if($bonus){
                                    //待补充，根据$node->mData.month_bonu执行返利
                                    $month_bonus = $this->getRepeatScore($node,$bonus);
                                    $repeat_score = $month_bonus['repeat_score'];
                                    // dump($month_bonus);
                                    $sql['month_bonus'] = '('.$node->mData['id'].','.$bonus.'),';
                                     //这里拼接好sql字符串，(id,month_bonus)
                                        
                                    $sql['bonus_reward_coin'] = '('.$node->mData['id'].','.$month_bonus['reward_coin'].','.$month_bonus['repeat_score'].','.$month_bonus['month_repeat_score'].'),';
                                    // 这里拼接好sql字符串，(id,reward_coin,repeat_score,month_repeat_score)
                                    
                                    //清算记录
                                    $total_reward = $bonus - $repeat_score;
                                    $sql['month_check2'] = '('.$node->mData['id'].','.$check_num.',0,0,0,'.$bonus.',0,'.$repeat_score.','.$total_reward.','.time().','.'1),';
                                    //(uid,check_no,day_reward1,day_reward2,month_rebate,month_bonus,toplimit,repeat_score,total_reward,check_time,status)
                                    //userid，期号，组织奖，领导奖，消费业绩奖，服务费，封顶，重复消费，奖金总数，来源时间
                                    $detail = "'月度分红来自第".$check_num."期'";
                                    $sql['month_reward_record2'] .= '('.$node->mData['id'].','.$check_num.',4,'.$month_bonus['reward_coin'].','.time().','.$detail.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                                   
                                    //转账记录
                                    $source = "'奖金币入账，月度分红来自第".$check_num."期'";     
                                     
                                    $sql['transfer_record3'] = '('.$node->mData['id'].',0,'.$source.','.time().',2,'.$month_bonus['reward_coin'].','.$source.',1),';
                                    // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                                    if( !empty($data['repeat_score']) ){
                                        $detail2 = "'月度分红转为重复消费积分来自第".$check_num."期'";
                                        $sql['month_repeat_record2'] .= '('.$node->mData['id'].','.$check_num.',6,'.$month_bonus['repeat_score'].','.time().','.$detail2.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                                        
                                        $source2 = "'重复消费积分入账，月度分红转为重复消费积分来自第".$check_num."期'";     
                                        
                                        $sql['transfer_record4'] = '('.$node->mData['id'].',0,'.$source2.','.time().',8,'.   $month_bonus['repeat_score'].','.$source2.',1),';
                                        // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                                    }
                                }   
                                    
                                return $sql;
                            }
                        }
                        if($center_node->mRchild != null) array_push($stack, $center_node->mRchild);
                        if($center_node->mLchild != null) array_push($stack, $center_node->mLchild);
                    }//endwhile

                    
                }//endif
            // dump($bonus);
            if($bonus){
                $bonus = round($bonus,2);
                $node->mData['month_bonus'] = $bonus; //未找全代数
                //待补充，根据$node->mData.month_bonu执行返利
                $month_bonus = $this->getRepeatScore($node,$bonus);
                $repeat_score = $month_bonus['repeat_score'];
                // dump($month_bonus);
                $sql['month_bonus'] = '('.$node->mData['id'].','.$bonus.'),';
                 //这里拼接好sql字符串，(id,month_bonus)
                    
                $sql['bonus_reward_coin'] = '('.$node->mData['id'].','.$month_bonus['reward_coin'].','.$month_bonus['repeat_score'].','.$month_bonus['month_repeat_score'].'),';
                // 这里拼接好sql字符串，(id,reward_coin,repeat_score,month_repeat_score)
                
                //清算记录
                $total_reward = $bonus - $repeat_score;
                $sql['month_check2'] = '('.$node->mData['id'].','.$check_num.',0,0,0,'.$bonus.',0,'.$repeat_score.','.$total_reward.','.time().','.'1),';
                //(uid,check_no,day_reward1,day_reward2,month_rebate,month_bonus,toplimit,repeat_score,total_reward,check_time,status)
                //userid，期号，组织奖，领导奖，消费业绩奖，服务费，封顶，重复消费，奖金总数，来源时间
                $detail = "'月度分红来自第".$check_num."期'";
                $sql['month_reward_record2'] .= '('.$node->mData['id'].','.$check_num.',4,'.$month_bonus['reward_coin'].','.time().','.$detail.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
               
                //转账记录
                $source = "'奖金币入账，月度分红来自第".$check_num."期'";     
                 
                $sql['transfer_record3'] = '('.$node->mData['id'].',0,'.$source.','.time().',2,'.$month_bonus['reward_coin'].','.$source.',1),';
                // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                if( !empty($data['repeat_score']) ){
                    $detail2 = "'月度分红转为重复消费积分来自第".$check_num."期'";
                    $sql['month_repeat_record2'] .= '('.$node->mData['id'].','.$check_num.',6,'.$month_bonus['repeat_score'].','.time().','.$detail2.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                    
                    $source2 = "'重复消费积分入账，月度分红转为重复消费积分来自第".$check_num."期'";     
                    
                    $sql['transfer_record4'] = '('.$node->mData['id'].',0,'.$source2.','.time().',8,'.   $month_bonus['repeat_score'].','.$source2.',1),';
                    // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                }
            }
            
                // dump($sql);
                return $sql;
        }
        
         /**
         *数据库查询并构造节点返回
         *
         *@param int 数据库中节点的id，如果0则取根节点
         *@return BTNode
         */
        public function sqlnode($nodeid){
            $BTNode = new BTNode();
            if ($nodeid == 0){
                // return $this->sqlnode(2);
                return $BTNode;
            }else{
                // dump($nodeid);
                $sql = "select id, user_level,store_level,total_score,month_sales,month_repeat_score,position, pid, lid,director_num,manager_num,rid, day_sales,day_reward1,day_reward2,month_rebate from `jiu_user_tree` join `jiu_user_info` on jiu_user_info.uid = jiu_user_tree.id where id = ".$nodeid;
                $tree_data = M()->query($sql);
                // dump($tree_data);
                if ($tree_data){
                    $father = explode(',',$tree_data['0']['position']);
                    $BTNode->mLchild = $tree_data['0']['lid'];
                    $BTNode->mRchild = $tree_data['0']['rid'];
                    $BTNode->mData = $tree_data['0'];
                    $BTNode->mFather = $father['0'];
                    // dump($BTNode->mData['id']);
                    // dump($BTNode);
                    return $BTNode;
                }
            }
        }
        
        /**
         *后序遍历的非递归算法
         *
         *@param BTNode $objRootNode 二叉树根节点
         *@param array $arrBTdata 接收值的数组变量，按引用方式传递
         *@return void
         */
        public function getPostorderTraversal($root){
            // dump($root);
            $pushstack = array(); //临时栈，存放待搜索节点
            $visitstack = array(); //访问栈，存最终访问顺序的节点
            array_push($pushstack, $root);
            // dump($pushstack);
            while (!empty($pushstack)) {
                $center_node = array_pop($pushstack); //最后入栈节点出栈
                array_push($visitstack, $center_node); //根节点最后访问
                 // dump($center_node->mData['id']);
                 // dump($center_node->mData['user_level']);
                // dump($pushstack);
                // dump($visitstack);
                // //找左右子节点，如有则生成节点，并建立引用，放到临时栈
                if ( !empty($center_node->mLchild) ) {
                                $tempnode = $this->sqlnode($center_node->mData['lid']);
                                $center_node->mLchild = $tempnode;
                                $tempnode->mFather =$center_node;

                                array_push($pushstack, $tempnode);
                                }
                if ( !empty($center_node->mRchild) ) {
                            $tempnode = $this->sqlnode($center_node->mData['rid']);
                            $center_node->mRchild = $tempnode;
                            $tempnode->mFather =$center_node;

                            array_push($pushstack, $tempnode);
                        }
            }
            // dump($visitstack);
            $this->mPostordreStack = $visitstack; //储存访问栈
            // exit();
        }
        
        
        
        //前序遍历，访问根节点->遍历子左树->遍历右左树
        public function preorder($root){
                    $stack = array();
                    array_push($stack, $root);
                    while(!empty($stack)){
                        $center_node = array_pop($stack);
                        // dump($center_node->mData['id']);
                        
                        if(!empty($center_node->mRchild)) {
                            array_push($stack, $this->sqlnode($center_node->mData['rid']));
                        }
                            
                        if(!empty($center_node->mLchild)){
                            array_push($stack, $this->sqlnode($center_node->mData['lid']));
                        }
                    }

        }
        
        //中序遍历，遍历子左树->访问根节点->遍历子右树
        public function inorder($root){
                $stack = array();
                $center_node = $root;
                // dump($center_node);
                while (!empty($stack) || $center_node != null) {
                         while ($center_node != null) {
                             array_push($stack, $center_node);
                             $center_node = $this->sqlnode($center_node->mData['lid']);
                         }
             
                         $center_node = array_pop($stack);
                         // dump($center_node->mData['id']);
             
                         $center_node = $this->sqlnode($center_node->mData['rid']);
                     }
        }
        /*
        * 根据id计算极差返利
        * @param $node 用户节点
        * @param $month_rebate 本月盈利
        * return $bonus;
          @auther bigfoot
        */
        public function getReferralBonus($node,$month_rebate){
            // dump($month_rebate);
            if( isset($node) && isset($month_rebate) && $month_rebate > 0 ){
                $user_info = D('User/Caiwu');
                $referee = $user_info->find($node->mData['pid'] );
                if( $referee['user_level'] > $node->mData['user_level'] ){
                    $bonus = $month_rebate * ( $this->getReferralPercet($referee['user_level']) - $this->getReferralPercet($node->mData['user_level']) );
                    $bonus = round($bonus,2);
                }else{
                    return false;
                }
            }else{
                return false;
            }
            // dump($bonus);
            return $bonus;
        }
        
        /*
        * 根据级别返回月度返利比例
        * @param $level 用户级别
        * return $percent;
          @auther bigfoot
        */
        public function getReferralPercet($user_level){
            if( isset($user_level) ){
                switch($user_level){
                    case  1:
                        $percent = 0.06;
                            break;
                    case  2:
                        $percent = 0.09;
                            break;
                    case  3:
                        $percent = 0.12;
                            break;
                    case  4:
                        $percent = 0.15;
                            break;
                    case  5:
                        $percent = 0.20;
                            break;
                    case  6:
                        $percent = 0.25;
                            break;
                    case  7:
                        $percent = 0.255;
                            break;
                    
                }
            }else{
                return false;
            }
            return $percent;
        }
        
        /*
        * 根据级别计算奖金的重复消费积分
        * @param $node 用户节点
        * @param $reward 本次获得奖金
        * return $data;
          @auther bigfoot
        */
        public function getRepeatScore($node, $reward){
            // dump($node->mData['user_level']);
            if ($reward > 0 && isset($reward) && isset($node)){
                switch($node->mData['user_level']){
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                        case 5:
                            if( $node->mData['month_repeat_score'] >= 100){
                                $data['reward_coin'] = round ($reward,2);//盈利四舍五入至小数点后两位，加奖金币，
                                $data['repeat_score'] = 0.00;
                                $data['month_repeat_score'] = 0.00;
                                
                            }else{
                                $data['reward_coin'] = round ($reward*0.9 , 2);//盈利四舍五入至小数点后两位，加奖金币，
                                $data['repeat_score'] = round ($reward * 0.1 , 2);
                                //盈利的10%要进行重复消费，加重复消费积分,因为第一次清算时没有加重复消费积分
                                //所以这里要算上第一次的。
                                //加本月累计重复消费积分
                                $data['month_repeat_score'] = $data['repeat_score'];
                            }
                            break;
                        case 6:
                            if( $node->mData['month_repeat_score'] >= 500){
                                $data['reward_coin'] = round ($reward,2);//盈利四舍五入至小数点后两位，加奖金币，
                                $data['repeat_score'] = 0.00;
                                $data['month_repeat_score'] = 0.00;
                                
                            }else{
                                $data['reward_coin'] = round ($reward*0.9 , 2);//盈利四舍五入至小数点后两位，加奖金币，
                                $data['repeat_score'] = round ($reward * 0.1 , 2);
                                //盈利的10%要进行重复消费，加重复消费积分,因为第一次清算时没有加重复消费积分
                                //所以这里要算上第一次的。
                                //加本月累计重复消费积分
                                $data['month_repeat_score'] = $data['repeat_score'];
                            }
                            break;
                        case 7:
                            if( $node->mData['month_repeat_score'] >= 1000){
                                $data['reward_coin'] = round ($reward,2);//盈利四舍五入至小数点后两位，加奖金币，
                                $data['repeat_score'] = 0.00;
                                $data['month_repeat_score'] = 0.00;
                                
                            }else{
                                $data['reward_coin'] = round ($reward*0.9 , 2);//盈利四舍五入至小数点后两位，加奖金币，
                                $data['repeat_score'] = round ($reward * 0.1 , 2);
                                //盈利的10%要进行重复消费，加重复消费积分,因为第一次清算时没有加重复消费积分
                                //所以这里要算上第一次的。
                                //加本月累计重复消费积分
                                $data['month_repeat_score'] =  $data['repeat_score'];
                            }
                            break;
                    }
               // dump($data);
                    return $data;
               }else{
                   return false;
               }
        }
}