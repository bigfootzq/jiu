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
 * 用户控制器
 * @author jry <598821125@qq.com>
 */
class CaiwuAdmin extends AdminController {
    /**
     * 用户财务列表
     * @author bigfoot
     */
    public function index() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id|username|nickname'] = array(
            $condition,
            $condition,
            $condition,
            '_multi'=>true
        );
        //获取所有管理组用户id
        $admin_arr = D('Admin/Access')->getField('uid',true);
        // 获取所有用户
        $map['jiu_admin_user.status'] = array('egt', '0'); // 禁用和正常状态
        $map['id'] = array('not in',$admin_arr);//排除所有管理组用户
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $user_object = D('User/User');
        $base_table   = C('DB_PREFIX').'admin_user';
        $extend_table = C('DB_PREFIX').'user_info';
        $data_list = $user_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id asc')
                   ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
                   ->select();
        $page = new Page(
            $user_object
            ->where($map)
            ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
            ->count(),
            C('ADMIN_PAGE_ROWS')
        );

        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('用户列表') // 设置页面标题
                ->addTopButton('resume', array('model' => 'user_info'))  // 添加启用按钮
                ->addTopButton('forbid', array('model' => 'user_info'))  // 添加禁用按钮
                // ->addTopButton('delete', array('model' => 'user_info'))  // 添加删除按钮
                ->setSearch('请输入ID/用户名／姓名', U('index'))
                ->addTableColumn('uid', 'UID')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('username', '用户名')
                ->addTableColumn('total_sales', '总消费额')
                ->addTableColumn('total_score', '总积分')
                ->addTableColumn('coin', '电子币')
                ->addTableColumn('reward_coin', '奖金币')
                ->addTableColumn('reward_score', '奖励积分')
                ->addTableColumn('repeat_score', '重消积分')
                ->addTableColumn('user_level', '用户级别','callback', array(D('User/Index'), 'get_user_level'))
                ->addTableColumn('store_level', '店铺级别','callback', array(D('User/Index'), 'get_store_level'))
                ->addTableColumn('start_time', '激活时间', 'date')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataListKey('uid') 
                ->setTableDataPage($page->show()) // 数据列表分页
                ->addRightButton('edit')
                // ->addRightButton('edit',array('href' => U(MODULE_NAME.'/'.CONTROLLER_NAME.'/edit/', array('uid'=> '__data_id__') )))       // 添加编辑按钮
                ->addRightButton('forbid',array('title' => '冻结'))        // 添加禁用/启用按钮
                ->display();
    }
    /**
     * 用户电子币列表
     * @author bigfoot
     */
    public function coin() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id|username|nickname'] = array(
            $condition,
            $condition,
            $condition,
            '_multi'=>true
        );
        //获取所有管理组用户id
        $admin_arr = D('Admin/Access')->getField('uid',true);
        // 获取所有用户
        $map['jiu_admin_user.status'] = array('egt', '0'); // 禁用和正常状态
        $map['id'] = array('not in',$admin_arr);//排除所有管理组用户
        $map['store_level'] = array('gt',0);//所有店铺用户，store_level>0
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $user_object = D('User/User');
        $base_table   = C('DB_PREFIX').'admin_user';
        $extend_table = C('DB_PREFIX').'user_info';
        $data_list = $user_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id asc')
                   ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
                   ->select();
        $page = new Page(
            $user_object
            ->where($map)
            ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
            ->count(),
            C('ADMIN_PAGE_ROWS')
        );
        $attr['name']  = 'coinedit';
        $attr['title'] = '修改';
        $attr['class'] = 'label label-primary';
        $attr['href']  = U('coinedit', array('uid' => '__data_id__'));
        
        $attr2['name']  = '';
        $attr2['title'] = '';
        $attr2['class'] = 'label label-primary';
        $attr2['href']  = U('');
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('电子币管理') // 设置页面标题
                ->setSearch('请输入ID/用户名／姓名', U('coin'))
                ->addTopButton('self',$attr2)  // 添加占位按钮
                ->addTableColumn('uid', 'UID')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('username', '用户名')
                ->addTableColumn('coin', '电子币')
                ->addTableColumn('start_time', '激活时间', 'date')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataListKey('uid') 
                ->setTableDataPage($page->show()) // 数据列表分页
                ->addrightButton('self',$attr)    // 添加自定义按钮修改
                ->display();
    }
    /**
     * 用户店铺列表
     * 用户开店，店铺升级
     * @author bigfoot
     */
    public function shop() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id|username|nickname'] = array(
            $condition,
            $condition,
            $condition,
            '_multi'=>true
        );
        //获取所有管理组用户id
        $admin_arr = D('Admin/Access')->getField('uid',true);
        // 获取所有用户
        $map['jiu_admin_user.status'] = array('egt', '0'); // 禁用和正常状态
        $map['id'] = array('not in',$admin_arr);//排除所有管理组用户
        // $map['store_level'] = array('gt',0);//所有店铺用户，store_level>0
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $user_object = D('User/User');
        $base_table   = C('DB_PREFIX').'admin_user';
        $extend_table = C('DB_PREFIX').'user_info';
        $data_list = $user_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id asc')
                   ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
                   ->select();
        $page = new Page(
            $user_object
            ->where($map)
            ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
            ->count(),
            C('ADMIN_PAGE_ROWS')
        );
        $attr['name']  = 'shopedit';
        $attr['title'] = '修改';
        $attr['class'] = 'label label-primary';
        $attr['href']  = U('shopedit', array('uid' => '__data_id__'));
        
        $attr2['name']  = '';
        $attr2['title'] = '';
        $attr2['class'] = 'label label-primary';
        $attr2['href']  = U('');
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('店铺管理') // 设置页面标题
                ->setSearch('请输入ID/用户名／姓名', U('shop'))
                ->addTopButton('self',$attr2)  // 添加占位按钮
                ->addTableColumn('uid', 'UID')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('username', '用户名')
                ->addTableColumn('store_level', '店铺级别','callback', array(D('User/Index'), 'get_store_level'))
                ->addTableColumn('start_time', '激活时间', 'date')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataListKey('uid') 
                ->setTableDataPage($page->show()) // 数据列表分页
                ->addrightButton('self',$attr)    // 添加自定义按钮修改
                ->display();
    }


    /**
     * 编辑用户
     * @author jry <598821125@qq.com>
     */
    public function edit($uid) {
        if (IS_POST) {

            // 提交数据
            $user_object = D('Finance/Caiwu');
            $data = $user_object->create();
            if ($data) {
                $result = $user_object
                        ->where('uid = %d',$uid)
                        ->field('uid,total_expenditure,total_score,coin,reward_coin,reward_score,repeat_score,basic_level,user_level,store_level')
                        ->save($data);
                if ($result) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败', $user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            // 获取账号信息
            $info = D('User/Caiwu')
                    ->where('uid = %d',$uid)
                    ->find();
            // unset($info['password']);

            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('编辑用户')  // 设置页面标题
                    ->setPostUrl(U('edit'))    // 设置表单提交地址
                    ->addFormItem('uid', 'hidden', 'ID', 'ID')
                    ->addFormItem('total_sales','num', '总消费额', '总消费额')
                    ->addFormItem('total_score','num', '总积分', '总积分')
                    ->addFormItem('coin', 'num','电子币','电子币')
                    ->addFormItem('reward_coin','num', '奖金币', '奖金币')
                    ->addFormItem('reward_score','num', '奖励积分', '奖励积分')
                    ->addFormItem('repeat_score', 'num','重消积分','重消积分')
                    ->addFormItem('level','select', '现级别', '现级别',array(
                                                                       '0'=>'会员',
                                                                       '1'=>'主任',
                                                                       '2'=>'经理',
                                                                       '3'=>'总监',
                                                                       ))
                    ->addFormItem('store_level','select', '店铺级别', '店铺级别',array(
                                                                       '0'=>'无',
                                                                       '1'=>'社区店',
                                                                       '2'=>'经理店',
                                                                       '3'=>'中心店',
                                                                       ))
                    ->setFormData($info)
                    ->display();
        }
    }
    
    /**
     * 编辑用户电子币信息
     * @author jry <598821125@qq.com>
     */
    public function coinedit($uid) {
        if (IS_POST) {

            // 提交数据
            $user_object = D('Finance/Caiwu');
            $oldcoin = $user_object->getFieldByUid(I('post.uid'),'coin');
            // dump($oldcoin);
            $data = $user_object->create();
            if ($data) {
                // dump($data);
                $result = $user_object
                        ->save($data);
                // dump($result);
                if ($result) {
                    if ($data['coin'] != $oldcoin){
                        if ($data['coin'] > $oldcoin){
                            $type = 6;
                            $source = '来自系统的电子币入账';
                        }else{
                            
                            $source = '来自系统的电子币出账';
                            $type = 1;
                        }
                        //写流水表
                        //构造转账数组
                        $transfer_data['uid'] = I('post.uid') ;
                        $transfer_data['from_uid'] = 0;
                        $transfer_data['source'] = $source ;
                        $transfer_data['transfer_value'] = $data['coin'] - $oldcoin;
                        $transfer_data['transfer_type'] = $type;
                        $transfer_data['transfer_time'] = time();
                        $transfer_data['detail'] = $source ;
                        $transfer_data['status'] = 1;
                        // dump($transfer_data);
                        if(D('Finance/TransferAdd')->create($transfer_data)){
                             if ($transfer_data['transfer_value'] != 0){
                                // dump($transfer_data);
                                D('Finance/TransferAdd')->add($transfer_data);  
                                // echo D('Finance/TransferAdd')->_sql();
                            }
                        }else{
                             $this->error(D('Finance/TransferAdd')->getError());
                        }
                    }
                    $this->success('修改成功',U('coin'));
                } else {
                    $this->error('修改失败1,'.$user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            // 获取账号信息
            $info = D('User/Caiwu')
                    ->where('uid = %d',$uid)
                    ->find();
            // unset($info['password']);

            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('编辑用户电子币')  // 设置页面标题
                    ->setPostUrl(U('coinedit'))    // 设置表单提交地址
                    ->addFormItem('uid', 'hidden', 'ID', 'ID')
                    ->addFormItem('coin', 'num','电子币','电子币')
                    ->setFormData($info)
                    ->display();
        }
    }

    /**
     * 编辑用户店铺信息
     * @author bigfoot
     */
    public function shopedit($uid) {
        if (IS_POST) {

            // 提交数据
            $user_object = D('Finance/Caiwu');
            $data = $user_object->create();
            if ($data) {
                // dump($data);
                // if ( $data['store_level'] == 1){
                    // $data['user_level'] = 5;
                // }else if($data['store_level'] == 2){
                    // $data['user_level'] = 6;
                // }else if($data['store_level'] == 3){
                    // $data['user_level'] = 7;
                // }
                $result = $user_object
                        ->where('uid = %d',$uid)
                        ->field('uid,user_level')
                        ->save($data);
                // dump($result);
                if ($result) {
                    $this->success('修改成功',U('shop'));
                } else {
                    $this->error('修改失败', $user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            // 获取账号信息
            $info = D('User/Caiwu')
                    ->where('uid = %d',$uid)
                    ->find();
            // unset($info['password']);

            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('店铺管理')  // 设置页面标题
                    ->setPostUrl(U('shopedit'))    // 设置表单提交地址
                    ->addFormItem('uid', 'hidden', 'ID', 'ID')
                    ->addFormItem('store_level', 'select','店铺等级','无',
                    array(  0 => '无',
                            1 => '主任店'  ,
                            2 => '经理店' ,
                            3 => '中心店' ) )
                    ->setFormData($info)
                    ->display();
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * 
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids = I('request.ids');
        parent::setStatus($model);
    }
}
