<?php
namespace Shop\Controller;

class TransportController extends HomeController {
	private $Transport;

	public function _initialize() {
		parent::_initialize();
		$this->Transport = D('Transport');
		
	}
	
	/**
	 * 地址管理
	 */
	public  function address() {
	
		$uid = is_login();
		$id = I('id',0);
		if($id){
			$uaddress = $this->Transport->where(array('uid'=>$uid,'id'=>$id))->find();
			$param = param($uaddress['area']);
			$this->assign('param',$param);
			$this->assign('uaddress',$uaddress);
			$msg = '修改';
		}else{
			$msg = '新增';
		}
	
		$this->assign('msg',$msg);
		$this->title = $msg.'收货地址';
	
		$list=$this->Transport->where(array('uid'=>$uid))->select();
		foreach ($list as &$val){
			$val['area'] = $this->areaadd($val['area']);
		}
		$this->assign('list', $list);
		$this->assign('atype',1);
	
		$this->meta_title = '收货地址管理';
		$this->display();
	}
	
	/**
	 * 设置默认地址
	 */
	public  function shezhi() {
		if(IS_AJAX){
			$uid=is_login();
			$data['status'] = 0;
			$this->Transport->where(array('uid'=>$uid))->save($data);
			$id = I('id');
			$map['uid'] = $uid;
			$map['id'] = $id;
			$result=$this->Transport->where($map)->setField("status",1);
			if($result){
				$msg = "设置成功";
			}else{
				$msg = "设置失败";
			}
	
			$this->ajaxreturn($msg);
		}
	}
	
	/**
	 * 增加地址
	 */
	public  function addAddress() {
		$uid = is_login();
		$data['cellphone'] = I('cellphone');
		$data['realname'] = I('realname');
	
		$data['area'] = I('area');
		$data['address'] = I('address');
	
	
		$defaultAddress = I('isdefault');
		if($defaultAddress){
			$data['status'] = 1;
		}else{
			$data['status'] = 0;
		}
		$res = $this->Transport->addAddress($data);
		if(!is_array($res)){
			$info['reponsecode'] = $res;
			$info['errmsg'] = $this->errorMsg($res);
		}else{
			$info['reponsecode'] = 1;
			$res['area'] = $this->areaadd($res['area']);
			$info = $res;
			
		}
	
		$this->ajaxReturn($info);
	}
	
	/**
	 * 删除地址
	 */
	public  function deleteAddress($id){
		$uid = is_login();
		$map['uid'] = $uid;
		$map['id'] = $id;
		if($this->Transport->where($map)->delete()){
			$data['msg'] = "删除成功";
			$data['status'] = 1;
		}else{
			$data['msg'] = "删除失败";
			$data['status'] = 0;
		}
	
		$this->ajaxreturn($data);
	}
	
	/**
	 * 修改收货地址
	 * @param unknown $id
	 */
	public function updateAddress($id){
		if(IS_POST){
			$uid = is_login();
			$data['cellphone'] = I('cellphone');
			$data['realname'] = I('realname');
			
			$data['area'] = I('area');
			$data['address'] = I('address');
			
			$defaultAddress = I('isdefault');
			if($defaultAddress){
				$data['status'] = 1;
			}else{
				$data['status'] = 0;
			}
			$data['id'] = $id;
			$res = $this->Transport->updateAddress($data);
			if($res>0){
				$this->success('编辑成功',U('address'));
			}else{
				$this->error($this->errorMsg($res));
			}
			
		}else{
			$uid = is_login();
			$map['uid'] = $uid;
			$map['id'] = $id;
			$data = $this->Transport->where($map)->find();
			$area = str2arr($data['area']);
			
			$param['class'] = 'selt selt1';
			$param['province'] = $area['0'];
			$param['city'] = $area['1'];
			$param['district'] = $area['2'];
			$this->assign('param',$param);
			$this->assign('data',$data);
			$this->meta_title='修改收货地址';
			$this->display();
		}
	}
	
	public function findAddress($id){
		$uid = is_login();
		$map['uid'] = $uid;
		$map['id'] = $id;
		$data = $this->Transport->where($map)->find();
		$area = str2arr($data['area']);
		
		$param['class'] = 'selt selt1';
 		$param['province'] = $area['0'];
        $param['city'] = $area['1'];
        $param['district'] = $area['2'];
        $data['param'] =$param;
		$this->ajaxreturn($data);
	}
	
	private function errorMsg($errcode){
		switch ($errcode) {
			case -1:
				$error = '手机号格式错误！';
				break;
			case -2:
				$error = '收货人姓名不能为空！';
				break;
			case -3:
				$error = '收货地址不能为空';
				break;
			default:
				$error = '未知错误';
		}
		return $error;
	}
	
}