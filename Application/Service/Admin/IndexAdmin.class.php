<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Service\Admin;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;
/**
 * 默认控制器
 * @author jry <598821125@qq.com>
 */
class IndexAdmin extends AdminController {
    /**
     * 默认方法
     * @author  bigfoot
     */
    public function index() {
        
        $this->assign('meta_title', "分销");
        $this->display();
    }
    // public function check(){
        // $this->assign('meta_title', "分销清算");
        // $this->display();
    // }
    /*
    *   分销体系设置
    *   @author bigfoot
    */
    
    public function marketing_config() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id'] = array(
            $condition,
            '_multi'=>true
        );

        // 获取所有配置
        $map['status'] = array('egt', '0'); // 禁用和正常状态
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $data_list =     C('MARKETING');
        // var_dump($data_list);

        $page = new Page(
            count($data_list),
            C('ADMIN_PAGE_ROWS')
        );

        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('分销体系配置') // 设置页面标题
                // ->addTopButton('addnew')  // 添加新增按钮
                // ->addTopButton('resume')  // 添加启用按钮
                // ->addTopButton('forbid')  // 添加禁用按钮
                // ->addTopButton('delete')  // 添加删除按钮
                ->setSearch('请输入ID', U('index'))
                ->addTableColumn('id', 'UID')
                ->addTableColumn('TITLE', '配置体系名')
                ->addTableColumn('create_time', '生成时间', 'time')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)    // 数据列表
                // ->setTableDataPage($page->show()) // 数据列表分页
                ->addRightButton('edit')          // 添加编辑按钮
                // ->addRightButton('forbid')        // 添加禁用/启用按钮
                // ->addRightButton('delete')        // 添加删除按钮
                ->display();
        
    }
    
    /*
    *   新增体系
    *   @author bigfoot
    */
    
    public function add(){
        if (IS_POST) {
            
            $data = I('post.');
            // dump($data);
            if ($data) {
                $data['id']  = count(C('MARKETING')) + 1 ;
                $data['create_time'] = time();
                $data['status'] = 0;
                $marketing = C('MARKETING');  //将默认配置参数的内容赋值给$marketing;
                $marketing[ "'".$data['id']."'" ] = $data;
                $path = COMMON_PATH.'conf/marketing.php';
                // dump($marketing);
                // dump($path);
                
                $settingstr = "<?php \n return array(\n'MARKETING' =>\n".var_export($marketing,true)." \n);\n?>\n";
                // dump($settingstr);
                $result = file_put_contents($path,$settingstr); //通过file_put_contents保存
                if ($result){
                    $this->success('新增成功',U('marketing_config'));
                }else{
                    $this->error('新增失败');
                }
               
                
            } else {
                $this->error('没有数据');
            }
        } else {
            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('新增') //设置页面标题
                    ->setPostUrl(U('add'))    //设置表单提交地址
                    ->addFormItem('TITLE', 'text', '分销体系名称', '新建分销体系的名称')
                    ->addFormItem('BASIC_VALUE', 'num', '基础额度', '会员基础消费')
                    ->addFormItem('REWARD_SCORE1', 'num', '会员消费奖励比例', '会员消费奖励比例')
                    ->addFormItem('REWARD_SCORE2', 'num', '会员安置人奖励比例', '会员安置人奖励比例')
                    ->addFormItem('S_REWARD_PERCENT', 'num', '组织奖', '组织奖百分比')
                    ->addFormItem('S_REWARD_VALUE', 'num', '每日限额', '组织奖+领导奖每日限额')
                    ->addFormItem('P_REWARD_PERCENT', 'num', '领导奖', '领导奖百分比')
                    ->addFormItem('SHOP1_REWARD_PERCENT', 'num', '社区店返利', '社区店返利百分比')
                    ->addFormItem('SHOP2_REWARD_PERCENT', 'num', '经理店返利', '经理店返利百分比')
                    ->addFormItem('SHOP3_REWARD_PERCENT', 'num', '中心店返利', '中心店返利百分比')
                    ->addFormItem('PERSON1_REWARD_PERCENT', 'num', '等级一', '月度返利')
                    ->addFormItem('PERSON2_REWARD_PERCENT', 'num', '等级二', '月度返利')
                    ->addFormItem('PERSON3_REWARD_PERCENT', 'num', '等级三', '月度返利')
                    ->addFormItem('PERSON4_REWARD_PERCENT', 'num', '等级四', '月度返利')
                    ->addFormItem('PERSON5_REWARD_PERCENT', 'num', '主任', '月度返利')
                    ->addFormItem('PERSON6_REWARD_PERCENT', 'num', '经理', '月度返利')
                    ->addFormItem('PERSON7_REWARD_PERCENT', 'num', '总监', '月度返利')
                    ->addFormItem('PERSON6_BONUS_PERCENT', 'num', '经理', '月度分红')
                    ->addFormItem('PERSON7_BONUS_PERCENT', 'num', '总监', '月度分红')
                    ->setExtraHtml('警告，分销返利配置关系着全局的返利，在没有验证过返利模型的情况下，不要改动此项目。')
                    // ->setAjaxSubmit(false)
                    ->display();
        }
    }

    /*
    *   编辑体系
    *   @author bigfoot
    */
    public function edit($id){
       
       if (IS_POST) {
            $data = I('post.');
            // dump($data);
            if ($data) {
                $data['create_time'] = time();
                // $data['status'] = 0;
                $marketing = C('MARKETING');  //将默认配置参数的内容赋值给$marketing;
                $marketing[$id] = $data;
                $path = COMMON_PATH.'conf/marketing.php';
                dump($marketing);
                dump($path);
                
                $settingstr = "<?php \n return array(\n'MARKETING' =>\n".var_export($marketing,true)."\n);\n?>\n";
                dump($settingstr);
                $result = file_put_contents($path,$settingstr); //通过file_put_contents保存
                if ($result){
                    $this->success('编辑成功',U('marketing_config'));
                }else{
                    $this->error('编辑失败');
                }
               
                
            } else {
                $this->error('没有数据');
            }
        } else {
            
            $marketing = C('MARKETING');
            // dump($marketing);
            $data = $marketing[$id];
            // dump($data);
            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('编辑') //设置页面标题
                    ->setPostUrl(U('edit'))    //设置表单提交地址
                    ->addFormItem('id', 'hidden')
                    ->addFormItem('status', 'hidden')
                    ->addFormItem('TITLE', 'text', '分销体系名称', '新建分销体系的名称')
                    ->addFormItem('BASIC_VALUE', 'num', '基础额度', '会员基础消费')
                    ->addFormItem('REWARD_SCORE1', 'num', '会员消费奖励比例', '本人返还比例')
                    ->addFormItem('REWARD_SCORE2', 'num', '会员安置人奖励比例', '安置人返还比例')
                    ->addFormItem('S_REWARD_PERCENT', 'num', '组织', '组织奖百分比')
                    ->addFormItem('S_REWARD_VALUE', 'num', '每日限额', '组织奖+领导奖每日限额')
                    ->addFormItem('P_REWARD_PERCENT', 'num', '领导奖', '领导奖百分比')
                    ->addFormItem('SHOP1_REWARD_PERCENT', 'num', '社区店返利', '社区店返利百分比')
                    ->addFormItem('SHOP2_REWARD_PERCENT', 'num', '经理店返利', '经理店返利百分比')
                    ->addFormItem('SHOP3_REWARD_PERCENT', 'num', '中心店返利', '中心店返利百分比')
                    ->addFormItem('PERSON1_REWARD_PERCENT', 'num', '等级一', '月度返利')
                    ->addFormItem('PERSON2_REWARD_PERCENT', 'num', '等级二', '月度返利')
                    ->addFormItem('PERSON3_REWARD_PERCENT', 'num', '等级三', '月度返利')
                    ->addFormItem('PERSON4_REWARD_PERCENT', 'num', '等级四', '月度返利')
                    ->addFormItem('PERSON5_REWARD_PERCENT', 'num', '主任', '月度返利')
                    ->addFormItem('PERSON6_REWARD_PERCENT', 'num', '经理', '月度返利')
                    ->addFormItem('PERSON7_REWARD_PERCENT', 'num', '总监', '月度返利')
                    ->addFormItem('PERSON6_BONUS_PERCENT', 'num', '经理', '月度分红比例，每个比例数字以英文逗号,分隔')
                    ->addFormItem('PERSON7_BONUS_PERCENT', 'num', '总监', '月度分红比例，每个比例数字以英文逗号,分隔')
                    ->setFormData($data)
                    // ->setAjaxSubmit(false)
                    ->display();
        }
    }
    
    public function check(){
        if (IS_POST) {
            $pdata = I('post.');
            if ($pdata) {
                // dump($pdata);
                echo "假设一个用户既是中心店又是总监。他的留存比例设定如下：<br/>";
                echo "消费奖励积分比例，购物人：".$pdata['REWARD_SCORE1'].'%,安置人'.$pdata['REWARD_SCORE2'].'%<br/>';
                echo "组织奖比例：".$pdata['S_REWARD_PERCENT'].'%,<br/>';
                echo "领导奖比例：".$pdata['P_REWARD_PERCENT'].'%,<br/>';
                echo "中心店返利比例：".$pdata['SHOP3_REWARD_PERCENT'].'%,<br/>';
                echo "月度返利比例：".$pdata['PERSON7_REWARD_PERCENT'].'%,<br/>';
                echo "月度分红比例：".$pdata['PERSON7_BONUS_PERCENT'].',<br/><br/>';
                
                $reward_score =($pdata['REWARD_SCORE1']+$pdata['REWARD_SCORE2'])/(100+$pdata['REWARD_SCORE1']+$pdata['REWARD_SCORE2'])*100;
                $sp_reward = ($pdata['S_REWARD_PERCENT']+$pdata['P_REWARD_PERCENT']/10)*0.5;
                $shop_reward = $pdata['SHOP3_REWARD_PERCENT'];
                $month_rebate = $pdata['PERSON7_REWARD_PERCENT']*0.66;
                $month_rebate_percent = explode(',',$pdata['PERSON7_BONUS_PERCENT']);
                $month_bonus =  $month_rebate*(array_sum($month_rebate_percent)/100);
                // dump($pdata['PERSON7_BONUS_PERCENT']);
                // dump(array_sum($month_rebate_percent));
                
                echo "以总量100%计算，该用户获得留存比例的最大值如下：<br/>";
                echo "消费奖励积分：".round($reward_score,2)."%";
                echo "<br/>";
                echo "组织奖+领导奖：".$sp_reward."%";
                echo "<br/>";
                echo "店补：".$shop_reward."%";
                echo "<br/>";
                echo "月度返利：".$month_rebate."%";
                echo "<br/>";
                echo "月度分红：".round($month_bonus,2)."%";
                echo "<br/>";
                echo "总计：".round((round($reward_score,2)+$sp_reward+$shop_reward+$month_rebate+$month_bonus),2)."%<br/>";
                echo ' <button class="btn btn-default return visible-md-inline visible-lg-inline" onclick="javascript:history.back(-1);return false;">返回</button>';
               
                
            } else {
                $this->error('没有数据');
            }
        } else {
            
            $marketing = C('MARKETING');
            // dump($marketing);
            $data = $marketing[1];
            // dump($data);
            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('编辑') //设置页面标题
                    ->setPostUrl(U('check'))    //设置表单提交地址
                    // ->addFormItem('id', 'hidden')
                    // ->addFormItem('status', 'hidden')
                    // ->addFormItem('TITLE', 'text', '分销体系名称', '新建分销体系的名称')
                    // ->addFormItem('BASIC_VALUE', 'num', '基础额度', '会员基础消费')
                    ->addFormItem('REWARD_SCORE1', 'num', '会员消费奖励比例', '本人返还比例')
                    ->addFormItem('REWARD_SCORE2', 'num', '会员安置人奖励比例', '安置人返还比例')
                    ->addFormItem('S_REWARD_PERCENT', 'num', '组织', '组织奖百分比')
                    ->addFormItem('S_REWARD_VALUE', 'num', '每日限额', '组织奖+领导奖每日限额')
                    ->addFormItem('P_REWARD_PERCENT', 'num', '领导奖', '领导奖百分比')
                    ->addFormItem('SHOP1_REWARD_PERCENT', 'num', '社区店返利', '社区店返利百分比')
                    ->addFormItem('SHOP2_REWARD_PERCENT', 'num', '经理店返利', '经理店返利百分比')
                    ->addFormItem('SHOP3_REWARD_PERCENT', 'num', '中心店返利', '中心店返利百分比')
                    ->addFormItem('PERSON1_REWARD_PERCENT', 'num', '等级一', '月度返利')
                    ->addFormItem('PERSON2_REWARD_PERCENT', 'num', '等级二', '月度返利')
                    ->addFormItem('PERSON3_REWARD_PERCENT', 'num', '等级三', '月度返利')
                    ->addFormItem('PERSON4_REWARD_PERCENT', 'num', '等级四', '月度返利')
                    ->addFormItem('PERSON5_REWARD_PERCENT', 'num', '主任', '月度返利')
                    ->addFormItem('PERSON6_REWARD_PERCENT', 'num', '经理', '月度返利')
                    ->addFormItem('PERSON7_REWARD_PERCENT', 'num', '总监', '月度返利')
                    ->addFormItem('PERSON6_BONUS_PERCENT', 'num', '经理', '月度分红比例，每个比例数字以英文逗号,分隔')
                    ->addFormItem('PERSON7_BONUS_PERCENT', 'num', '总监', '月度分红比例，每个比例数字以英文逗号,分隔')
                    ->setFormData($data)
                    ->setAjaxSubmit(false)
                    ->display();
        }
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
        switch ($status) {
            case 'forbid' :  // 禁用条目
                $data = array('status' => 0);
                
                break;
            case 'resume' :  // 启用条目
                $marketing = C('MARKETING');  //将默认配置参数的内容赋值给$marketing;
                foreach($marketing as $k => $v){
                    // dump($k);
                    // dump($ids);
                    if ($k == (int)$ids){
                        $marketing[$ids]['status'] =1;
                        $marketing[$ids]['start_time'] =time();
                    }else{
                        $marketing[$k]['status'] =0;
                   }
                }
                
                $path = MODULE_PATH.'conf/marketing.php';
                // dump($marketing);
                // dump($path);
                
                $settingstr = "<?php \n return array(\n'MARKETING' =>\n".var_export($marketing,true)."\n);\n?>\n";
                // dump($settingstr);
                $result = file_put_contents($path,$settingstr); //通过file_put_contents保存
                if ($result) {
                    $this->success('启用成功！');
                } else {
                    $this->error('启用失败');
                }
                break;
            case 'delete'  :  // 删除条目
                $marketing = C('MARKETING');  //将默认配置参数的内容赋值给$marketing;
                unset($marketing[$ids]);
                $path = MODULE_PATH.'conf/marketing.php';
                // dump($marketing);
                // dump($path);
                
                $settingstr = "<?php \n return array(\n'MARKETING' =>\n".var_export($marketing,true)."\n);\n?>\n";
                // dump($settingstr);
                $result = file_put_contents($path,$settingstr); //通过file_put_contents保存
                if ($result) {
                    $this->success('删除成功，不可恢复！');
                } else {
                    $this->error('删除失败');
                }
                break;
            default :
                $this->error('参数错误');
                break;
        }
        
    }
}
