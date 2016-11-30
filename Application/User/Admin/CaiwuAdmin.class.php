<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace User\Admin;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;
/**
 * 用户控制器
 * @author jry <598821125@qq.com>
 */
class CaiwuAdmin extends AdminController {
    /**
     * 用户财务列表
     * @author jry <598821125@qq.com>
     */
    public function index() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id|username|nickname|email|mobile'] = array(
            $condition,
            $condition,
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
        $builder->setMetaTitle('用户财务列表') // 设置页面标题
                ->addTopButton('addnew')  // 添加新增按钮
                ->addTopButton('resume', array('model' => 'admin_user'))  // 添加启用按钮
                ->addTopButton('forbid', array('model' => 'admin_user'))  // 添加禁用按钮
                // ->addTopButton('delete', array('model' => 'admin_user'))  // 添加删除按钮
                ->setSearch('请输入ID/用户名／邮箱／手机号', U('index'))
                ->addTableColumn('uid', 'UID')
                ->addTableColumn('avatar', '头像', 'picture')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('username', '用户名')
                ->addTableColumn('total_expenditure', '总消费额')
                ->addTableColumn('total_score', '总积分')
                ->addTableColumn('coin', '电子币')
                ->addTableColumn('reward_coin', '奖金币')
                ->addTableColumn('reward_score', '奖励积分')
                ->addTableColumn('repeat_score', '重消积分')
                ->addTableColumn('basic_level', '基础级别')
                ->addTableColumn('user_level', '现级别')
                ->addTableColumn('store_level', '店铺级别')
                ->addTableColumn('create_time', '注册时间', 'time')
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
     * 编辑用户
     * @author jry <598821125@qq.com>
     */
    public function edit($uid) {
        if (IS_POST) {

            // 提交数据
            $user_object = D('User/Caiwu');
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
                    ->addFormItem('total_expenditure','num', '总消费额', '总消费额')
                    ->addFormItem('total_score','num', '总积分', '总积分')
                    ->addFormItem('coin', 'num','电子币','电子币')
                    ->addFormItem('reward_coin','num', '奖金币', '奖金币')
                    ->addFormItem('reward_score','num', '奖励积分', '奖励积分')
                    ->addFormItem('repeat_score', 'num','重消积分','重消积分')
                    ->addFormItem('basic_level','select', '基础级别', '基础级别',array(
                                                                       '0'=>'基础会员',
                                                                       '1'=>'代理会员',
                                                                       ))
                    ->addFormItem('level','select', '现级别', '现级别',array(
                                                                       '0'=>'会员',
                                                                       '1'=>'主任',
                                                                       '2'=>'经理',
                                                                       '3'=>'总监',
                                                                       ))
                    ->addFormItem('store_level','select', '店铺级别', '店铺级别',array(
                                                                       '0'=>'无',
                                                                       '1'=>'直营店',
                                                                       '2'=>'经理店',
                                                                       '3'=>'中心店',
                                                                       ))
                    ->setFormData($info)
                    ->display();
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids = I('request.ids');
        if (is_array($ids)) {
            if(in_array('1', $ids)) {
                $this->error('超级管理员不允许操作');
            }
        } else {
            if($ids === '1') {
                $this->error('超级管理员不允许操作');
            }
        }
        parent::setStatus($model);
    }
}
