<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace User\Model;
use Think\Model\ViewModel;
/**
 * 用户信息视图模型
 * @author bigfoot
 */
class InfoViewModel extends ViewModel {

    public $viewFields = array( 
                                'User'=>array( 'id','username','nickname','create_time','id_number',
                                               '_table'=>"jiu_admin_user"     
                                              ), 
                                'Info'=>array(  'user_level','store_level','total_sales' ,'total_score', 'start_time','month_repeat_score','coin','reward_score','repeat_score','reward_coin',
                                                'status' => 'info_status',
                                                '_table'=>"jiu_user_info" ,
                                                '_on'=>'Info.uid=User.id'
                                                ),
                                'Tree'=>array(  'fid','pid','lid','rid','position','month_sales','day_sales','total_market_sales',
                                                'status' => 'tree_status',
                                                'id'     => 'tree_id',
                                                '_table'=>"jiu_user_tree",                            
                                                '_on'=> 'Tree.id=User.id'
                                                ),
                                );
}
