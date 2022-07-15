document.write("<style type='text/css'>.task-author {display: none;}</style>");

$(function() {

	$(".task-author").before("<a class='task-author-link' href='#'>Об авторе</a>");

	$(".task-author-link").click(function() {
		$(this).next().show();
		$(this).remove();
	});

});
