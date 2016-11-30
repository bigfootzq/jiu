<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | Copyright (c) 2016
// +----------------------------------------------------------------------
// | Author: bigfoot<bigfootzq@163.com>
// +----------------------------------------------------------------------
namespace Shop\Admin;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;
/**
 * 后台购物中心控制器
 * @author bigfoot<bigfootzq@163.com>
 */
class IndexAdmin extends AdminController {
    /**
     * 默认列表方法
     * @author bigfoot<bigfootzq@163.com>
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
        $map['status'] = array('egt', '0'); // 禁用和正常状态
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $goods_object = D('Shop/Index');
        // dump($goods_object);
        $data_list = $goods_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('sort asc, id asc')
                   ->select();
        $page = new Page(
            $goods_object->where($map)->count(),
            C('ADMIN_PAGE_ROWS')
        );

        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('商品列表') // 设置页面标题
                ->addTopButton('addnew')  // 添加新增按钮
                ->addTopButton('resume', array('model' => 'shop_goods'))  // 添加启用按钮
                ->addTopButton('forbid', array('model' => 'shop_goods'))  // 添加禁用按钮
                ->addTopButton('recycle', array('model' => 'shop_goods'))  // 添加删除按钮
                ->setSearch('请输入商品编号|商品名称', U('index'))
                // ->addTableColumn('id', 'UID')
                ->addTableColumn('sort', '排序')
                ->addTableColumn('no','商品编号')
                ->addTableColumn('name', '商品名称')
                ->addTableColumn('goods_pic', '商品图片','picture')
                ->addTableColumn('description', '商品描述')
                ->addTableColumn('price', '商品价格')
                ->addTableColumn('bv', 'BV值')
                ->addTableColumn('number', '库存数量')
                ->addTableColumn('create_time', '创建时间')
                ->addTableColumn('update_time', '更新时间')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->addRightButton('edit')          // 添加编辑按钮
                ->addRightButton('forbid')        // 添加禁用/启用按钮
                ->addRightButton('recycle')        // 添加回收按钮
                ->display();
    }

    /**
     * 新增商品
     * @author bigfoot<bigfootzq@163.com>
     */
    public function add() {
       if (IS_POST) {
            $goods_object = D('Shop/index');
            $data = $goods_object->create();
            if ($data) {
                $id = $goods_object->add();
                if ($id) {
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($goods_object->getError());
            }
        } else {
            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('新增商品') //设置页面标题
                    ->setPostUrl(U('add'))    //设置表单提交地址
                    ->addFormItem('sort', 'num', '商品排序', '商品排序')
                    ->addFormItem('no', 'num', '商品编号', '商品编号')
                    ->addFormItem('name', 'text', '商品名称', '商品名称')
                    ->addFormItem('goods_pic', 'picture', '商品图片', '商品图片')
                    ->addFormItem('description', 'text', '商品描述', '商品描述')
                    ->addFormItem('price', 'num', '商品价格', '商品价格')
                    ->addFormItem('bv', 'num', '商品BV值', '商品BV值')
                    ->addFormItem('number', 'num', '商品数量', '商品数量')
                    ->setFormData(array('reg_type' => 'admin'))
                    ->display();
        }
    }

    /**
     * 编辑商品
     * @author bigfoot<bigfootzq@163.com>
     */
    public function edit($id) {
        if (IS_POST) {

            // 提交数据
            $goods_object = D('Shop/Index');
            $data = $goods_object->create();
            if ($data) {
                $result = $goods_object
                        ->field('id,sort,no,name,goods_pic,description,price,bv,number,update_time')
                        ->save($data);
                if ($result) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败,'.$goods_object->getError());
                }
            } else {
                $this->error($goods_object->getError());
            }
        } else {
            // 获取商品信息
            $info = D('Shop/Index')->find($id);

            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('编辑商品')  // 设置页面标题
                    ->setPostUrl(U('edit'))    // 设置表单提交地址
                    ->addFormItem('id', 'hidden', 'ID', 'ID')
                    ->addFormItem('sort', 'num', '商品排序', '商品排序')
                    ->addFormItem('no', 'num', '商品编号', '商品编号')
                    ->addFormItem('name', 'text', '商品名称', '商品名称')
                    ->addFormItem('goods_pic', 'picture', '商品图片', '商品图片')
                    ->addFormItem('description', 'text', '商品描述', '商品描述')
                    ->addFormItem('price', 'num', '商品价格', '商品价格')
                    ->addFormItem('bv', 'num', '商品BV值', '商品BV值')
                    ->addFormItem('number', 'num', '商品数量', '商品数量')
                    ->setFormData($info)
                    // ->setAjaxSubmit(false)
                    ->display();
        }
    }


    /**
     * 回收站
     * @author bigfoot<bigfootzq@163.com>
     */
    public function recycle() {
        $map['status'] = array('eq', '-1');
        $goods_list = D('Index')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->select();
        $page = new Page(D('Shop/Index')->where($map)->count(), C('ADMIN_PAGE_ROWS'));
        // dump($goods_list);
        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('回收站') //设置页面标题
                ->addTopButton('delete', array('model' => D('Index')->tableName)) //添加删除按钮
                ->addTopButton('restore', array('model' => D('Index')->tableName)) //添加还原按钮
                ->setSearch('请输入商品编号|商品名称', U('index'))
                ->addTableColumn('id', 'UID')
                ->addTableColumn('no','商品编号')
                ->addTableColumn('name', '商品名称')
                ->addTableColumn('goods_pic', '商品图片','picture')
                ->addTableColumn('description', '商品描述')
                ->addTableColumn('price', '商品价格')
                ->addTableColumn('bv', 'BV值')
                ->addTableColumn('create_time', '创建时间')
                ->addTableColumn('update_time', '更新时间')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($goods_list) //数据列表
                ->setTableDataPage($page->show()) //数据列表分页
                ->addRightButton('restore') //添加还原按钮
                ->addRightButton('delete') //添加删除按钮
                ->display();
    }

    /**
     * 设置一条或者多条数据的状态
     * @author bigfoot<bigfootzq@163.com>
     */
    public function setStatus($model = CONTROLLER_NAME) {
        $ids    = I('request.ids');
        $status = I('request.status');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        $map['id'] = array('in',$ids);
        switch ($status) {
            case 'delete' :  // 删除条目
                $map['status'] = -1;
                $info = D('Index')->detail($ids, $map);
                $extend_table_object = D(strtolower(D('Index')->moduleName.'_'.$info['doc_type_info']['name']));
                $exist = $extend_table_object->find($ids);
                if ($exist) {
                    $result = $extend_table_object->delete($ids);
                } else {
                    $result = true;
                }
                if ($result) {
                    $result2 = D('Index')->delete($ids);
                    if ($result2) {
                        $this->success('彻底删除成功');
                    } else {
                        $this->error('删除失败');
                    }
                } else {
                    $this->error('删除失败');
                }
                break;
            default :
                parent::setStatus($model);
                break;
        }
    }
}
