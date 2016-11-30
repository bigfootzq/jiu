<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace User\Model;
use Think\Model\AdvModel;
/**
 * 用户模型
 * @author jry <598821125@qq.com>
 */
class TreeModel extends AdvModel {
    /**
     * 数据库表名
     * @author jry <598821125@qq.com>
     */
    protected $tableName = 'user_tree';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('sid', 'require', '商务中心用户名输入不正确', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('sid', 'is_shop', '您所填写的用户不是商务中心', self::MUST_VALIDATE, 'callback',self::MODEL_BOTH),
        array('pid', 'require', '推荐人用户名输入不正确', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('fid', 'require', '请输入安置人用户名', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('fid', 'is_live', '您选择的安置人未激活或已被禁止', self::MUST_VALIDATE, 'callback',self::MODEL_BOTH),
        array('position', 'require', '请选择安置市场', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        
        
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('status', '1', self::MODEL_INSERT),
    );
    
    protected function is_shop($sid){
        if ( $sid && !empty($sid) ){
            $store_level = D('User/Caiwu')->where('uid = %d',$sid)->getField('store_level');
            // dump($store_level);
            if(isset($store_level) && (int)$store_level > 0 ){
                    return true;
              }
        }
        return false;
    }
    //判断安置人是否已经激活
    protected function is_live($fid){
        if ( $fid && !empty($fid) ){
            $status = D('User/Caiwu')->where('uid = %d',$fid)->getField('status');
            // dump($status);
            if(isset($status) && $status == 1 ){
                    return true;
              }
        }
        return false;
    }
}
