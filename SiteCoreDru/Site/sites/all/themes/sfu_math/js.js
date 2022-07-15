$(document).ready(function() {
    $("#plusminus").click(function(){
        if ($("#pmhide").css("display")=="none")
        {
            $("#pmhide").css("display", "block");
            $(this).attr("class","minusplus");
        }
        else
        {
            $("#pmhide").css("display", "none");
            $(this).attr("class","plusminus");
        }
        return false;
    });

	// поддержка разворачиваемых блоков
	$('.collapsed-block .trigger').click(function() {
		var content = $(this).parents('.collapsed-block:first').find('.collapsed-content');
		if (content.css('display') == 'none') {
			content.show();
		}
		else {
			content.hide();
		}
		return false;
	});

	// открытие ссылок в публикациях в новом окне
	$('.publications-list a').click(function() {
		window.open($(this).attr('href'), '_blank');
		return false;
	});

});
