<?php
namespace Home\Controller;

class MessageController extends HomeController{	
	/**
	 * 用户留言
	 */
	public function liuyan(){
		$uid  = is_login() ;
		if(IS_POST){
			$data['title'] = I('title');
			$data['content'] = trim(I('content'));
          	if(empty($data['content'])){
            	$this->error('留言不能为空');
            }
			$data['fromuserid'] = $uid;
			$data['touser'] = 0;
			$data['create_time'] = time();
			$data['status'] = 0;
          
			$res = M('liuyan')->create($data);
			if($res){
				M('liuyan')->add();
				$this->success('留言成功',U(''));
			}else{
				$this->error('留言失败');
				 
			}
		}else{
		    $maps['type'] = $type = I('type',1);
		    $map['status'] = array('egt',0);
		    $map['fromuserid'] = $uid;//发件箱
		    $map['fromstatus'] = $uid;//发件箱
		    $m = new  \Think\Model();
		    $sql = "select * from zx_liuyan where status>=0 and (fromuserid=$uid or touser = $uid)";
		    
		    $list = $m->query($sql);
		    $where['fromuserid']=$uid;
          	$where['yidu']=0;
          	$where['status']=1;
          	$result = M('liuyan')->where($where)->setField('yidu',1);
		    
          
          $this->assign("bonus",get_bonus_rule());
		    $this->assign('_list',$list);
			$this->assign('title','会员留言');
			$this->display();
		}
		 
	}
	/**
	 * 留言列表
	 */
	public function liuyanList(){
		
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
		$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
		$maps['type'] = $type = I('type',1);
		 
		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");
		$map['create_time'] = array(array('egt',$starttime),array('elt',$endtime));
		
		
		$uid  = is_login() ;
		$map['status'] = array('egt',0);
		if($type==1){
			$map['touser'] = $uid;//收件箱
			$map['tostatus'] = 1;//收件箱
			$this->assign('title','收件箱');
		}
	
		if($type==2){
			$map['fromuserid'] = $uid;//发件箱
			$map['fromstatus'] = 1;//发件箱
			$this->assign('title','发件箱');
		}
		$opt = array(
				array(
						'type'=>'input',
						'ipttype'=>'hidden',
						'name'=>'type',
						'value'=>$maps['type'],
				)
		);
		$this->searchCondition($maps);
		
		$list = $this->lists("Liuyan",$map,$maps,"status");
		$this->assign('_list',$list);
		$this->assign('uid',$uid);
		$this->assign('type',$type);
	
		$this->display();
	}
	/**
	 * 留言内容
	 */
	public function liuyanContent(){
		$id = I('id');
		$content = M('Liuyan')->where(array('id'=>$id))->find();
		$this->assign('content',$content);
		$this->display();
	}
	/**
	 * 留言回复
	 */
	public function liuyanReply(){
		$id = I('id');
		$content = M('Liuyan')->where(array('id'=>$id))->getField('content');
		if(IS_POST){
			$reply = I('reply');
			$data['reply'] = $reply;
			$data['status'] = 1;
			$data['reply_time'] = time();
			$res = M('Liuyan')->where(array('id'=>$id))->save($data);
			if($res){
				$this->success('回复成功',U('liuyanList',array('type'=>1)));
			}else{
				$this->error('回复失败');
			}
		}else{
			$this->assign('content',$content);
			$this->assign('id',$id);
			$this->assign('title','留言回复');
			$this->display();
		}
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
		}else if($type == 2){
			if($status==0){
				$res = M('Liuyan')->where($map)->delete();
			}else{
				$res = M('Liuyan')->where($map)->setField('fromstatus',0);
			}
		}
		$this->success('删除成功',U('liuyanList',array('type'=>$type)));
	}	
}

