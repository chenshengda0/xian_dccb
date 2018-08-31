<?php

namespace Home\Model;

use Think\Model;

/**
 * 充值模型
 */
class RechargeModel extends Model
{
    /* 用户模型自动完成 */
    protected $_auto = array(
        array('createtime', 'time',self::MODEL_INSERT,'function'),
        array('status', 0, self::MODEL_INSERT),
        array('order_id', 'create_order', self::MODEL_INSERT,'callback'),
    );

    protected $_validate = array(
    	array('out_banknumber', 'require', '请输入汇款账号', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
    	//array('out_banknumber', 'check_card', '银行卡号格式不正确', self::EXISTS_VALIDATE, 'callback', self::MODEL_INSERT),
    	array('out_bankholder', 'require', '请输入开户人', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
    	array('out_bankname', 'require', '请输入汇开户行', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
    	array('mobile', 'require', '请输入预留手机号，方便核实', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
    	array('outtime', 'require', '请输入汇款时间，以便核对信息', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
    );
    
    /*创建充值订单号*/
    public function create_order($out_banknumber){
    	$uid = is_login();
    	$type = 2;
    	return create_order($uid,2);
    }
    
    /*银行卡号格式检测*/
    public function check_card($out_banknumber){
    	$res = bankInfo($out_banknumber);
    	if(($res==-2)||($res==-1)){
    		return false;
    	}
    	return true;
    }

}
