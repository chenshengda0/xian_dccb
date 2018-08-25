function isBrowser(){
	var Sys={};
	var ua=navigator.userAgent.toLowerCase();
	var s;
	(s=ua.match(/msie ([\d.]+)/))?Sys.ie=s[1]:
	(s=ua.match(/firefox\/([\d.]+)/))?Sys.firefox=s[1]:
	(s=ua.match(/chrome\/([\d.]+)/))?Sys.chrome=s[1]:
	(s=ua.match(/opera.([\d.]+)/))?Sys.opera=s[1]:
	(s=ua.match(/version\/([\d.]+).*safari/))?Sys.safari=s[1]:0;
	if(Sys.ie){//Js判断为IE浏览器
		layer.alert('为了您有更好的体验，建议您将浏览器更换为火狐浏览器或者是谷歌浏览器！');
		if(Sys.ie=='9.0'){//Js判断为IE 9
		}else if(Sys.ie=='8.0'){//Js判断为IE 8
		}else{
		}
	}
	/*if(Sys.firefox){//Js判断为火狐(firefox)浏览器
		alert('http://www.phpernote.com'+Sys.firefox);
	}
	if(Sys.chrome){//Js判断为谷歌chrome浏览器
		alert('http://www.phpernote.com'+Sys.chrome);
	}
	if(Sys.opera){//Js判断为opera浏览器
		alert('http://www.phpernote.com'+Sys.opera);
	}
	if(Sys.safari){//Js判断为苹果safari浏览器
		alert('http://www.phpernote.com'+Sys.safari);
	}*/
}