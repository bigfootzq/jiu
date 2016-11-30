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
 * 
 * @author bigfoot
 */
class GetmoneyModel extends Model {
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
    public $tableName = 'finance_getmoney';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('value', 'require', '提现金额不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
         // 验证金额
        array('value', '/^\d+(\.\d+)?$/', '金额只能为数字', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
        array('bank_name', 'require', '开户行不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('card_no', 'require', '银行卡号不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('account_name', 'require', '开户名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('paypassword', 'require', '支付密码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
    );
    protected $_auto = array(
        array('status', '0', self::MODEL_INSERT),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );
}
