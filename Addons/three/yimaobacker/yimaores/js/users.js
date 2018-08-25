// JavaScript Document 中文

function quickset(u,k,t){

  $.ajax({
    type: "post",
    url: "yimaoajax.php?act=quickset",
    dataType: "json",
    data:{"optkey":k,'username':u},
    success: function(json){
      if(k=='pwd'){
        if(json.statu){
          artalert(json.name,1);
        }else{
          artalert(json.name,0);
        }
      }else{
         $(t).find('span').html(json.name);
      }
     
    },
    error: function(){
      artalert('设置失败',0);
    }
  }); 
}
