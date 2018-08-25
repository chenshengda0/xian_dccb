<?php

namespace Shop\Model;
use Think\Model;

/**
 * 文档基础模型
 */
class TransportModel extends Model{
	protected $_validate = array(
			array('cellphone', '/^((0?1[3578]\d{9})|(\d{3,4}-)?\d{7,8})$/','-1', self::EXISTS_VALIDATE, 'regex'), //手机号格式不正确
			array('realname','require','-2',self::EXISTS_VALIDATE), //收货人姓名不能为空
			array('address','require','-3',self::EXISTS_VALIDATE), //收货地址不能为空
			
	);
	
	protected $_auto = array(
			array('time', NOW_TIME, self::MODEL_BOTH),
			array('uid', 'getUid', self::MODEL_BOTH,'callback'),
	);
	
	public function getUid(){
		return is_login();
	}
	
	public function addAddress($data){
		$res = $this->create($data);
		if($res<0){
			return $res;
		}else {
			$id = $this->add();
			$info = $this->find($id);
			if($info['status']==1){
				$map['uid'] = $info['uid'];
				$map['id'] = array('neq',$id);
				$this->where($map)->setField('status',0);
			}
			return $info;
		}
	}
	
	public function updateAddress($data){
		$res = $this->create($data);
		if($res<0){
			return $res;
		}else {
			$id = $this->save();
			$info = $this->where(array('id'=>$data['id']))->find();
			if($info['status']==1){
				$map['uid'] = $info['uid'];
				$map['id'] = array('neq',$data['id']);
				$this->where($map)->setField('status',0);
			}
			return $id;
		}
	}
	

}
