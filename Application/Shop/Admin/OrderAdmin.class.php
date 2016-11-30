<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Shop\Admin;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;
/**
 * 后台订单控制器
 * @author jry <598821125@qq.com>
 */
class OrderAdmin extends AdminController {

    /**
     * 所有订单
     * @author bigfoot
     */
    public function index() {
         // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['no|buy_id|shop_id'] = array(
            $condition,
            array('like','%'.get_user_id($keyword).'%'),
            array('like','%'.get_user_id($keyword).'%'),
            '_multi'=>true
        );
        $delivery_status = I('get.delivery_status');
        if(isset($delivery_status) && $delivery_status != ''){
           $map['delivery_status'] = $delivery_status; 
        }
        // dump($map);
        
        // 获取我的订单信息
        $map['status'] = array('egt', '1'); // 正常状态
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $order_object = D('Shop/Order');

        $data_list = $order_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id desc')
                   ->select();
        // echo $order_object->_sql();
        if(empty($data_list)){
            $this->error('没有查询到符合条件的订单');
        }
        $page = new Page(
            $order_object->where($map)->count(),
            C('ADMIN_PAGE_ROWS')
        );
        
        //自定义按钮详细
        $attr['name']  = 'detail';
        $attr['title'] = '详情';
        $attr['class'] = 'label label-primary';
        $attr['href']  = U('detail', array('id' => '__data_id__'));
        //自定义按钮发货
        $attr2['name']  = 'delivery';
        $attr2['title'] = '发货';
        $attr2['class'] = 'label label-primary';
        $attr2['href']  = U('delivery', array('ids' => '__data_id__'));
        //自定义按钮顶部批量发货
         $attr3['title'] = '发货';
         $attr3['class'] = 'btn btn-primary ajax-post';
         $attr3['href']  = U('Shop/Order/delivery');
                // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('订单列表') // 设置页面标题
                ->setSelectSearch('delivery_status','发货状态',
                                    array( '0'=>'未发货',
                                    '1'=>'已发货' ))
                ->setSearch('请输入订单编号|购物人用户名|报单中心用户名', U('index'))
                ->setSearch('请输入报单中心用户名', U('index'))
                // ->addTopButton('resume', array('model' => 'admin_shop'))  // 添加启用按钮
                // ->addTopButton('forbid', array('model' => 'admin_shop'))  // 添加禁用按钮
                ->addTopButton('delete', array('model' => D('Shop/Order')->tableName)) // 添加删除按钮
                ->addTopButton('self', $attr3)  // 添加发货按钮
                ->addTableColumn('id', 'ID')
                ->addTableColumn('no','订单编号')
                ->addTableColumn('buy_id','购物人','username')
                ->addTableColumn('pay_id','付款人','username')
                ->addTableColumn('shop_id','报单中心','username')
                ->addTableColumn('create_time', '订购时间', 'time')
                ->addTableColumn('pay_status', '支付状态')
                ->alterTableData(
                    //修改付款状态显示
                            array('key' => 'pay_status', 'value' => '0'),
                            array('pay_status' => '未付款')
                            // 0未付款1已付款
                            )
                ->alterTableData(
                    //修改发货状态显示
                            array('key' => 'pay_status', 'value' => '1'),
                            array('pay_status' => '已付款')
                            // 0未付款1已付款
                            )
                ->addTableColumn('delivery_status', '发货状态')
                ->alterTableData(
                    //修改发货状态显示
                            array('key' => 'delivery_status', 'value' => '0'),
                            array('delivery_status' => '未发货')
                            // 0未发货1已发货
                            )
                ->alterTableData(
                    //修改发货状态显示
                            array('key' => 'delivery_status', 'value' => '1'),
                            array('delivery_status' => '已发货')
                            // 0未发货1已发货
                            )
                ->addTableColumn('delivery_time', '发货时间',time)
                ->addTableColumn('order_type', '订单类型')
                ->alterTableData(
                    //修改订单类型显示
                            array('key' => 'order_type', 'value' => '1'),
                            array('order_type' => '主动消费')
                            // '主动消费购物'
                            )
                ->alterTableData(
                    //修改订单类型显示
                            array('key' => 'order_type', 'value' => '2'),
                            array('order_type' => '奖励积分')
                            // '奖励积分购物'
                            )
                ->alterTableData(
                    //修改订单类型显示
                            array('key' => 'order_type', 'value' => '3'),
                            array('order_type' => '重消积分')
                            // '重复消费积分购物'
                            )
                ->addTableColumn('value', '金额')
                ->addTableColumn('right_button', '操作', 'btn')
                // ->addRightButton('edit')          // 添加编辑按钮
                // ->addRightButton('forbid')        // 添加禁用/启用按钮
                ->addrightButton('self',$attr)    // 添加自定义按钮详细 
                ->addrightButton('self',$attr2)    // 添加自定义按钮发货
                ->addRightButton('delete')        // 添加删除按钮
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->display();
    }
    
    /**
     * 订单详情
     * @author bigfoot
     */
    public function detail($id) {
        $info = D('Shop/Order')->find($id);
        if ($info['status'] !== '1') {
            $this->error('该订单不存在或已删除',U('Index/myOrder'));
        }
        $goods_list = json_decode($info['order_detail'],true);
        // dump($goods_list);
        $this->assign('info', $info);
        $this->assign('goods_list', $goods_list);
        $this->assign('meta_title', $info['no'].'订单详情');
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }
    
    /*
     * 发货，将发货状态置为1即可
     * @author:bigfoot
     */
    public function delivery(){
        $ids    = I('request.ids');
        // dump($ids);
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        $map['id'] = array('in',$ids);
        $exist = D('Shop/Order')->where($map)->getField('delivery_status');
        // dump($exist);
        if ($exist != 1) {//检查是否已经发货
            $data['delivery_status'] = 1;
            $data['delivery_time'] = time();
            $delivery = D('Shop/Order')->where($map)->setField($data);
        } else {
            $this->error('货物已经发出');
        }
        if ($delivery){
            $this->success('发货成功');
        }else{
            $this->error('发货失败');
        }

    }
    
}