var add = $('.mid .dd');
var add1 = $('.dd1');
var aa1 = $('.aa');
add.click(function() {
	add.removeClass('active');
	aa1.removeClass('cur');
	add1.removeClass('cur');
	$(this).addClass('active');
	add1.eq(add.index(this)).addClass('cur');
	aa1.eq(add.index(this)).addClass('cur');
});
$('.buy').click(function(){
	$('.buy-1').toggle();
});
$('.cancle').click(function(){
	$('.buy-1').hide();
})

	 
	  
	  