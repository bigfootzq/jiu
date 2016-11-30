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
 * 多叉树节点类
 * 
 * @author long
 */
class MTNode{
    //子树数组
    public $mChild=array();
    //子树数量
    public $mChildnum=0;
    //结点数据域
    public $mData=null; 
    //父节点，    
    public $mFather=null;
    //深度，层数   
    public $mDepth=null;
    
	public function addChild($node){
        // dump($node);
		// array_push($mChild, $node);
        $this->mChild[] = $node;
		$this->mChildnum++;
        // dump($this->mChild);
	}
}