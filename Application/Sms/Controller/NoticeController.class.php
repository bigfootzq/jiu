<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Sms\Controller;
use Home\Controller\HomeController;
use Common\Util\Think\Page;
/**
 * 文章控制器
 * @author jry <598821125@qq.com>
 */
class NoticeController extends HomeController {
    
     /**
     * 初始化方法
     * @author jry 
     */
    protected function _initialize(){
        parent::_initialize();
        $this->is_login();
    }

    /**
     * 公告列表
     * @author bigfoot<bigfootzq@163.com>
     */
    public function index(){ 
            $map['status'] = array('egt', '1'); // 正常状态
            $notice_list = M('sms_notice')
                        ->where($map)  
                       ->order('id DESC')  
                       ->select(); 
            $page = new Page(
                M('sms_notice')->where($map)->count(),
                C('ADMIN_PAGE_ROWS')
            ); 
            // dump($notice_list);
            $this->assign('page', $page->show());
            $this->assign('meta_title', '公告列表');
            $this->assign('notice_list', $notice_list);
            $this->display();

       } 
       
    /**
     * 详细信息
     * @author jry <598821125@qq.com>
     */
    public function detail($id) {
        $info = D('Notice')->find($id);
        if ($info['status'] !== '1') {
            $this->error('该公告不存在或已禁用',U('index'));
        }
        $this->assign('info', $info);
        $this->assign('meta_title', $info['title']);
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }
}
