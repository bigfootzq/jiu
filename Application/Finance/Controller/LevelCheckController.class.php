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
use Common\Util\MTNode;
/**
 * 每日级别清算控制器
 * @author long bigfoot
 */
class LevelCheckController extends HomeController {
       //根结点
        public $mRoot;
        
        public $mNodeArray=array();
        
        //后序访问栈
        public $mMTStack;

        /**
         *构造方法,初始化建立二叉树
         *
         *
         *@return void
         */
        public function __construct(){
            parent::__construct();
            $this->mNodeArray=$this->getNodearrayById(); //查询数据库返回所有用户节点，数组下标＝用户id
            $this->getPostorderTraversal($this->mNodeArray);
        }
        
        /*
        * 默认函数
        *
        */
        public function index(){
            // echo 'test';
            // $this->display();
        }
       
        /**
         *月清算，利用前面建立的访问栈 mMTStack
         *
         *@return void
         */
        public function levelcheck(){
            $visitstack = $this->mMTStack;
            // dump($visitstack);
            //级别遍历，更新会员级别，
                while (!empty($visitstack)) {
                    $center_node = array_pop($visitstack);
                    $values = $this->levelupdate($center_node); //更新该会员级别
                    if (!empty($values)){
                        $user_level .= $values['user_level'];
                        // dump($values);
                    }
                }
                
                $user_level  = rtrim($user_level , ",");
                // dump($user_level);
               
                if(!empty($user_level)){
                    $sql2 = "insert into `jiu_user_info` (uid,`user_level`) values ".$user_level."on duplicate key update user_level = values(user_level)";
                    M()->execute($sql2);
                    // echo M()->_sql();
                }
                // 最后清空director_num,manager_num
                $clearsql = "update jiu_user_tree set director_num = 0 , manager_num = 0";
                // M()->execute($clearsql);
            
           
        }
      
        
        /**
         *级别每日遍历，更新节点对应会员积分，级别，更新回数据中
         *
         *@param int 数据库中节点的id，如果0则取根节点
         *@return BTNode
         */
        public function levelupdate($node){
            dump($node->mData['id']);
            $info_object = D('User/Caiwu');
            $tree_object = D('User/Tree');
            $score = $node->mData['total_score'];
            // $node->mData['director_num'] = 0;
            // $node->mData['manager_num'] = 0;
            
             //找子节点并全部累加
            if ($node->mChildnum != 0) { //有子节点
                foreach ($node->mChild as &$cnode)  {
                    $node->mData['director_num'] += $cnode->mData['director_num'];
                    $node->mData['manager_num'] += $cnode->mData['manager_num'];
                }
            }
            
            dump($node->mData['director_num']);
            dump($node->mData['manager_num']);
             
            if ( ( $node->mData['manager_num'] >= 3) && ( $node->mData['user_level'] <7) ) { //下面有3个经理为总监，已经是总监级别不减
                // dump($node->mData['id']);
                $node->mData['user_level'] = 7;
                $node->mData['manager_num']=0; //自己是总监，为独立市场，经理数清零，自己算一个经理
                $info_object->where('uid = %d',$node->mData['id'])->setField('user_level',7);
                $tree_object->where('id = %d',$node->mData['id'])->setField('manager_num',0);
            } else if ( ($node->mData['director_num'] >= 2) && ($node->mData['user_level']<6) ) { //下面有2个主任为经理，已经是经理级别不减
                // dump($node->mData['id']);
                // dump($node->mData['user_level']);
                // dump($node->mData['store_level']);
                $node->mData['user_level'] = 6;
                $node->mData['manager_num']++; //自己是经理，经理数＋1
                $node->mData['director_num'] = 0; //自己是经理，为独立市场，主任数清零
                $tree_object->where('id = %d',$node->mData['id'])->setInc('manager_num');
                $tree_object->where('id = %d',$node->mData['id'])->setField('director_num',0);
                if ($node->mData['store_level'] == '1'){// 如果已经开店且店铺为社区店，提升为经理店。
                    $info_object->where('uid = %d',$node->mData['id'])->setField('user_level',6);
                    $info_object->where('uid = %d',$node->mData['id'])->setField('store_level',2);
                }
            } else if ($score >= 15000 && ($node->mData['user_level'] < 5) ) { //主任
                
                // dump($node->mData['id']);
                $node->mData['user_level'] = 5;
                $node->mData['director_num']++; //加主任数
                $tree_object->where('id = %d',$node->mData['id'])->setInc('director_num');
            } else if ($score >= 7001 && ($node->mData['user_level'] < 4) ) {
                $node->mData['user_level'] = 4;
            } else if ($score >= 2001 && ($node->mData['user_level'] < 3) ) { 
                $node->mData['user_level'] = 3;
            } else if ($score >= 401 && ($node->mData['user_level'] < 2) ) { 
                $node->mData['user_level'] = 2;
            } 
            // else if ($score >= 1 && ($node->mData['user_level'] = 1) ) { 
                // $node->mData['user_level'] = 1;
            // }
            
                // dump($node->mData['user_level']);
                $sql['user_level'] = '('.$node->mData['id'].','.$node->mData['user_level'].'),';// 这里拼接好sql字符串，(id,user_level)
             return $sql;
        }
        
        
        /**
         *查询数据库返回所有用户节点，数组下标＝用户id
         *
         *@param int 数据库中节点的id，如果0则取根节点
         *@return array()
         */
        public function getNodearrayById(){
            $rows = D('User/tree')->getField('id,id');
            foreach ($rows as $key=>$value){
                $rows[$key] = $this->sqlMTNode($key);
            }
            // dump($rows);
            return $rows;
        }
            
         /**
         *数据库查询并构造节点返回
         *
         *@param int 数据库中节点的id，如果0则取根节点
         *@return BTNode
         */
        public function sqlMTNode($nodeid){
            $MTNode = new MTNode();
            if ($nodeid == 0){
                // return $this->sqlnode(2);
                return $MTNode;
            }else{
                // dump($nodeid);
                $sql = "select id, user_level,store_level,total_score,month_sales,month_repeat_score,position, pid, lid,director_num,manager_num,rid, day_sales,day_reward1,day_reward2,month_rebate from `jiu_user_tree` join `jiu_user_info` on jiu_user_info.uid = jiu_user_tree.id where id = ".$nodeid;
                $tree_data = M()->query($sql);
                // dump($tree_data);
                if ($tree_data){
                    $MTNode->mData = $tree_data['0'];
                    return $MTNode;
                }
            }
        }
        
        /**
         *后序遍历的非递归算法，建立多叉树和访问栈
         *
         *@param $dataArray 多叉树节点数组，按引用方式传递
         *@return void
         */
        public function getPostorderTraversal($dataArray){
            // dump($dataArray);
            //建立多叉树
            foreach ($dataArray as &$node) {
                // dump($node);
                if ($node->mData['pid'] == null || $node->mData['pid'] == 0 || $node->mData['pid'] == 1 ) {
                    $mRoot = $node;
                } else {
                    $dataArray[$node->mData['pid']]->addChild($node);
                    // dump($dataArray[$node->mData['pid']]);
                    $node->mFather=$dataArray[$node->mData['pid']];
                }
            }
            // dump($mRoot);
            //后序遍历
            $pushstack = array(); //临时栈，存放待搜索节点
            $visitstack = array(); //访问栈，存最终访问顺序的节点
            $mRoot->mDepth = 0;
            array_push($pushstack, $mRoot);

            while (!empty($pushstack)) {
                $center_node = array_pop($pushstack); //最后入栈节点出栈
                if ( !empty($center_node)  && isset($center_node->mData['id'])){
                    array_push($visitstack, $center_node); //父节点最后访问
                    // dump($center_node->mData['id']);
                }
                //找子节点并全部入栈
                if ($center_node->mChildnum != 0) { //还有子节点
                    foreach ($center_node->mChild as &$node)  {
                        $node->mDepth = $center_node->mDepth+1;
                        array_push($pushstack, $node);
                    }
                }
            }
            $this->mMTStack=$visitstack; //储存访问栈
            
            // dump($visitstack);
        }
}