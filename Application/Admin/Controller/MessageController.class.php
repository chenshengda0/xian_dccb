<?php
namespace Admin\Controller;

use User;

/**
 * 用户留言
 * @author yao
 *
 */
class MessageController extends AdminController {
	private static  $liuyan;
	
	public function _initialize(){
		parent::_initialize();
		self::$liuyan = D('Common/liuyan');
	}
	/**
	 * 用户留言
	 */
	public function liuyan(){
		if(IS_POST){
			$username = I('usernumber');
			$uinfo = M('member')->where(array('usernumber'=>$username))->find();
			if(empty($uinfo)){
				$this->error('用户不存在');
			}
			$data['title'] = I('title');
			$data['content'] = I('content');
			$data['fromuserid'] = 0;
			$data['touser'] = $uinfo['uid'];
			
			$res = self::$liuyan->create($data);
			if($res){
				self::$liuyan->add();
				$this->success('留言成功',U('liuyanList',array('type'=>2)));
			}else{
				$this->error(self::$liuyan->getError());
	
			}
		}else{
			$this->assign('meta_title','写留言');
			$this->display();
		}
	
	}
	/**
	 * 留言列表
	 */
	public function liuyanList(){
		$type = I('type',1);
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
		$map['status'] = array('egt',0);
		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");
		if($type==2){
			$map['fromuserid'] = 0;//发件箱
			$map['fromstatus'] = 1;//发件箱
			$this->assign('meta_title','发件箱');
			$this->assign('thtitle','收件用户');
		}
		if($type==1){
			$map['touser'] = 0;//收件箱
			$map['tostatus'] = 1;//收件箱
			$this->assign('meta_title','收件箱');
			$this->assign('thtitle','发件用户');
		}
		$maps['type'] = $type;
		$map['count_date'] = array(array('gt',$starttime),array('lt',$endtime)) ;
		if(!empty($usernumber)){
			$numwhere['usernumber']= $usernumber ;
			$user = M('Member')->where($numwhere)->find();
			if(!$user){
				$this->error('用户不存在！');
			}else{
				if($type==2){
					$map['touser'] = $user['uid'];//发件箱
				}
				if($type==1){
					$map['fromuserid'] = $user['uid'];//收件箱
				}
			}
		}
		
		$list = $this->lists('Liuyan',$map,$maps);
		$this->searchCondition($maps);
	
		$this->assign('_list',$list);
		$this->assign('type',$type);
	
		$this->display();
	}
	/**
	 * 留言回复
	 */
	public function liuyanReply(){
		$id = I('id');
		$fromuserid = I('fromuserid');
		$content = M('Liuyan')->where(array('id'=>$id))->getField('content');
		if(IS_POST){
			$reply = I('reply');
			$data['reply'] = $reply;
			$data['id'] = $id;
			$liuyan = D('liuyan');
			$res = $liuyan->create($data);
			if($res){
				$liuyan->save();
				$this->success('回复成功',U('liuyanList',array('type'=>1)));
			}else{
				$this->error($liuyan->getError());
			}
		}else{
			$this->assign('fromuserid',$fromuserid);
			$this->assign('content',$content);
			$this->assign('id',$id);
			$this->assign('meta_title','留言回复');
			$this->display();
		}
	}
	/**
	 * 留言内容
	 */
	public function liuyanContent(){
		$id = I('id');
		$type = I('type');
		$content = M('Liuyan')->where(array('id'=>$id))->find();
		$this->assign('content',$content);
		$this->assign('type',$type);
		 
		$this->display();
	}
	/**
	 * 删除留言信息
	 */
	public function deleteLiuyan(){
		$id = I('id');
		$map['id'] = $id;
		$type = I('type');
		$status = I('status');
		if($type == 1){
			$res = M('Liuyan')->where($map)->setField('tostatus',0);
		}
		if($type == 2){
			if($status==0){
				$res = M('Liuyan')->where($map)->delete();
			}else{
				$res = M('Liuyan')->where($map)->setField('fromstatus',0);
			}
		}
		if($res){
			$this->success('删除成功',U('liuyanList',array('type'=>$type)));
		}else{
			$this->error('删除失败',U('liuyanList',array('type'=>$type)));
		}
	}
}