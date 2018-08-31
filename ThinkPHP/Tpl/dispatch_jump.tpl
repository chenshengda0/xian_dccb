{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <title>跳转提示</title>
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        body{ background: #fff; font-family: "Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif; color: #333; font-size: 16px; }
        .system-message{ padding: 24px 48px; }
        .system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
        .system-message .jump{ padding-top: 10px; }
        .system-message .jump a{ color: #333; }
       .system-message .success,.system-message .error{ line-height: 1.8em;  }
        .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display: none; }
      	.img{
				width: 50%;
				margin:auto;
				margin-top: 100px;
          		margin-left: 25%;
			}
			.img img{
				width: 100%;				
			}
			.word{
				text-align: center;
				color: #999999;
				font-size: 14px;
			}
          .error,.success{
                text-align: center;
                font-size: 22px;
          }
      	 .jump{
                text-align: center;
          }
    </style>
</head>
<body>
    <div class="system-message">
      
      <present name="message">
       
         <!--<img src="Public/static/common/img/success.gif"/ class="img">-->
        <p class="success"><?php echo($message); ?></p>
        <else/>
           <!-- <img src="Public/static/common/img/error.gif"/ class="img">-->
        <p class="error"><?php echo($error); ?></p>
        </present>
     
        <p class="detail"></p>
        <p class="jump">
 <a id="href" href="<?php echo($jumpUrl); ?>">跳转中</a> 等待时间： <b id="wait">1</b>        </p>
    </div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
