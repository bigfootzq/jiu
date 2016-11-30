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
class TransferAdmin extends AdminController {
    
    /**
     * 转账信息列表
     * @author bigfoot
     */
    public function index() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['uid|username|nickname'] = array(
            $condition,
            $condition,
            $condition,
            '_multi'=>true
        );
        
        // 获取所有转账信息
        $map['jiu_finance_transfer.status'] = array('egt', '0'); // 禁用和正常状态
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $reward_object = D('Finance/Transfer');
        $base_table   = C('DB_PREFIX').'finance_transfer';
        $extend_table = C('DB_PREFIX').'admin_user';
        $data_list = $reward_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('jiu_finance_transfer.transfer_time desc')
                   ->join($extend_table.' ON '.$base_table.'.uid = '.$extend_table.'.id', 'LEFT')
                   ->select();
        $page = new Page(
            $reward_object
            ->where($map)
            ->join($extend_table.' ON '.$base_table.'.uid = '.$extend_table.'.id', 'LEFT')
            ->count(),
            C('ADMIN_PAGE_ROWS')
        );
        // dump($data_list);
        $attr2['name']  = '';
        $attr2['title'] = '';
        $attr2['class'] = 'label label-primary';
        $attr2['href']  = U('');
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('转账信息列表') // 设置页面标题
                ->addTopButton('self',$attr2)  // 添加占位按钮
                ->setSearch('请输入ID/用户名／姓名', U('index'))
                // ->addTableColumn('id', 'ID')
                ->addTableColumn('uid', 'UID')
                // ->addTableColumn('from_uid', 'FUID')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('username', '用户名')
                ->addTableColumn('source', '来源')
                ->addTableColumn('transfer_value', '转账金额')
                ->addTableColumn('transfer_type', '转账类型','callback', array(D('Finance/Transfer'), 'get_transfer_type'))
                ->addTableColumn('detail', '备注')
                ->addTableColumn('transfer_time', '转账时间', 'time')
                // ->addTableColumn('status', '状态', 'status')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataListKey('uid') 
                ->setTableDataPage($page->show()) // 数据列表分页
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
