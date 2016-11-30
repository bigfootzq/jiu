<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Shop\Controller;
use Home\Controller\HomeController;
use Common\Util\Think\Page;
/**
 * 订单控制器
 * @author jry <598821125@qq.com>
 */
class OrderController extends HomeController {
    
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
     * 默认方法
     * @author jry <598821125@qq.com>
     */
    public function index() {
         // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['no|name'] = array(
            $condition,
            $condition,
            '_multi'=>true
        );

        // 获取所有商品
        $map['status'] = array('egt', '1'); // 正常状态
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $goods_object = D('Shop/Index');
        // dump($goods_object);
        $data_list = $goods_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id desc')
                   ->select();
        $page = new Page(
            $goods_object->where($map)->count(),
            C('ADMIN_PAGE_ROWS')
        );

        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('商品列表') // 设置页面标题
                ->setSearch('请输入商品编号|商品名称', U('index'))
                ->addTableColumn('id', 'ID')
                ->addTableColumn('no','商品编号')
                ->addTableColumn('name', '商品名称')
                ->addTableColumn('description', '商品描述')
                ->addTableColumn('price', '商品价格')
                ->addTableColumn('bv', 'BV值')
                ->addTableColumn('number', '库存数量')
                ->addTableColumn('right_button', '操作', 'btn')
                ->addrightButton('self',array(  
                'title' => '购买',//按钮标题  
                'href' => U(  
                    MODULE_NAME.'/'.CONTROLLER_NAME.'/buy'  
                ),//跳转url  
                ))    // 添加自定义按钮购买  
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->setTemplate(C('USER_CENTER_LIST'))
                ->display();
    }
    
    /**
     * 我的订单
     * @author bigfoot
     */
    public function my() {
         // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['no|buy_id'] = array(
            $condition,
            $condition,
            '_multi'=>true
        );

        // 获取我的订单信息
        // $map['status'] = array('egt', '1'); // 正常状态
        $map['buy_id'] = is_login(); // 
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $order_object = D('Shop/Order');

        $data_list = $order_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id desc')
                   ->select();
        $page = new Page(
            $order_object->where($map)->count(),
            C('ADMIN_PAGE_ROWS')
        );
        
        //自定义按钮详细
        $attr['name']  = 'detail';
        $attr['title'] = '详情';
        $attr['class'] = 'label label-primary';
        $attr['href']  = U('detail', array('id' => '__data_id__'));
                // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('我的订单') // 设置页面标题
                ->setSearch('请输入商品编号|商品名称', U('index'))
                ->addTableColumn('id', 'ID')
                ->addTableColumn('no','订单编号')
                // ->addTableColumn('username', '用户名')
                ->addTableColumn('create_time', '订购时间', 'date')
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
                ->addTableColumn('delivery_time', '发货时间',date)
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
                ->addTableColumn('value', '订单金额')
                ->addTableColumn('right_button', '操作', 'btn')
                ->addrightButton('self',$attr)    // 添加自定义按钮详细 
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->setTemplate(C('USER_CENTER_LIST'))
                ->display();
    } 
    
    /**
     * 我的销售订单
     * @author bigfoot
     */
    public function mySale() {
         // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['no|buy_id'] = array(
            $condition,
            $condition,
            '_multi'=>true
        );

        // 获取我的订单信息
        // $map['status'] = array('egt', '1'); // 正常状态
        $map['shop_id'] = is_login(); // 
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $order_object = D('Shop/Order');

        $data_list = $order_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id desc')
                   ->select();
        $page = new Page(
            $order_object->where($map)->count(),
            C('ADMIN_PAGE_ROWS')
        );
        
        //自定义按钮详细
        $attr['name']  = 'detail';
        $attr['title'] = '详情';
        $attr['class'] = 'label label-primary';
        $attr['href']  = U('detail', array('id' => '__data_id__'));
        $attr2['name']  = '';
        $attr2['title'] = '';
        $attr2['class'] = 'label label-primary';
        $attr2['href']  = U('');
                // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('我的销售订单') // 设置页面标题
                ->setSearch('请输入商品编号|商品名称', U('index'))
                ->addTopButton('self',$attr2)  // 添加占位按钮
                ->addTableColumn('id', 'ID')
                ->addTableColumn('no','订单编号')
                ->addTableColumn('buy_id', '购物会员','callback','get_user_name')
                ->addTableColumn('create_time', '订购时间', 'date')
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
                ->addTableColumn('delivery_time', '发货时间',date)
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
                ->addTableColumn('value', '订单金额')
                ->addTableColumn('right_button', '操作', 'btn')
                ->addrightButton('self',$attr)    // 添加自定义按钮详细 
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->setTemplate(C('USER_CENTER_LIST'))
                ->display();
    }
    
    /**
     * 订单详情
     * @author bigfoot
     */
    public function detail($id) {
        $info = D('Shop/Order')->find($id);
        if ($info['status'] !== '1') {
            $this->error('该订单不存在或已删除',U('Shop/Order/my'));
        }
        $goods_list = json_decode($info['order_detail'],true);
        // dump($goods_list);
        $this->assign('info', $info);
        $this->assign('goods_list', $goods_list);
        $this->assign('meta_title', $info['no'].'订单详情');
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }
    
    
}