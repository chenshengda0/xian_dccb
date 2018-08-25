(function ($) {
    $.fn.stip = function (settings, pp, ss) {
        var $div = this;
        var stipyes = $("<span class='stipyes'></span>");
        var stipno = $("<span class='stipno'></span>");
		var stiptishi = $("<span class='stiptishi'></span>");
        var stiptxt = $("<span class='stiptxt'>" + pp + "</span>");
        var stipstxtpan = $("<span>&nbsp;</span>");
        function Remove(here) {
            here.parent().find(".stipyes,.stipno,.stiptishi").remove();
        }

        if (ss == 1) {
            Remove(this);
			stiptxt.prepend(stipstxtpan);
            stiptishi.append(stiptxt);
            $(this).parent().append(stiptishi);
        }
        else {
            if (ss == 2) {
                Remove(this);
                stiptxt.prepend(stipstxtpan);
				stipno.append(stiptxt)
                $(this).parent().append(stipno);
            }else if(ss == 3) {
                Remove(this);
                stiptxt.prepend(stipstxtpan);
                stipyes.append(stiptxt);
                $(this).parent().append(stipyes);
            }else {
                Remove(this);
            }

        }

    };
})(jQuery);
