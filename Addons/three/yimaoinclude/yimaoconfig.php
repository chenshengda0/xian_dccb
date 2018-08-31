<?php
defined('YIMAOMONEY') or exit('Access denied');
class config {
	var $db;

	public function __construct(){
		global $db;
		$this->db = &$db;
	}

	public function getjjconfig($s=' us006=0'){
		$r=array();
		$this->db->yiquery("select * from ymus where $s");
		foreach ($this->db->rs as $k => $v) {
			if(strstr($v['us004'],'|')){
				$r[$v['us001']]=explode('|',$v['us004']);
			}else{
				$r[$v['us001']]=$v['us004'];
			}
		}
		return $r;
	}	

	public function getregconfig($s=' ur006=0'){
		$r=array();
		$this->db->yiquery("select * from ymur where $s");
		foreach ($this->db->rs as $k => $v) {
			if(strstr($v['ur003'],'|')){
				$r[$v['ur001']]=explode('|',$v['ur003']);
			}else{
				$r[$v['ur001']]=$v['ur003'];
			}
		}
		return $r;
	}	

	public function getregmust($s=' ur006=0'){
		$r=array();
		$this->db->yiquery("select * from ymur where $s");
		foreach ($this->db->rs as $k => $v) {
			$r[$v['ur001']]=$v['ur007'];
		}
		return $r;
	}

	public function getbank(){
		$r=array();
		$this->db->yiquery("select * from ymus where us003=2");
		foreach ($this->db->rs as $k => $v) {
			$r[$v['us001']]=$v['us004'];
		}
		return $r;
	}	

	public function getpwd(){
		$r=array();
		$this->db->yiquery("select * from ymur where ur006=0 and ur004=3");
		foreach ($this->db->rs as $k => $v) {
			$r[$v['ur001']]=$v['ur002'];
		}
		return $r;
	}

	public function getmb(){
		$this->db->yiquery("select ur003 from ymur where ur001='rgmbq'",2);
		return explode('|',$this->db->rs['ur003']);
	}		
}
?>