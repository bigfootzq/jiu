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
 * 
 * @author bigfoot
 */
class RewardAdmin extends AdminController {
    
    /**
     * 奖励发放列表
     * @author bigfoot
     */
    public function index() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['reward_number|username|nickname'] = array(
            $condition,
            $condition,
            $condition,
            '_multi'=>true
        );
        
        // 获取所有奖励信息
        $map['jiu_finance_reward.status'] = array('egt', '0'); // 禁用和正常状态
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $reward_object = D('Finance/Reward');
        $base_table   = C('DB_PREFIX').'finance_reward';
        $extend_table = C('DB_PREFIX').'admin_user';
        $data_list = $reward_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('jiu_finance_reward.id asc')
                   ->join($extend_table.' ON '.$base_table.'.uid = '.$extend_table.'.id', 'LEFT')
                   ->select();
        $page = new Page(
            $reward_object
            ->where($map)
            ->join($extend_table.' ON '.$base_table.'.uid = '.$extend_table.'.id', 'LEFT')
            ->count(),
            C('ADMIN_PAGE_ROWS')
        );
        
        $attr2['name']  = '';
        $attr2['title'] = '';
        $attr2['class'] = 'label label-primary';
        $attr2['href']  = U('');
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('奖励发放列表') // 设置页面标题
                ->setSearch('请输入期号/用户名／姓名', U('index'))
                ->addTopButton('self',$attr2)  // 添加占位按钮
                ->addTableColumn('id', 'ID')
                ->addTableColumn('reward_number', '期号')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('username', '用户名')
                ->addTableColumn('reward_type', '奖金类型','callback', array(D('Finance/Reward'), 'get_reward_type'))
                ->addTableColumn('reward_value', '奖金额度')
                ->addTableColumn('reward_time', '发放时间', 'time')
                // ->addTableColumn('status', '状态', 'status')
                // ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataListKey('uid') 
                ->setTableDataPage($page->show()) // 数据列表分页
                // ->addRightButton('edit')
                // ->addRightButton('edit',array('href' => U(MODULE_NAME.'/'.CONTROLLER_NAME.'/edit/', array('uid'=> '__data_id__') )))       // 添加编辑按钮
                // ->addRightButton('forbid',array('title' => '冻结'))        // 添加禁用/启用按钮
                ->display();
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
