<?php 
defined('YIMAOMONEY') or exit('Access denied');
class mysql{
	public	    $conn;               //数据库连接句柄
	public   	$rs;
	public 		$rownums;
	public 		$fval;
	public 		$debug;
	private 	$dbhost;
	private 	$dbuser;
	private 	$dbpwd;
	private 	$dbdata;

	public function __construct($arr){
		$this->yiconnect($arr);	
	}

	public function yiconnect($arr){
		global $YCF;
		try{
			$this->debug=$arr[4];
			$this->dbhost=$arr[0];
			$this->dbdata=$arr[1];
			$this->dbuser=$arr[2];
			$this->dbpwd=$arr[3];
			$this->conn=new PDO('mysql:host='.$arr[0].';dbname='.$arr[1],$arr[2],$arr[3]);
			$this->conn->query('set names utf8');
			$this->conn->setAttribute(PDO::ATTR_CASE,PDO::CASE_LOWER);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			exit('抱歉，系统连接失败!');
		}
	}

	public function yiverson(){

      $linkid=@mysql_connect($this->dbhost, $this->dbuser, $this->dbpwd);
      return mysql_get_server_info($linkid);
      $linkid=null;
	}

	public function yipre($sql,$types=1,$arr=''){
		try{
			$pre=$this->conn->prepare($sql);

			if(is_array($arr)){
				$nums=count($arr[0]);
				for($i=0;$i<$nums;$i++){ 
					$pre->bindParam($arr[0][$i],$arr[1][$i]); 
				}
			}
			$pre->execute();
			$pre->setFetchMode(PDO::FETCH_ASSOC);
			switch ($types) {
				case 1:
					$this->rs=$pre->fetchAll();
				break;
				case 2:
					$this->rs=$pre->fetch();
				break;	
				case 3:
					$this->fval=$pre->fetchColumn();
				break;	
				case 4:
					$this->rownums=$pre->rowCount();
				break;	
				case 5:
					$this->fval=$this->conn->lastInsertId();
				break;													
			}
		}catch(PDOException $e){
			$this->showErr($e);
		}			
	}

	public function yiquery($sql,$types=1){
		try{
			$r=$this->conn->query($sql);
			$r->setFetchMode(PDO::FETCH_ASSOC);
			switch ($types) {
				case 1:
					$this->rs=$r->fetchAll();
				break;
				case 2:
					$this->rs=$r->fetch();
				break;	
				case 3:
					$this->fval=$r->fetchColumn();
				break;	
				case 4:
					$this->rownums=$r->rowCount();
				break;	
				case 5:
					$this->fval=$this->conn->lastInsertId();
				break;													
			}
		}catch(PDOException $e){
			$this->showErr($e);
		}	
	}

	public function yiexec($s){
		try{
			$this->rownums=$this->conn->exec($s);
		}catch(PDOException $e){
			$this->showErr($e);
		}	
	}

	public function  begintransaction(){
		$this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
		$this->conn->beginTransaction();
	}

	public function  committransaction(){
		$this->conn->commit();
		$this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
	}

	public function  rollbacktransaction(){
		$this->conn->rollback();
		$this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
	}
	
	public function yisqli($s){

		$mysqli=new MySQLi($this->dbhost,$this->dbuser,$this->dbpwd,$this->dbdata);
		$mysqli->query("set names utf8");

		if($mysqli->multi_query($s) === false){
				echo '<div style="padding:50px 5px;width:400px;line-height:45px;border:1px solid #8D907D;margin:0 auto;margin-top:150px;text-align:center;background:#313C57;box-shadow: 1px 1px 5px #888888;font-size:25px"><p>( ิ◕㉨◕ ิ)</p><p>→ 抱歉，执行失败！</p></div>';
			exit;
		}
	}

	public function destroy(){
		$this->conn=null;
		$this->rs=null;
		$this->rownums=null;
		$this->fval=null;
	}

	public function showErr($msg){
		$this->destroy();
		if(is_object($msg)){
			if(!$this->debug){
				echo '<div style="line-height:35px;height:35px;font-size:16px">您处于调式模式中，以下是程序反馈的错误信息(危险！请关闭调式)：</div>';
				dump($msg->getTrace());
				echo $msg->getMessage().'<br>';
				exit;
			}else{
				echo '<div style="padding:50px 5px;width:500px;line-height:45px;border:1px solid #8D907D;margin:0 auto;margin-top:150px;text-align:center;background:#313C57;box-shadow: 1px 1px 5px #888888;font-size:25px"><p>( ิ◕㉨◕ ิ)</p><p style="color:#ff0">→ 抱歉，系统出错啦！</p></div>';
				exit;
			}
		}
	}

}
?>