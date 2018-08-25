<?php
defined('YIMAOMONEY') or exit('Access denied');
function geteqval($arr){
	if($arr[0]==$arr[1]){
		return $arr[2];
	}else{
		return $arr[3];
	}
}

function getpeistatus($arr){
	if(!empty($arr[3])){
			return '<span style="color:#f00">交易完成</span>';
	}elseif($arr[2]==0){
		return '<span style="color:#49AB47">等待配对</span>';
	}elseif($arr[2]==1){
		return '<span style="color:#000">等待打款</span>';
	}elseif($arr[2]==2){
		return '<span style="color:#ddd">配对已过期</span>';
	}elseif($arr[2]==3){
		return '<span style="color:#000">已取消</span>';
	}

}


function getpeistatus1($arr){
	if(!empty($arr[3])){
			return '<span style="color:#f00">交易完成</span>';
	}elseif($arr[2]==0){
		return '<span style="color:#49AB47">等待配对</span>';
	}elseif($arr[2]==1){
		return '<span style="color:#000">等待收款</span>';
	}elseif($arr[2]==2){
		return '<span style="color:#ddd">配对已过期</span>';
	}elseif($arr[2]==3){
		return '<span style="color:#000">已取消</span>';
	}

}


function random_filename()
{
        $str = '';
        for($i = 0; $i < 9; $i++)
        {
            $str .= mt_rand(0, 9);
        }

        return time().$str;
}


function getcunstatus($arr){
	if($arr["pd011"]==1){
		return "配对过期";
	}elseif($arr["pd009"]==0&&empty($arr["pd008"])){
		return "匹配成功尽快打款确认";
	}elseif($arr["pd009"]==0&&!empty($arr["pd008"])){
		return "收款人未确认";
	}elseif($arr["pd009"]==1){
		return "已结束";
	}


	// if($arr["pd009"]==0){
	// 	return '<span style="color:#f00">配对成功</span>';
	// }elseif($arr["pd009"]==1){
	// 	return '<span style="color:#49AB47">配对完成</span>';
	// }
}

function getcunstatus1($arr){
	if($arr["pd011"]==1){
		return "配对过期";
	}elseif($arr["pd009"]==0&&empty($arr["pd008"])){
		return "等待付款人付款";
	}elseif($arr["pd009"]==0&&!empty($arr["pd008"])){
		return "对方已付款尽快确认付款";
	}elseif($arr["pd009"]==1){
		return "已结束";
	}
	// if($arr["pd009"]==0){
	// 	return '<span style="color:#f00">配对成功</span>';
	// }elseif($arr["pd009"]==1){
	// 	return '<span style="color:#49AB47">配对完成</span>';
	// }

}


function historygo(){
	echo "<script>history.go(-1)</script>";
}

function locationurl($url){
	echo '<script type="text/javascript">window.location.href="'.$url.'"</script>';
	exit;
}

function geturls(){
	return substr($_SERVER['HTTP_REFERER'],0,strlen($_SERVER['HTTP_REFERER'])-strlen(strrchr($_SERVER['HTTP_REFERER'],"/")));
}


function getcharnums($s){
	$s=trim($s);
	if(empty($s)) return 0;

	preg_match_all('/./us', $s, $match);

	return count($match[0]);
}

function getstrval($arr){
	if(empty($arr[0])||empty($arr[1])) return $arr[3];
	if(strstr($arr[0],$arr[1])){
		return $arr[2];
	}else{
		return $arr[3];
	}
}


function getPath($path,$xin){
	if(empty($path)) $path="";
	return str_replace(",,",",",str_replace(",,",",",",".$path.",").$xin.",");
}

function getmenu($arr,$search,$t=0){
	$str='';
	foreach ($arr as $kr => $vr) {
		foreach ($vr as $k => $v) {
			if($v[0]==$search){
				if($t==1){
					$str=$kr;
				}else{
					$str=$k;
				}		
				break;
			}
		}
	}
	return $str;
}

function getfnum($num){
	if($num==""||empty($num)){
		return 0;
	}
	if(!is_numeric($num)){
		return 0;
	}
	return (floor($num*100)/100);
}


function getatricletype($a){
	$arr=array('公告','通知','新闻','滚动','其他');
	if($a[0]==1){
		return $arr[$a[1]];
	}elseif($a[0]==2){
		$s.='<select name="attype" class="yimaoselect">';
		foreach ($arr as $k => $v) {
			$s.='<option value="'.$k.'">'.$v.'</option>';
		}
		$s.='</select>';
		return $s;
	}elseif($a[0]==3){
		$s.='<select name="attype" class="yimaoselect">';
		foreach ($arr as $k => $v) {
			$s.='<option value="'.$k.'" '.geteqval(array($k,$a[1],'selected','')).'>'.$v.'</option>';
		}
		$s.='</select>';
		return $s;
	}elseif($a[0]==4){
		return $arr;
	}

}

function a_bck($s){
	echo '<script type="text/javascript">artbck("'.$s.'");</script>';
	exit;
}

function msg_b($s){
	echo '<script type="text/javascript">alert("'.$s.'");history.go(-1)</script>';
	exit;
}

function msg_l($s,$u){
	echo '<script type="text/javascript">alert("'.$s.'");window.location.href="'.$u.'"</script>';
	exit;
}

function chkusername($s){
	return preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_]{3,14}$/u",$s);
}

function checkstr($strsql)
{     //检测字符串是否有注入风险
       
	$strsql=trim($strsql);
	$check=preg_match('/select|or|and|SELECT|INSERT|UPDATE|DELETE|insert|script|function|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i',$strsql);
  
	if($check)
	{   
		echo "<script language='javascript'>alert('您输入的信息存在非法字符！\\n\\n".$strsql."');history.go(-1)</script>";
		exit;
	}        
	return  $strsql;			          
			   
}

function getemailtype($a){
	$arr=array('咨询','帮助','奖金','提现','充值','其他');
	if($a[0]==1){
		return $arr[$a[1]];
	}elseif($a[0]==2){
		$s='';
		foreach ($arr as $k => $v) {
			$s.='<label><input type="radio" name="emtype" value="'.$k.'" '.geteqval(array($k,0,'checked','')).'>&nbsp;'.$v.'</label>&nbsp;';
		}
		return $s;
	}elseif($a[0]==3){
		$s='';
		foreach ($arr as $k => $v) {
			$s.='<label><input type="radio" name="emtype" value="'.$k.'" '.geteqval(array($k,$a[1],'checked','')).'>&nbsp;'.$v.'</label>&nbsp;';
		}
		return $s;
	}elseif($a[0]==4){
		return $arr;
	}
}

function getinsertsql($arr,$t,$s=''){
	$key=array();
	$value=array();
	foreach ($arr as $k => $v) {
		$key[]=$k;
		$type=gettype($v);
		if((!empty($s)&&!($v==='')&&strpos("=".$s,$k))||($type=='integer'||$type=='double'||$type=='float')){
			$value[]=$v;
		}else{
			$value[]="'".$v."'";
		}
		
	}
	return "insert into $t(".implode(',',$key).") values(".implode(',',$value).")";
}

function getupdatesql($arr,$t,$c,$s=''){
	$r=array();
	foreach ($arr as $k => $v) {
		$type=gettype($v);
		if((!empty($s)&&!($v==='')&&strpos("=".$s,$k))||($type=='integer'||$type=='double'||$type=='float')){
			$r[]="$k=$v";
		}else{
			$r[]="$k='$v'";
		}

	}
	return "update $t set ".implode(',',$r)." where $c";
}

function chkpwd($arr,$t=0) {

	if($t){
		if(empty($arr[0])) return '请输入旧密码';
		if(empty($arr[1])) return '请输入新密码';
		if($arr[0]==$arr[1]) return '新密码和旧密码不能相同';
		if(strlen($arr[1]) < 8||strlen($arr[0]) < 8) return '密码最少8位，请修改';
	}else{
		if(empty($arr[0])) return '请输入密码';
		if(strlen($arr[0]) < 8) return '密码最少8位，请修改';
	}

	return true;
}


function getqueurl($ming,$num){
	$url= $_SERVER["QUERY_STRING"];
	$pattern="/".$ming."=[0-9]*/i";
	if(preg_match($pattern,$url))
	{
		return preg_replace($pattern,$ming."=".$num,YMADMINDEX.'?'.$url);
	}else{
		if(empty($url))
			return YMADMINDEX."?$ming=$num";
		else
			return YMADMINDEX.'?'.$url."&$ming=$num";
	}	
}

function geturl(){
	return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$_SERVER["QUERY_STRING"];
}

function getnums($v,$d=1){
	if(empty($v)) return $d;
	if(!is_numeric($v)) return $d;
	$v=floor(abs($v));
	if($v<1&&$d>0){
		$v=$d;
	}elseif($v<1&&$d<1){
		$v=0;
	}

	return $v;
}

function formatrmb($v){
	if(strstr($v,'.')){

		$s=explode('.',$v);
		if(empty($s[1])){
			return $s[0];
		}else{

			if(strpos($s[1],'0')===false){
				return $v;
			}else{
				$x1=substr($s[1],0,1);
				$x2=substr($s[1],1,1);
				if(empty($x1)&&!empty($x2)){
					return $s[0].'.0'.$x2;
				}elseif(!empty($x1)&&empty($x2)){
					return $s[0].'.'.$x1;
				}elseif(empty($x1)&&empty($x2)){
					return $s[0];
				}
			}
		}
	}else{
		return $v;
	}
}


function getsex($s){
	$arr=array('男','女','未知');
	return $arr[$s];
}

function getaccounttype($s=-1){
	$arr=array("现金钱包","收益钱包","佣金钱包",'激活币','小金库','冻结小金库','无');
	if($s>=0)
		return $arr[$s];
	else
		return $arr;
}


function gethuantype($s=-1){
	$arr=array("现金钱包转激活币","小金库转现金币");
	if($s>=0)
		return $arr[$s];
	else
		return $arr;
}


function getzhuantype($s=-1){
	$arr=array("激活币账户");
	if($s>=0)
		return $arr[$s];
	else
		return $arr;
}


function getsessiontime(){
	$cha=time()-$_SESSION["savetime"];
	$fen=floor($cha/60);

	if($fen>30){
		session_unset();
		session_destroy();
		locationurl("index.php");
	}
	$_SESSION["savetime"]=time();
}

function getshouyistatus($arr){

	if($arr[0]==3){
		return "已取消";
	}elseif($arr[0]==2){
		return "已过期";	
	}elseif($arr[1]==0){
		return '<span style="color:#F7C640">收益中</span>';

	}elseif($arr[1]==1){
		return '<span style="color:#f00">收益结束</span>';
	}elseif($arr[1]==2){
		return '<span style="color:#f00">中断收益结束</span>';
	}
}


function getstatutype($v,$t){
	$arr=array(array('<span style="color:#E54C5B">待确认</span>','<span style="color:#707070">已确认</span>'),
			   array('<span style="color:#E54C5B">待确认</span>','<span style="color:#707070">已确认</span>','<span style="color:#29A2EB">已撤销</span>'),
			   array('<span style="color:#f00">待激活</span>','<span style="color:#707070">已激活</span>','<span style="color:#707070">已激活</span>'),
			   array('<span style="color:#f00">否</span>','<span style="color:#707070">是</span>'),
			   array('<span style="color:#f00">未阅</span>','<span style="color:#707070">已阅</span>'),
			   array('<span style="color:#f00">否</span>','<span style="color:#f00">待审核</span>','<span style="color:#707070">是</span>'),
			   array('<span style="color:#f00">未正式</span>','<span style="color:#ccc">已正式</span>','<span style="color:#707070">空单</span>'),
			   array('<span style="color:#74B673">等待</span>','<span style="color:#f00">完成</span>','<span style="color:#ddd">已过期</span>','<span style="color:#ddd">已取消</span>'),
			   array('<span style="color:#565656">收益中</span>','<span style="color:#f00">收益结束</span>','<span style="color:#f00">中断收益结束</span>'));
	switch ($t) {
		case 1:
			return $arr[0][$v];
		break;
		case 2:
			return $arr[1][$v];
		break;		
		case 3:
			return $arr[2][$v];
		break;	
		case 4:
			return $arr[3][$v];
		break;	
		case 5:
			return $arr[4][$v];
		break;	
		case 6:
			return $arr[5][$v];
		break;	
		case 7:
			return $arr[6][$v];
		break;	
		case 8:
			return $arr[7][$v];
		break;	
		case 9:
			return $arr[8][$v];
		break;																
	}
}

function formatdate($d,$t=0){
	$arr=array('Y-m-d H:i:s','Y-m-d','m-d H:i:s');
	if(empty($d)) return '';
	if(date($arr[1],$d)=='1970-01-01') return '';
	return date($arr[$t],$d);
}

function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}


function menuval($s,$v,$l){
$menus1=array("prizelist","tiqulist","chonglist","zhuanlist","huanlist");
$menus2=array("register","tumap","anmap","mytj");
$menus3=array("openlist","bdshen");
$menus4=array("myinfo","modpwd","modmb","mybank","myuplevel");
$menus5=array("liuyan","sjx","fjx","yjx");
if($l==0){
    $l1="active";
    $l2="normal";
}else{
    $l1="";
    $l2="none";
}

switch ($v) {
    case 1:

        if(in_array($_GET["yim"], $menus1)) 
            return $l1;
        else
            return $l2;
    
    break;
    case 2:
     	if(empty($_GET["yim"])){
    		 return $l1;

    	}else{
   
        if(in_array($_GET["yim"], $menus2)) 
            return $l1;
        else
            return $l2;
    	}
    break;   
    case 3:
        if(in_array($_GET["yim"], $menus3)) 
            return $l1;
        else
            return $l2;
    break;   
    case 4:
        if(in_array($_GET["yim"], $menus4)) 
            return $l1;
        else
            return $l2;
    break;            
    case 5:
        if(in_array($_GET["yim"], $menus5)) 
            return $l1;
        else
            return $l2;
    break;   

}

}


function getrealip()
{
    static $realip = NULL;

    if ($realip !== NULL)
    {
        return $realip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);

                if ($ip != 'unknown')
                {
                    $realip = $ip;

                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}	

?>