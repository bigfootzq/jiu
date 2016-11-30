<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace User\Controller;
use Home\Controller\HomeController;
/**
 * 生日提醒控制器
 * @author bigfoot
 */
class BirthdayController extends HomeController {
    
    
    /**
     * 默认方法
     * 每天日清算后运行一次
     * @author jry 
     */
    public function index(){
        
        $data = D('User/User')->select();
        // dump($data);
        $todaydate = date("m-d");
        $bir_name = array();
        // $todaydate = "05-13";
        // echo $todaydate;
        for( $i  =  0 ,  $size  =  count ( $data );  $i  <  $size ; ++ $i ){
            
            if(!empty($data[$i]['id_number'])){
                // dump($data[$i]['id_number']);
                $year = substr($data[$i]['id_number'],6,4);
                $month = substr($data[$i]['id_number'],10,2);
                $day  = substr($data[$i]['id_number'],12,2);
                $date = $month."-".$day;
                // dump($date);
                if( $todaydate == $date){
                    $bir_name[] = $data[$i]['username'];
                    //生日，发送一封祝福私信
                    $msg_data['to_uid'] = $data[$i]['id'];
                    $msg_data['title']  = '生日快乐！';
                    $msg_data['content'] = '尊敬的用户'.$data[$i]['username'].'您好：<br>'
                                           .'今天是您的生日。'.'<br>'
                                           .'系统祝您生日快乐，给您送上诚挚的生日祝福！';
                   D('User/Message')->sendMessage($msg_data);
                }
            }
            
        }
       // dump($bir_name);
    }
    
    
}
