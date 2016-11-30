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
class CheckAdmin extends AdminController {
    
    /**
     * 清算信息列表
     * @author bigfoot
     */
    public function index() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['uid|username|nickname|check_no'] = array(
            $condition,
            $condition,
            $condition,
            $condition,
            '_multi'=>true
        );
        // 获取所有清算信息
        $map['jiu_finance_check.status'] = array('egt', '0'); // 禁用和正常状态
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $reward_object = M('finance_check');
        $base_table   = C('DB_PREFIX').'finance_check';
        $extend_table = C('DB_PREFIX').'admin_user';
        $data_list = $reward_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('jiu_finance_check.check_time desc')
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
        $builder->setMetaTitle('清算信息列表') // 设置页面标题
                ->addTopButton('self',$attr2)  // 添加占位按钮
                ->setSearch('请输入ID/用户名／姓名/期号', U('index'))
                ->addTableColumn('check_no', '期号')
                ->addTableColumn('uid', 'UID')
                // ->addTableColumn('from_uid', 'FUID')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('username', '用户名')
                ->addTableColumn('day_reward1', '组织奖')
                ->addTableColumn('day_reward2', '领导奖')
                ->addTableColumn('month_rebate', '月返利')
                ->addTableColumn('month_bonus', '月分红')
                ->addTableColumn('toplimit', '封顶')
                ->addTableColumn('repeat_score', '重消积分')
                ->addTableColumn('total_reward', '总金额')
                ->addTableColumn('check_time', '清算时间','date')
                // ->addTableColumn('status', '状态', 'status')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->display();
    }
    // //导出CSV文件
    // public function export(){
        // if(IS_GET) {
        
        // // 获取所有清算信息
        // $map['jiu_finance_check.status'] = array('egt', '0'); // 禁用和正常状态
        // $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        // $reward_object = M('finance_check');
        // $data_list = $reward_object
                   // ->page($p , C('ADMIN_PAGE_ROWS'))
                   // ->where($map)
                   // ->order('jiu_finance_check.check_no desc')
                   // ->getField('check_no,check_time');
        // $page = new Page(
            // $reward_object
            // ->where($map)
            // ->count(),
            // C('ADMIN_PAGE_ROWS')
        // );
        // // echo $reward_object->_sql();
        // // dump($data_list);
        // $this->assign('data_list',$data_list);
        // $this->assign('page',$page);
        // }else{
            // $today = strtotime(date('Y-m-d', time())); //今天
            // // $start_date = I('post.start_date') ? strtotime(I('post.start_date')) : $today-14*86400;
            // // $end_date   = I('post.end_date') ? (strtotime(I('post.end_date'))+1) : $today+86400;
            // $start_date = strtotime(I('post.start_date')) ;
            // $end_date   = strtotime(I('post.end_date'))+86399;
            // $count_day  = ($end_date-$start_date)/86400; //查询最近n天
            // // dump($start_date);
            // // dump($end_date);
            // if(!empty($start_date) && isset($start_date) && $end_date && isset($end_date)  ){
                
                // if ( $start_date > $end_date ) {
                // $this->error('请注意结束日期必须大于等于开始日期',U('Check/Index'));
                // }else if ( $start_date == $end_date ){
                    // $filter['check_time'] = array( array('egt',$start_date),
                                                // array('elt',$start_date+86399));
                // }else{
                    // // dump($end_date);
                    // $filter['check_time'] = array(  array('egt',$start_date),
                                                    // array('elt',$end_date)
                                                  // );
                // }
            
            
            // // dump($filter);
            // $res = M('finance_check');
            // $base_table   = C('DB_PREFIX').'finance_check';
            // $extend_table = C('DB_PREFIX').'admin_user';
            // $z = $res->where($filter)->select();
            // $reward_person_count = $res->where($filter)->count('day_reward1');
            // // echo $res->_sql();
            // // dump($z);
            // // dump($reward_person_count);
            // if( !$z || empty($z) ){
                        // // $this->error('您查询的日期并无清算数据',U('Check/export'));
                        // exit;
                // } else {
                        // $order = array(
                                        // 'check_no'=>'asc',
                                        // 'uid'=>'asc'
                                        // );
                        // $rlist = $res->order($order)->join($extend_table.' ON '.$base_table.'.uid = '.$extend_table.'.id', 'LEFT')->where($filter);
                        // // echo $res->_sql();
                        // // dump($rlist);
                        // $headerArr = array(  
                                            // 'check_no'=>'期号',
                                            // 'uid'=>'会员编号', 
                                             // 'username' =>'用户名',
                                             // 'nickname' =>'姓名',
                                            // 'day_reward1'=>'组织奖', 
                                            // 'day_reward2'=>'领导奖', 
                                            // 'month_rebate'=>'月度返利', 
                                            // 'month_bonus'=>'月度分红', 
                                            // 'toplimit'=>'封顶', 
                                            // 'repeat_score'=>'重消积分', 
                                            // 'total_reward'=>'总金额', 
                                            // 'check_time'=>'清算时间'
                                            // );
                            // // dump($headerArr);
                            // $callBack = function( $list ) {
                                    // //对数据的二次处理;
                                    // // dump($list);
                                    // foreach ($list as $k=>$v){
                                        // // dump($v);
                                        // $list[$k]['check_time'] = date("Y-m-d",$v['check_time']);
                                    // }
                                    // return $list;
                            // };
                            // $this->downloadCSV( $rlist, $headerArr, 'checkreport_'.$date.date('YmdHi',time()), $callBack );
                // }
            // }
        
        // }
        // $this->display();
    // }
    
    //导出CSV文件
    public function export(){
        if(IS_POST) {
        
        }else{
            // 获取所有清算信息
            $map['jiu_finance_check.status'] = array('egt', '0'); // 禁用和正常状态
            $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
            $reward_object = M('finance_check');
            $data_list = $reward_object
                       ->page($p , C('ADMIN_PAGE_ROWS'))
                       ->where($map)
                       ->order('jiu_finance_check.check_no desc')
                       ->distinct(true)->field('check_no')
                       ->select();
                       // ->getField('check_no,check_time',true);
           
            // echo $reward_object->_sql();
            $page = new Page(
                        $reward_object
                        ->where($map)
                        ->count('distinct check_no'),
                        C('ADMIN_PAGE_ROWS')
                    );
            // echo $reward_object->_sql();
            // dump($data_list);
            // dump($page);
            $this->assign('data_list',$data_list);
            $this->assign('page',$page->show());
        }
        $this->display();
    }
    public function exportcsv(){
            $map = I('get.');
            // dump($map);
            if(!empty($map['id'])){
                $filter['check_no'] = $map['id'];
            }
            if(!empty($map['username'])){
                $filter['uid'] = get_user_id($map['username']);
            }
          
            // dump($filter);
            $res = M('finance_check');
            $base_table   = C('DB_PREFIX').'finance_check';
            $extend_table = C('DB_PREFIX').'admin_user';
            $z = $res->where($filter)->select();
            $reward_person_count = $res->where($filter)->count('day_reward1');
            // echo $res->_sql();
            // dump($z);
            // dump($reward_person_count);
            if( !$z || empty($z) ){
                        $this->error('您查询的日期并无清算数据',U('Check/export'));
                        exit;
                } else {
                        $order = array(
                                        'check_no'=>'asc',
                                        'uid'=>'asc'
                                        );
                        $rlist = $res->order($order)->join($extend_table.' ON '.$base_table.'.uid = '.$extend_table.'.id', 'LEFT')->where($filter);
                        // echo $res->_sql();
                        // dump($rlist);
                        $headerArr = array(  
                                            'check_no'=>'期号',
                                            'uid'=>'会员编号', 
                                             'username' =>'用户名',
                                             'nickname' =>'姓名',
                                            'day_reward1'=>'组织奖', 
                                            'day_reward2'=>'领导奖', 
                                            'month_rebate'=>'月度返利', 
                                            'month_bonus'=>'月度分红', 
                                            'toplimit'=>'封顶', 
                                            'repeat_score'=>'重消积分', 
                                            'total_reward'=>'总金额', 
                                            'check_time'=>'清算时间'
                                            );
                            // dump($headerArr);
                            $callBack = function( $list ) {
                                    //对数据的二次处理;
                                    // dump($list);
                                    foreach ($list as $k=>$v){
                                        // dump($v);
                                        $list[$k]['check_time'] = date("Y-m-d",$v['check_time']);
                                    }
                                    return $list;
                            };
                            $this->downloadCSV( $rlist, $headerArr, 'checkreport_'.$date.date('YmdHi',time()), $callBack );
                }
          
        
    }

    public function detail($id){
        if(!empty($id)){
            $res = M('finance_check');
            $filter['check_no'] = $id;
            $data['check_no'] = $id;
            $data['reward_person_count'] = $res->where($filter)->count('uid');//获得奖金总人数
            $data['day_reward1_count'] = $res->where($filter)->where('day_reward1 >0')->count('day_reward1');//获得组织奖人数
            $data['day_reward2_count'] = $res->where($filter)->where('day_reward2 >0')->count('day_reward2');//获得领导奖人数
            $data['month_rebate_count'] = $res->where($filter)->where('month_rebate >0')->count('month_rebate');//获得领导奖人数
            $data['month_bonus_count'] = $res->where($filter)->where('month_bonus >0')->count('month_bonus');//获得领导奖人数
            $data['repeat_score_count'] = $res->where($filter)->where('repeat_score >0')->count('repeat_score');//获得重复消费积分
            
            $data['day_reward1_sum'] = round($res->where($filter)->Sum('day_reward1'),2);//获得组织奖总额
            $data['day_reward2_sum'] = round($res->where($filter)->Sum('day_reward2'),2);//获得领导奖总额
            $data['month_rebate_sum'] = round($res->where($filter)->Sum('month_rebate'),2);//获得月度分红总额
            $data['month_bonus_sum'] = round($res->where($filter)->Sum('month_bonus'),2);//获得月度返利总额
            $data['repeat_score_sum'] = round($res->where($filter)->Sum('repeat_score'),2);//获得重复消费积分总额
            $data['reward_person_sum'] = round($res->where($filter)->Sum('total_reward'),2);//获得奖金总数
        }
        $this->assign('data',$data);
        $this->display();
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
    
	/*
    * 通用方法
    * 导出大数据为CSV 
    * 参数依次传入 查询对象,CSV文件列头(键是数据表中的列名,值是csv的列名),文件名.对数据二次处理的函数;
    */
    private function downloadCSV($selectObject, $head, $fileName,$callBack = ''){
			
     if ( !is_object( $selectObject ) || !is_array( $head ) ) {
        exit('参数错误!');
     }
     set_time_limit(0);
     //下载头.
     header ('Content-Type: application/vnd.ms-excel;charset=gbk');
     header ('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
     header ('Cache-Control: max-age=0');
        
     //输出流;
     $file = 'php://output';
     $fp = fopen ( $file, 'a' );
     $changCode = function( $changArr ) {
         // 破Excel2003中文只支持GBK编码;
         foreach ( $changArr as $k => $v ) {
             $changArr [$k] = iconv ( 'utf-8', 'gbk', $v );
         }
         //返回一个 索引数组;
         return array_values( $changArr );
     };
     //写入头部;
     fputcsv ( $fp, $changCode( $head ) );
        
     //写入数据;
     $pageSize = 100;//每次查询一百条;
     $page  = 1;//起始页码;
     $list = array();
     //查库;
     $cloneObj = clone $selectObject;//因为thinkphp内部执行完select方法后会清空对象属性,所以clone;
        
     while ( $list = $cloneObj ->limit( $pageSize*( ($page++)-1 ), $pageSize )->select()  ) {
            $cloneObj = clone $selectObject;
                    
            //对查询结果二次处理
            is_callable( $callBack ) && $list = call_user_func( $callBack, $list );
            foreach ( $list as $key => $value ) {
                    
                 $value = array_intersect_key( $value, $head );//返回需要写入CSV的数据;
                 $value = array_merge( $head, $value );//利用此函数返回需要的顺序;
                 $value = $changCode( $value );
                 fputcsv ( $fp, $value );//写入数据;
                 flush();
                    
            }
            ob_flush();
                        
         }
         exit();
    }	
}
