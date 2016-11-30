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
 * 文章模型
 * @author jry <598821125@qq.com>
 */
class TransferModel extends Model {
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
        array('transfer_value', 'number', '转换金额必须为数字', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('transfer_value', 'require', '转换金额不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        
    );
    
    public function get_transfer_type($transfer_type){
        switch($transfer_type){
            case   0:
                return '未知';
            case   1:
                return '电子币转出';
            case   2:
                return '奖金币转入';
            case   3:
                return '奖金币转换为电子币';
            case   4:
                return '消费奖励积分转入';
            case   5:
                return '消费奖励积分转出';
            case   6:
                return '电子币转入';
            case   7:
                return '奖金币转出';
            case   8:
                return '重复消费积分转入';
            case   9:
                return '重复消费积分转出';
        }
        
    }
   
}
