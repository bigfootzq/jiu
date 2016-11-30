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
/**
 * 消息控制器
 * @author jry <598821125@qq.com>
 */
class MessageController extends HomeController{
    /**
     * 初始化方法
     * @author jry <598821125@qq.com>
     */
    protected function _initialize(){
        parent::_initialize();
        $this->is_login();
    }

    /**
     * 默认方法
     * @param $type 消息类型
     * @author jry <598821125@qq.com>
     */
    public function index($type = 0){
        $map['type'] = array('eq', $type);
        $map['status'] = array('eq', 1);
        $map['to_uid'] = array('eq', is_login());
        $message_object = D('User/Message');
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $data_list = $message_object
                   ->page($p, C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('sort desc,id desc')
                   ->select();
        $page = new Page(
            $message_object->where($map)->count(),
            C('ADMIN_PAGE_ROWS')
        );
        $message_type = $message_object->message_type();
        foreach ($message_type as $key => $val) {
            if ($count = D('User/Message')->newMessageCount($key)) {
                $new_message_type[$key] = $count;
            }
        }
        // dump($data_list);
        $this->assign('message_list', $data_list);
        $this->assign('page', $page->show());
        $this->assign('message_type', $message_type);
        $this->assign('new_message_type', $new_message_type);
        $this->assign('current_type', $type);
        $this->assign('meta_title', "消息中心");
        $this->display();
    }

    /**
     * 查看消息
     * @param $type 消息类型
     * @author jry <598821125@qq.com>
     */
    public function detail($id){
        $message_object = D('User/Message');
        $user_message_info = $message_object->find($id);
        if(!$user_message_info){
            $this->error('该消息已禁用或不存在');
        }
        $map['id'] = array('eq', $id);
        $message_object->where($map)->setField('is_read', 1);
        $this->assign('user_message_info', $user_message_info);
        $this->assign('current_type', $user_message_info['type']);
        $this->assign('meta_title', $user_message_info['title']);
        $this->display();
    }


    /**
     * 发送私信
     * @param $type 消息类型
     * @author jry <598821125@qq.com>
     */
    public function send(){
        if(IS_POST){
            //构造消息数组
            $msg_data['to_uid'] = get_user_id( I('post.username') );
            $msg_data['from_uid'] = $this->is_login();
            $msg_data['title']  = I('post.title');
            $msg_data['content'] = I('post.content');
             $msg_data['type']     =  2;
            // dump($msg_data);
            $result = D('User/Message')->sendMessage($msg_data);
            if ($result){
                $this->success('发送私信成功');
            }
        }else{
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('发送私信') // 设置页面标题
                    ->setPostUrl(U(''))        // 设置表单提交地址
                    ->addFormItem('username', 'text', '收信人')
                    ->addFormItem('title', 'text', '标题')
                    ->addFormItem('content', 'textarea', '内容')
                    ->setTemplate(C('USER_CENTER_FORM'))
                    ->display();
        }
    }

    /**
     * 获取当前用户未读消息数量
     * @param $type 消息类型
     * @author jry <598821125@qq.com>
     */
    public function newMessageCount($type = null){
        $data['status'] = 1;
        $data['new_message'] = D('User/Message')->newMessageCount($type);
        $this->ajaxReturn($data);
    }

    /**
     * 设置已读
     * @param $type 消息类型
     * @author jry <598821125@qq.com>
     */
    public function setRead($ids = null, $type = null){
        $map['status']  = array('eq', 1);
        $map['to_uid']  = array('eq', is_login());
        $map['is_read'] = array('eq', 0);
        if ($ids !== null) {
            $map['id'] = array('in', $ids);
        }
        if ($type !== null) {
            $map['type'] = array('eq', $type);
        } else {
            if ($ids === null) {
                $this->error('请勾选消息');
            }
        }
        $result = D('User/Message')->where($map)->setField('is_read', 1);
        if ($result) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
    
    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME) {
        $ids    = I('request.ids');
        $status = I('request.status');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        $map['id'] = array('in',$ids);
                parent::setStatus($model);
        }
}
