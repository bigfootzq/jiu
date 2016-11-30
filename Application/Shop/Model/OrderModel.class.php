<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Shop\Model;
use Think\Model;
/**
 * 订单信息
 * @auther bigfoot
 */
class OrderModel extends Model {
    /**
     * 模块名称
     * @author bigfoot
     */
    public $moduleName = 'Shop';

    /**
     * 数据库真实表名
     * 一般为了数据库的整洁，同时又不影响Model和Controller的名称
     * 我们约定每个模块的数据表都加上相同的前缀，比如微信模块用weixin作为数据表前缀
     * @author bigfoot
     */
    protected $tableName = 'shop_order';

    /**
     * 自动验证规则
     * @author bigfoot
     */
    protected $_validate = array(
        array('buy_id', 'require', '请填写购物人', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('shop_id', 'require', '请填写商务中心', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('shop_id', 'is_shop', '您所填写的用户不是商务中心', self::MUST_VALIDATE, 'callback',self::MODEL_BOTH),
        array('order_detail', 'require', '请选择您需要的商品', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('buy_fullname', 'require', '请填写收货人', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('address', 'require', '请选择您的地址', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('mob', 'require', '请填写您的手机号码', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
    );

    /**
     * 自动完成规则
     * @author bigfoot
     */
    protected $_auto = array(

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
}
