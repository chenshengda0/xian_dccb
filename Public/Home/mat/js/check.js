

function check_nickname(str,info){
    var reg = /^(?!_)(?!.*?_$)[a-zA-Z0-9_\u4e00-\u9fa5]+$/; 
	var str=$("#"+str).val();
	if(!reg.test(str)){
		check_Error(strerr);
		return false;
	}else{
		return true;
	}
   }

	function check_empty(id,info){
		var str=$("#"+id).val();
		if(str==false){
			check_Error(id,info+'不能为空');
			return false;
		}else{
			check_Ok(id);
			return true;	
		}
	}


	function check_number(id,info){
		var reg = new RegExp("^[0-9]*$");
		var str=$("#"+id).val();
		if(!reg.test(str)){
			$("#"+id).focus();
			check_Error(id,info+'必须为纯数字');
			return false;
		}else{
			check_Ok(id);
			return true;
		}
	}
	
	/**
	 * QQ号格式检测
	 */
	function check_qq(str,info,strerr){
		var reg = new RegExp("^[0-9]*$");
		var str=$("#"+str).val();
		if(!reg.test(str)){
			$("#"+str).focus();
			check_Error(strerr);
			return false;
		}else{
			check_Ok(id);
			return true;
		}
	}
	
	/**
	 * 手机号格式检测
	 */
	function check_phone(id,info){
		var reg = new RegExp("^1[3|5|7|8|][0-9]{9}");
		var str=$("#"+id).val();
		if(!reg.test(str)){
			check_Error(id,info+'格式不正确');
			return false;
		}else{
			check_Ok(id);
			return true;
		}
	}
	
	/**
	 * 邮箱格式检测
	 */
	function check_Mail(str,info){
		var reg = new RegExp("^[A-Za-z0-9]+([-_.][A-Za-z0-9]+)*@([A-Za-z0-9]+[-.])+[A-Za-z0-9]{2,5}$");
		var str=$("#"+str).val();
		if(!reg.test(str)){
			$("#"+str).focus();
			alert(info+'格式不正确');
			return false;
		}else{
			return true;
		}
	}
	function check_Same(id1,info1,id2,info2){
		var str1=$("#"+id1).val();
		var str2=$("#"+id2).val();
		if(str1==str2){
			check_Ok(id2)
			return true;
		}else{
			check_Error(strerr);
			return false;
		}
	}
	function check_Size(id,info,min,max){
		var str=$("#"+id).val();
		var len=str.length;
		if(len>=min&&len<=max){
			check_Ok(id)
			return true;
		}else{
			check_Error(id,info+'必须在'+min+'和'+max+'之间');
			return false;
		}
	}
	
	function check_card(id,info){
		var str=$("#"+id).val();
		if(!isIdCardNo(str)){
			$("#"+id).focus();
			check_Error(id,info+'格式不正确');
			return false;
		}else{
			check_Ok(id)
			return true;
		}
	}
	
	function isCardID(sId){ 
		
		var iSum=0 ;  
		  var info="" ;  
		  if(!/^\d{17}(\d|x)$/i.test(sId)) return "你输入的身份证长度或格式错误";  
		  sId=sId.replace(/x$/i,"a");  
		  if(aCity[parseInt(sId.substr(0,2))]==null) return false;  
		  sBirthday=sId.substr(6,4)+"-"+Number(sId.substr(10,2))+"-"+Number(sId.substr(12,2));  
		  var d=new Date(sBirthday.replace(/-/g,"/")) ;  
		  if(sBirthday!=(d.getFullYear()+"-"+ (d.getMonth()+1) + "-" + d.getDate()))return "身份证上的出生日期非法";  
		  for(var i = 17;i>=0;i --) iSum += (Math.pow(2,i) % 11) * parseInt(sId.charAt(17 - i),11) ;  
		  if(iSum%11!=1) return false;  
		  return true;//aCity[parseInt(sId.substr(0,2))]+","+sBirthday+","+(sId.substr(16,1)%2?"男":"女")   
	}  
	
	/*身份证号验证*/
	function isIdCardNo(num) {
	    var factorArr = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1);
	    var parityBit = new Array("1", "0", "X", "9", "8", "7", "6", "5", "4", "3", "2");
	    var varArray = new Array();
	    var intValue;
	    var lngProduct = 0;
	    var intCheckDigit;
	    var intStrLen = num.length;
	    var idNumber = num;
	    // initialize
	    if ((intStrLen != 15) && (intStrLen != 18)) {
	        return false;
	    }
	    
	    var aCity={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",  
				21:"辽宁",22:"吉林",23:"黑龙江",31:"上海",32:"江苏",33:"浙江",  
				34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",  
				43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川"  
				,52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",  
				64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"}  ; 
	    
	    if(aCity[parseInt(num.substr(0,2))]==null) return false; //身份证区验证
	    for (i = 0; i < intStrLen; i++) {
	        varArray[i] = idNumber.charAt(i);
	        if ((varArray[i] < '0' || varArray[i] > '9') && (i != 17)) {
	            return false;
	        } else if (i < 17) {
	            varArray[i] = varArray[i] * factorArr[i];
	        }
	    }
	    if (intStrLen == 18) {
	        //check date
	        var date8 = idNumber.substring(6, 14);
	        if (isDate8(date8) == false) {
	            return false;
	        }
	        // calculate the sum of the products
	        for (i = 0; i < 17; i++) {
	            lngProduct = lngProduct + varArray[i];
	        }
	        // calculate the check digit
	        intCheckDigit = parityBit[lngProduct % 11];
	        // check last digit
	        if (varArray[17] != intCheckDigit) {
	            return false;
	        }
	    }
	    else {        //length is 15
	        //check date
	        var date6 = idNumber.substring(6, 12);
	        if (isDate6(date6) == false) {
	            return false;
	        }
	    }
	    return true;
	}
	function isDate6(sDate) {
	    if (!/^[0-9]{6}$/.test(sDate)) {
	        return false;
	    }
	    var year, month, day;
	    year = sDate.substring(0, 4);
	    month = sDate.substring(4, 6);
	    if (year < 1700 || year > 2500) return false
	    if (month < 1 || month > 12) return false
	    return true
	}

	function isDate8(sDate) {
	    if (!/^[0-9]{8}$/.test(sDate)) {
	        return false;
	    }
	    var year, month, day;
	    year = sDate.substring(0, 4);
	    month = sDate.substring(4, 6);
	    day = sDate.substring(6, 8);
	    var iaMonthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]
	    if (year < 1700 || year > 2500) return false
	    if (((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0)) iaMonthDays[1] = 29;
	    if (month < 1 || month > 12) return false
	    if (day < 1 || day > iaMonthDays[month - 1]) return false
	    return true
	}
	
	/**
	 * 验证结果错误的显示信息
	 */
	function check_Error(str,info){
		layer.tips(info,'#'+str,{time:5,guide:0});
		$("#"+str).focus();
		$("#"+str).removeClass('form-success');
		$("#"+str).addClass('form-error');
		$("#"+str).parents('form').find('input[target-form]').hide();
	}
	/**
	 * 验证结果正确的显示信息
	 */
	function check_Ok(str){
		layer.closeTips()
		$("#"+str).removeClass('form-error');
		$("#"+str).addClass('form-success');
		$("#"+str).parents('form').find('input[target-form]').show();
	}
