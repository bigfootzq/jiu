<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace User\Admin;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;
/**
 * 用户控制器
 * @author jry <598821125@qq.com>
 */
class UserAdmin extends AdminController {
    /**
     * 用户列表
     * @author jry <598821125@qq.com>
     */
    public function index() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id|username|nickname|email|mobile'] = array(
            $condition,
            $condition,
            $condition,
            $condition,
            $condition,
            '_multi'=>true
        );
        
        //获取所有管理组用户id
        $admin_arr = D('Admin/Access')->getField('uid',true);
        // 获取所有用户
        $map['jiu_admin_user.status'] = array('egt', '0'); // 禁用和正常状态
        $map['id'] = array('not in',$admin_arr);//排除所有管理组用户
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $user_object = D('User/User');
        // $base_table   = C('DB_PREFIX').'admin_user';
        // $extend_table = C('DB_PREFIX').'user_info';
        $data_list = $user_object
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                   ->order('id asc')
                   // ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
                   ->select();
        $page = new Page(
            $user_object
            ->where($map)
            // ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.uid', 'LEFT')
            ->count(),
            C('ADMIN_PAGE_ROWS')
        );

        // 使用Builder快速建立列表页面。
        //自定义按钮重置密码
        $attr2['name']  = 'resetPwd';
        $attr2['title'] = '重置密码';
        $attr2['class'] = 'label label-primary ajax-get confirm';
        $attr2['href']  = U('resetPwd', array('ids' => '__data_id__'));
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('用户列表') // 设置页面标题
                ->addTopButton('addnew')  // 添加新增按钮
                ->addTopButton('resume', array('model' => 'admin_user'))  // 添加启用按钮
                ->addTopButton('forbid', array('model' => 'admin_user'))  // 添加禁用按钮
                ->addTopButton('delete', array('model' => 'admin_user'))  // 添加删除按钮
                ->setSearch('请输入ID/用户名／邮箱／手机号', U('index'))
                ->addTableColumn('id', 'UID')
                ->addTableColumn('avatar', '头像', 'picture')
                ->addTableColumn('username', '用户名')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('email', '邮箱')
                ->addTableColumn('id_number', '身份证号码')
                ->addTableColumn('mobile', '手机号')
                // ->addTableColumn('score', '积分')
                ->addTableColumn('create_time', '注册时间', 'time')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->addRightButton('edit')          // 添加编辑按钮
                ->addRightButton('forbid')        // 添加禁用/启用按钮
                ->addRightButton('delete')        // 添加删除按钮 
                ->addrightButton('self',$attr2)    // 添加自定义按钮重置密码
                ->display();
    }

    /**
     * 新增用户
     * @author jry <598821125@qq.com>

    public function add() {
        if (IS_POST) {
            $user_object = D('User/User');
            $data = $user_object->create();
            if ($data) {
                $id = $user_object->add();
                if ($id) {
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('新增用户') //设置页面标题
                    ->setPostUrl(U('add'))    //设置表单提交地址
                    ->addFormItem('reg_type', 'hidden', '注册方式', '注册方式')
                    ->addFormItem('user_type', 'radio', '用户类型', '用户类型', select_list_as_tree('User/Type'))
                    ->addFormItem('nickname', 'text', '昵称', '昵称')
                    ->addFormItem('username', 'text', '用户名', '用户名')
                    ->addFormItem('password', 'password', '密码', '密码')
                    ->addFormItem('email', 'text', '邮箱', '邮箱')
                    ->addFormItem('email_bind', 'radio', '邮箱绑定', '手机绑定', array('1' => '已绑定', '0' => '未绑定'))
                    ->addFormItem('mobile', 'text', '手机号', '手机号')
                    ->addFormItem('mobile_bind', 'radio', '手机绑定', '手机绑定', array('1' => '已绑定', '0' => '未绑定'))
                    ->addFormItem('gender', 'radio', '性别', '性别', D('User/User')->user_gender())
                    ->addFormItem('avatar', 'picture', '头像', '头像')
                    ->setFormData(array('reg_type' => 'admin'))
                    ->display();
        }
    }
    */
    
    /**
     * 新增用户
     * @author 
     */
     public function add() {
        
        if (IS_POST) {
            if (!C('user_config.reg_toggle')) {
                $this->error('注册已关闭！');
            }
            
            $reg_data = array();
            switch ($reg_type) {
                case 'username': //用户名注册
                    //图片验证码校验
                    // if (!$this->check_verify(I('post.verify'))) {
                        // $this->error('验证码输入错误！');
                    // }
                    if (I('post.email')) {
                        $reg_data['email'] = I('post.email');
                    }
                    if (I('post.mobile')) {
                        $reg_data['mobile'] = I('post.mobile');
                    }
                    break;
                case 'email': //邮箱注册
                    //验证码严格加盐加密验证
                    // if (user_md5(I('post.verify'), I('post.email')) !== session('reg_verify')) {
                        // $this->error('验证码错误！');
                    // }
                    $_POST['username'] = I('post.username') ? I('post.username') : 'U'.time();
                    $reg_data['email'] = I('post.email');
                    $reg_data['email_bind'] = 1;
                    if (I('post.mobile')) {
                        $reg_data['mobile'] = I('post.mobile');
                    }
                    break;
                case 'mobile': //手机号注册
                    //验证码严格加盐加密验证
                    // if (user_md5(I('post.verify'), I('post.mobile')) !== session('reg_verify')) {
                        // $this->error('验证码错误！');
                    // }
                    $_POST['username'] = I('post.username') ? I('post.username') : 'U'.time();
                    $reg_data['mobile'] = I('post.mobile');
                    $reg_data['mobile_bind'] = 1;
                    if (I('post.email')) {
                        $reg_data['email'] = I('post.email');
                    }
                    break;
            }

            // 构造注册数据
            $reg_data['user_type'] = I('post.user_type') ? I('post.user_type') : 1;
            // $reg_data['nickname']  = I('post.nickname') ? I('post.nickname') : I('post.username');
            $reg_data['id_number'] = I('post.id_number');
            $reg_data['mobile']    = I('post.mobile');
            $reg_data['username']  = I('post.username');
            $reg_data['password']  = I('post.password');
            
            //user_info
           
            $user_info['visitpassword'] = I('post.visitpassword');
            $user_info['revisitpassword'] = I('post.revisitpassword');
            $user_info['paypassword'] =   I('post.paypassword');
           
            // dump($user_info);
            $info_object = D('User/Caiwu');
            $info2 = $info_object ->create($user_info);
            if (!$info2){
                $this->error('注册失败1,'.$info_object->getError());
            }
            // dump($info2);
            //构造用户树数据 
            // $tree_data['id']  = $id;
            $tree_data['sid'] =  get_user_id( I('post.shop_username') ); //商务中心用户ID
            $tree_data['pid'] =  get_user_id( I('post.promote_username') );//推荐人用户ID
            $tree_data['fid'] = get_user_id ( I('post.farther_username') );//父节点用户ID
            $tree_data['position'] =  I('post.position');//安置位置A|B
            // dump($tree_data);
            $tree_object = D('User/Tree');
            $tree2 = $tree_object->create($tree_data);
            if(!$tree2){
                $this->error('注册失败2,'.$tree_object->getError());
            }
            // dump($tree2);
            $pdata = $tree_object->where('id = %d',$tree2['fid'])->find();
            if($tree2['position'] == 'A'){
                // dump($pdata);
                if($pdata['lid'] > 0){
                    $this->error('注册失败2，安置人的A市场已经存在会员！');
                }
            }else if($tree2['position'] == 'B'){
                if($pdata['lid'] == 0){
                    $this->error('注册失败2，安置人的A市场还没有会员，必须先安置A市场！');
                }else if($pdata['rid'] > 0){
                    $this->error('注册失败2，安置人的B市场已经存在会员！');
                }
            }
            if ($_POST['repassword']) {
                $reg_data['repassword'] = $_POST['repassword'];
            }
            $reg_data['reg_type']  = I('post.reg_type');
            
            $reg_data["info"] = $info2;
            $reg_data["tree"] = $tree2;
            // dump($reg_data);
            $user_object = D("User/Reg");
            $data = $user_object->create($reg_data);
            // dump($data);
            // dump($user_object);
            if ($data) {
                $id = $user_object->relation(true)->add($data);
                // echo $user_object->_sql();
                // dump($id);
                if ($id) {
                    session('reg_verify', null);
                    // $user_info = $user_object->login($data['username'], I('post.password'), true);
                    // dump($position);
                    // dump($fid);
                    $map['id'] = $tree_data['fid'];
                    if($position = 'A'){
                         $p['lid'] = $id;
                    }else if ($position = 'B'){
                         $p['rid'] = $id;
                    }
                    // dump($map);
                    // dump($position);
                    $tree_object->where($map)->save($p);
                    // echo $tree_object->_sql();
                    $user_info = get_user_info($id);

                    

                    // 构造消息数据
                    $msg_data['to_uid'] = $user_info['id'];
                    $msg_data['title']  = '注册成功';
                    $msg_data['content'] = '尊敬的用户您好：<br>'
                                       .'恭喜您成功注册'.C('WEB_SITE_TITLE').'的帐号<br>'
                                       .'您的帐号信息如下（请妥善保管）：<br>'
                                       .'UID：'.$user_info['id'].'<br>'
                                       .'姓名：'.$user_info['nickname'].'<br>'
                                       .'用户名：'.$user_info['username'].'<br>'
                                       .'安置人用户名：'.I('post.farther_username').'<br>'
                                       .'推荐人用户名：'.I('post.promote_username').'<br>'
                                       .'商务中心用户名：'.I('post.shop_username').'<br>'
                                       // .'密码：'.$_POST['password'].'<br>'
                                       .'<br>';
                    // dump($msg_data);
                    D('User/Message')->sendMessage($msg_data);
                    if (is_wap()) {
                        // $url = U('User/Center/index');
                    } else {
                        $url = U('add');
                    }
                    $this->success('用户注册成功', $url);
                } else {
                    $this->error('注册失败3'.$user_object->getError());
                }
            } else {
                $this->error('注册失败4'.$user_object->getError());
            }
        } else {
            // if (is_login()) {
                // $this->error("您已登陆系统", Cookie('__forward__') ? : C('HOME_PAGE'));
            // }
            $this->assign('meta_title', '新增用户');
            $this->display();
        }
    }
    
    /**
     * 编辑用户
     * @author jry <598821125@qq.com>
     */
    public function edit($id) {
        if (IS_POST) {
            // 密码为空表示不修改密码
            if ($_POST['password'] === '') {
                unset($_POST['password']);
            }

            // 提交数据
            $user_object = D('User/User');
            $data = $user_object->create();
            if ($data) {
                $result = $user_object
                        ->field('id,nickname,username,password,email,email_bind,mobile,mobile_bind,gender,avatar,update_time')
                        ->save($data);
                if ($result) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败', $user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            // 获取账号信息
            $info = D('User/User')->find($id);
            unset($info['password']);

            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('编辑用户')  // 设置页面标题
                    ->setPostUrl(U('edit'))    // 设置表单提交地址
                    ->addFormItem('id', 'hidden', 'ID', 'ID')
                    ->addFormItem('user_type', 'radio', '用户类型', '用户类型', select_list_as_tree('User/Type'))
                    ->addFormItem('nickname', 'text', '姓名', '姓名')
                    ->addFormItem('username', 'text', '用户名', '用户名')
                    ->addFormItem('password', 'password', '密码', '密码')
                    ->addFormItem('email', 'text', '邮箱', '邮箱')
                    ->addFormItem('email_bind', 'radio', '邮箱绑定', '手机绑定', array('1' => '已绑定', '0' => '未绑定'))
                    ->addFormItem('id_number', 'text', '身份证号码', '身份证号码')
                    ->addFormItem('mobile', 'text', '手机号', '手机号')
                    ->addFormItem('mobile_bind', 'radio', '手机绑定', '手机绑定', array('1' => '已绑定', '0' => '未绑定'))
                    ->addFormItem('gender', 'radio', '性别', '性别', D('User/User')->user_gender())
                    ->addFormItem('avatar', 'picture', '头像', '头像')
                    ->setFormData($info)
                    ->display();
        }
    }
    
    /**
     * 重置密码
     * @author bigfoot
     *
     */
     public function resetPwd($ids){
        $ids = I('request.ids');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        if (is_array($ids)) {
            if(in_array('1', $ids)) {
                $this->error('超级管理员不允许操作');
            }
        } else {
            if($ids === '1') {
                $this->error('超级管理员不允许操作');
            }
        }
        $map['id'] = array('in',$ids);
        $info = array( 
                        'visitpassword' => user_md5('123456abc'),
                        'paypassword' => user_md5('123456abc'),
                    );
        
        $pwd = array( 
                        'password' => user_md5('123456abc'),
                        'info' => $info,
                    );
        $user_object = D("User/Reset");
        // dump($user_object);
        $reset = $user_object->where($map)->relation(true)->save($pwd);
        // echo $user_object->_sql();
        
        if ($reset !== false ){
            $this->success('重置密码成功');
        }else{
            $this->error('重置密码失败');
        }
     }
    
    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids = I('request.ids');
        $status = I('request.status');
        $map['id'] = array('in',$ids);
        if (is_array($ids)) {
            if(in_array('1', $ids)) {
                $this->error('超级管理员不允许操作');
            }
        } else {
            if($ids === '1') {
                $this->error('超级管理员不允许操作');
            }
        }
        
        if ($status == 'delete'){
            $result = D('Del')->where($map)->relation(true)->delete();
                if ($result) {
                    $this->success('删除成功，不可恢复！');
                } else {
                    $this->error('删除失败');
                }
        }else{
            parent::setStatus($model);
        }
    }
}
