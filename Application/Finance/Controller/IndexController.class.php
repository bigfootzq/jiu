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
use Common\Util\Think\Page;
/**
 * 默认控制器
 * @author jry <598821125@qq.com>
 */
class IndexController extends HomeController {
    
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
   
    /**
     * 默认方法
     * @author bigfoot
     */
    public function index() {
        // $this->display();
    }
    
   /*
    *财务流水
    *@bigfoot
    */
    
    public function myAccount(){
        $uid = $this->is_login();
        $map['status'] = array('egt', '0'); // 禁用和正常状态
        $where['uid'] = $uid;
        $where['from_uid'] = $uid;
        $where['_logic'] = 'or'; 
        $map['_complex'] = $where;
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $account_object = D('Finance/Transfer');
        $data_list = $account_object
                       ->page($p , C('ADMIN_PAGE_ROWS'))
                       ->where($map)
                       ->order('transfer_time desc')
                       ->select();
        // echo $account_object->_sql();
        $page = new Page(
                    $account_object
                    ->where($map)
                    ->count(),
                    C('ADMIN_PAGE_ROWS')
                    );
        // echo $account_object->_sql();
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('财务流水') // 设置页面标题
                ->addTableColumn('transfer_time', '时间', 'time')
                ->addTableColumn('transfer_type', '类型')
                ->alterTableData(
                            array('key' => 'transfer_type', 'value' => '1'),
                            array('transfer_type' => '电子币转出'))
                ->alterTableData(
                            array('key' => 'transfer_type', 'value' => '2'),
                            array('transfer_type' => '奖金币转入'))
                ->alterTableData(
                            array('key' => 'transfer_type', 'value' => '3'),
                            array('transfer_type' => '奖金币转换为电子币'))
                ->alterTableData(
                            array('key' => 'transfer_type', 'value' => '4'),
                            array('transfer_type' => '消费奖励积分转入'))
                ->alterTableData(
                            array('key' => 'transfer_type', 'value' => '5'),
                            array('transfer_type' => '消费奖励积分转出'))
                ->alterTableData(
                            array('key' => 'transfer_type', 'value' => '6'),
                            array('transfer_type' => '电子币转入'))
                ->alterTableData(
                            array('key' => 'transfer_type', 'value' => '7'),
                            array('transfer_type' => '重消积分转入'))
                ->alterTableData(
                            array('key' => 'transfer_type', 'value' => '8'),
                            array('transfer_type' => '重消积分转出'))
                ->addTableColumn('source', '来源')
                ->addTableColumn('transfer_value', '出入账金额')
                ->addTableColumn('detail', '备注')
                ->addTableColumn('status', '状态', 'status')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->setTemplate(C('USER_CENTER_LIST'))
                ->display();
    }
    
   /*
    *奖金查询
    *@bigfoot
    */
    
    public function myLiquidate(){
        $uid = $this->is_login();
        $map['status'] = array('egt', '0'); // 禁用和正常状态
        $map['uid'] = $uid;
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $liquidate_object = D('finance_check');
        $data_list = $liquidate_object
                       ->page($p , C('ADMIN_PAGE_ROWS'))
                       ->where($map)
                       ->order('check_time desc')
                       ->select();
        $page = new Page(
                    $liquidate_object
                    ->where($map)
                    ->count(),
                    C('ADMIN_PAGE_ROWS')
                    );
        // echo $user_object->_sql();
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('财务流水') // 设置页面标题
                ->addTableColumn('check_no', '期号')
                ->addTableColumn('day_reward1', '组织奖')
                ->addTableColumn('day_reward2', '领导奖')
                ->addTableColumn('month_rebate', '消费业绩奖')
                ->addTableColumn('month_bonus', '月度分红')
                ->addTableColumn('toplimit', '封顶')
                ->addTableColumn('repeat_score', '重复消费')
                ->addTableColumn('total_reward', '奖金总额')
                ->addTableColumn('check_time', '时间', 'time')
                // ->addTableColumn('status', '状态', 'status')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->setTemplate(C('USER_CENTER_LIST'))
                ->display();
    }
    
   /*
    *奖金明细
    *@bigfoot
    */
    
    public function myReward(){
        $uid = $this->is_login();
        $map['status'] = array('egt', '0'); // 禁用和正常状态
        $map['uid'] = $uid;
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $reward_object = M('finance_reward');
        $data_list = $reward_object
                       ->page($p , C('ADMIN_PAGE_ROWS'))
                       ->where($map)
                       ->order('reward_time desc')
                       ->select();
        $page = new Page(
                    $reward_object
                    ->where($map)
                    ->count(),
                    C('ADMIN_PAGE_ROWS')
                    );
        // echo $user_object->_sql();
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('奖金明细') // 设置页面标题
                ->addTableColumn('reward_number', '期号')
                ->addTableColumn('reward_type', '奖金类型')
                ->alterTableData(
                    //修改奖金类型显示
                            array('key' => 'reward_type', 'value' => '1'),
                            array('reward_type' => '组织奖')
                            // '组织奖'
                            )
                ->alterTableData(
                    //修改奖金类型显示
                            array('key' => 'reward_type', 'value' => '2'),
                            array('reward_type' => '领导奖')
                            // '领导奖'
                            )
                ->alterTableData(
                    //修改奖金类型显示
                            array('key' => 'reward_type', 'value' => '3'),
                            array('reward_type' => '月度返利')
                            // '月度返利'
                            )
                ->alterTableData(
                    //修改奖金类型显示
                            array('key' => 'reward_type', 'value' => '4'),
                            array('reward_type' => '月度分红')
                            // '月度分红'
                            )
                ->alterTableData(
                    //修改奖金类型显示
                            array('key' => 'reward_type', 'value' => '5'),
                            array('reward_type' => '店补')
                            // '店补'
                            )
                ->alterTableData(
                    //修改奖金类型显示
                            array('key' => 'reward_type', 'value' => '6'),
                            array('reward_type' => '重复消费')
                            // ''
                            )
                ->addTableColumn('reward_value', '金额')
                ->addTableColumn('reward_time', '来源时间','time')
                ->addTableColumn('detail', '备注')
                ->addTableColumn('status', '状态', 'status')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->setTemplate(C('USER_CENTER_LIST'))
                ->display();
    }
    
    /*
    *转换：将奖金币转换为电子币
    *@bigfoot
    */
    
    public function transfer(){
        If (IS_POST){
            //检查支付密码
            $paypassword = I('post.paypassword');
            $t_object = D('User/Caiwu');
            $uid = is_login();
            $auth = $t_object->paypassword_auth($paypassword);
            // dump($auth);
            if ($auth){
                //构造转换数组
                $transfer_data['uid'] = $uid ;
                $transfer_data['form_uid'] = $uid ;
                $transfer_data['source'] = '用户'.get_user_name(is_login()).'奖金币转换为电子币，金额'.
                $transfer_data['transfer_value'] = I('post.transfer_value');
                $transfer_data['transfer_type'] = 3;//3为奖金币转电子币
                $transfer_data['transfer_time'] = time();
                $transfer_data['detail'] = I('post.detail');$transfer_data['transfer_value'];
                $transfer_data['status'] = 1;
                //检查转账金额
                $info = get_user_caiwu_info($uid);
                $transfer_data = D('Finance/Transfer')->create($transfer_data);
                if ($transfer_data){
                    // dump($transfer_data);
                    if ( $info['reward_coin'] < $transfer_data['transfer_value'] ){
                        $this->error('转换失败,您输入的转换金额超过了您的奖金币');
                    }
                   
                    $t_value = round($transfer_data['transfer_value'],2);
                    $sql = "UPDATE jiu_user_info
                            SET coin = coin +".$t_value.",
                                reward_coin  = reward_coin -".$t_value."
                            WHERE  uid = ".$uid;
                    $result = M()->execute($sql);
                    //奖金币减少，电子币增加对应金额
                    if ($result){
                        D('Finance/Transfer')->add($transfer_data);//写入转账记录
                        //构造信息数组
                        $msg_data['to_uid'] = $transfer_data['uid'];
                        $msg_data['title']  = '您提交的转换已经成功';
                        $msg_data['content'] = '尊敬的用户您好：<br>'
                                               .'您提交的转换奖金币¥'.$transfer_data['transfer_value'].'转换为对应金额电子币已经成功。<br>'
                                               .'您的奖金币账户减少了¥'.$t_value
                                               .'。<br>'
                                               .'您的电子币账户增加了¥'.$t_value
                                               .'。<br>'
                                               .'备注：'.$transfer_data['detail']
                                               .'<br>';
                        D('User/Message')->sendMessage($msg_data);
                        $this->success('转换成功');
                    }else{
                        $this->error('转换失败');
                    }
                }else{
                    $this->error('转换失败2,'.D('Finance/Transfer')->getError() );
                }
            }else{
                $this->error('支付密码错误');
            }
        }else{
            
            $caiwu = get_user_caiwu_info(is_login());
            $reward['username'] = get_user_name(is_login());
            $reward['reward_coin'] = '￥'.$caiwu['reward_coin'];
            //使用Builder快速建立表单页
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('转换')
            ->addFormItem('username','static','用户名')
            ->addFormItem('reward_coin','static','您现在拥有奖金币')
            ->setFormData($reward)
            ->addFormItem('transfer_value','price','转换金额','必填，注意转换金额不能超过您拥有的奖金币')
            ->addFormItem('detail','textarea','备注','选填')
            ->addFormItem('paypassword', 'password', '支付密码')
            ->setExtraHtml('本页面用于将奖金币转换为电子币')
            ->setTemplate(C('USER_CENTER_FORM'))
            ->display();
        }
    }

    
    
    /*
    *提现: 提取奖金币为现金
    *@bigfoot
    */
    
    public function getMoney(){
        If (IS_POST){
            $paypassword = I('post.paypassword');
            $uid = is_login();
            $t_object = D('User/Caiwu');
            $auth = $t_object->paypassword_auth($paypassword);
            if ($auth){
                $getmoney_object = D('Finance/Getmoney');
                // dump($getmoney_object);
                $gm_data = $getmoney_object->create();
                // dump($gm_data);
                $info = get_user_caiwu_info($uid);
                
                if ($getmoney_object->create()){
                    //判断提现金额是否超过拥有的奖金币。
                    if ( $info['reward_coin'] < $gm_data['value']*1.05 ){
                        $this->error('提现申请失败,您输入的提现金额超过了您的奖金币');
                    }else{
                        $gm_data['uid'] = $uid;
                        $result = $getmoney_object->add($gm_data);
                        $value = round($gm_data['value']*1.05,2);//提现需要扣除5%的手续费
                        $data = array(
                                    'reward_coin' => array('exp', '`reward_coin`-'.$value),
                                    );
                        $t_object->where('uid = %d',$uid)->save($data);
                        if ($result){
                            $msg_data['to_uid'] = $uid;
                            $msg_data['title']  = '您的提现申请已经成功提交';
                            $msg_data['content'] = '尊敬的用户您好：<br>'
                                                   .'您申请提现奖金币¥'.$gm_data['value'].'。<br>'
                                                   .'您的奖金币账户暂时扣除了¥'.$value
                                                   .'，含手续费5%。<br>'
                                                   .'提现开户行：'.$gm_data['bank_name'].'<br>'
                                                   .'提现银行卡号：'.$gm_data['card_no'].'<br>'
                                                   .'提现银行用户名：'.$gm_data['account_name'].'<br>'
                                                   .'备注：'.$gm_data['detail']
                                                   .'<br>';
                            D('User/Message')->sendMessage($msg_data);
                            $this->success('提现申请已经提交，请您耐心等待。');
                        }else{
                            $this->error('提现申请提交失败',$getmoney_object->getError());
                        }
                    }
                }
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $caiwu = get_user_caiwu_info(is_login());
            $reward['username'] = get_user_name(is_login());
            $reward['reward_coin'] = '￥'.$caiwu['reward_coin'];
            //使用Builder快速奖励表单页
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('提现')
            ->addFormItem('username','static','用户名')
            ->addFormItem('reward_coin','static','您现在拥有奖金币')
            ->setFormData($reward)
            ->addFormItem('value','price','提取金额','必填，注意提取金额不能超过您拥有的奖金币，提现将扣除5%的手续费')
            ->addFormItem('bank_name','text','开户行','必填')
            ->addFormItem('card_no','num','银行卡号','必填')
            ->addFormItem('account_name','text','开户名','必填')
            ->addFormItem('detail','textarea','备注','选填')
            ->addFormItem('paypassword', 'password', '支付密码')
            ->setExtraHtml('本页面用于奖金币提现')
            ->setTemplate(C('USER_CENTER_FORM'))
            ->display();
        }
    }

    
    /*
    *转账：将奖金币转让给其他用户
    *@bigfoot
    */
    public function rewardTransfer(){
        If (IS_POST){
            //检查支付密码
            $paypassword = I('post.paypassword');
            $t_object = D('User/Caiwu');
            $auth = $t_object->paypassword_auth($paypassword);
            // dump($auth);
            if ($auth){
                if( !D('Finance/RewardTransfer')->create()){
                    $this->error('转账失败,'.D('Finance/RewardTransfer')->getError());
                }
                //构造转账数组
                $transfer_data['uid'] = get_user_id( I('post.username') );
                $transfer_data['from_uid'] = is_login();
                $transfer_data['transfer_value'] = I('post.transfer_value');
                $transfer_data['transfer_type'] = 2;
                $transfer_data['transfer_time'] = time();
                $transfer_data['detail'] = I('post.detail');
                $transfer_data['source'] = '用户'.get_user_name(is_login()).'转账奖金币至'.I('post.username');
                $transfer_data['status'] = 1;
                //检查转账金额
                $info = get_user_caiwu_info(is_login());
                if ($transfer_data){
                    // dump($transfer_data);
                    if ( $info['reward_coin'] < $transfer_data['transfer_value'] ){
                        $this->error('转账失败,您输入的转账金额超过了您的奖金币');
                    }
                    $result = M('finance_transfer')->add($transfer_data);//写入转账记录
                    $from['uid'] =  is_login();
                    $to['uid'] =  $transfer_data['uid'];
                    $t_object->where($to)->setInc('reward_coin',$transfer_data['transfer_value']);//接受者加奖金币
                    $t_object->where($from)->setDec('reward_coin',$transfer_data['transfer_value']);//转出者扣除对应奖金币
                    if ($result){
                        //构造信息数组
                        $msg_data['to_uid'] = $transfer_data['uid'];
                        $msg_data['title']  = '用户'.get_user_name(is_login()).'向您转账奖金币¥'.$transfer_data['transfer_value'];
                        $msg_data['content'] = '尊敬的用户您好：<br>'
                                               .'用户'.get_user_name(is_login()).'向您转账奖金币¥'.$transfer_data['transfer_value'].'。<br>'
                                               .'您的奖金币账户增加了¥'.$transfer_data['transfer_value']
                                               .'。<br>'
                                               .'留言：'.$transfer_data['detail']
                                               .'<br>';
                        D('User/Message')->sendMessage($msg_data);
                        $this->success('转账成功');
                    }else{
                        $this->success('转账失败');
                    }
                }
            }else{
                $this->error('支付密码错误');
            }
        }else{
            
            $caiwu = get_user_caiwu_info(is_login());
            $reward['username'] = get_user_name(is_login());
            $reward['reward_coin'] = '￥'.$caiwu['reward_coin'];
            //使用Builder快速奖励表单页
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('转账')
            ->addFormItem('username','static','用户名')
            ->addFormItem('reward_coin','static','您现在拥有奖金币')
            ->setFormData($reward)
            ->addFormItem('transfer_value','num','转账金额','必填，注意:转账金额不能超过您现在拥有的奖金币'.$info['reward_coin'])
            ->addFormItem('username','text','接受者用户名','必填')
            ->addFormItem('reusername','text','确认接受者用户名','必填')
            ->addFormItem('detail','textarea','留言','选填')
            ->addFormItem('paypassword', 'password', '支付密码')
            ->setTemplate(C('USER_CENTER_FORM'))
            ->display();
        }
    }
    
    /*
    *消费奖励积分转账：将消费奖励积分转让给其他用户
    *@bigfoot
    */
    public function rewardScoreTransfer(){
        If (IS_POST){
            //检查支付密码
            $paypassword = I('post.paypassword');
            $t_object = D('User/Caiwu');
            $auth = $t_object->paypassword_auth($paypassword);
            // dump($auth);
            if ($auth){
                if( !D('Finance/RewardTransfer')->create()){
                    $this->error('转账失败,'.D('Finance/RewardTransfer')->getError());
                }
                //构造转账数组
                $transfer_data['uid'] = get_user_id( I('post.username') );
                $transfer_data['from_uid'] = is_login();
                $transfer_data['transfer_value'] = I('post.transfer_value');
                $transfer_data['transfer_type'] = 2;
                $transfer_data['transfer_time'] = time();
                $transfer_data['detail'] = I('post.detail');
                $transfer_data['source'] = '用户'.get_user_name(is_login()).'转账消费奖励积分至用户'.I('post.username');
                $transfer_data['status'] = 1;
                //检查转账金额
                $info = get_user_caiwu_info(is_login());
                
                if ($transfer_data){
                    // dump($transfer_data);
                    if ( $info['reward_score'] < $transfer_data['transfer_value'] ){
                        $this->error('转账失败,您输入的转账金额超过了您的消费奖励积分');
                    }
                    $result = M('finance_transfer')->add($transfer_data);//写入转账记录
                    $from['uid'] =  is_login();
                    $to['uid'] =  $transfer_data['uid'];
                    $t_object->where($to)->setInc('reward_score',$transfer_data['transfer_value']);//接受者加消费奖励积分
                    $t_object->where($from)->setDec('reward_score',$transfer_data['transfer_value']);//转出者扣除对应消费奖励积分
                    if ($result){
                        //构造信息数组
                        $msg_data['to_uid'] = $transfer_data['uid'];
                        $msg_data['title']  = '用户'.get_user_name(is_login()).'向您转账消费奖励积分'.$transfer_data['transfer_value'];
                        $msg_data['content'] = '尊敬的用户您好：<br>'
                                               .'用户'.get_user_name(is_login()).'向您转账消费奖励积分'.$transfer_data['transfer_value'].'。<br>'
                                               .'您的消费奖励积分账户增加了¥'.$transfer_data['transfer_value']
                                               .'。<br>'
                                               .'留言：'.$transfer_data['detail']
                                               .'<br>';
                        D('User/Message')->sendMessage($msg_data);
                        $this->success('转账成功');
                    }else{
                        $this->success('转账失败');
                    }
                }
            }else{
                $this->error('支付密码错误');
            }
        }else{
            
            $caiwu = get_user_caiwu_info(is_login());
            $reward['username'] = get_user_name(is_login());
            $reward['reward_score'] = $caiwu['reward_score'];
            //使用Builder快速奖励表单页
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('消费奖励积分转账')
            ->addFormItem('username','static','用户名')
            ->addFormItem('reward_score','static','您现在拥有消费奖励积分')
            ->setFormData($reward)
            ->addFormItem('transfer_value','num','转账金额','必填，注意:转账金额不能超过您现在拥有的消费奖励积分'.$info['reward_coin'])
            ->addFormItem('username','text','接受者用户名','必填')
            ->addFormItem('reusername','text','确认接受者用户名','必填')
            ->addFormItem('detail','textarea','留言','选填')
            ->addFormItem('paypassword', 'password', '支付密码')
            ->setTemplate(C('USER_CENTER_FORM'))
            ->display();
        }
    }
    
    /*
    *中心店转账：开店用户可以将电子币转让给其他用户
    *@bigfoot
    */
    public function coinTransfer(){
        If (IS_POST){
            //检查支付密码
            $paypassword = I('post.paypassword');
            $t_object = D('User/Caiwu');
            $auth = $t_object->paypassword_auth($paypassword);
            // dump($auth);
            if ($auth){
                
                $info = get_user_caiwu_info(is_login());
                if ($info['store_level'] != 3){
                    $this->error('转账失败,您的店铺目前不是中心店');
                }
                if( !D('Finance/CoinTransfer')->create()){
                    $this->error('转账失败,'.D('Finance/CoinTransfer')->getError());
                }
                //构造转账数组
                $transfer_data['uid'] = get_user_id( I('post.username') );
                $transfer_data['from_uid'] = is_login();
                $transfer_data['transfer_value'] = I('post.transfer_value');
                $transfer_data['transfer_type'] = 1;
                $transfer_data['transfer_time'] = time();
                $transfer_data['detail'] = I('post.detail');
                $transfer_data['source'] = '用户'.get_user_name(is_login()).'电子币转账至用户'.I('post.username');
                $transfer_data['status'] = 1;
                
                if ($transfer_data){
                    // dump($transfer_data);
                    //检查转账金额
                    if ( $info['coin'] < $transfer_data['transfer_value'] ){
                        $this->error('转账失败,您输入的转账金额超过了您的电子币');
                    }
                    $result = M('finance_transfer')->add($transfer_data);//写入转账记录
                    $from['uid'] =  is_login();
                    $to['uid'] =  $transfer_data['uid'];
                    $to_info = get_user_caiwu_info($to['uid']);
                    if ($to_info['store_level'] < 3 && $to_info['store_level'] > 0){
                        $t_object->where($from)->setInc('reward_coin',$transfer_data['transfer_value']*0.03);//转出者+奖金币，3%的店补
                        //构造转账数组
                        $transfer_data2['uid'] = $from['uid'];
                        $transfer_data2['from_uid'] = 0;
                        $transfer_data2['transfer_value'] = $transfer_data['transfer_value']*0.03;
                        $transfer_data2['transfer_type'] = 2;
                        $transfer_data2['transfer_time'] = time();
                        $transfer_data2['detail'] = '来自系统的奖金币入账';
                        $transfer_data2['source'] = '店补,比例3%，销售给商务中心'.I('post.username');
                        $transfer_data2['status'] = 1;
                         M('finance_transfer')->add($transfer_data2);
                         //往奖金表里写一条记录
                        $data['uid'] = $from['uid'];
                        $data['reward_number'] = F('check_num')?F('check_num'):1;
                        $data['reward_type'] = 5;
                        $data['reward_value'] = $transfer_data['transfer_value']*0.03;
                        $data['reward_time'] = time();
                        $data['detail'] = '店补,比例3%，销售给商务中心'.I('post.username');
                        $data['status'] = 1;
                        M("finance_reward")->add($data);
                    }
                    $t_object->where($to)->setInc('coin',$transfer_data['transfer_value']);//接受者加电子币
                    $t_object->where($from)->setDec('coin',$transfer_data['transfer_value']);//转出者扣除对应电子币
                    if ($result){
                        //构造信息数组
                        $msg_data['to_uid'] = $transfer_data['uid'];
                        $msg_data['title']  = '用户'.get_user_name(is_login()).'向您转账电子币¥'.$transfer_data['transfer_value'];
                        $msg_data['content'] = '尊敬的用户您好：<br>'
                                               .'用户'.get_user_name(is_login()).'向您转账电子币¥'.$transfer_data['transfer_value'].'。<br>'
                                               .'您的电子币账户增加了¥'.$transfer_data['transfer_value']
                                               .'。<br>'
                                               .'留言：'.$transfer_data['detail']
                                               .'<br>';
                        D('User/Message')->sendMessage($msg_data);
                        $this->success('转账成功');
                    }else{
                        $this->success('转账失败');
                    }
                }
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $caiwu = get_user_caiwu_info(is_login());
            $reward['username'] = get_user_name(is_login());
            $reward['coin'] = '￥'.$caiwu['coin'];
            //使用Builder快速奖励表单页
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('中心店转账')
            ->addFormItem('username','static','用户名')
            ->addFormItem('coin','static','您现在拥有电子币')
            ->setFormData($reward)
            ->addFormItem('transfer_value','num','转账金额','必填，注意转账金额不能超过您拥有的电子币')
            ->addFormItem('username','text','接受者用户名','必填')
            ->addFormItem('reusername','text','确认接受者用户名','必填')
            ->addFormItem('detail','textpro','留言','选填')
            ->addFormItem('paypassword', 'password', '支付密码')
            ->setTemplate(C('USER_CENTER_FORM'))
            ->display();
        }
    }

}