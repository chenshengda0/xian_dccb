function imgChange(obj1, obj2) {
	//获取点击的文本框
	var file = document.getElementById("file");
	//存放图片的父级元素
	var imgContainer = document.getElementsByClassName(obj1)[0];
	//获取的图片文件
	var fileList = file.files;
	//文本框的父级元素
	var input = document.getElementsByClassName(obj2)[0];
	var imgArr = [];
	//遍历获取到得图片文件
	for(var i = 0; i < 1; i++) {
		var imgUrl = window.URL.createObjectURL(file.files[i]);
		imgArr.push(imgUrl);
		var img = document.createElement("img");
		img.setAttribute("src", imgArr[i]);
		var imgAdd = document.createElement("div");
		imgAdd.setAttribute("class", "z_addImg");
		imgAdd.appendChild(img);
		imgContainer.appendChild(imgAdd);

	};
	$('.z_photo').append('<p style="clear:both"></p>')
	imgRemove();
};

function imgRemove() {
	var imgList = document.getElementsByClassName("z_addImg");
	var mask = document.getElementsByClassName("z_mask")[0];
	var cancel = document.getElementsByClassName("z_cancel")[0];
	var sure = document.getElementsByClassName("z_sure")[0];
	for(var j = 0; j < imgList.length; j++) {
		imgList[j].index = j;
		imgList[j].onclick = function() {
			var t = this;
			mask.style.display = "block";
			cancel.onclick = function() {
				mask.style.display = "none";
			};
			sure.onclick = function() {
				mask.style.display = "none";
              	$(t).remove();
			};

		}
	};
};
var a = $('.z_addImg').length;
if(a > 1) {
	alert(1);
}

function imgChange1(obj1, obj2) {
	//获取点击的文本框
	var file = document.getElementById("file1");
	//存放图片的父级元素
	var imgContainer = document.getElementsByClassName(obj1)[0];
	//获取的图片文件
	var fileList = file.files;
	//文本框的父级元素
	var input = document.getElementsByClassName(obj2)[0];
	var imgArr = [];
	//遍历获取到得图片文件
	for(var i = 0; i < fileList.length; i++) {
		var imgUrl = window.URL.createObjectURL(file.files[i]);
		imgArr.push(imgUrl);
		var img = document.createElement("img");
		img.setAttribute("src", imgArr[i]);
		var imgAdd = document.createElement("div");
		imgAdd.setAttribute("class", "z_addImg1");
		imgAdd.appendChild(img);
		imgContainer.appendChild(imgAdd);

	};
	$('.z_photo1').append('<p style="clear:both"></p>')
	imgRemove1();
};

function imgRemove1() {
	var imgList = document.getElementsByClassName("z_addImg1");
	var mask = document.getElementsByClassName("z_mask1")[0];
	var cancel = document.getElementsByClassName("z_cancel1")[0];
	var sure = document.getElementsByClassName("z_sure1")[0];
	for(var j = 0; j < imgList.length; j++) {
		imgList[j].index = j;
		imgList[j].onclick = function() {
			var t = this;
			mask.style.display = "block";
			cancel.onclick = function() {
				mask.style.display = "none";
			};
			sure.onclick = function() {
				mask.style.display = "none";
				$(t).remove();
			};

		}
	};
};
var a = $('.z_addImg1').length;
if(a > 1) {
	alert(1);
} //
//2
function imgChange2(obj1, obj2) {
	//获取点击的文本框
	var file = document.getElementById("file2");
	//存放图片的父级元素
	var imgContainer = document.getElementsByClassName(obj1)[0];
	//获取的图片文件
	var fileList = file.files;
	//文本框的父级元素
	var input = document.getElementsByClassName(obj2)[0];
	var imgArr = [];
	//遍历获取到得图片文件
	for(var i = 0; i < 1; i++) {
		var imgUrl = window.URL.createObjectURL(file.files[i]);
		imgArr.push(imgUrl);
		var img = document.createElement("img");
		img.setAttribute("src", imgArr[i]);
		var imgAdd = document.createElement("div");
		imgAdd.setAttribute("class", "z_addImg2");
		imgAdd.appendChild(img);
		imgContainer.appendChild(imgAdd);

	};
	$('.z_photo2').append('<p style="clear:both"></p>')
	imgRemove2();
};

function imgRemove2() {
	var imgList = document.getElementsByClassName("z_addImg2");
	var mask = document.getElementsByClassName("z_mask2")[0];
	var cancel = document.getElementsByClassName("z_cancel2")[0];
	var sure = document.getElementsByClassName("z_sure2")[0];
	for(var j = 0; j < imgList.length; j++) {
		imgList[j].index = j;
		imgList[j].onclick = function() {
			var t = this;
			mask.style.display = "block";
			cancel.onclick = function() {
				mask.style.display = "none";
			};
			sure.onclick = function() {
				mask.style.display = "none";
				$(t).remove();
			};

		}
	};
};
var a = $('.z_addImg2').length;
if(a > 1) {
	alert(1);
} //
//3
function imgChange3(obj1, obj2) {
	//获取点击的文本框
	var file = document.getElementById("file3");
	//存放图片的父级元素
	var imgContainer = document.getElementsByClassName(obj1)[0];
	//获取的图片文件
	var fileList = file.files;
	//文本框的父级元素
	var input = document.getElementsByClassName(obj2)[0];
	var imgArr = [];
	//遍历获取到得图片文件
	for(var i = 0; i < 1; i++) {
		var imgUrl = window.URL.createObjectURL(file.files[i]);
		imgArr.push(imgUrl);
		var img = document.createElement("img");
		img.setAttribute("src", imgArr[i]);
		var imgAdd = document.createElement("div");
		imgAdd.setAttribute("class", "z_addImg3");
		imgAdd.appendChild(img);
		imgContainer.appendChild(imgAdd);

	};
	$('.z_photo3').append('<p style="clear:both"></p>')
	imgRemove3();
};

function imgRemove3() {
	var imgList = document.getElementsByClassName("z_addImg3");
	var mask = document.getElementsByClassName("z_mask3")[0];
	var cancel = document.getElementsByClassName("z_cancel3")[0];
	var sure = document.getElementsByClassName("z_sure3")[0];
	for(var j = 0; j < imgList.length; j++) {
		imgList[j].index = j;
		imgList[j].onclick = function() {
			var t = this;
			mask.style.display = "block";
			cancel.onclick = function() {
				mask.style.display = "none";
			};
			sure.onclick = function() {
				mask.style.display = "none";
				$(t).remove();
			};

		}
	};
};
var a = $('.z_addImg3').length;
if(a > 1) {
	alert(1);
} //