function getuserid(s){

  $.ajax({
    type: "get",
    url: ymurl+"ajax.php?act=getuserid",
    dataType: "json",
    success: function(json){ 
      $('#'+s).attr('value',json.name);
    },
    error: function(){
       artalert('获取失败',0);
    }
  })
}
