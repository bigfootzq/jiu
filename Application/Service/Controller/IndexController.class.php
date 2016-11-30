<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Service\Controller;
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
    }
   
    /**
     * 默认方法
     * @author bigfoot
     */
    public function index() {
        $this->assign('meta_title', '服务中心');
        $this->display();
    }
    
    /**
     * 我的报单
     * @author bigfoot
     */
    public function declaration () {
        $uid  = $this->is_login();
        // $map['jiu_user_info.status'] = array('egt', '0'); // 禁用和正常状态
        $map['sid'] = $uid;
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $d_object = D('User/InfoView');
        // dump($d_object);
        $data_list = $d_object
                       ->page($p , C('ADMIN_PAGE_ROWS'))
                       ->where($map)
                       ->order('create_time desc')
                       ->select();
        $page = new Page(
                    $d_object
                    ->where($map)
                    ->count(),
                    C('ADMIN_PAGE_ROWS')
                    );
        // dump($data_list);
        
        $marketing = C('marketing.1');
        $basic_value = $marketing['BASIC_VALUE'];
        $html = "<h4>注意：每激活一个新会员自动从您的电子币账户扣除￥".$basic_value."</h4>";
        //自定义按钮激活
        $attr2['name']  = 'start';
        $attr2['title'] = '激活';
        $attr2['class'] = 'label label-primary ajax-get confirm';
        $attr2['href']  = U('start', array('ids' => '__data_id__'));
        //自定义按钮顶部批量激活
         $attr3['title'] = '激活';
         $attr3['class'] = 'btn btn-primary ajax-post';
         $attr3['href']  = U('Service/Index/start');
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('我的报单') // 设置页面标题
        ->addTopButton('self', $attr3)  // 添加批量激活按钮
        ->setExtraHtml($html)
        ->addTableColumn('id', 'ID')
        ->addTableColumn('username', '用户名')
        ->addTableColumn('nickname', '姓名')
        ->addTableColumn('pid', '推荐人','callback','get_user_name')
        ->addTableColumn('fid', '安置人','callback', 'get_user_name')
        ->addTableColumn('position', '位置')
        ->addTableColumn('user_level', '级别','callback', array(D('User/Index'), 'get_user_level'))
        ->addTableColumn('basic_level', '金额','callback', array(D('User/Index'), 'get_basic_value'))
        ->addTableColumn('create_time', '注册时间', 'time')
        ->addTableColumn('start_time', '激活时间', 'time')
        ->addTableColumn('info_status', '状态', 'status')
        ->addTableColumn('right_button', '操作', 'btn')
        ->addrightButton('self',$attr2)    // 添加自定义按钮激活
        ->setTableDataList($data_list)    // 数据列表
        ->setTableDataPage($page->show()) // 数据列表分页
        ->setTemplate(C('USER_CENTER_LIST'))
        ->display();
                

    }

    /**
     * 汇款通知
     * remittance advice
     * @author bigfoot
     */
    public function remittance_advice () {
        $uid  = $this->is_login();
        if (IS_POST) {
            //检查支付密码
            $paypassword = I('post.paypassword');
            $auth = D('User/Caiwu')->paypassword_auth($paypassword);
            if (!$auth){
                $this->error('支付密码错误！');
            }else{
                    $data = I('post.');
                    // dump($data);
                    
                    $remit_object = D('Finance/Remit');
                    // dump($remit_object);
                    // dump($remit_object->create($data) );
                    if( $remit_object->create() ){
                       
                        $result = $remit_object->add();
                        if($result){
                            $this->success('汇款通知已经送达！');
                        }else{
                            $this->error('汇款通知发送失败！'.$remit_object->getError(),U('remittance_advice'));
                        }
                    }else{
                        $this->error('汇款通知发送失败！'.$remit_object->getError());
                    }
            }
            
        } else {
            $coin = D('User/Caiwu')->find($uid);
            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('汇款通知') // 设置页面标题
                    ->setPostUrl(U(''))        // 设置表单提交地址
                    ->addFormItem('uid', 'hidden')
                    ->addFormItem('coin', 'static', '电子币余额')
                    ->addFormItem('bank_name', 'text', '开户行')
                    ->addFormItem('card_no', 'text', '银行卡号')
                    ->addFormItem('account_name', 'text', '开户名')
                    ->addFormItem('value', 'num', '汇款金额')
                    ->addFormItem('remit_pic', 'picture', '汇款单', '汇款单图片')
                    ->addFormItem('detail', 'textarea', '备注')
                    ->addFormItem('paypassword', 'password', '支付密码')
                    ->setFormData($coin)
                    ->setExtraHtml('为了区分您的汇款记录，请在汇款时在金额后加小数，比如0.03、0.12 ')
                    ->setAjaxSubmit(false)
                    ->setTemplate(C('USER_CENTER_FORM'))
                    ->display();
        }
    }
    
    /*
     * 激活，将user_info,user_tree的status置为1，从注册用户的店铺手中转账390电子币给该用户
     * @author:bigfoot
     */
    public function start(){
        $ids    = I('request.ids');
        // dump($ids);
        if (empty($ids)) {
            $this->error('请选择要激活的会员');
        }
        $map['uid'] = array('in',$ids);
        $info_object = D('User/Caiwu');
        $start_info = $info_object->where($map)->getField('uid,uid,start_time');
        // echo $info_object->_sql();
        // dump($start_info);
        foreach ($start_info as $value){
            if ($value['start_time'] == '0'){
                $start_info2[] = $value;
            }
        }
        // dump($start_info2);
        
        if (!empty($start_info2)) {//检查是否激活
            $data['status'] = 1;
            $data['start_time'] = time();
            $coin =$info_object->where('uid = %d',is_login())->getField('coin');
            // dump($coin);
            $marketing = C('marketing.1');
            $basic_value = $marketing['BASIC_VALUE'];
            if ( $coin < $basic_value * count($start_info2) ){
                $this->error('您的电子币不足，请充值');
            }else{
                
                //+日销售额+月销售额+总销售额+不加积分
                if (is_array($start_info2)){
                        foreach ($start_info2 as $uid){
                            D('Shop/Index')->addSales($uid['uid'],$basic_value,2);
                            $info_object->where('uid = %d',$uid['uid'])->setField($data);//激活
                        }
                }
                // else{
                    // D('Shop/Index')->addSales($ids,$basic_value,2);
                // }
                $data2['coin'] = $coin - $basic_value * count($start_info2);//店铺减去对应金额
                $id2 = $info_object->where('uid = %d',is_login())->setField($data2); 
                 if ( $id2){
                        //构造转账数组
                        $transfer_data['uid'] = is_login();
                        $transfer_data['from_uid'] = 0;
                        $transfer_data['transfer_value'] = 390;
                        $transfer_data['transfer_type'] = 1;
                        $transfer_data['transfer_time'] = time();
                        $transfer_data['detail'] = '';
                        $transfer_data['status'] = 1;
                        //写转账记录
                        if (is_array($start_info2)){
                            foreach ($start_info2 as $uid){
                                $transfer_data['source'] = '激活用户'.get_user_name($uid['uid']).'扣除电子币';
                                M('finance_transfer')->add($transfer_data);
                            }
                        }
                        // else{
                            // $transfer_data['source'] = '激活用户'.get_user_name($ids).'扣除电子币';
                            // M('finance_transfer')->add($transfer_data);
                        // }
                        $this->success('会员激活成功,-'.($basic_value * count($start_info2)).'电子币');
                    }else{
                        $this->error('会员激活失败');
                }
            }
            
        } else {
            $this->error('会员已经激活'.D('User/Caiwu')->getError());
        }
       
    }


}