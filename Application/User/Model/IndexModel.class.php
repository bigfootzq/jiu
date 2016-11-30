<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace User\Model;
use Think\Model;
/**
 * 
 * @author jry <598821125@qq.com>
 */
class IndexModel extends Model {
    /**
     * 数据库表名
     * @author jry <598821125@qq.com>
     */
    protected $tableName = 'admin_user';
    
    /*
    *
    *
    */
  
    
    /*
    * 返回用户级别
    * @author: bigfoot
    */
    public function get_user_level($user_level){
       
        switch ( $user_level ) {
        case  0 :
        case  1 :
        case  2 :
        case  3 :
        case  4 :
            return  "会员" ;

        case  5 :
            return  "主任" ;

        case  6 :
            return "经理" ;

        case  7 :
            return  "总监" ;
        default:
            return  "会员";

        }
    }
    
    /*
    * 返回用户店铺级别
    * @author: bigfoot
    */
    public function get_store_level($store_level){
       
        switch ( $store_level ) {
        case  0 :
            return  "无" ;

        case  1 :
            return  "社区店" ;

        case  2 :
            return "经理店" ;

        case  3 :
            return  "中心店" ;
        default:
            return  "无";

        }
    }

    /*
    * 返回用户注册金额,0 390 |1 780
    * @author: bigfoot
    */
    public function get_basic_value($basic_level){
       
        switch ( $basic_level ) {
        case  0 :
            return  "390" ;

        case  1 :
            return  "780" ;
            
        default:
            return  "未定";

        }
    }
}
