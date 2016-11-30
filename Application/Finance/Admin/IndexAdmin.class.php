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
use Common\Util\MTNode;
/**
 * 
 * @author bigfoot
 */
class IndexAdmin extends AdminController {
    
    /**
     * 默认方法
     * @author bigfoot
     */
    public function index() {
        
    }
    
    /*
     * 汇款信息列表 remittanceAdviceList
     * @author bigfoot
     */
     public function remit(){
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['bank_name|account_name'] = array(
            $condition,
            $condition,
            '_multi'=>true
        );
        $status = I('get.status');
        if(isset($status) && $status != ''){
           $map['status'] = $status; 
        }else{
            $map['status'] = array('egt', '0'); // 禁用和正常状态
        }
        
        // 获取所有汇款信息
        
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $remit_object = M('finance_remittance');
        $data_list = $remit_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id desc')
                   ->select();
        $page = new Page(
            $remit_object
            ->where($map)
            ->count(),
            C('ADMIN_PAGE_ROWS')
        );
         $attr['name']  = 'forbid';
         $attr['title'] = '处理';
         $attr['class'] = 'label label-success ajax-get confirm';
         $attr['data-toggle'] = 'modal';
         // $attr['href']  = U('Caiwu/coinedit', array('uid' => '__data_id__'));
         $attr['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus',
                            array(
                                'status' => 'resume',
                                'ids' => '__data_id__',
                                'model' => 'finance_remittance')
                            );
        //自定义按钮顶部批量处理
         $attr2['title'] = '处理';
         $attr2['class'] = 'btn btn-primary ajax-post confirm';
         $attr2['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus',
                            array(
                                'status' => 'resume',
                                'model' => 'finance_remittance')
                            );
         // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('汇款通知列表') // 设置页面标题
                ->addTopButton('delete', array('model' => 'finance_remittance'))  // 添加删除按钮
                ->addTopButton('self', $attr2)
                ->setSelectSearch('status','处理状态',
                                    array( '0'=>'未处理',
                                    '1'=>'已处理' ))   
                ->setSearch('请输入开户行／姓名', U('remit'))
                ->addTableColumn('id', 'ID')
                ->addTableColumn('bank_name', '开户行')
                ->addTableColumn('card_no', '银行卡号')
                ->addTableColumn('account_name', '开户名')
                ->addTableColumn('uid', '汇款人用户名','callback','get_user_name')
                ->addTableColumn('value', '汇款金额')
                ->addTableColumn('remit_pic', '汇款通知单','picture')
                ->addTableColumn('detail', '备注')
                ->addTableColumn('create_time', '汇款时间', 'time')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->addRightButton('delete',array('model' => 'finance_remittance'))
                ->addRightButton('self', $attr)   
                ->display();
     }
     
     /*
     * 提现申请列表 remittanceAdviceList
     * @author bigfoot
     */
     public function getMoney(){
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['uid|account_name'] = array(
            get_user_id($condition),
            $condition,
            '_multi'=>true
        );
        $status = I('get.status');
        if(isset($status) && $status != ''){
           $map['status'] = $status; 
        }else{
            $map['status'] = array('egt', '0'); // 禁用和正常状态
        }
        
        // 获取所有提现信息
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $money_object = M('finance_getmoney');
        $data_list = $money_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id desc')
                   ->select();
        $page = new Page(
            $money_object
            ->where($map)
            ->count(),
            C('ADMIN_PAGE_ROWS')
        );
         $attr['name']  = 'forbid';
         $attr['title'] = '处理';
         $attr['class'] = 'label label-success ajax-get confirm';
         // $attr['href']  = U('setstatus/status/forbid', array('id' => '__data_id__'));
         $attr['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus',
                            array(
                                'status' => 'resume',
                                'ids' => '__data_id__',
                                'model' => 'finance_getmoney')
                            );
         //自定义按钮顶部批量处理
         $attr2['title'] = '处理';
         $attr2['class'] = 'btn btn-primary ajax-post confirm';
         $attr2['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus',
                            array(
                                'status' => 'resume',
                                'model' => 'finance_getmoney')
                            );
         // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('提现申请表') // 设置页面标题
                ->addTopButton('delete', array('model' => 'finance_getmoney'))  // 添加删除按钮
                ->addTopButton('self', $attr2)  // 添加处理按钮
                ->setSelectSearch('status','处理状态',
                                    array( '0'=>'未处理',
                                    '1'=>'已处理' )) 
                ->setSearch('请输入用户名／开户名', U('getmoney'))
                ->addTableColumn('id', 'ID')
                ->addTableColumn('uid', '用户名','callback','get_user_name')
                ->addTableColumn('bank_name', '开户行')
                ->addTableColumn('card_no', '银行卡号')
                ->addTableColumn('account_name', '开户名')
                ->addTableColumn('value', '金额')
                ->addTableColumn('detail', '备注')
                ->addTableColumn('create_time', '申请时间', 'time')
                ->addTableColumn('remit_time', '处理时间', 'time')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->addRightButton('delete',array('model' => 'finance_getmoney'))
                ->addRightButton('self', $attr)   
                ->display();
     }
     
      /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids = I('request.ids');
        $status = I('request.status');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        $map['id'] = array('in',$ids);
        if ($status == 'resume'){
            $exist = D($model)->where($map)->getField('status');
            // dump($exist);
            if ($exist != 1) {//检查是否已经处理
                $data['status'] = 1;
                $data['remit_time'] = time();
                $delivery = D($model)->where($map)->setField($data);
            } else {
                $this->error('信息已经处理');
            }
            if ($delivery){
                $this->success('处理成功');
            }else{
                $this->error('处理失败');
            }
        }else{
            parent::setStatus($model);
        }
        
    }
    
     /**
    *   以推荐关系形成的多叉树
    *
    *   @author:bigfoot
    */
    public function  promotetree(){
        // $mNodeArray = $this->getNodearrayById(); //查询数据库返回所有用户节点，数组下标＝用户id
        // $ids =  $this->getPostorderTraversal($mNodeArray);
        // dump($ids);
        // if($ids) $map['id'] = array('in',$ids);
        $rows = D('User/InfoView')->getField('id,id,pid,fid,lid,rid,username,nickname,user_level,position,day_sales,total_market_sales');
        // dump($rows);
        $data = $this->generateTree($rows);
        // dump($data);
        $j_data = json_encode($data,true);
        // echo $j_data;
        // dump(json_encode($mytree));
        $this->assign('promotetree', $j_data);
        $this->assign('meta_title', '推荐树');
        $this->display();
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
                    array_push($visitstack, $center_node->mData['id']); //父节点最后访问
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
            return $visitstack; //储存访问栈
            
            // dump($visitstack);
        }
        
        public function generateTree($items){
            // dump($items);
            $tree = array();
            foreach($items as $item){
                // dump($items[$items[$item['fid']]['lid']]);
                $items[$item['id']]['value'] = $item['user_level'];
                $items[$item['id']]['name'] = $item['nickname'];
                    // dump($item);
                // $items[$item['id']]['tooltip'] = $item;
                
                
                if(isset($items[$item['pid']])){
                    
                    $items[$item['pid']]['children'][] = &$items[$item['id']];
                    // if ($items[$item['fid']]['lid'] != 0)
                        // $items[$item['fid']]['children'][0] = &$items[$items[$item['fid']]['lid']];
                
                    // if ($items[$item['fid']]['rid'] != 0)
                        // $items[$item['fid']]['children'][1] = &$items[$items[$item['fid']]['rid']];
                    // dump($items[$item['fid']]);
                }else{
                    $tree[] = &$items[$item['id']];
                }
            }
            // dump($tree);
            return $tree;
        }

}
