<?php
defined('YIMAOMONEY') or exit('Access denied');


$YCF['menu']=array('财务中心'=>array('奖金总计记录'=>array('prizetotal',YMADMINDEX.'?yimao=prizetotal'),
									 '奖金每期拨比'=>array('prizebobi',YMADMINDEX.'?yimao=prizebobi'),
									 '奖金记录列表'=>array('prizemeiqi',YMADMINDEX.'?yimao=prizemeiqi'),
									 '奖金明细列表'=>array('prizedetail',YMADMINDEX.'?yimao=prizedetail'),
									 '财务明细列表'=>array('finaldetail',YMADMINDEX.'?yimao=finaldetail'),

									 '货币充值列表'=>array('chonglist',YMADMINDEX.'?yimao=chonglist'),
									 '货币转账记录'=>array('zhuanlist',YMADMINDEX.'?yimao=zhuanlist'),
							 '货币转换记录'=>array('huanlist',YMADMINDEX.'?yimao=huanlist'),
									 '提供帮助申请投诉'=>array('shentslist',YMADMINDEX.'?yimao=shentslist'),
									 '提供帮助利息收益'=>array('souyilist',YMADMINDEX.'?yimao=souyilist'),
									 '会员提供帮助列表'=>array('cunklist',YMADMINDEX.'?yimao=cunklist'),
									 '会员获取帮助列表'=>array('quklist',YMADMINDEX.'?yimao=quklist'),
									 '手工充值账户'=>array('helprech',YMADMINDEX.'?yimao=helprech')),
				   '会员中心'=>array('正式会员列表'=>array('formallist',YMADMINDEX.'?yimao=formallist'),
									 '临时会员审核'=>array('temporarylist',YMADMINDEX.'?yimao=temporarylist'),
									 '会员关系图表'=>array('networkmap',YMADMINDEX.'?yimao=networkmap'),
									 '公司配对信息'=>array('gongsipei',YMADMINDEX.'?yimao=gongsipei'),
									 '会员瞬间秒配'=>array('sunpei',YMADMINDEX.'?yimao=sunpei'),
			
									 '会员银行列表'=>array('banklist',YMADMINDEX.'?yimao=banklist')),
				   '邮箱中心'=>array('收件箱'=>array('sjxlist',YMADMINDEX.'?yimao=sjxlist'),
									 '发件箱'=>array('fjxlist',YMADMINDEX.'?yimao=fjxlist'),
									 '发邮件'=>array('sendemail',YMADMINDEX.'?yimao=sendemail'),
									 '邮件管理'=>array('emailmanage',YMADMINDEX.'?yimao=emailmanage')),
				   '文章中心'=>array('文章列表'=>array('articlelist',YMADMINDEX.'?yimao=articlelist'),
				   					 '文章添加'=>array('articleadd',YMADMINDEX.'?yimao=articleadd')),
				   '管理中心'=>array('管理列表'=>array('controllist',YMADMINDEX.'?yimao=controllist'),
				   					 '管理添加'=>array('controladd',YMADMINDEX.'?yimao=controladd'),
				   					 '角色列表'=>array('rolelist',YMADMINDEX.'?yimao=rolelist'),
				   					 '角色添加'=>array('roleadd',YMADMINDEX.'?yimao=roleadd'),
				   					 '密码修改'=>array('controlpass',YMADMINDEX.'?yimao=controlpass')),
				   '系统中心'=>array('系统参数设置'=>array('configset',YMADMINDEX.'?yimao=configset'),
				   					 '手工结算奖金'=>array('handwork',YMADMINDEX.'?yimao=handwork'),
				   					 'SQL语句执行'=>array('sentencesql',YMADMINDEX.'?yimao=sentencesql'),
				   					 '可疑IP锁定'=>array('lockip',YMADMINDEX.'?yimao=lockip'),
				   					 '快捷小工具'=>array('quicktool',YMADMINDEX.'?yimao=quicktool'),
				   					 '服务器环境'=>array('serveenvir',YMADMINDEX.'?yimao=serveenvir'),
				   					 '系统日志表'=>array('yimaolog',YMADMINDEX.'?yimao=yimaolog'),
				   					 '数据初始化'=>array('dataclear',YMADMINDEX.'?yimao=dataclear'),
				   					 '数据库备份'=>array('databack',YMADMINDEX.'?yimao=databack'),
				   					 '数据库还原'=>array('datarestore',YMADMINDEX.'?yimao=datarestore')),			 		
				);

?>