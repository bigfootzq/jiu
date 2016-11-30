<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Finance\Model;
use Think\Model;
/**
 * 电子币转账模型
 * @author bigfoot
 */
 
class CoinTransferModel extends Model {
    /**
     * 模块名称
     * @author jry <598821125@qq.com>
     */
    public $moduleName = 'Finance';

    /**
     * 数据库真实表名
     * 一般为了数据库的整洁，同时又不影响Model和Controller的名称
     * 我们约定每个模块的数据表都加上相同的前缀，比如微信模块用weixin作为数据表前缀
     * @author jry <598821125@qq.com>
     */
    public $tableName = 'Finance_transfer';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('transfer_value', 'number', '转账金额必须为数字', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('transfer_value', 'require', '转账金额不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('username', 'require', '接受者用户名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('reusername', 'username', '两次输入的用户名不一致', self::EXISTS_VALIDATE, 'confirm', self::MODEL_INSERT),
        array('username', 'checkUser', '该用户不存在或被禁用', self::EXISTS_VALIDATE, 'callback'), 
        array('username', 'checkUserShop', '该用户未开店', self::EXISTS_VALIDATE, 'callback'), 
        
    );
    
    /**
     * 检测用户名是否存在或禁止
     * @param  string $username 用户名
     * @return boolean ture 未禁用，false 禁止
     */
    protected function checkUser($username){
        $id = get_user_id($username);
        if(!$id){
            return false;
        }else{
            $status = D('User/Caiwu')->getFieldByUid($id,'status');
            if ($status < 1 ){
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 检测用户名是否开店
     * @param  string $username 用户名
     * @return boolean ture 未禁用，false 禁止
     */
    protected function checkUserShop($username){
        $id = get_user_id($username);
        if(!$id){
            return false;
        }else{
            $shop = D('User/Caiwu')->getFieldByUid($id,'store_level');
            if ($shop < 1 ){
                return false;
            }
        }
        
        return true;
    }
}
