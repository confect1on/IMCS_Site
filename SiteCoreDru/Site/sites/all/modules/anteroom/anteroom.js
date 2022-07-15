Drupal.behaviors.anteroom = function() {

	//форма ответа
	$('ul.links>li.anteroom_answer>a').click( function() {
		anteroom_comment_cancel();
		var sender = $(this).parents('div.links');
		sender.css('display', 'none');
		$.get($(this).attr("href"), {ajax: '1'}, function(data) {
			var result = Drupal.parseJson(data);
			var wrapper = sender.parents('div.comment').find('div.ajax_wrapper');
			wrapper.html(result['comment_form']);
			anteroom_comment_form_restyle(wrapper);
		});
		return false;
	});

	//форма редактирования комментария
	$('ul.links>li.anteroom_edit>a').click( function() {
		anteroom_comment_cancel();
		var sender = $(this).parents('.comment');
		sender.find('div.submitted, div.links, div.content').css('display', 'none');
		$.get($(this).attr("href"), {ajax: '1'}, function(data) {
			var result = Drupal.parseJson(data);
			var wrapper = sender.find('div.ajax_wrapper');
			wrapper.html(result['comment_form']);
			wrapper.find("form>div>fieldset").css({'border': 'none', 'margin': '0', 'padding': '0'});
			wrapper.find("form>div>fieldset>legend").css('display', 'none');

			anteroom_comment_form_restyle(wrapper);
		});
		return false;
	});
	//форма удаления
	$('ul.links>li.comment_delete>a').click( function() {
		anteroom_comment_cancel();
		var sender = $(this).parents('.comment');
		sender.find('div.links').css('display', 'none');
		$.get($(this).attr("href"), {ajax: '1'}, function(data) {
			var result = Drupal.parseJson(data);
			var wrapper = sender.find('div.ajax_wrapper');
			wrapper.html(result['delete_form']);
		});
		return false;
	});
}

//удаляем уже загруженный ajax
function anteroom_comment_cancel() {
	$('div.ajax_wrapper>form').remove();
	$('.comment div.submitted, .comment div.links, .comment div.content').css('display', 'block');	//показываем скрытые до этого ссылки
}

//добавка стилей к форме, загруженной через ajax
function anteroom_comment_form_restyle(parent) {
	parent.find('div.textarea-identifier.description').css('display', 'none');
	parent.find('textarea#edit-comment').css('width', '95%');

	parent.find('form>div>fieldset fieldset').addClass('collapsible collapsed');	//навешиваем всем субфилдсетам класс collapsed
	Drupal.behaviors.collapse(parent);		//подгружаем обработчик для субфилдсетов
}