<?php
namespace Common\Api;
class CommonApi 
{
	public static function sendMessage($uid,$title,$content)
	{
		$Liuyan = M('Liuyan');
		$data['title'] = $title;
		$data['content'] = $content;
		$data['fromuserid'] = 0;
		$data['touser'] = $uid;
		$data['create_time'] = time();
		$data['status'] = 0;
		$res = M('liuyan')->create($data);
		if($res){
			M('liuyan')->add();
			return true;
		}else{
			return false;
		}
	}
}