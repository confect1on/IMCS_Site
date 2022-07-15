<?php
function theme_anteroom_view_comments($node) {
	//постраничник
	$query = 'SELECT COUNT(*) FROM {comments} WHERE nid=%d AND pid=0';
	if (!anteroom_answer_access($node)) $query .= ' AND status = '.COMMENT_PUBLISHED;
	$comments_per_page = variable_get('anteroom_comment_per_page', 10);
	$result = pager_query($query, $comments_per_page, 0, $query, array($node->nid));
	$pager .= theme('pager', NULL, $comments_per_page, 0);

	global $pager_page_array;
	$page = $pager_page_array[0];
	$page_start = $page*$comments_per_page;

	$query = 'SELECT 
	question.cid, question.pid, question.nid, question.subject, question.comment, question.format, question.timestamp, question.name AS registered_name, question.mail, question.homepage, question_author.uid, question_author.name, question_author.signature, question_author.signature_format, question_author.picture, question_author.data, question.thread, question.status,
	answer.cid AS children_cid, answer.pid AS children_pid, answer.nid AS children_nid, answer.subject AS children_subject, answer.comment AS children_comment, answer.format AS children_format, answer.timestamp AS children_timestamp, answer.name AS children_name, answer.mail AS children_mail, answer.homepage AS children_homepage, answer_author.uid AS children_uid, answer_author.name AS children_registered_name, answer_author.signature AS children_signature, answer_author.signature_format AS children_signature_format, answer_author.picture AS children_picture, answer_author.data AS children_data, answer.thread AS children_thread, answer.status AS children_status
	FROM {comments} question
	INNER JOIN {users} question_author ON question.uid = question_author.uid 
	LEFT JOIN {comments} answer ON question.cid=answer.pid
	LEFT JOIN {users} answer_author ON answer.uid = answer_author.uid 
	WHERE question.pid = 0 AND question.nid = %d';

	if (!anteroom_answer_access($node)) $query .= ' AND question.status = '.COMMENT_PUBLISHED.' AND answer.status = '.COMMENT_PUBLISHED;

	$query .= " ORDER BY question.thread DESC LIMIT $page_start, $comments_per_page";
	$all_comments = db_query($query, $node->nid);
	while ($comment = db_fetch_array($all_comments)) {
		if ($comment['name'] == '') $comment['name'] = $comment['registered_name'];
		//делим строку на два коммента (вопрос/ответ)
		$thread = array_chunk($comment, 18, true);

		//правим ключи у ответа
		foreach ($thread[1] as $key => $value) {
			$thread[1][substr($key, 9)] = $value;
		}

		//вывод веток
		foreach ($thread as $comment)
			$output .= anteroom_comment_view($comment, $node);
	}

	//постраничный просмотр
	return $output.$pager;
}

function anteroom_comment_view($comment, $node) {
	if (!$comment['cid']) return;
	$comment=(object)$comment;
	if (anteroom_answer_access($node))
	$links = array();
	if (anteroom_answer_access($node))
		$links['comment_delete'] = array(
			'title' => t('delete'),
			'href' => "anteroom/comment/delete/$comment->cid"
		);

	if (anteroom_answer_access($node))
		$links['anteroom_edit'] = array(
			'title' => t('edit'),
			'href' => "anteroom/comment/edit/$comment->cid"
		);

	if ($comment->status == COMMENT_NOT_PUBLISHED) 
		$links['anteroom_answer'] = array(
			'title' => t('reply'),
			'href' => "anteroom/comment/add/$node->nid/$comment->cid"
		);

	if (count(explode('.', $comment->thread)) == 2) { 
		$output .= '<div class="indented answer">'.theme('comment_view', $comment, $node, $links).'</div>';
	} else {
		$output .= theme('comment_view', $comment, $node, $links);
	}
	return $output;
}

//функция, возвращающяя форму ответа для AJAX
function theme_anteroom_comment_add($node) {
	//при отправке сначала через js проверить, не пустые ли формы
	global $user;
	$comment = array(
		'nid' => arg(3),
		'pid' => arg(4),
		'uid' => uid,
		);

	$form = drupal_get_form('anteroom_comment_form', $comment);
	if ($_REQUEST['ajax']) {
		echo drupal_to_js(array('comment_form' => $form));
		exit;
	}
	else return $form;
}

//функция, возвращающяя форму редактирования комментария для AJAX
function theme_anteroom_comment_edit($cid) {
	$comment = (array)_comment_load($cid);
	$form = drupal_get_form('anteroom_comment_form', $comment);
	if ($_REQUEST['ajax']) {
		echo drupal_to_js(array('comment_form' => $form));
		exit;
	}
	else return $form;
}

//функция темизации формы подтверждения удаления ветки комментариев
function theme_anteroom_comment_confirm_delete($cid) {
	$comment = (array)_comment_load($cid);
	$form = drupal_get_form('anteroom_comment_delete_form', $comment);
	if ($_REQUEST['ajax']) {
		echo drupal_to_js(array('delete_form' => $form));
		exit;
	}
	else return $form;
}
