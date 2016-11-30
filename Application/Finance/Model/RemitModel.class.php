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
 * 汇款通知模型
 * @author jry <598821125@qq.com>
 */
class RemitModel extends Model {
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
    public $tableName = 'finance_remittance';

    // protected $patchValidate = true;
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('bank_name', 'require', '请填写开户行', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('card_no', 'require', '请填写银行卡号', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('account_name', 'require', '请填写开户名', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('value', 'require', '请填写汇款金额', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('value', 'number', '汇款金额必须为数字', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('paypassword', 'require', '请填写支付密码', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        // array('card_no', '/^\d{16}$/', '银行卡号格式不正确', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
        // array('value', '/^\d{1,16}$/', '金额只能为数字', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
    );
    
    protected $_auto = array(
        array('status', '0', self::MODEL_INSERT),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );
}
