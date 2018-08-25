<?php
defined('YIMAOMONEY') or exit('Access denied');
class member {
	var $db;
	var $userid;

	public function __construct(){
		global $db;
		$this->db = &$db;
	}

	public function getum($userid='',$str='*'){
		$condition=$userid?"um002='$userid'":"um002='$this->userid'";
		$this->db->yiquery("select $str from ymum where $condition",2);
		return $this->db->rs;
	}

	public function getuv($userid='',$str='*'){
		$condition=$userid?"uv002='$userid'":"uv002='$this->userid'";
		$this->db->yiquery("select $str from ymuv where $condition",2);
		return $this->db->rs;
	}

	public function getuv1($userid='',$str='*'){
		$condition=$userid?"uv001='$userid'":"uv001='$this->userid'";
		$this->db->yiquery("select $str from ymuv where $condition",2);
		return $this->db->rs;
	}


	public function getuvinfo($userid='',$str='*'){
		$condition=$userid?"uv001=$userid":"uv001=$this->userid";
		$this->db->yiquery("select $str from ymuv where $condition",2);
		return $this->db->rs;
	}

	public function getua($userid='',$str='*'){
		$condition=$userid?"a.ua002='$userid'":"a.ua002='$this->userid'";
		$this->db->yiquery("select $str from ymua a,ymuo o where a.ua004=o.uo001 and $condition",2);
		return $this->db->rs;
	}
	
	public function getuserinfo($s,$c=''){
		$condition=$c?"v.uv002='$s'":"v.uv001=$s";
		$this->db->yiquery("select * from ymuv v,ymum m where v.uv001=m.um001 and $condition ",2);
		return $this->db->rs;
	}

	public function getusername($s){
		if(empty($s)) return '';

		$this->db->yiquery("select um003 from ymum where um001=$s",2);
		if(empty($this->db->rs)){
			return '';
		}else{
			return $this->db->rs['um003'];
		}
	}

	public function getkainums($s){
		if(empty($s)) return '';
		if($s=="0|0") return "链接注册";
		
		$this->db->yiquery("select count(0) as a from ymuv where  LOCATE('".($s."|1")."',uv037)>0",2);
		if(empty($this->db->rs)){
			return '';
		}else{
			return $this->db->rs['a'];
		}
	}

	public function getuserid($s){
		if(empty($s)) return '';

		$this->db->yiquery("select um002 from ymum where um001=$s",2);
		if(empty($this->db->rs)){
			return '';
		}else{
			return $this->db->rs['um002'];
		}
	}


	public function getusid($s){
		if(empty($s)) return '';

		$this->db->yiquery("select um001 from ymum where um002='$s'",2);
		if(empty($this->db->rs)){
			return '';
		}else{
			return $this->db->rs['um001'];
		}
	}


	public function getreguser($s){
		if(empty($s)) return '';
		$a=explode('|',$s);
		$this->db->yiquery("select um002 from ymum where um001=".$a[0],2);
		if(empty($this->db->rs)){
			return '';
		}else{
			return $this->db->rs['um002'].' '.($a[1]?'<span style=\'color:#f00\'>后台</span>':'');
		}
	}

	public function getanreauser($s){
		$arr=array();

		$this->db->yiquery("select uv024,uv001 from ymuv where uv019=$s");
		foreach ($this->db->rs as $k => $v) {
			if($v['uv024']==0){
				$arr[0]=$v['uv001'];
			}elseif($v['uv024']==1){
				$arr[1]=$v['uv001'];
			}elseif($v['uv024']==2){
				$arr[2]=$v['uv001'];
			}			
		}

		return $arr;
	}

	public function upperuser_exists($s){
		$this->db->yiquery("select uv001 from ymuv where uv001 in(0".$s."0) and uv008=0 limit 1",2);
		if(empty($this->db->rs)){
			return false;
		}else{
			return true;
		}
		
	}

	public function downan_exists($s){
		$this->db->yiquery("select uv001 from ymuv where uv021 like '%,$s,%' and uv008=0 limit 1",2);
		if(empty($this->db->rs)){
			return false;
		}else{
			return true;
		}
	}

	public function getduinums($s){
		$this->db->yiquery("select count(uv001) from ymuv where uv020 like '%,$s,%' and uv008>0",3);
		return $this->db->fval;
	}

	public function downtu_exists($s){
		$this->db->yiquery("select uv001 from ymuv where uv020 like '%,$s,%' and uv008=0 limit 1",2);
		if(empty($this->db->rs)){
			return false;
		}else{
			return true;
		}
	}

	public function user_exists($s){
		$this->db->yiquery("select uv001,uv020,uv021,uv023,uv002,uv022 from ymuv where uv002='$s'",2);
		return $this->db->rs;
	}

	public function bd_exists($s){
		$this->db->yiquery("select uv001 from ymuv where uv008>0 and uv038=2 and uv002='$s'",2);
		return  $this->db->rs;
	}	

	public function tu_exists($s){
		$this->db->yiquery("select uv001,uv020,uv022 from ymuv where uv008>0 and uv002='$s'",2);
		return $this->db->rs;
	}	

	public function an_exists($s){
		$this->db->yiquery("select uv001,uv021,uv023,uv024,uv025,uv018 from ymuv where uv002='$s'",2);
		return $this->db->rs;
	}	

	public function ans_exists($s,$w){
		$this->db->yiquery("select uv001 from ymuv where uv024=$w and uv019=$s",2);
		return $this->db->rs;
	}	

	public function email_exists($s){
		$this->db->yiquery("select um001 from ymum where um011='$s'",2);
		if(empty($this->db->rs)){
			return false;
		}else{
			return true;
		}
	}		

	public function card_exists($s){
		$this->db->yiquery("select um001 from ymum where um012='$s'",2);
		if(empty($this->db->rs)){
			return false;
		}else{
			return true;
		}
	}	

	public function mobile_exists($s){
		$this->db->yiquery("select um001 from ymum where um010='$s'",2);
		if(empty($this->db->rs)){
			return false;
		}else{
			return true;
		}
	}		


	public function getlceng($a,$b){
		if(empty($a)) return 0;
		$this->db->yiquery("select uv023 from ymuv where  (uv021 like '%,".$a.",%' or uv001=$a) order by uv023 desc",2);

		if(empty($this->db->rs)){
			return 0;
		}else{
			return $this->db->rs["uv023"]-$b;
		}
	}	
	
	public function getcunkuan($s){
		$this->db->yiquery("select count(gp001) a from ymugp where gp011=0 and gp002=$s",2);
		if(empty($this->db->rs["a"])){
			$arr[0]=0;
		}else{
			$arr[0]=$this->db->rs["a"];
		}

		$this->db->yiquery("select count(gp001) a from ymugp where gp009=0 and gp011=0 and gp002=$s",2);
		if(empty($this->db->rs["a"])){
			$arr[1]=0;
		}else{
			$arr[1]='<span style="color:#f00">'.$this->db->rs["a"]."</span>";
		}		
		return $arr[0]."/".$arr[1];
	}

	public function getqukuan($s){
		$this->db->yiquery("select count(gq001) a from ymugq where gq002=$s",2);
		if(empty($this->db->rs["a"])){
			$arr[0]=0;
		}else{
			$arr[0]=$this->db->rs["a"];
		}

		$this->db->yiquery("select count(gq001) a from ymugq where gq009=0 and gq002=$s",2);
		if(empty($this->db->rs["a"])){
			$arr[1]=0;
		}else{
			$arr[1]='<span style="color:#f00">'.$this->db->rs["a"]."</span>";
		}		
		return $arr[0]."/".$arr[1];
	}

	public function getgqname($s){
		if(empty($s)) return '';

		$this->db->yiquery("select gq002 from ymugq where gq001=$s",3);

		if(empty($this->db->fval)){
			return '';
		}else{
			$this->db->yiquery("select um002 from ymum where um001=".$this->db->fval,3);
			if(empty($this->db->fval)){
				return '';
			}else{
				return $this->db->fval;
			}
		}
	}	


	public function getgpname($s){
		if(empty($s)) return '';

		$this->db->yiquery("select gp002 from ymugp where gp001=$s",3);

		if(empty($this->db->fval)){
			return '';
		}else{
			$this->db->yiquery("select um002 from ymum where um001=".$this->db->fval,3);
			if(empty($this->db->fval)){
				return '';
			}else{
				return $this->db->fval;
			}
		}
	}	

	public function getldpname($s){
		if(empty($s)) return '';

		$this->db->yiquery("select gp002 from ymugp where gp001=$s",3);

		if(empty($this->db->fval)){
			return array();
		}else{

			$this->db->yiquery("select um002,um010,um015 from ymum where um001=(select uv018 from ymuv where uv001=".$this->db->fval.")",2);
			if(empty($this->db->rs)){
				return array();
			}else{
				return $this->db->rs;
			}
		}
	}		

	public function getldqname($s){
		if(empty($s)) return '';

		$this->db->yiquery("select gq002 from ymugq where gq001=$s",3);

		if(empty($this->db->fval)){
			return array();
		}else{

			$this->db->yiquery("select um002,um010,um015 from ymum where um001=(select uv018 from ymuv where uv001=".$this->db->fval.")",2);
			if(empty($this->db->rs)){
				return array();
			}else{
				return $this->db->rs;
			}
		}
	}	
		
}
?>