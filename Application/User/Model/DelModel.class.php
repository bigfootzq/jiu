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

class DelModel extends RelationModel{
    
    protected $tableName = 'admin_user';
    
    protected $_link = array( 
                            'info'=>array( 'mapping_type' => self::HAS_ONE, 
                                            'class_name' => 'user_info',
                                            'foreign_key' => 'uid',
                                            ),
                            'tree'=>array( 'mapping_type' => self::HAS_ONE, 
                                            'class_name' => 'user_tree',
                                            'foreign_key' => 'id',
                                            ),
                            );
                            
    protected $_validate = array(
       
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
    );
    
}