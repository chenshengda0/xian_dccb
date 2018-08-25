<?php
namespace think\wxpay;
use Think\Exception;
class WxPayException extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
