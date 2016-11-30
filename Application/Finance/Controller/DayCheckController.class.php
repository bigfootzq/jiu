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
class DayCheckController extends HomeController {
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
                    $day_check_num = F('day_check_num')?F('day_check_num'):1;
                    $day_check_num = $day_check_num +1;
                    F('day_check_num',$day_check_num);//期号+1
                    // dump($day_check_num);
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
                    //写转账表
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
                    //写转账表，奖金币
                    $sql10 = "insert into `jiu_finance_transfer` (uid,from_uid,source,transfer_time,transfer_type,transfer_value,detail,status) values ".$transfer_record2;
                    M()->execute($sql10);
                    // echo M()->_sql();
                }
                if ($transfer_record3){
                    //写转账表，重消积分
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
                
                
                // 最后清空day_reward1,day_reward2,day_sales
                $clearsql = "update jiu_user_tree set day_reward1 = 0 , day_reward2 = 0,day_sales = 0";
                M()->execute($clearsql);
                $birthday = A('User/Birthday');
                $birthday->index();
        }
        
        
        /**
         *日清算一次遍历，计算下面AB的当日销售额（后序遍历，AB已算出销售额），取较小的10%为销售提成
         *
         *@param int 数据库中节点的id，如果0则取根节点
         *@return BTNode
         */
        public function dayupdate1($node){
            $info_object = D('User/Caiwu');
            $tree_object = D('User/Tree');
           
            $reward1=min($node->mLchild->mData['day_sales'], $node->mRchild->mData['day_sales'])*0.1;
            $node->mData['day_reward1'] = min($reward1, 800.00);
            $dr1 = $node->mData['day_reward1'];
            // dump($dr1);
            //待补充，根据$node->mData['.day_reward1']进行返利,加奖金币
            if($dr1){
                $dr2 = round($dr1*0.5,2);
                if($node->mData['pid'] != 0){
                    D('User/Tree')->where('id = %d',$node->mData['pid'] )->setInc('day_reward2',$dr2);
                    //组织奖的50%作为领导奖返给他的推荐人。
                }
                $sql['reward_coin'] .= '('.$node->mData['id'].','.$dr1.'),';// 这里拼接好sql字符串，(id,reward_coin)
                $sql['day_reward1'] .= '('.$node->mData['id'].','.$dr1.'),';// 这里拼接好sql字符串，(id,dayreward1)
                $day_check_num = F('day_check_num')?F('day_check_num'):1;//读取期号，如果期号不存在，则是第一期
                $day_check_num = sprintf("%03d", $day_check_num);
                $detail = "'组织奖来自日清算第".$day_check_num."期'";
                
                $sql['reward_record'] .= '('.$node->mData['id'].','.$day_check_num.',1,'.$dr1.','.time().','.$detail.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                
                $source = "'组织奖来自日清算第".$day_check_num."期'";
                
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
            $reward2= D('User/Tree')->where('id = %d',$node->mData['id'] )->getField('day_reward2');
            // dump($node->mData['id']);
            // echo D('User/Tree')->_sql();
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
                
                $day_check_num = F('day_check_num')?F('day_check_num'):1;//读取期号，如果期号不存在，则是第一期
                $day_check_num = sprintf("%03d", $day_check_num);
                $detail = "'领导奖来自第".$day_check_num."期'";
                $sql['reward_record2'] = '('.$node->mData['id'].','.$day_check_num.',2,'.$data['reward_coin'].','.time().','.$detail.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                $source = $detail;       
                 //转账记录
                $sql['transfer_record2'] = '('.$node->mData['id'].',0,'.$source.','.time().',2,'.$data['reward_coin'].','.$detail.',1),';
                // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                
                if( !empty($data['repeat_score']) ){
                    $detail2 = "'转为重复消费积分来自第".$day_check_num."期'";
                    $sql['repeat_record'] = '('.$node->mData['id'].','.$day_check_num.',6,'.$data['repeat_score'].','.time().','.$detail2.',1'.'),';//奖金记录，主键自增不用写 这里拼接好sql字符串，(uid,reward_numer,reward_type,reward_value,reward_time,detail,status)
                    $source = "'重复消费积分入账来自第".$day_check_num."期'";    
                    //转账记录
                    $sql['transfer_record3'] = '('.$node->mData['id'].',0,'.$source.','.time().',8,'.$data['repeat_score'].','.$source.',1),';
                    // (uid,form_uid,source,transfer_time,transfer_type,transfer_value,detail,status)
                }
                
                //清算记录
                $total_reward = $reward - $data['repeat_score'];
                $sql['day_check'] = '('.$node->mData['id'].','.$day_check_num.','.$reward1.','.$reward2.',0,0,0,'.$data['repeat_score'].','.$total_reward.','.time().','.'1),';
                //(uid,check_no,day_reward1,day_reward2,month_rebate,month_bonus,toplimit,repeat_score,total_reward,check_time,status)
                //userid，期号，组织奖，领导奖，消费业绩奖，服务费，封顶，重复消费，奖金总数，来源时间
            }
           
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
                // dump($center_node->mData['id']);
                if ( !empty($center_node)  && isset($center_node->mData['id'])){
                    array_push($visitstack, $center_node); //根节点最后访问
                    // dump($center_node->mData['id']);
                }
                
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