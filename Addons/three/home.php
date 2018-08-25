<?php
require("yimaoini.php");
@require(ROOTTHEME.'head.php');


$yim=$_GET['yim'];
$menu1=$menu2=$menu3=$menu4="none";
switch($yim)
{
	case 'openlist':
	$menu3="block";
		$YCF['curl']=YMINDEX.'?yim=openlist';
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'openlist.inc.php');
	break;		
	case 'shents':
		$YCF['curl']=YMINDEX.'?yim=shents';
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'shents.inc.php');
	break;
	case 'xiaojinlist':
		$YCF['curl']=YMINDEX.'?yim=xiaojinlist';
		$menu4="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'xiaojinlist.inc.php');
	break;
	case 'shouyi':
		$YCF['curl']=YMINDEX.'?yim=shouyi';
		$menu4="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'shouyi.inc.php');
	break;
	case 'peiduiinfo':
		$YCF['curl']=YMINDEX.'?yim=peiduiinfo';
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'peiduiinfo.inc.php');
	break;
	case 'qukuan':
		$YCF['curl']=YMINDEX.'?yim=qukuan';
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'qukuan.inc.php');
	break;
	case 'cunkuan':
		$YCF['curl']=YMINDEX.'?yim=cunkuan';
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'cunkuan.inc.php');
	break;
	case 'houlogin':
		$YCF['curl']=YMINDEX.'?yim=houlogin';
		$menu2="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'houlogin.inc.php');
	break;	


	case 'fjx':
		$YCF['curl']=YMINDEX.'?yim=fjx';
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'fjx.inc.php');
	break;
	case 'yjx':
		$YCF['curl']=YMINDEX.'?yim=yjx';
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'yjx.inc.php');
	break;
	case 'sjx':
		$YCF['curl']=YMINDEX.'?yim=sjx';
		$menuval=6;
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'sjx.inc.php');
	break;
	case 'liuyan':
		$YCF['curl']=YMINDEX.'?yim=liuyan';
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'liuyan.inc.php');
	break;
	case 'newslist':
		$YCF['curl']=YMINDEX.'?yim=newslist';
		$menuval=4;
		$menu1="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'newslist.inc.php');
	break;
	case 'yzmm'	   :
		$YCF['curl']=YMINDEX.'?yim=yzmm';
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'yzmm.inc.php');
	break;
	case 'yzmbm'	   :
		$YCF['curl']=YMINDEX.'?yim=yzmbm';

		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'yzmbm.inc.php');
	break;
	case 'yzmb'	   :
		$YCF['curl']=YMINDEX.'?yim=yzmb';
		$menu2="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'yzmb.inc.php');
	break;
	case 'huanlist':
		
		$YCF['curl']=YMINDEX.'?yim=huanlist';
		$menu4="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'huanlist.inc.php');
	break;
	case 'zhuanlist':
		$YCF['curl']=YMINDEX.'?yim=zhuanlist';
		$menu4="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'zhuanlist.inc.php');
	break;
	case 'chonglist':
		$YCF['curl']=YMINDEX.'?yim=chonglist';
		$menu4="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'chonglist.inc.php');
	break;
	case 'prizelist':
		$YCF['curl']=YMINDEX.'?yim=prizelist';
		$menu4="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'prizelist.inc.php');
	break;
	case 'mybank'	:
		$YCF['curl']=YMINDEX.'?yim=mybank';

		$menu2="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'mybank.inc.php');
	break;
	case 'modmb'	:
		$YCF['curl']=YMINDEX.'?yim=modmb';
		$menu2="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'modmb.inc.php');
	break;
	case 'modpwd'	:
		$YCF['curl']=YMINDEX.'?yim=modpwd';
		$menuval=2;
		$menu2="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'modpwd.inc.php');
	break;
	case 'mytj'	:
		$YCF['curl']=YMINDEX.'?yim=mytj';
		$menu3="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'mytj.inc.php');
	break;

	case 'tumap':
		$YCF['curl']=YMINDEX.'?yim=tumap';
		$menuval=3;
		$menu3="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'tumap.inc.php');
	break;
	case 'myinfo':
		$YCF['curl']=YMINDEX.'?yim=myinfo';
		$menuval=1;
		$menu2="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'myinfo.inc.php');
	break;
	case 'register':
		$YCF['curl']=YMINDEX.'?yim=register';
		$menuval=0;
		$menu3="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'register.inc.php');
	break;
	case 'newsview':
		$YCF['curl']=YMINDEX.'?yim=newsview';
		$menu1="block";
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'newsview.inc.php');
	break;
	default:
		@require(ROOTTHEME.'menu.php');
		@require(ROOTTHEMETEMP.'index.inc.php');
}






@require(ROOTTHEME.'foot.php');
?>