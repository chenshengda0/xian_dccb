<?php
require("yimaoini.php");
yimao_admin_main();

$yimao=$_GET['yimao'];
switch ($yimao) {
	case 'temporarylist'	:
		chkpower('temporarylist');
		$YCF['curl']=YMCURL.'temporarylist';
		@include(YMADMINCOMP.'y-temporarylist.php');
		break;		
	case 'sunpei':
		chkpower('sunpei');
		$YCF['curl']=YMCURL.'sunpei';
		@include(YMADMINCOMP.'y-sunpei.php');
		break;	
	break;	
	case 'shentslist':
		chkpower('shentslist');
		$YCF['curl']=YMCURL.'shentslist';
		@include(YMADMINCOMP.'y-shentslist.php');
		break;	
	break;	
	case 'souyilist':
		chkpower('souyilist');
		$YCF['curl']=YMCURL.'souyilist';
		@include(YMADMINCOMP.'y-souyilist.php');
		break;	
	break;	
	case 'pdinfo1':
		chkpower('quklist');
		$YCF['curl']=YMCURL.'pdinfo1';
		@include(YMADMINCOMP.'y-pdinfo1.php');
		break;	
	break;	
	case 'pdinfo':
		chkpower('cunklist');
		$YCF['curl']=YMCURL.'pdinfo';
		@include(YMADMINCOMP.'y-pdinfo.php');
		break;	
	break;	
	case 'cunklist':
		chkpower('cunklist');
		$YCF['curl']=YMCURL.'cunklist';
		@include(YMADMINCOMP.'y-cunklist.php');
		break;	
	break;	
	case 'quklist':
		chkpower('quklist');
		$YCF['curl']=YMCURL.'quklist';
		@include(YMADMINCOMP.'y-quklist.php');
		break;	
	break;		
	case 'gongsipeiinfo':
		chkpower('gongsipei');
		$YCF['curl']=YMCURL.'gongsipeiinfo';
		@include(YMADMINCOMP.'y-gongsipeiinfo.php');
		break;	
	break;	
	case 'peiduiadd':
		chkpower('gongsipei');
		$YCF['curl']=YMCURL.'peiduiadd';
		@include(YMADMINCOMP.'y-peiduiadd.php');
		break;	
	break;		
	case 'gongsiinfo':
		chkpower('gongsipei');
		$YCF['curl']=YMCURL.'gongsiinfo';
		@include(YMADMINCOMP.'y-gongsiinfo.php');
		break;	
	break;	
	case 'gongsipei'	:
		chkpower('gongsipei');
		$YCF['curl']=YMCURL.'gongsipei';
		@include(YMADMINCOMP.'y-gongsipei.php');
		break;		
	case 'serveenvir'	:
		chkpower('serveenvir');
		$YCF['curl']=YMCURL.'serveenvir';
		@include(YMADMINCOMP.'y-serveenvir.php');
		break;		
	case 'huanlist'		:
		chkpower('huanlist');
		$YCF['curl']=YMCURL.'huanlist';
		@include(YMADMINCOMP.'y-huanlist.php');
		break;	
	case 'zhuanlist'		:
		chkpower('zhuanlist');
		$YCF['curl']=YMCURL.'zhuanlist';
		@include(YMADMINCOMP.'y-zhuanlist.php');
		break;	
	case 'chonglist'		:
		chkpower('chonglist');
		$YCF['curl']=YMCURL.'chonglist';
		@include(YMADMINCOMP.'y-chonglist.php');
		break;	
	case 'tiqulist'			:
		chkpower('tiqulist');
		$YCF['curl']=YMCURL.'tiqulist';
		@include(YMADMINCOMP.'y-tiqulist.php');
		break;	
	case 'helprech'			:
		chkpower('helprech');
		$YCF['curl']=YMCURL.'helprech';
		@include(YMADMINCOMP.'y-helprech.php');
		break;	
	case 'finaldetail'		:
		chkpower('finaldetail');
		$YCF['curl']=YMCURL.'finaldetail';
		@include(YMADMINCOMP.'y-finaldetail.php');
		break;	
	case 'bdyeno'			:
		chkpower('bdyeno');
		$YCF['curl']=YMCURL.'bdyeno';
		@include(YMADMINCOMP.'y-bdyeno.php');
		break;	
	case 'bdlist'			:
		chkpower('bdlist');
		$YCF['curl']=YMCURL.'bdlist';
		@include(YMADMINCOMP.'y-bdlist.php');
		break;
	case 'uplevellist'		:
		chkpower('uplevellist');
		$YCF['curl']=YMCURL.'uplevellist';
		@include(YMADMINCOMP.'y-uplevellist.php');
		break;
	case 'bankedit':
		chkpower('banklist');
		$YCF['curl']=YMCURL.'bankedit';
		@include(YMADMINCOMP.'y-bankedit.php');
		break;	
	break;			
	case 'banklist'			:
		chkpower('banklist');
		$YCF['curl']=YMCURL.'banklist';
		@include(YMADMINCOMP.'y-banklist.php');
		break;
	case 'prizedetail'		:
		chkpower('prizedetail');
		$YCF['curl']=YMCURL.'prizedetail';
		@include(YMADMINCOMP.'y-prizedetail.php');
		break;
	case 'prizemeiqi'		:
		chkpower('prizemeiqi');
		$YCF['curl']=YMCURL.'prizemeiqi';
		@include(YMADMINCOMP.'y-prizemeiqi.php');
		break;	
	case 'xxprize'			:
		chkpower('prizebobi');
		$YCF['curl']=YMCURL.'xxprize';
		@include(YMADMINCOMP.'y-xxprize.php');
		break;		
	case 'benqiprize'		:
		chkpower('prizebobi');
		$YCF['curl']=YMCURL.'benqiprize';
		@include(YMADMINCOMP.'y-benqiprize.php');
		break;	
	case 'benqiuser'		:
		chkpower('prizebobi');
		$YCF['curl']=YMCURL.'benqiuser';
		@include(YMADMINCOMP.'y-benqiuser.php');
		break;	
	case 'prizebobi'		:
		chkpower('prizebobi');
		$YCF['curl']=YMCURL.'prizebobi';
		@include(YMADMINCOMP.'y-prizebobi.php');
		break;	
	case 'prizetotal'		:
		chkpower('prizetotal');
		$YCF['curl']=YMCURL.'prizetotal';
		@include(YMADMINCOMP.'y-prizetotal.php');
		break;	
	case 'moduserulevel'	:
		chkpower('formallist');
		$YCF['curl']=YMCURL.'moduserulevel';
		@include(YMADMINCOMP.'y-moduserulevel.php');
		break;
	case 'modusername'		:
		chkpower('formallist');
		$YCF['curl']=YMCURL.'modusername';
		@include(YMADMINCOMP.'y-modusername.php');
		break;
	case 'moduserinfo'		:
		chkpower('formallist');
		$YCF['curl']=YMCURL.'moduserinfo';
		@include(YMADMINCOMP.'y-moduserinfo.php');
		break;
	case 'formallist'		:
		chkpower('formallist');
		$YCF['curl']=YMCURL.'formallist';
		@include(YMADMINCOMP.'y-formallist.php');
		break;	
	case 'temporarylist'	:
		chkpower('temporarylist');
		$YCF['curl']=YMCURL.'temporarylist';
		@include(YMADMINCOMP.'y-temporarylist.php');
		break;		
	case 'networkmap'	:
		chkpower('networkmap');
		$YCF['curl']=YMCURL.'networkmap';
		@include(YMADMINCOMP.'y-networkmap.php');
		break;	
	case 'quickreg'		:
		chkpower('emailmanage');
		$YCF['curl']=YMCURL.'quickreg';
		@include(YMADMINCOMP.'y-quickreg.php');
		break;
	case 'autoreg'		:
		chkpower('emailmanage');
		$YCF['curl']=YMCURL.'autoreg';
		@include(YMADMINCOMP.'y-autoreg.php');
		break;			
	case 'yjxviewmag'	:
		chkpower('emailmanage');
		$YCF['curl']=YMCURL.'yjxviewmag';
		@include(YMADMINCOMP.'y-yjxviewmag.php');
		break;	
	case 'emailmanage'	:
		chkpower('emailmanage');
		$YCF['curl']=YMCURL.'emailmanage';
		@include(YMADMINCOMP.'y-emailmanage.php');
		break;	
	case 'yjxview'		:
		chkpower('sjxlist');
		$YCF['curl']=YMCURL.'yjxview';
		@include(YMADMINCOMP.'y-yjxview.php');
		break;	
	case 'sjxlist'		:
		chkpower('sjxlist');
		$YCF['curl']=YMCURL.'sjxlist';
		@include(YMADMINCOMP.'y-sjxlist.php');
		break;	
	case 'fjxlist'		:
		chkpower('fjxlist');
		$YCF['curl']=YMCURL.'fjxlist';
		@include(YMADMINCOMP.'y-fjxlist.php');
		break;	
	case 'sendemail'		:
		chkpower('sendemail');
		$YCF['curl']=YMCURL.'sendemail';
		@include(YMADMINCOMP.'y-sendemail.php');
		break;	
	case 'articlelist'		:
		chkpower('articlelist');
		$YCF['curl']=YMCURL.'articlelist';
		@include(YMADMINCOMP.'y-articlelist.php');
		break;
	case 'articleedit'		:
		chkpower('articlelist');
		$YCF['curl']=YMCURL.'articleedit';
		@include(YMADMINCOMP.'y-articleedit.php');
		break;
	case 'articleadd'		:
		chkpower('articleadd');
		$YCF['curl']=YMCURL.'articleadd';
		@include(YMADMINCOMP.'y-articleadd.php');
		break;		
	case 'controlpass'		:
		chkpower('controlpass');
		$YCF['curl']=YMCURL.'controlpass';
		@include(YMADMINCOMP.'y-controlpass.php');
		break;	
	case 'controllist'		:
		chkpower('controllist');
		$YCF['curl']=YMCURL.'controllist';
		@include(YMADMINCOMP.'y-controllist.php');
		break;	
	case 'controledit'		:
		chkpower('controllist');
		$YCF['curl']=YMCURL.'controledit';
		@include(YMADMINCOMP.'y-controledit.php');
		break;			
	case 'controladd'		:
		chkpower('controladd');
		$YCF['curl']=YMCURL.'controladd';
		@include(YMADMINCOMP.'y-controladd.php');
		break;	
	case 'rolelist'		:
		chkpower('rolelist');
		$YCF['curl']=YMCURL.'rolelist';
		@include(YMADMINCOMP.'y-rolelist.php');
		break;
	case 'roleadd'		:
		chkpower('roleadd');
		$YCF['curl']=YMCURL.'roleadd';
		@include(YMADMINCOMP.'y-roleadd.php');
		break;
	case 'roleedit'		:
		chkpower('rolelist');
		$YCF['curl']=YMCURL.'roleedit';
		@include(YMADMINCOMP.'y-roleedit.php');
		break;					
	case 'datarestore'	:
		chkpower('datarestore');
		$YCF['curl']=YMCURL.'datarestore';
		getconfig();
		@include(YMADMINDATA.'datarestore.php');
		break;	
	case 'databack'		:
		chkpower('databack');
		$YCF['curl']=YMCURL.'databack';
		@include(YMADMINDATA.'databackup.php');
		break;	
	case 'handwork'		:
		chkpower('handwork');
		$YCF['curl']=YMCURL.'handwork';
		@include(YMADMINCOMP.'y-handwork.php');
		break;
	case 'sentencesql'	:
		chkpower('sentencesql');
		$YCF['curl']=YMCURL.'sentencesql';
		@include(YMADMINCOMP.'y-sentencesql.php');
	break;	
	case 'lockip'		:
		chkpower('sentencesql');
		$YCF['curl']=YMCURL.'lockip';
		@include(YMADMINCOMP.'y-lockip.php');
	break;	
	case 'configset'	:
		chkpower('configset');
		$YCF['curl']=YMCURL.'configset';
		@include(YMADMINCOMP.'y-configset.php');
	break;
	case 'yimaolog'		:
		chkpower('yimaolog');
		$YCF['curl']=YMCURL.'yimaolog';
		@include(YMADMINCOMP.'y-logs.php');
	break;
	case 'quicktool'	:
		chkpower('quicktool');
		$YCF['curl']=YMCURL.'quicktool';
		@include(YMADMINCOMP.'y-quicktool.php');
	break;
	case 'dataclear'	:
		chkpower('dataclear');
		$YCF['curl']=YMCURL.'dataclear';
		@include(YMADMINCOMP.'y-dataclear.php');
	break;
	default:
		@include(YMADMINCOMP.'y-index.php');
	break;
}

yimao_admin_foot();
?>