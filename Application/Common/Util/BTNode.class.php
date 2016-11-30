<?php
// +----------------------------------------------------------------------
// | OpenCMF [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.opencmf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <59821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Util;
/**
 * 二叉树节点类
 * 
 * @author long
 */
class BTNode{
    //左子树“指针”
    public $mLchild=null;
    //右子树“指针”
    public $mRchild=null;
    //结点数据域
    public $mData=null; 
    //父节点，    
    public $mFather=null;
    //深度，层数
    public $mDepth=null;

}