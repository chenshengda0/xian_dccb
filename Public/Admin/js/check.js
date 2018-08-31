function check_card(str,info){
    var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/; 
	var str=$("#"+str).val();
	if(!reg.test(str)){
		alert(info+'格式不正确');
		return false;
	}else{
		return true;
	}
   }

function check_empty(str,info){
		var str=$("#"+str).val();
		if(str==false){
			alert(info+'不能为空');
			return false;
		}else{
			return true;	
		}
	}
	function check_number(str,info){
		var reg = new RegExp("^[0-9]*$");
		var str=$("#"+str).val();
		if(!reg.test(str)){
			alert(info+'必须为数字');
			return false;
		}else{
			return true;
		}
	}
	function check_Mail(str,info){
		var reg = new RegExp("^[A-Za-z0-9]+([-_.][A-Za-z0-9]+)*@([A-Za-z0-9]+[-.])+[A-Za-z0-9]{2,5}$");
		var str=$("#"+str).val();
		if(!reg.test(str)){
			alert(info+'格式不正确'+str);
			return false;
		}else{
			return true;
		}
	}
	function check_Same(str1,info1,str2,info2){
		var str1=$("#"+str1).val();
		var str2=$("#"+str2).val();
		if(str1==str2){
			return true;
		}else{
			alert(info1+"和"+info2+"不一致");
			return false;
		}
	}
	function check_Size(str,info,min,max){
		var str=$("#"+str).val();
		var len=str.length;
		if(len>=min&&len<=max){
			return true;
		}else{
			alert(info+"长度必须在"+min+"-"+max+"个字符之间！");
			return false;
		}
	}