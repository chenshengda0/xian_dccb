 document.getElementById('photo').addEventListener('change',function(e){		
		var files = this.files;
		var img = new Image();
		var reader = new FileReader();
		reader.readAsDataURL(files[0]);
		reader.onload = function(e){
			var mb = (e.total/1024)/1024;
			if(mb>= 1){
				$.layer({
					title:'提示',
				    shade: [0],  
				    area: ['auto','auto'],  
				    dialog: {  
				        msg: '上传图片不能大于1M',                     
				        type: 0 
				    }  
				});   
				  
                return;
			}
			img.src = this.result;
			img.style.width = "85px";
			document.getElementById('click').src = '';
			document.getElementById('click').src =img.src;
			
		}
	   });