<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | Copyright (c) 2016 bigfoot All rights reserved.
// +----------------------------------------------------------------------
// | Author: bigfoot
// +----------------------------------------------------------------------
use Home\Controller\HomeController;
use Common\Util\Think\Page;
/**
 * 每日清算程序
 * 每日销售提成清算：第一项收入：计算下面AB的当日销售额，取较小的10%为销售提成，+奖励币（领导奖励），
 * 第二项收入：获得所有下线的第一项收入的50%。+推荐人奖励币（推荐奖励）
 * 两项收入累积上限为800元。，强制消费，-奖励币 +重复消费积分
 * 清空当日销售额，每日第一项收入，每日第二项收入
**/
    //二叉树后序遍历user_tree
    //首先读取出节点
    class nodeType
    {
      public $left, $right;
     
      public function nodeType( $left = null, $right = null)
      {
         $this->left = &$left;
         $this->right = &$right;
      } 
    }
    $list = D('User/Tree')->select();
    // dump($list);
    foreach ($list as $key => $value){
        $tree =>left = 
    }
   

    echo '后序遍历二叉树算法：';

    postOrderTraverse($arr);

    echo '<Br>';

    function postOrderTraverse($node){

        if(empty($node)){

            return;

        }

        //左节点

        postOrderTraverse($node['lChild']);

        //右节点

        postOrderTraverse($node['rChild']);

        //输出值

        print_r($node['data']);

    }