

function selectQ(questions,selectobj){
	this.questions = questions;
	this.selectobj = selectobj;
	this.currentValue=new Array();
}

selectQ.prototype.init = function(){
	  for(var j=0;j<this.selectobj.length;j++){
          //清除原先选项：
          this.$$(this.selectobj[j]).options.length=0;
          //添加选项：
           this.addOption(this.selectobj[j],'--请选择密保问题--','');
           
          for(var i=0;i<this.questions.length;i++){
                this.addOption(this.selectobj[j],this.questions[i],this.questions[i]);
              }
     }
}

selectQ.prototype.$$ = function(id){
	 return document.getElementById(id);
}

selectQ.prototype.collectVlue =function(){
      var currentobj=null;
      for(var j=0;j<this.selectobj.length;j++){
          currentobj=this.$$(selectobj[j]);
          this.currentValue[j]=currentobj.value;
       }
    }    
selectQ.prototype.change = function(i){
      if(i!=10000){
         this.collectVlue();
      }
      
      for(var j=0;j<this.selectobj.length;j++){
          //清除原先选项：
          this.$$(this.selectobj[j]).options.length=0;
         //添加选项：
        this.addOption(this.selectobj[j],'--请选择密保问题--','');
            
          for(var i=0;i<this.questions.length;i++){
                  
                    var available=true;//选项是否被占用
                    for(var k=0;k<this.currentValue.length;k++)
                    {
                       if(this.questions[i]==this.currentValue[k]&&j!=k){
                            available=false;
                            break;   
                        }    
                    }
                    if(available){
                        //添加选项：
                      this.addOption(this.selectobj[j],this.questions[i],this.questions[i]);
                   }
                
              }
            //选中项：
            //$(selectobj[j]).value=currentValue[j];
            this.setSelect(this.selectobj[j],this.currentValue[j]);
       }
    }
//使select选中特定值：
selectQ.prototype.setSelect = function(id,value){
       for(var i=0;i<this.$$(id).options.length;i++){
             if(this.$$(id).options[i].value==value){
                   this.$$(id).selectedIndex=i;
                   break;
                 }
           }
    }
//给select添加值：
selectQ.prototype.addOption = function (id,text,value){
        var oOption = document.createElement("option"); 
         oOption.text=text; 
         oOption.value=value; 
         try {
            this.$$(id).add(oOption); //这个在IE下可以
  
        } catch(ex) {
            this.$$(id).add(oOption,null);//这个在FF下可以
   
        }
    }  
