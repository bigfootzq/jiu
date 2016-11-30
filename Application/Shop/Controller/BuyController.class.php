<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Shop\Controller;
use Home\Controller\HomeController;
use Common\Util\Think\Page;
/**
 * 购物控制器
 * @author bigfoot
 */
class BuyController extends HomeController {
    
     /**
     * 初始化方法
     * @author jry 
     */
    protected function _initialize(){
        parent::_initialize();
        $this->is_login();
        $this->is_visit();
        $this->is_disable();
    }
    public function cart(){
        $buy = I('post.');
        // dump($buy);
        
    
        
        $total_price = 0;
        // dump($buy_id);
        foreach($buy['buy'] as $key => $value){
            if( empty($value['number'])){
                unset($buy['buy'][$key]);

            }
            
            $total_price += (int)$value['price'] * (int)$value['number'];

        }
        $detail = $buy['buy'];
        $this->assign('info', $buy);
        $this->assign('jsoninfo', json_encode($buy));
        $this->assign('total_price', $total_price);
        $this->assign('goods_list', $detail);
        $this->display('cart');
    }
    
    /**
     * 主动消费购物
     * 电子币购物
     * @author  bigfoot
     */
    public function coinBuy() {
        if (IS_POST){
            $buy = I('post.');
            // dump($buy);
            $total_price = 0;
            $pay_id = is_login();
            $buy_id = get_user_id($buy['buy_username']);
            $shop_id = get_user_id($buy['shop_username']);
            $coin = get_user_caiwu_info( $pay_id );
            // dump($buy_id);
            foreach($buy['buy'] as $key => $value){
                if( empty($value['number'])){
                    unset($buy['buy'][$key]);

                }
                
                $total_price += (int)$value['price'] * (int)$value['number'];

            }
            $detail = $buy['buy'];
            if( empty($detail) ){
                $detail2 = false;
            }else{
                $detail2 = json_encode($detail);
            }
            // dump($detail);
            // dump($total_price);

            if ($total_price > $coin['coin']){
                $this->error('您所购买的商品总金额超过了您拥有的电子币，请重新购买!');
            }else{
                
                //构造订单信息数组
                $order_data['no'] = create_out_trade_no();
                $order_data['order_type'] = 1;
                $order_data['value'] = $total_price;
                $order_data['buy_id'] = $buy_id;
                $order_data['pay_id'] = $pay_id;
                $order_data['shop_id'] = $shop_id;
                $order_data['delivery_type'] = $buy['delivery_type'];
                $order_data['address'] = $buy['address'];
                $order_data['buy_fullname'] = $buy['buy_fullname'];
                $order_data['mob'] = $buy['mob'];
                $order_data['order_detail'] = $detail2;
                $order_data['create_time'] = time();
                $order_data['pay_status'] = 1;
                $order_data['status'] = 1;
                // dump($order_data);
                $order_data2 = D('Shop/Order')->create($order_data);
                if (!$order_data2){
                    $this->error('订单提交失败,'.D('Shop/Order')->getError());
                }else{
                    //记录订单信息
                    $order_info = D('Shop/Order')->add($order_data2);
                    
                    //主动消费购物 -付款人电子币（或者付款人的奖励币）
                    $sql = 'UPDATE `jiu_user_info` SET `coin`= coin -'.$total_price.' WHERE ( uid = '.$order_data['pay_id'].' )';
                    $result1 = M()->execute($sql);
                    // dump($result1);
                    //减库存
                    foreach($detail as $key => $value){
                        M('shop_goods')->where('id = %d',$value['id'])->setDec('number',$value['number']);
                    
                    }
                    //购物人返22%，安置人返20%，但是我们按照0.11,0.10算，购物的时候再乘以二
                    $reward_score = D('Shop/Index')->addRewardScore($buy_id,$total_price);
                    //积分为购物金额的66%
                    //+购物人的本月销售额、+购物人的当日销售额
                    // +购物人的累积销售额、累积积分
                    $result2 = D('Shop/Index')->addSales($buy_id,$total_price);
                    // dump($result2);
                    //商务中心获得店补，根据级别不同返5%,5%，8%
                    $result3 = D('Shop/Index')->addShopReward($shop_id,$total_price);
                    if ($result1 !==false && $result2 !==false && $order_info !==false){
                        //构造消息数组
                        $marketing = C('marketing.1');
                        $reward_score1 = $marketing['REWARD_SCORE1']/100;//本人返利消费奖励积分比例0.22
                        $reward_score2 = $marketing['REWARD_SCORE2']/100;//接点人返利比例0.20
                        $msg_data['to_uid'] = $buy_id;
                        $msg_data['title']  = '主动消费购物成功';
                        $msg_data['content'] = '尊敬的用户您好：<br>'
                                               .'您的购物订单编号为'.$order_data['no'].', <a href="'.U('Shop/Order/detail',array('id'=>$order_info),'',true).'">点击查看详情</a>。<br>'
                                               .'本次购物金额：¥ '.$order_data['value'].'。<br>'
                                               .'您的消费奖励积分增加了'.round($total_price*$reward_score1/2,2)
                                               .'。<br>'
                                               .'您的接点人消费奖励积分增加了'.round($total_price*$reward_score2/2,2)
                                               .'。<br>';
                        D('User/Message')->sendMessage($msg_data);
                        $this->success("购买成功！",U('coinBuy'));
                    }else{
                        $this->error('购买失败');
                    }
                }
            }
        }else{
            // 搜索
            // $keyword   = I('keyword', '', 'string');
            // $condition = array('like','%'.$keyword.'%');
            // $map['no|name'] = array(
                // $condition,
                // $condition,
                // '_multi'=>true
            // );
            
            // dump(C('marketing.1'));
            // 获取所有商品
            $map['status'] = array('egt', '1'); // 正常状态
            $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
            $goods_object = D('Shop/Index');
            // dump($goods_object);
            $goods_list = $goods_object
                       // ->page($p , C('ADMIN_PAGE_ROWS'))
                       ->where($map)
                       ->order('sort asc ,id asc')
                       ->select();
            $page = new Page(
                $goods_object->where($map)->count(),
                C('ADMIN_PAGE_ROWS')
            );
            // dump($goods_list);
                $this->assign('goods_list',$goods_list);
                // $this->assign('page',$page->show());
                $this->assign('meta_title', '主动消费购物');
                $this->display();
            }       
    }
    
    /**
     * 消费奖励积分购物
     * 购买物品返回的20%|22%积分购物
     * @author bigfoot
     */
    public function rewardScoreBuy() {
        
        if (IS_POST){
            $buy = I('post.');
            // dump($buy);
            $total_price = 0;
            $pay_id = is_login();
            $buy_id = $pay_id;
            $shop_id = get_user_id($buy['shop_username']);
            $coin = get_user_caiwu_info( $pay_id );
                
            foreach($buy['buy'] as $key => $value){
                if( empty($value['number'])){
                    unset($buy['buy'][$key]);

                }
                $total_price += (int)$value['price'] * (int)$value['number'];

            }
            $detail = $buy['buy'];
            if( empty($detail) ){
                $detail2 = false;
            }else{
                $detail2 = json_encode($detail);
            }
            // dump($total_price);

            if ($total_price > $coin['reward_score']*2 ){
                $this->error('您所购买的商品总金额超过了您拥有的消费奖励积分，请重新购买!');
            }else{
                //构造订单信息数组
                $order_data['no'] = create_out_trade_no();
                $order_data['order_type'] = 2;
                $order_data['value'] = $total_price;
                $order_data['buy_id'] = $buy_id;
                $order_data['pay_id'] = $pay_id;
                $order_data['shop_id'] = $shop_id;
                $order_data['delivery_type'] = $buy['delivery_type'];
                $order_data['address'] = $buy['address'];
                $order_data['buy_fullname'] = $buy['buy_fullname'];
                $order_data['mob'] = $buy['mob'];
                $order_data['order_detail'] = $detail2;
                $order_data['create_time'] = time();
                $order_data['pay_status'] = 1;
                $order_data['status'] = 1;
                $order_data2 = D('Shop/Order')->create($order_data);
                if (!$order_data2){
                    $this->error('订单提交失败,'.D('Shop/Order')->getError());
                }else{
                    //记录订单信息
                    $order_info = D('Shop/Order')->add($order_data2);
                    //减库存
                    foreach($detail as $key => $value){
                        M('shop_goods')->where('id = %d',$value['id'])->setDec('number',$value['number']);
                    
                    }
                    //奖励积分购物 只影响用户自己参数 -奖励积分 无销售额无积分
                    //构造sql
                    $reward_score = round($total_price/2,2);
                    $sql = 'UPDATE `jiu_user_info` SET `reward_score`= reward_score-'.$reward_score.' WHERE ( uid = '.$order_data['pay_id'].' )';
                    //是否加商务中心消费奖励积分，待定
                    $sql2 = 'UPDATE `jiu_user_info` SET `reward_score`= reward_score+'.$reward_score.' WHERE ( uid = '.$order_data['shop_id'].' )';
                   
                    $result = M()->execute($sql);
                    $result = M()->execute($sql2);
          
                    // echo M()->_sql();
                    if ($result && $order_info){
                        //构造消息数组
                        $msg_data['to_uid'] = $buy_id;
                        $msg_data['title']  = '奖励消费积分购物成功';
                        $msg_data['content'] = '尊敬的用户您好：<br>'
                                               .'您本次购物订单编号为'.$order_data['no'].', <a href="'.U('Shop/Order/detail',array('id'=>$order_info),'',true).'">点击查看详情</a>。<br>'
                                               .'本次购物金额：¥ '.$order_data['value'].'。<br>'
                                               .'您的消费奖励积分账户减少了 '.$reward_score
                                               .'。<br>';
                        D('User/Message')->sendMessage($msg_data);
                        $this->success("购买成功！",U('rewardScoreBuy'));
                    }else{
                        $this->error('购买失败',$caiwu_object->getError());
                    }
                }
            }
        }else{
            // 搜索
            // $keyword   = I('keyword', '', 'string');
            // $condition = array('like','%'.$keyword.'%');
            // $map['no|name'] = array(
                // $condition,
                // $condition,
                // '_multi'=>true
            // );
            $user_info = get_user_info(is_login());
            // dump($user_info);
            // 获取所有商品
            $map['status'] = array('egt', '1'); // 正常状态
            $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
            $goods_object = D('Shop/Index');
            // dump($goods_object);
            $goods_list = $goods_object
                       // ->page($p , C('ADMIN_PAGE_ROWS'))
                       ->where($map)
                       ->order('sort asc ,id asc')
                       ->select();
            $page = new Page(
                $goods_object->where($map)->count(),
                C('ADMIN_PAGE_ROWS')
            );
            // dump($page->show());
            // dump($page);
                $this->assign('user_info',$user_info);
                $this->assign('goods_list',$goods_list);
                // $this->assign('page',$page->show());
                $this->assign('meta_title', '消费奖励积分购物');
                $this->display();
            }       
    }
    
    /**
     * 重复消费积分购物
     * 每月盈利的10%必须重复消费，根据级别不同，有上限
     * @author bigfoot
     */
    public function repeatScoreBuy() {
        if (IS_POST){
            // $buy = I('post.');
            $buy = $_POST;
            // dump($buy);
            $total_price = 0;
            $pay_id = is_login();
            $buy_id = get_user_id($buy['buy_username']);
            $shop_id = get_user_id($buy['shop_username']);
            $coin = get_user_caiwu_info( $id );
                
            foreach($buy['buy'] as $key => $value){
                if( empty($value['number'])){
                    unset($buy['buy'][$key]);

                }else{
                    M('shop_goods')->where('id = %d',$value['id'])->setDec('number',$value['number']);
                }
                $total_price += (int)$value['price'] * (int)$value['number'];

            }
            $detail = $buy['buy'];
            if( empty($detail) ){
                $detail2 = false;
            }else{
                $detail2 = json_encode($detail);
            }
            // dump($total_price);

            
            if ($total_price > $coin['repeat_score']){
                $this->error('您所购买的商品总金额超过了您拥有的重复消费积分，请重新购买!');
            }else{
                
                //构造订单信息数组
                $order_data['no'] = create_out_trade_no();
                $order_data['order_type'] = 3;
                $order_data['value'] = $total_price;
                $order_data['buy_id'] = $buy_id;
                $order_data['pay_id'] = $pay_id;
                $order_data['shop_id'] = $shop_id;
                $order_data['delivery_type'] = $buy['delivery_type'];
                $order_data['address'] = $buy['address'];
                $order_data['buy_fullname'] = $buy['buy_fullname'];
                $order_data['mob'] = $buy['mob'];
                $order_data['order_detail'] = $detail2;
                $order_data['create_time'] = time();
                $order_data['pay_status'] = 1;
                $order_data['status'] = 1;
                
                $order_data2 = D('Shop/Order')->create($order_data);
                if (!$order_data2){
                    $this->error('订单提交失败,'.D('Shop/Order')->getError());
                }else{
                    //记录订单信息
                    $order_info = D('Shop/Order')->add($order_data2);
                    //减库存
                    foreach($detail as $key => $value){
                        M('shop_goods')->where('id = %d',$value['id'])->setDec('number',$value['number']);
                    }
                    //扣除付款人的重复消费积分
                    $sql = 'UPDATE `jiu_user_info` SET `repeat_score`= repeat_score -'.$total_price.' WHERE ( uid = '.$order_data['pay_id'].' )';
                    //是否加商务中心重复消费积分,
                    $sql2 = 'UPDATE `jiu_user_info` SET `repeat_score`= repeat_score +'.$total_price.' WHERE ( uid = '.$order_data['shop_id'].' )';
                    $result1 = M()->execute($sql);
                    $result1 = M()->execute($sql2);
                    
                    //购物人返22%，安置人返20%，但是我们按照0.11,0.10算，购物的时候再乘以二
                    // $reward_score = D('Shop/Index')->addRewardScore($buy_id,$total_price);
                    //积分为购物金额的100%
                    //+购物人的本月销售额、+购物人的当日销售额
                    // +购物人的累积销售额、累积积分
                    $result2 = D('Shop/Index')->addSales($buy_id,$total_price,1);
                    //商务中心获得店补
                    $result3 = D('Shop/Index')->addShopReward($shop_id,$total_price);
                    if ($result1 && $result2 && $order_info){
                        //读取分销体系配置
                        $marketing = C('marketing.1');
                        $reward_score1 = $marketing['REWARD_SCORE1']/100;//本人返利消费奖励积分比例0.22
                        $reward_score2 = $marketing['REWARD_SCORE2']/100;//接点人返利比例0.20
                        //构造消息数组
                        $msg_data['to_uid'] = $buy_id;
                        $msg_data['title']  = '重复消费购物成功';
                        $msg_data['content'] = '尊敬的用户您好：<br>'
                                               .'您本次购物订单编号为'.$order_data['no'].', <a href="'.U('Shop/Order/detail',array('id'=>$order_info),'',true).'">点击查看详情</a>。<br>'
                                               .'本次购物金额：¥ '.$order_data['value'].'。<br>'
                                               .'您的重复消费账户减少了 '.$order_data['value']
                                               .'。<br>'
                                               .'您的消费奖励积分增加了'.round($total_price*$reward_score1/2,2)
                                               .'。<br>'
                                               .'您的接点人消费奖励积分增加了'.round($total_price*$reward_score2/2,2)
                                               .'。<br>';
                        D('User/Message')->sendMessage($msg_data);
                        $this->success("购买成功！",U('repeatScoreBuy'));
                    }else{
                        $this->error('购买失败,'.$caiwu_object->getError());
                    }
                }
            }
        }else{
            // 搜索
            // $keyword   = I('keyword', '', 'string');
            // $condition = array('like','%'.$keyword.'%');
            // $map['no|name'] = array(
                // $condition,
                // $condition,
                // '_multi'=>true
            // );
            $user_info = get_user_info(is_login());
            // 获取所有商品
            $map['status'] = array('egt', '1'); // 正常状态
            $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
            $goods_object = D('Shop/Index');
            // dump($goods_object);
            $goods_list = $goods_object
                       // ->page($p , C('ADMIN_PAGE_ROWS'))
                       ->where($map)
                       ->order('sort asc ,id asc')
                       ->select();
            $page = new Page(
                $goods_object->where($map)->count(),
                C('ADMIN_PAGE_ROWS')
            );
                $this->assign('user_info',$user_info);
                $this->assign('goods_list',$goods_list);
                // $this->assign('page',$page->show());
                $this->assign('meta_title', '重复消费积分购物');
                $this->display();
            }       
    }

    
}