<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace User\Model;
use Think\Model\RelationModel;
/**
 * 用户关联模型
 * @author bigfoot
 */

class RegModel extends RelationModel{
    
    protected $tableName = 'admin_user';
    
    protected $_link = array( 
                            'info'=>array( 'mapping_type' => self::HAS_ONE, 
                                            'class_name' => 'user_info',
                                            'foreign_key' => 'uid', 
                                            'mapping_fields' => 'visitpassword,paypassword', 
                                            ),
                            'tree'=>array( 'mapping_type' => self::HAS_ONE, 
                                            'class_name' => 'user_tree',
                                            'foreign_key' => 'id',
                                            'mapping_fields' => 'position,pid,sid,fid', 
                                            ),
                            );
                            
    protected $_validate = array(
        // 验证用户类型
        array('user_type', 'require', '请选择用户类型', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),

        //验证用户名
        // array('nickname', 'require', '姓名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),

        // 验证用户名
        array('username', 'require', '请填写用户名', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('username', '3,32', '用户名长度为1-32个字符', self::MUST_VALIDATE, 'length', self::MODEL_INSERT),
        array('username', '', '用户名被占用', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
        array('username', '/^(?!_)(?!\d)(?!.*?_$)[\w]+$/', '用户名只可含有数字、字母、下划线且不以下划线开头结尾，不以数字开头！', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('username', 'checkDenyMember', '该用户名禁止使用', self::EXISTS_VALIDATE, 'callback'), //用户名禁止注册

        // 验证密码
        array('password', 'require', '请填写密码', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('password', '6,30', '密码长度为6-30位', self::MUST_VALIDATE, 'length', self::MODEL_INSERT),
        array('password', '/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+)$)^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+$/', '密码至少由数字、字符、特殊字符三种中的两种组成', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('repassword', 'password', '两次输入的密码不一致', self::EXISTS_VALIDATE, 'confirm', self::MODEL_INSERT),
        

        // 验证邮箱
        // array('email', 'email', '邮箱格式不正确', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
        // array('email', '1,32', '邮箱长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        // array('email', '', '邮箱被占用', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        // 验证身份证号码
        array('id_number', 'require', '身份证号码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('id_number,tree','is_pid','该身份证号码已经存在推荐人',1,'callback',1), 

        // 验证手机号码
        array('mobile', '/^1\d{10}$/', '手机号码格式不正确', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
        // array('mobile', '', '手机号被占用', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        // 验证注册来源
        array('reg_type', 'require', '注册来源不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
       
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('score', '0', self::MODEL_INSERT),
        array('money', '0', self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('password', 'user_md5', self::MODEL_BOTH, 'function'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('status', '1', self::MODEL_INSERT),
    );
    
    /**
     * 检测用户名是不是被禁止注册
     * @param  string $username 用户名
     * @return boolean ture 未禁用，false 禁止注册
     */
    protected function checkDenyMember($username){
        $deny = C('user_config.deny_username');
        $deny = explode ( ',', $deny);
        foreach ($deny as $k=>$v) {
            if(stristr($username, $v)){
                return false;
            }
        }
        return true;
    }
    /**
     * 检测某个身份证号码是否已经有推荐人
     * @param  string $data
     * @return boolean ture 未禁用，false 禁止
     */
    protected function is_pid($data){
        // dump($data);
        $id_number = $data['id_number'];
        // dump($id_number);
        $pid = $data['tree']['pid'];
        // dump($pid);
        $ex_pid = D('User/InfoView')->where("id_number = '%s'",$id_number)->getField('pid');
        // dump($ex_pid);
        if(!empty($ex_pid) && isset($ex_pid)){
            if ( $pid != $ex_pid){
                $pid_id_number = D('User/InfoView')->where("User.id = '%d'",$pid)->getField('id_number');
                // echo D('User/InfoView')->_sql();
                // dump($pid_id_number);
                // dump($id_number);
                if( $id_number == $pid_id_number){
                    return true;//如果推荐人的身份证和输入的身份证号码一样，返回true，推荐人可以推荐自己的小号
                }
                return false;
            }
        }
        return true;
    }
    
    /**
     * 创建数据对象 但不保存到数据库
     * @access public
     * @param mixed $data 创建数据
     * @param string $type 状态
     * @return mixed
     */
     public function create($data='',$type='') {
        $is_link = $this->_link; //对$this->_link字段进行备份，因为后面有一个 数据自动验证 函数会删除这个属性
        // 如果没有传值默认取POST数据
        if(empty($data)) {
            $data   =   I('post.');
        }elseif(is_object($data)){
            $data   =   get_object_vars($data);
        }
        // 验证数据
        if(empty($data) || !is_array($data)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        // 状态
        $type = $type?:(!empty($data[$this->getPk()])?self::MODEL_UPDATE:self::MODEL_INSERT);
        // 检查字段映射
        if(!empty($this->_map)) {
            foreach ($this->_map as $key=>$val){
                if(isset($data[$key])) {
                    $data[$val] =   $data[$key];
                    unset($data[$key]);
                }
            }
        }
        // 检测提交字段的合法性
        if(isset($this->options['field'])) { // $this->field('field1,field2...')->create()
            $fields =   $this->options['field'];
            unset($this->options['field']);
        }elseif($type == self::MODEL_INSERT && isset($this->insertFields)) {
            $fields =   $this->insertFields;
        }elseif($type == self::MODEL_UPDATE && isset($this->updateFields)) {
            $fields =   $this->updateFields;
        }
        if(isset($fields)) {
            if(is_string($fields)) {
                $fields =   explode(',',$fields);
            }
            // 判断令牌验证字段
            if(C('TOKEN_ON'))   $fields[] = C('TOKEN_NAME', null, '__hash__');
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields)) {
                    unset($data[$key]);
                }
            }
        }
        // 数据自动验证
        if(!$this->autoValidation($data,$type)) return false;
        // 表单令牌验证
        if(!$this->autoCheckToken($data)) {
            $this->error = L('_TOKEN_ERROR_');
            return false;
        }
        // 验证完成生成数据对象
        if($this->autoCheckFields) { // 开启字段检测 则过滤非法字段数据
            $fields =   $this->getDbFields();
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields)) {
                    if($is_link) { //如果是关联模型，则保留关联数据
                        if(!is_array($data[$key])) unset($data[$key]);
                    } else { //否则剔除非法字段
                        unset($data[$key]);
                    }
                }elseif(MAGIC_QUOTES_GPC && is_string($val)){
                    $data[$key] =   stripslashes($val);
                }
            }
        }
        $this->options['link'] = $is_link; //还原关联模型的属性
        // 创建完成对数据进行自动处理
        $this->autoOperation($data,$type);
        // 赋值当前数据对象
        $this->data =   $data;
        // 返回创建的数据以供其他调用
        return $data;
     }
     
     /**
	 * 自动表单处理
	 * @access public
	 * @param array $data 创建数据
	 * @param string $type 创建类型
	 * @return mixed
	 */
	private function autoOperation(&$data,$type) {
		if(!empty($this->options['auto'])) {
			$_auto   =   $this->options['auto'];
			unset($this->options['auto']);
		}elseif(!empty($this->_auto)){
			$_auto   =   $this->_auto;
		}
		// 自动填充
		if(isset($_auto)) {
			foreach ($_auto as $auto){
				// 填充因子定义格式
				// array('field','填充内容','填充条件','附加规则',[额外参数])
				if(empty($auto[2])) $auto[2] =  self::MODEL_INSERT; // 默认为新增的时候自动填充
				if( $type == $auto[2] || $auto[2] == self::MODEL_BOTH) {
					if(empty($auto[3])) $auto[3] =  'string';
					switch(trim($auto[3])) {
						case 'function':	//  使用函数进行填充 字段的值作为参数
						case 'callback': // 使用回调方法
							$args = isset($auto[4])?(array)$auto[4]:array();
							if(isset($data[$auto[0]])) {
								array_unshift($args,$data[$auto[0]]);
							}
							if('function'==$auto[3]) {
								$data[$auto[0]]  = call_user_func_array($auto[1], $args);
							}else{
								$data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
							}
							break;
						case 'field':	// 用其它字段的值进行填充
							$data[$auto[0]] = $data[$auto[1]];
							break;
						case 'ignore': // 为空忽略
							if($auto[1]===$data[$auto[0]])
								unset($data[$auto[0]]);
							break;
						case 'string':
						default: // 默认作为字符串填充
							$data[$auto[0]] = $auto[1];
					}
					if(isset($data[$auto[0]]) && false === $data[$auto[0]] )   unset($data[$auto[0]]);
				}
			}
		}
		return $data;
	}
    
    
}