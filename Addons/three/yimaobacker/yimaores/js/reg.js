

function getusername(s){
  $.ajax({
    type: "get",
    url: "yimaoajax.php?act=getusername",
    dataType: "json",
    success: function(json){ 
      $('#'+s).attr('value',json.name);
    },
    error: function(){
       artalert('获取失败',0);
    }
  })
}
