// JavaScript Document
var tt='start';
//turnit函数弹出子菜单
//ss是子菜单，bb是主菜单
function turnit(ss,bb) {

  if (ss.style.display=="none") {   //显示子菜单
    if(tt!='start') tt.style.display="none";  //先隐藏打开状态的子菜单，再显示当前子菜单
    ss.style.display="";
    tt=ss; 
  }
  else {   //隐藏子菜单
    ss.style.display="none"; 
  }
}

