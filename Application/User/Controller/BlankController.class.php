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
use Common\Util\BTNode;
use Common\Util\IDValidator\IDValidator;
/**
 * 后台默认控制器
 * @author jry <598821125@qq.com>
 */
class BlankController extends HomeController {
    /**
     * 初始化方法
     * @author bigfoot
     */
    protected function _initialize(){
        parent::_initialize();
        $this->is_login();
        $this->is_visit();
    }
    
    /**
     * 默认方法
     * @author jry <598821125@qq.com>
     */
    public function index(){
        // $Reg = D("User/Reg"); 
        // $user = $Reg->relation(true)->find(2);
        // echo $Reg->_sql();
        // dump($user);
        // dump(get_user_id(bigfoot));
        // D('Shop/Index')->addSales(2,1000,1);
        // D('Shop/Index')->addRewardScore(3,100);
        // D('Shop/Index')->addShopReward(3,100);
        // $node = $this->sqlnode(2);
        // dump($node);
        // $this->getPostorderTraversal($node);
        // $this->visitnlevel($node,6);
        $rows = D('User/Tree')->getField('id,id,fid');
        // dump($rows);
        // $rows = array(
            // 1 => array('id' => 1, 'fid' => 0, 'name' => '安徽省'),
            // 2 => array('id' => 2, 'fid' => 0, 'name' => '浙江省'),
            // 3 => array('id' => 3, 'fid' => 1, 'name' => '合肥市'),
            // 4 => array('id' => 4, 'fid' => 3, 'name' => '长丰县'),
            // 5 => array('id' => 5, 'fid' => 1, 'name' => '安庆市'),
        // );
        $data = $this->generateTree($rows);
        // dump($data);
        $j_data = json_encode($data,true);
        echo $j_data;
        
        $this->assign('meta_title', "空白页，暂未制作");
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
        array_push($stack, $node);
        // dump($stack);
        $data[0] = 0;
        
        while(!empty($stack)){
            // dump(count($stack));
            $center_node = array_pop($stack);
            //待补充，画该节点
            // dump($center_node->mRchild);
            // dump($center_node->mData['id']);
            // dump($center_node->mDepth - $topdepth);

            if ( ($center_node->mDepth - $topdepth ) < ($maxlevel-1)) { //只有第maxlevel-1层以内才会访问下一级节点
                if($center_node->mRchild != 0 ) {
                    $tempnode = $this->sqlnode($center_node->mData['rid']);
                    $center_node->mLchild = $tempnode;
                    $tempnode->mFather =$center_node;
                    $tempnode->mDepth = $center_node->mDepth+1;
                    array_push($stack, $tempnode);
                    $data[$center_node->mDepth]++;
                }
                if($center_node->mLchild != 0){
                    
                    $tempnode = $this->sqlnode($center_node->mData['lid']);
                    $center_node->mLchild = $tempnode;
                    $tempnode->mFather =$center_node;
                    $tempnode->mDepth = $center_node->mDepth+1;
                    array_push($stack, $tempnode);
                    $data[$center_node->mDepth]++;
                }
                
                
            }
            
        }
        dump($data);
        

    }
    function generateTree($items){
        // dump($items);
        $tree = array();
        foreach($items as $item){
            $items[$item['id']]['value'] = 6;
            $items[$item['id']]['name'] = $item['id'];
            if(isset($items[$item['fid']])){
                $items[$item['fid']]['children'][] = &$items[$item['id']];
                // dump($items[$item['fid']]['children']);
            }else{
                $tree[] = &$items[$item['id']];
            }
        }
        return $tree;
    }
    
    function generateTree2($rows, $id='id', $fid='fid'){  
        // dump($rows);
        $items = array();  
        foreach ($rows as $row){ 
            $items[$row[$id]] = $row;
        }      // dump($items);
        foreach ($items as $item) {
            // dump($item);
            $items[$item[$fid]]['children'][$item[$id]] = &$items[$item[$id]];
         } 
         dump($items);    
        return isset($items[0]['children']) ? $items[0]['children'] : array();  
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
                return $this->sqlnode(2);
                // return $BTNode;
            }else{
                // dump($nodeid);
                $sql = "select id, user_level,total_score,month_sales,month_repeat_score,position, pid, lid,director_num,manager_num,rid, day_sales,day_reward1,day_reward2,month_rebate from `jiu_user_tree` join `jiu_user_info` on jiu_user_info.uid = jiu_user_tree.id where id = ".$nodeid;
                $tree_data = M()->query($sql);
                // dump($tree_data);
                if ($tree_data){
                    $father = explode(',',$tree_data['0']['position']);
                    $BTNode->mLchild = $tree_data['0']['lid'];
                    $BTNode->mRchild = $tree_data['0']['rid'];
                    $BTNode->mData = $tree_data['0'];
                    $BTNode->mFather = $father['0'];
                    $BTNode->mDepth = 0;
                    // dump($BTNode->mData['id']);
                    // dump($BTNode);
                    return $BTNode;
                }
            }
        }
        
         //用php从身份证中提取生日,包括15位和18位身份证 
        function getIDCardInfo($IDCard){ 
            $result['error']=0;//0：未知错误，1：身份证格式错误，2：无错误 
            $result['isAdult']='';//0标示成年，1标示未成年 
            $result['birthday']='';//生日，格式如：2012-11-15 
            if( !$this->isIdCard($IDCard) ){ 
                $result['error']=1; 
                return $result; 
            }else{ 
                if(strlen($IDCard)==18){ 
                    $tyear=intval(substr($IDCard,6,4)); 
                    $tmonth=intval(substr($IDCard,10,2)); 
                    $tday=intval(substr($IDCard,12,2)); 
                    if($tyear>date("Y")||$tyear<(date("Y")-100)){ 
                        $flag=0; 
                    }elseif($tmonth<0||$tmonth>12){ 
                        $flag=0; 
                    }elseif($tday<0||$tday>31){ 
                        $flag=0; 
                    }else{ 
                        $tdate = $tyear."-".$tmonth."-".$tday; 
                        if((time()-mktime(0,0,0,$tmonth,$tday,$tyear))>18*365*24*60*60){ 
                            $flag=0; 
                        }else{ 
                            $flag=1; 
                        } 
                    } 
                }elseif(strlen($IDCard)==15){ 
                    $tyear=intval("19".substr($IDCard,6,2)); 
                    $tmonth=intval(substr($IDCard,8,2)); 
                    $tday=intval(substr($IDCard,10,2)); 
                    if($tyear>date("Y")||$tyear<(date("Y")-100)){ 
                        $flag=0; 
                    }elseif($tmonth<0||$tmonth>12){ 
                        $flag=0; 
                    }elseif($tday<0||$tday>31){ 
                        $flag=0; 
                    }else{ 
                        $tdate=$tyear."-".$tmonth."-".$tday." 00:00:00"; 
                        if((time()-mktime(0,0,0,$tmonth,$tday,$tyear))>18*365*24*60*60){ 
                            $flag=0; 
                        }else{ 
                            $flag=1; 
                        } 
                    } 
                } 
            } 
            $result['error']=2;//0：未知错误，1：身份证格式错误，2：无错误 
            $result['isAdult']=$flag;//0标示成年，1标示未成年 
            $result['birthday']=$tdate;//生日日期 
            return $result; 
        }
        
        /**
         * 函数说明：验证身份证是否真实
         * 注：加权因子和校验码串为互联网统计  尾数自己测试11次 任意身份证都可以通过
         * 传递参数：
         * $number身份证号码
         * 返回参数：
         * true验证通过
         * false验证失败
         */
        function isIdCard($number) {
            $sigma = '';
            //加权因子 
            $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            //校验码串 
            $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            //按顺序循环处理前17位 
            for ($i = 0;$i < 17;$i++) { 
                //提取前17位的其中一位，并将变量类型转为实数 
                $b = (int) $number{$i}; 
                //提取相应的加权因子 
                $w = $wi[$i]; 
                //把从身份证号码中提取的一位数字和加权因子相乘，并累加 得到身份证前17位的乘机的和 
                $sigma += $b * $w;
            }
        //echo $sigma;die;
            //计算序号  用得到的乘机模11 取余数
            $snumber = $sigma % 11; 
            //按照序号从校验码串中提取相应的余数来验证最后一位。 
            $check_number = $ai[$snumber];
            // dump($number);
            // dump($check_number);
            // dump($number{17});
            if ($number{17} == $check_number) {
                return true;
            } else {
                return false;
            }
        }
}
