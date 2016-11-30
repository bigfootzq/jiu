<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace User\Controller;
use Home\Controller\HomeController;
use Common\Util\Think\Page;
use Common\Util\BTNode;
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
     * 用户UID
     * @author jry <598821125@qq.com>
     */
    public function uid() {
        $this->success('已登录', U('User/Center/index'), array('uid' => $this->is_login()));
    }

    /**
     * 用户列表
     * @author jry <598821125@qq.com>
     */
    public function index($user_type = 1) {
        // 获取用户类型的搜索字段
        $user_type_info = D('User/Type')->find($user_type);
        $con = array();
        $con['user_type'] = $user_type;
        $con['id'] = array('in', $user_type_info['list_field']);
        $query_attribute = D('User/Attribute')->where($con)->select();
        foreach ($query_attribute as &$value) {
            $value['options'] = parse_attr($value['options']);

            // 构造搜索条件
            if ($_GET[$value['name']] !== 'all' && $_GET[$value['name']]) {
                switch ($value['type']) {
                    case 'radio':
                        $tmp = $_GET[$value['name']];
                        $map[$value['name']] = $tmp;
                        break;
                    case 'select':
                        $tmp = $_GET[$value['name']];
                        $map[$value['name']] = $tmp;
                        break;
                    case 'checkbox':
                        $tmp = $_GET[$value['name']];
                        $map[$value['name']] = array(
                            'like',
                            array(
                                $tmp,
                                $tmp.',%',
                                '%,'.$tmp.',%',
                                '%,'.$tmp
                            ),
                            'OR'
                        );
                        break;
                }
            }
        }

        // 获取用户基本信息
        $map['status']    = 1;

        // 关键字搜索
        $keyword = I('keyword', '', 'string');
        if ($keyword) {
            $condition = array('like','%'.$keyword.'%');
            $map['id|nickname|username|email|mobile'] = array(
                $condition,
                $condition,
                $condition,
                $condition,
                $condition,
                '_multi'=>true
            );
        }

        // 获取列表
        $map['user_type'] = $user_type;
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $user_object  = D('User/User');
        $base_table   = C('DB_PREFIX').'admin_user';
        $extend_table = C('DB_PREFIX').'user_'.strtolower($user_type_info['name']);
        $user_list = $user_object
                   ->page($p, 16)
                   ->where($map)
                   ->order('id desc')
                   ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
                   ->select();
        $page = new Page(
            $user_object
            ->where($map)
            ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
            ->count(),
            16
        );

        foreach ($user_list as &$val) {
            $val['gender_icon'] = $user_object->user_gender_icon($val['gender']);
        }

        $this->assign('page', $page->show());
        $this->assign('query_attribute', $query_attribute);
        $this->assign('meta_title', '用户');
        $this->assign('user_list', $user_list);
        $this->display();
    }

    /**
     * 用户个人主页
     * @author jry <598821125@qq.com>
     */
    public function home($uid) {
        $user_info = get_user_info($uid);
        
        // 关注信息
        // $user_info['follow_status'] = D('User/Follow')->get_follow_status($uid);

        $user_type_info = D('User/Type')->find($user_info['user_type']);
        if ($user_info['status'] !== '1') {
            $this->error('该用户不存在或已禁用');
        }
        if ($user_type_info['home_template']) {
            $template = $user_type_info['home_template'];
        } else {
            $template = 'home';
        }
        $this->assign('meta_title', $user_info['username'].'的主页');
        $this->assign('user_info', $user_info);
        $this->display($template);
    }
    
    /**
    *  我的市场
    *
    *   @author:bigfoot
    */
    public function  market(){
        $uid = is_login();
        $node = $this->sqlnode($uid);
        $market = $this->visitnlevel($node,6);
        $i = 5;
        while($i){
            if ( empty($market[$i]) ) {
                array_push($market , 0);
            }
            $i--;
        }
        // dump($market);
        $this->assign('meta_title', '我的市场');
        $this->assign('market', $market);
        $this->display();
    }
    
    /**
    *  位置系谱实际上是以我为根节点的二叉树
    *
    *   @author:bigfoot
    */
    public function  mytree(){
        $uid = is_login();
        $node = $this->sqlnode($uid);
        $ids = $this->getPostorderTraversal($node);
        // dump($ids);
        $numdata = array();
        foreach($ids as $v ) {
                    $values = $this->inorder($this->sqlnode($v));//返回每个结点的总结点数、左、右子树节点数，
                    $numdata += $values;
         }
         // dump($values);
         // dump($numdata);
        if($ids) $map['id'] = array('in',$ids);
        $rows = D('User/InfoView')->where($map)->getField('id,id,fid,lid,rid,username,nickname,user_level,position,day_sales,total_market_sales');
        // dump($rows);
        $arr = array();
        foreach($rows as $k=>$r){
            $arr[$k] = array_merge($r,$numdata[$k]);
        }//把结点数合并到信息数组。
        // dump($arr);
        $data = $this->generateTree($arr);
        // dump($data);
        $j_data = json_encode($data,true);
        // echo $j_data;
        // dump(json_encode($mytree));
        $this->assign('mytree', $j_data);
        $this->assign('meta_title', '位置系谱');
        $this->display();
    }
    
   
    
    /**
     * 我的推荐
     * @author bigfoot
     */
    public function promote() {
        $uid = $this->is_login();
        // 获取所有用户
        $map['User.status'] = array('egt', '0'); // 禁用和正常状态
        $map['Info.status'] = array('egt', '0'); // 禁用和正常状态
        $map['Tree.pid'] = $uid;
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $user_object = D('User/InfoView');
        $data_list = $user_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order($base_table.'uid asc')
                   ->select();
        $page = new Page(
            $user_object
            ->where($map)
            ->count(),
            C('ADMIN_PAGE_ROWS')
        );
        // dump($data_list);
        // echo $user_object->_sql();
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('我的推荐') // 设置页面标题
                ->addTableColumn('id', 'UID')
                ->addTableColumn('username', '用户名')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('fid', '安置人','callback', 'get_user_name')
                ->addTableColumn('position', '位置')
                ->addTableColumn('user_level', '用户级别','callback', array(D('User/Index'), 'get_user_level'))
                ->addTableColumn('create_time', '注册时间', 'time')
                ->addTableColumn('start_time', '激活时间', 'time')
                ->addTableColumn('info_status', '状态', 'status')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->setTemplate(C('USER_CENTER_LIST'))
                ->display();
    }
    
    /**
     * 用户协议
     * @author jry <598821125@qq.com>
     */
    public function agreement() {
        $this->assign('meta_title', '用户协议');
        $this->display();
    }
    
    /**
     *访问某节点下n层节点
     *
     *@param node 树中节点
     *@param maxlevel 最大访问层数
     *@return BTNode
     */
    public function visitnlevel($node, $maxlevel){
        // dump($node);
        // dump($maxlevel);
        $topdepth=$node->mDepth;
        // dump($topdepth);
        $stack = array();
        $visitstack = array();
        array_push($stack, $node);
        // dump($stack);
        $data[0] = 0;
        while(!empty($stack)){
            $center_node = array_pop($stack);
            // array_push($visitstack, $center_node->mData['id']);
            if ( ($center_node->mDepth - $topdepth ) < ($maxlevel-1)) { //只有第maxlevel-1层以内才会访问下一级节点
                if($center_node->mRchild != 0 ) {
                    $tempnode = $this->sqlnode($center_node->mData['rid']);
                    // $center_node->mRchild = $tempnode;
                    // $tempnode->mFather =$center_node;
                    $tempnode->mDepth = $center_node->mDepth+1;
                    array_push($stack, $tempnode);
                    $data[$center_node->mDepth]++;
                }
                if($center_node->mLchild != 0){
                    
                    $tempnode = $this->sqlnode($center_node->mData['lid']);
                    // $center_node->mLchild = $tempnode;
                    // $tempnode->mFather =$center_node;
                    $tempnode->mDepth = $center_node->mDepth+1;
                    array_push($stack, $tempnode);
                    $data[$center_node->mDepth]++;
                }
                
                // dump($center_node->mDepth.':id：'.$center_node->mData['id'].'，节点个数:'.$data[$center_node->mDepth]);
            }
            
        }
        return $data;
    }
    
    /**
         *数据库查询并构造节点返回
         *
         *@param int 数据库中节点的id，如果0则取根节点
         *@return BTNode
         */
        public function sqlnode($nodeid){
            $BTNode = new BTNode();
            if ($nodeid == 0){
                // return $this->sqlnode(2);
                return ;
            }else{
                // dump($nodeid);
                // $sql = "select id,user_level,total_score,position,fid, pid, lid,rid, day_sales,day_reward1,day_reward2,month_rebate from `jiu_user_tree` join `jiu_user_info` on jiu_user_info.uid = jiu_user_tree.id where id = ".$nodeid;
                $sql = "select id, pid, lid,rid from `jiu_user_tree`  where id = ".$nodeid;
                $tree_data = M()->query($sql);
                // dump($tree_data);
                if ($tree_data){
                    $BTNode->mLchild = $tree_data['0']['lid'];
                    $BTNode->mRchild = $tree_data['0']['rid'];
                    $BTNode->mData = $tree_data['0'];
                    $BTNode->mFather = $tree_data['0']['fid'];
                    $BTNode->mDepth = 0;
                    // dump($BTNode->mData['id']);
                    // dump($BTNode);
                    return $BTNode;
                }
            }
        }
        /**
         *中序遍历的非递归算法计算结点数，左子树结点数，右子树节点数
         *
         *@param BTNode $objNode 二叉树根节点
         *
         *@return array
         */
        function inorder($node){
            $stack = array();
            $total = 0;
            $data = array();
            $center_node = $node;
            while (!empty($stack) || $center_node != null) {
                     while ($center_node != null) {
                         array_push($stack, $center_node);
                         $center_node = $this->sqlnode($center_node->mLchild);
                     }
         
                     $center_node = array_pop($stack);
                     if ($center_node != $node){
                         $total++;
                         
                     }else if ($center_node == $node){
                         $left = $total;
                     }
                  
                     // echo $center_node->mData['id'] . " ";
         
                     $center_node = $this->sqlnode($center_node->mRchild);
                 }
                 $right = $total -$left;
                 // dump($total);
                 // dump($left);
                 // dump($right);
                 $data[$node->mData['id']]['id'] = $node->mData['id'] ;
                 $data[$node->mData['id']]['total'] = $total ;
                 $data[$node->mData['id']]['left'] = $left ;
                 $data[$node->mData['id']]['right'] = $right ;
                 // dump($data);
                 return $data;
                 
        }
        /**
         *后序遍历的非递归算法
         *
         *@param BTNode $objRootNode 二叉树根节点
         *@param array $arrBTdata 接收值的数组变量，按引用方式传递
         *@return void
         */
        public function getPostorderTraversal($node){
            // dump($root);
            $pushstack = array(); //临时栈，存放待搜索节点
            $visitstack = array(); //访问栈，存最终访问顺序的节点
            array_push($pushstack, $node);
            // dump($pushstack);
            while (!empty($pushstack)) {
                $center_node = array_pop($pushstack); //最后入栈节点出栈
                array_push($visitstack, $center_node->mData['id']); //根节点最后访问
                // array_push($visitstack, $center_node); //根节点最后访问
                 // dump($center_node->mData['id']);
                 // dump($center_node->mData['user_level']);
                // dump($pushstack);
                // dump($visitstack);
                // //找左右子节点，如有则生成节点，并建立引用，放到临时栈
                if ( !empty($center_node->mLchild) ) {
                                $tempnode = $this->sqlnode($center_node->mData['lid']);
                                $center_node->mLchild = $tempnode;
                                $tempnode->mFather =$center_node;

                                array_push($pushstack, $tempnode);
                                }
                if ( !empty($center_node->mRchild) ) {
                            $tempnode = $this->sqlnode($center_node->mData['rid']);
                            $center_node->mRchild = $tempnode;
                            $tempnode->mFather =$center_node;

                            array_push($pushstack, $tempnode);
                        }
            }
            // dump($visitstack);
            return $visitstack; //储存访问栈
            // exit();
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
                
                if($item['fid'] == '0'){
                    $items[$item['id']]['category'] = 2;//根节点
                }else if($item['lid'] == '0' && $item['rid'] == '0'){
                    $items[$item['id']]['category'] = 0;//叶子节点
                    $items[$item['id']]['symbol'] = 'rectangle';
                }else if($item['lid'] == '0' || $item['rid'] == '0'){
                    $items[$item['id']]['category'] = 1;//非叶子节点
                    $items[$item['id']]['symbol'] = 'diamond';
                }else{
                    $items[$item['id']]['category'] = 1;
                }
                if(isset($items[$item['fid']])){
                    
                    // $items[$item['fid']]['children'][] = &$items[$item['id']];
                    if ($items[$item['fid']]['lid'] != 0)
                        $items[$item['fid']]['children'][0] = &$items[$items[$item['fid']]['lid']];
                
                    if ($items[$item['fid']]['rid'] != 0)
                        $items[$item['fid']]['children'][1] = &$items[$items[$item['fid']]['rid']];
                    // dump($items[$item['fid']]);
                }else{
                    $tree[] = &$items[$item['id']];
                }
            }
            // dump($tree);
            return $tree;
        }
}
