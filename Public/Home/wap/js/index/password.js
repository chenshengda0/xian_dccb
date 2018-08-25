var add = $('.mid .dd');
var add1 = $('.dd1');
add.click(function() {
	add.removeClass('active');
	add1.removeClass('cur');
	$(this).addClass('active');
	add1.eq($(this).index()).addClass('cur');
});