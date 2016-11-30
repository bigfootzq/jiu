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
 * 奖励模型
 * @author bigfoot
 */
class RewardModel extends Model {
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
    public $tableName = 'Finance_reward';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        // array('title', 'require', '', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
    );
    
    public function get_reward_type($reward_type){
        switch($reward_type){
            case   0:
                return '系统';
            case   1:
                return '组织奖';
            case   2:
                return '领导奖';
            case   3:
                return '月度返利';
            case   4:
                return '月度分红';
            case   5:
                return '店补';
            case   6:
                return '重消积分';
        }
    }
}
