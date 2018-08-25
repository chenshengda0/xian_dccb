var userid="";useridtxt="账号必须是4-15位字母，数字和下划线的组合";
var username="";usernametxt="请输入姓名";
var bdname="";bdnametxt="请输入报单中心";
var tuname="";tunametxt="请输入推荐人";
var anname="";annametxt="请输入安置人";
var anrea="";anreatxt="请先输入安置人";
var pwd1="";pwd1txt="密码请输入8-20位的字符";
var pwd11="";pwd11txt="密码请输入8-20位的字符";
var pwd2="";pwd2txt="密码请输入8-20位的字符";
var pwd21="";pwd21txt="密码请输入8-20位的字符";
var pwd3="";pwd3txt="密码请输入8-20位的字符";
var pwd31="";pwd31txt="密码请输入8-20位的字符";
var mba="";mbatxt="请输入您的密保答案";
var mobile="";mobiletxt="请输入您的手机号码";
var email="";emailtxt="请输入您的电子邮箱";
var card="";cardtxt="请输入您的身份证号";
var weixin="";weixintxt="请输入您的"+((typeof(rgweixinxx)=='undefined')?'':rgweixinxx);
var qq="";qqtxt="请输入您的"+((typeof(rgqqxx)=='undefined')?'':rgqqxx);
var fax="";faxtxt="请输入您的"+((typeof(rgfaxxx)=='undefined')?'':rgfaxxx);
var address;addresstxt="请输入您的"+((typeof(rgaddressxx)=='undefined')?'':rgaddressxx);


$(function () {

    $('#mbq').change(function(){
        if($(this).val()==-1){
            $(this).stip(this,"请选择密保问题!", 2);
        }else{
            $(this).stip(this,"", 3);
        }
    })
    $('#province').change(function(){
        if($(this).val()==""){
            $(this).stip(this,"请选择省份!", 2);
        }else{
            $(this).stip(this,"", 3);
        }
    })

    $("#userid").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, useridtxt, 1);
        }
    }).blur(function () {
        chkuserid();
        if (userid=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&userid!="") {
                $(this).stip(this,userid, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    })  

    $("#username").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, usernametxt, 1);
        }
    }).blur(function () {
        chkusername();
        if (username=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&username!="") {
                $(this).stip(this,username, 2);
            }else{
                $(this).stip(this, "", 4);
            }            
        }
    })  



    $("#bdname").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, bdnametxt, 1);
        }
    }).blur(function () {
        chkbdname()
        if (bdname=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&bdname!="") {
                $(this).stip(this,bdname, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    }) 

    $("#tuname").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, tunametxt, 1);
        }
    }).blur(function () {
        chktuname()
        if (tuname=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&tuname!="") {
                $(this).stip(this,tuname, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    })

    $("#anname").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, annametxt, 1);
        }
    }).blur(function () {
        chkanname()
        if (anname=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&anname!="") {
                $(this).stip(this,anname, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    })

    $("input[name='anrea']").bind("click",chkanrea)


    $("#rgpwd1").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, pwd1txt, 1);
        }
    }).blur(function () {
        chkpwd1()
        if (pwd1=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&pwd1!="") {
                $(this).stip(this,pwd1, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    }) 
    $("#rgpwd11").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, pwd11txt, 1);
        }
    }).blur(function () {
        chkpwd11()
        if (pwd11=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&pwd11!="") {
                $(this).stip(this,pwd11, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    }) 


    $("#rgpwd2").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, pwd2txt, 1);
        }
    }).blur(function () {
        chkpwd2()
        if (pwd2=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&pwd2!="") {
                $(this).stip(this,pwd2, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    })  

    $("#rgpwd21").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, pwd21txt, 1);
        }
    }).blur(function () {
        chkpwd21()
        if (pwd21=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&pwd21!="") {
                $(this).stip(this,pwd21, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    })        

    $("#rgpwd3").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, pwd3txt, 1);
        }
    }).blur(function () {
        chkpwd3()
        if (pwd3=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&pwd3!="") {
                $(this).stip(this,pwd3, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    })  

    $("#rgpwd31").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, pwd31txt, 1);
        }
    }).blur(function () {
        chkpwd31()
        if (pwd31=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&pwd31!="") {
                $(this).stip(this,pwd31, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    }) 

    $("#rgmba").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, mbatxt, 1);
        }
    }).blur(function () {
        chkmba()
        if (mba=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&mbatxt!="") {
                $(this).stip(this,mba, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    }) 

    $("#rgmobile").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, mobiletxt, 1);
        }
    }).blur(function () {
        chkmobile()
        if (mobile=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&mobiletxt!="") {
                $(this).stip(this,mobile, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    })

    $("#rgemail").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, emailtxt, 1);
        }
    }).blur(function () {
        chkemail()
        if (email=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&emailtxt!="") {
                $(this).stip(this,email, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    }) 

    $("#rgcard").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, cardtxt, 1);
        }
    }).blur(function () {
        chkcard()
        if (card=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&cardtxt!="") {
                $(this).stip(this,card, 2);
            }else{
                $(this).stip(this, "", 4);
            }
        }
    })    


    $("#rgweixin").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, weixintxt, 1);
        }
    }).blur(function () {
        chkweixin();
        if (weixin=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&weixin!="") {
                $(this).stip(this,weixin, 2);
            }else{
                $(this).stip(this, "", 4);
            }   
        }
    })  

    $("#rgqq").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, qqtxt, 1);
        }
    }).blur(function () {
        chkqq();
        if (qq=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&qq!="") {
                $(this).stip(this,qq, 2);
            }else{
                $(this).stip(this, "", 4);
            }   
        }
    })  

    $("#rgfax").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, faxtxt, 1);
        }
    }).blur(function () {
        chkfax();
        if (fax=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&fax!="") {
                $(this).stip(this,fax, 2);
            }else{
                $(this).stip(this, "", 4);
            }  
        }
    })  


    $("#rgaddress").focus(function () {
        if ($(this).val() == "") {
            $(this).stip(this, addresstxt, 1);
        }
    }).blur(function () {
        chkaddress();
        if (address=="ok") {
            $(this).stip(this, "", 3);
        } else {
            if ($(this).val() != ""&&address!="") {
                $(this).stip(this,address, 2);
            }else{
                $(this).stip(this, "", 4);
            }  
        }
    })          
})


function chkuserid() {
    var name = $.trim($("#userid").val());
    var result = name.match(/^[a-zA-Z0-9][a-zA-Z0-9_]{3,14}$/);

    if ($.trim(name) == "") {
        userid= useridtxt;
    }
    else if (result == null) {
        userid = useridtxt;
    }else {
      userid='';
      $.ajax({
        type: "get",
        dataType: "json",
        url: ymurl+"ajax.php?act=chkuser&userid="+name,
        success: function(data){
            userid=data.tips;
        },
        error: function(){
           
        },
        cache: false,
        async: false
      });
        
    }
}

function chkbdname() {
    var name = $.trim($("#bdname").val());
    var result = name.match(/^[a-zA-Z0-9][a-zA-Z0-9_]{3,14}$/);

    if ($.trim(name) == "") {
        bdname= bdnametxt;
    }
    else if (result == null) {
        bdname = "报单中心输入不正确";
    }else {
      bdname='';
      $.ajax({
        type: "get",
        dataType: "json",
        url: ymurl+"ajax.php?act=chkbd&userid="+name,
        success: function(data){
            bdname=data.tips;

        },
        error: function(){
           
        },
        cache: false,
        async: false
      });
        
    }
}

function chktuname() {
    var name = $.trim($("#tuname").val());
    var result = name.match(/^[a-zA-Z0-9][a-zA-Z0-9_]{3,14}$/);

    if ($.trim(name) == "") {
        tuname= tunametxt;
    }
    else if (result == null) {
        tuname = "推荐人输入不正确";
    }else {
      tuname='';
      $.ajax({
        type: "get",
        dataType: "json",
        url: ymurl+"ajax.php?act=chktu&userid="+name,
        success: function(data){
            tuname=data.tips;
        },
        error: function(){
           
        },
        cache: false,
        async: false
      });
        
    }
}

function chkanname() {
    var name = $.trim($("#anname").val());
    var result = name.match(/^[a-zA-Z0-9][a-zA-Z0-9_]{3,14}$/);

    if ($.trim(name) == "") {
        anname= annametxt;
    }
    else if (result == null) {
        anname = "安置人输入不正确";
    }else {
      anname='';
      $.ajax({
        type: "get",
        dataType: "json",
        url: ymurl+"ajax.php?act=chkan&userid="+name,
        success: function(data){
            anname=data.tips;
        },
        error: function(){
           
        },
        cache: false,
        async: false
      });
        
    }
}


function chkanrea(){
    var name =$.trim($("#anname").val());
    var anreas=$("input[name='anrea']:checked").val();

    var result = name.match(/^[a-zA-Z0-9][a-zA-Z0-9_]{3,14}$/);

    if ($.trim(name) == "") {
        $("#anreatxt").stip(this, anreatxt, 2);
    }
    else if (result == null) {
        $("#anreatxt").stip(this, "安置人输入不正确", 2);
    }else {
      anrea='';
      $.ajax({
        type: "get",
        dataType: "json",
        url: ymurl+"ajax.php?act=chkanrea&userid="+name+"&anrea="+anreas,
        success: function(data){
            anrea=data.tips;
        },
        error: function(){
           
        },
        cache: false,
        async: false
      });
        

      if(anrea=="ok")
        $("#anreatxt").stip(this, "", 3);
      else if(anrea!=""){
        $("#anreatxt").stip(this, anrea, 2);}
      else{
        $("#anreatxt").stip(this, "", 4);}
    }


}


function chkusername(){
    var result=$.trim($("#username").val());
    if (result.match(/^\s*$/)) {
        username = usernametxt;
        return true;
    } else {
        username = "ok";
        return true;
    }   
}


function chkmba(){
    var result=$.trim($("#rgmba").val());
    if (result.match(/^\s*$/)) {
        mba = mbatxt;
        return true;
    } else {
        mba = "ok";
        return true;
    } 
}

function chkpwd1() {
    var password= $.trim($("#rgpwd1").val());
    if (password.length<8 || password.length>20) {
        pwd1 = pwd1txt;
        return false;
    } else {
        pwd1 = "ok";
        return true;
    }
}

function chkpwd11() {
    var pwd = $.trim($("#rgpwd1").val());
    var conFirmPwd = $.trim($("#rgpwd11").val());
    if(pwd==""){
        pwd11="请先输入您的"+rgpwd1name
        return false;
    }else if(pwd.length<8 || pwd.length>20){
        pwd11 ="您输入的"+rgpwd1name+"不正确";
        return false;
    }else if(conFirmPwd==""){
        pwd11 = pwd11txt;
        return false;
    }else if(conFirmPwd.length<8 || conFirmPwd.length>20){
        pwd11 = pwd11txt;
        return false;
    }else if(conFirmPwd!=pwd ){
        pwd11 = "您输入的确认密码不正确";
        return false;
    }else{
        pwd11 = "ok";
        return true;
    }
}

function chkpwd2() {
    var password= $.trim($("#rgpwd2").val());
    if (password.length<8 || password.length>20) {
        pwd2 = pwd2txt;
        return false;
    } else {
        pwd2 = "ok";
        return true;
    }
}

function chkpwd21() {
    var pwd = $.trim($("#rgpwd2").val());
    var conFirmPwd = $.trim($("#rgpwd21").val());
    if(pwd==""){
        pwd21="请先输入您的"+rgpwd2name
        return false;
    }else if(pwd.length<8 || pwd.length>20){
        pwd21 ="您输入的"+rgpwd2name+"不正确";
        return false;
    }else if(conFirmPwd==""){
        pwd21 = pwd21txt;
        return false;
    }else if(conFirmPwd.length<8 || conFirmPwd.length>20){
        pwd21 = pwd21txt;
        return false;
    }else if(conFirmPwd!=pwd ){
        pwd21 = "您输入的确认密码不正确";
        return false;
    }else{
        pwd21 = "ok";
        return true;
    }
}


function chkpwd3() {
    var password= $.trim($("#rgpwd3").val());
    if (password.length<8 || password.length>20) {
        pwd3 = pwd3txt;
        return false;
    } else {
        pwd3 = "ok";
        return true;
    }
}


function chkpwd31() {
    var pwd = $.trim($("#rgpwd3").val());
    var conFirmPwd = $.trim($("#rgpwd31").val());
    if(pwd==""){
        pwd31="请先输入您的"+rgpwd3name
        return false;
    }else if(pwd.length<8 || pwd.length>20){
        pwd31 ="您输入的"+rgpwd3name+"不正确";
        return false;
    }else if(conFirmPwd==""){
        pwd31 = pwd31txt;
        return false;
    }else if(conFirmPwd.length<8 || conFirmPwd.length>20){
        pwd31 = pwd31txt;
        return false;
    }else if(conFirmPwd!=pwd ){
        pwd31 = "您输入的确认密码不正确";
        return false;
    }else{
        pwd31 = "ok";
        return true;
    }
}


function chkmobile(){
    var mobiles = $.trim($("#rgmobile").val());

    /*中国移动拥有号码段为:139,138,137,136,135,134,159,158,157(3G),151,152,150,188(3G),183,187(3G),188;15个号段
    中国联通拥有号码段为:130,131,132,155,156(3G),186(3G),185(3G);7个号段
    中国电信拥有号码段为:133,153,189(3G),180(3G);4个号码段*/
    var result = mobiles.match(/^1(([3][456789])|([5][012789])|([8][2378]))[0-9]{8}$/);
    result = result || mobiles.match(/^1(([3][012])|([5][56])|([8][56]))[0-9]{8}$/);
    result = result || mobiles.match(/^1(([3][3])|([5][3])|([8][09]))[0-9]{8}$/);
    if(mobiles==""){
        mobile = mobiletxt;
        return false;
    }

    if (result == null) {
        mobile = "手机号码格式不正确";
        return false;
    
    }else {
      mobile='';
      $.ajax({
        type: "get",
        dataType: "json",
        url: ymurl+"ajax.php?act=chkmobile&mobile="+mobiles,
        success: function(data){

            mobile=data.tips;
        },
        error: function(){
           
        },
        cache: false,
        async: false
      });

    }
}

function chkemail() {
    var emails = $.trim($("#rgemail").val());
    if (emails != "") {
        var result = emails.match(/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/);
        if (result == null) {
            email = "您输入的邮箱格式不正确";
            return false;
        }
        else {
            email = "ok";
            return true;
        }
    }else{
            email = emailtxt;
            return false;
    }
}


function chkcard(){
    var pCard =$.trim($("#rgcard").val());
    if (pCard != "") {
        var result =IdentityCodeValid(pCard);
        if (result == false) {
            card = "您输入的身份证不正确";
            return false;
        }
        else {
            card = "ok";
            return true;
        }
    }else {
        card = cardtxt;
        return true;
    }
}

function IdentityCodeValid(code) { 
    var city={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外 "};
    var tip = "";
    var pass= true;
            
    if(!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i.test(code)){
        tip = "身份证号格式错误";
        pass = false;
    }else if(!city[code.substr(0,2)]){
        tip = "地址编码错误";
        pass = false;
    }else{
        //18位身份证需要验证最后一位校验位
        if(code.length == 18){
            code = code.split('');
            //∑(ai×Wi)(mod 11)
            //加权因子
            var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
            //校验位
            var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
            var sum = 0;
            var ai = 0;
            var wi = 0;
            for (var i = 0; i < 17; i++)
            {
                ai = code[i];
                wi = factor[i];
                sum += ai * wi;
            }
            var last = parity[sum % 11];
            if(parity[sum % 11] != code[17]){
                    tip = "校验位错误";
                    pass =false;
            }
        }
    }

    //if(!pass) alert(tip);
    return pass;
}


function chkweixin(){
    var result=$.trim($("#rgweixin").val());
    if (result.match(/^\s*$/)) {
        weixin = weixintxt;
        return true;
    } else {
        weixin = "ok";
        return true;
    }   
}

function chkqq(){
    var result=$.trim($("#rgqq").val());
    if (result.match(/^\s*$/)) {
        qq = qqtxt;
        return true;
    } else {
        qq = "ok";
        return true;
    }   
}

function chkfax(){
    var result=$.trim($("#rgfax").val());
    if (result.match(/^\s*$/)) {
        fax = faxtxt;
        return true;
    } else {
        fax = "ok";
        return true;
    }   
}


function chkaddress(){
    var result=$.trim($("#rgaddress").val());
    if (result.match(/^\s*$/)) {
        address = addresstxt;
        return true;
    } else {
        address = "ok";
        return true;
    }   
}


function register(){
    var hou=true;

if((rgmbq!=undefined)&&rgmbq==0){    
    if ($('#mbq option:selected').val()==-1){
        $("#mbq").stip(this,"请选择密保问题!", 2);
        hou=false;
    }
}

if((rgprovince!=undefined)&&rgprovince==0){  
    if ($('#province option:selected').val()==""){
        $("#province").stip(this,"请选择省份!", 2);
        hou=false;
    }else if ($('#city option:selected').val()==""||$('#city option:selected').val()==undefined){
        $("#province").stip(this,"请选择城市!", 2);
        hou=false;
    }else if ($('#area option:selected').val()==""||$('#area option:selected').val()==undefined){
        $("#province").stip(this,"请选择地区!", 2);
        hou=false;
    }        
}

    chkuserid();
    if(userid!="ok"){
        $("#userid").stip(this,userid, 2);
        hou=false;
    }

    chkusername();
    if(username!="ok"){
        $("#username").stip(this,username, 2);
        hou=false;
    }
if((rgpwd1!=undefined)&&rgpwd1==0){    
    chkpwd1();
    if(pwd1!="ok"){
        $("#rgpwd1").stip(this,pwd1, 2);
        hou=false;
    }
    chkpwd11();
    if(pwd11!="ok"){
        $("#rgpwd11").stip(this,pwd11, 2);
        hou=false;
    }
}

if((rgpwd2!=undefined)&&rgpwd2==0){       
    chkpwd2();
    if(pwd2!="ok"){
        $("#rgpwd2").stip(this,pwd2, 2);
        hou=false;
    }
    chkpwd21();
    if(pwd21!="ok"){
        $("#rgpwd21").stip(this,pwd21, 2);
        hou=false;
    }
}
if((rgpwd3!=undefined)&&rgpwd3==0){         
    chkpwd3();
    if(pwd3!="ok"){
        $("#rgpwd3").stip(this,pwd3, 2);
        hou=false;
    }
    chkpwd31();
    if(pwd31!="ok"){
        $("#rgpwd31").stip(this,pwd31, 2);
        hou=false;
    }
}
if((rgmba!=undefined)&&rgmba==0){         
    chkmba();
    if(mba!="ok"){
        $("#rgmba").stip(this,mba, 2);
        hou=false;
    }
}
if((rgmobile!=undefined)&&rgmobile==0){        
    chkmobile();
    if(mobile!="ok"){
        $("#rgmobile").stip(this,mobile, 2);
        hou=false;
    }
}

if((rgemail!=undefined)&&rgemail==0){        
    chkemail();
    if(email!="ok"){
        $("#rgemail").stip(this,email, 2);
        hou=false;
    }
}
if((rgcard!=undefined)&&rgcard==0){        
    chkcard();
    if(card!="ok"){
        $("#rgcard").stip(this,card, 2);
        hou=false;
    }
}

if((rgweixin!=undefined)&&rgweixin==0){     
    chkweixin();
    if(weixin!="ok"){
        $("#rgweixin").stip(this,weixin, 2);
        hou=false;
    } 
}

if((rgqq!=undefined)&&rgqq==0){   
    chkqq();
    if(qq!="ok"){
        $("#rgqq").stip(this,qq, 2);
        hou=false;
    } 
}   
if((rgfax!=undefined)&&rgfax==0){  
    chkfax();
    if(fax!="ok"){
        $("#rgfax").stip(this,fax, 2);
        hou=false;
    } 
}  
if((rgaddress!=undefined)&&rgaddress==0){  
    chkaddress();
    if(address!="ok"){
        $("#rgaddress").stip(this,address, 2);
        hou=false;
    } 
}    
if(bd){
    chkbdname();
    if(bdname!="ok"){
        $("#bdname").stip(this,bdname, 2);
        hou=false;
    } 
}    
    chktuname();
    if(tuname!="ok"){
        $("#tuname").stip(this,tuname, 2);
        hou=false;
    }
if(an){
    chkanname();
    if(anname!="ok"){
        $("#anname").stip(this,anname, 2);
        hou=false;
    }

    if ($("input[name='anrea']:checked").val()==undefined){
        $("#anreatxt").stip(this,"请选择区域!", 2);
        hou=false;
    }else{
        chkanrea();
        if (anrea!="ok"){
            hou=false;
        }
    }
}

if(!document.getElementById('mobileAcceptIpt').checked){
    alert("请选择同意注册条款！");
    hou=false;
}

if(hou){
    tuname=$.trim($("#tuname").val());
    xx=boxcheck("您注册的邀请人确定为 "+tuname+"，确认后不可修改！\n\n您确定要注册吗?",'注册正在执行中,请耐心等待！')
    return xx;
}else{
    return hou;
}

}