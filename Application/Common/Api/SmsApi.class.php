<?php
namespace Common\Api;

final class SmsApi {

	/**
	 * 激活会员后向该会员发送通知
	 */
	public function activeSMS($uid,$mobile)
	{
		$content = "尊敬的%s阁下：恭喜您已成功加入百年杏花酒电子商务平台，您的会员编号是：%s，密码：%s，二级密码：%s，请及时修改密码以确保信息安全。让我们同心携力，共同开创属于我们的美好未来！";
		$defalut = C('SMS_CONTENT');
		if(!empty($defalut)){
			$content = $defalut;
		}
		$uinfo = M('Member')->where(array('uid'=>$uid))->field('usernumber,realname,psd1,psd2')->find();
		
		$content = sprintf($content,$uinfo['realname'],$uinfo['usernumber'],$uinfo['psd1'],$uinfo['psd2']);
		$res = self::sendSMS($mobile, $content);
		
		return $res;
	}
	
	/**
	 * 发送通知类信息
	 */
	public function noticeSMS($uid,$mobile)
	{
		
	}
	
	/**
	 * 发送验证类信息
	 */
	public function checkSMS($uid,$mobile)
	{
	
	}
	
	/**
	 * 接受短信
	 * @param string $mobile 接受短信的手机号
	 * @param string $content 短信内容
	 * @param string $time 发送时间
	 * @param string $mid
	 * @return string 短信发送成功后返回结果
	 */
	private function sendSMS($mobile,$content,$time='',$mid='')
	{
		if(!C('IS_SMS')){
			return ;
		}
		$http="http://service.winic.org:8009/sys_port/gateway/";
		$uid="hjdzx038";
		$pwd="asldjoanin";

		$content=iconv('utf-8','gb2312',$content);
		$data = array
		(
				'id'=>$uid,					//用户账号
				'pwd'=>$pwd,			//MD5位32密码,密码和用户名拼接字符
				'to'=>$mobile,				//号码
				'content'=>$content,			//内容
				'time'=>$time,					//定时发送
		);
		$re= self::postSMS($http,$data);			//POST方式提交
		return $re;
	
	}
	/**
	 * 提交短信到平台
	 * @param unknown $url
	 * @param string $data
	 * @return string
	 */
	private function postSMS($url,$data=''){
		$port="";
		$post="";
		$row = parse_url($url);
		$host = $row['host'];
		$port = $row['port'] ? $row['port']:80;
		$file = $row['path'];
		while (list($k,$v) = each($data)){
			$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
		}
		$post = substr( $post , 0 , -1 );
		$len = strlen($post);
		$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
		if (!$fp) {
			return "$errstr ($errno)\n";
		} else {
			$receive = '';
			$out = "POST $file HTTP/1.1\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Content-type: application/x-www-form-urlencoded\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Length: $len\r\n\r\n";
			$out .= $post;
			fwrite($fp, $out);
			while (!feof($fp)) {
				$receive .= fgets($fp, 128);
			}
			fclose($fp);
			$receive = explode("\r\n\r\n",$receive);
			unset($receive[0]);
			return implode("",$receive);
		}
	}
}