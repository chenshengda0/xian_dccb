<?php
		define('OP' ,'操作');
		define('DATE_TIME' ,'日期');
		
		
		/**
		 * ox_member
		 * @var unknown
		*/
		define('USERNUMBER' ,'会员编号');
		define('REALNAME' ,'会员姓名');
		define('USERRANK' ,'会员级别');
		define('REGTYPE' ,'会员职务');
		define('HASMONEY' ,'总钱包');
		define('GL' ,'管理费');
		define('HASBILL' ,'在线钱包');
		define('MONEY' ,'动态钱包');
        define('PROXY_STATE' ,'分红');
		define('HASCP' ,'动态钱包');
		define('TUIJIANMUMBER' ,'推荐人');
		define('PARENTNUMBER' ,'接点人');
		define('BILLCENTER' ,'所属报单中心');
		define('ISBILL' ,'报单中心');
		define('ACTIVE_TIME' ,'激活时间');
		define('REG_TIME' ,'注册时间');
		define('TATUS' ,'会员状态');
		define('PWD' ,'密码');
		
		/**
		 * ox_bonus_count
		 * @var unknown
		*/
		define('BONUS1' ,'推荐奖');
		define('BONUS2' ,'领导奖');
		define('BONUS3' ,'报单奖');
        define('BONUS4' ,'分 红');
		define('TAX' ,'奖金税');
		define('CP' ,'复消');
		
		/**
		 * money_change
		 */
		define('MONEY_TYPE' ,'币种');
		define('BONUS_TYPE' ,'奖金类型');
		define('TARGET' ,'目标会员');
		define('RELEATE' ,'相关会员');
		define('ALLMONEY' ,'总金额');
		define('MONEY' ,'实得金额');
		define('BEFOREMONEY' ,'变更前金额');
		define('MONEY' ,'实得金额');
		
		/**
		 * finance
		 */
		define('INCOME' ,'收入');
		define('EXPEND' ,'支出');
		define('SURPLUS' ,'沉淀');
		define('OUTRATE' ,'拨出率');
		
		return array(
			/*奖金变更类型配置*/
			'CHANGETYPE'=>array(
					1=> "团队奖",
                    2=> "矿机收益",
					 3=> "卖出",
                     4=>"买入",
			         5=>"懒人奖",
			         6=>'动态收益',
			         7=>'分红',
              		 8=>'手续费',
			         9=>'挂到交易中心',
			         10=>'取消交易',
			         11=>'交易中买家取消',
			         12=>'购买vip',
			         13=>'签到',
			         14=>'交易中买家未付款',
			         15=>'代理商收益',
                     16=>'账户转出',
              		 17=>'孵化分红',
                    18=>'解除孵化',
					20=> "激活",
					21=> "充值",
					22=> "提现",
					23=> "提现拒批",
					24=> "币种转换",
					25=> "升级",
					26=> "购物",
//					27=> "撤销订单",
// 					28=> "平台管理费",
					28=> "扣币",
					29=> "重置密码扣费",
// 					30=> "话费充值",
// 					31=> "退回话费",
					32=> "购物购物币转保单币",
			),
			/*币种类型配置*/
			'MONEYTYPE'=>array(
				'title'=>array(1=>'在线钱包',2=>'总钱包',3=>'动态钱包',4=>'静态钱包',5=>'孵化仓',6=>"孵化钱包"),
//                'title'=>array(1=>'注册币',2=>'电子币',3=>'积 分'),
				'color'=>array(1=>'green',2=>'#A2520A',3=>'red',4=>"blue"),
			),
			
			/*标签配置*/
			'LANG'=>array(
					'member'=>array('uid'=>'会员id','usernumber'=>'会员编号','realname'=>'会员姓名','userrank'=>'会员级别',
									'tuijianid'=>'推荐人','parentid'=>'接点人','billcenterid'=>'所属报单中心','isbill'=>'报单中心',
									'hasbill'=>'报单币余额','hasmoney'=>'奖金币余额','reg_time'=>'注册时间','active_time'=>'激活时间',
								),
						
			),

		);
