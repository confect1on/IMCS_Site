<?php

////////////   обработчики страниц со списком задач /tasks/*   ////////////

define (TASKS_UNSOLVED, 1);
define (TASKS_SOLVED, 2);
define (TASKS_MY, 3);
define (TASKS_UNPUBLISHED, 4);

function _tasks_unsolved_page() {
	return _tasks_list(TASKS_UNSOLVED, arg(1));
}

function _tasks_solved_page() {
	return _tasks_list(TASKS_SOLVED, arg(2));
}

function _tasks_my_page() {
	return _tasks_list(TASKS_MY, arg(2));
}

function _tasks_unpublished_page() {
	return _tasks_list(TASKS_UNPUBLISHED, arg(2));
}

function _tasks_list($mode, $topic = NULL, $return_query = FALSE) {
	global $user;
	
	// ПОСТРОЕНИЕ ЗАПРОСА
	$where = array(); $join = array();
	$where[] = "n.type='task'";
	// условие для выборки задач нужного статуса
	if ($mode == TASKS_MY) {
		$where[] = "n.uid=$user->uid";
	}
	elseif ($mode == TASKS_UNSOLVED) {
		$join[] = "LEFT JOIN tasks_solutions s ON n.nid=s.task_nid AND s.uid=$user->uid";
		$where[] = "(s.solved IS NULL OR s.solved=0)";
	}
	elseif ($mode == TASKS_SOLVED) {
		$join[] = "JOIN tasks_solutions s ON n.nid=s.task_nid AND s.uid=$user->uid AND s.solved=1";
	}
	if ($mode == TASKS_UNPUBLISHED) {
		$where[] = "n.status=0";
	}
	elseif ($mode != TASKS_MY) {
		$where[] = "n.status=1";
	}
	// условие на тему
	$topic += 0;
	if ($topic) {
		$where[] = "t.field_task_topic_value=$topic";
		$allowed_values = content_allowed_values(content_fields('field_task_topic'));
		$topic_title = $allowed_values[$topic];
		if ($topic_title != '') drupal_set_title(drupal_get_title() . " по теме «" . drupal_ucfirst($topic_title) . "»");
	}
	// итоговый запрос
	$where = join(" AND ", $where);
	$join = join(' ', $join);
	$query = "SELECT n.nid FROM node n JOIN content_type_task t ON n.vid=t.vid $join WHERE $where ORDER BY t.field_task_number_value DESC, n.created DESC";

	// ВЫВОД
	$empty_message[TASKS_UNSOLVED] = 'Вы решили все задачи';
	$empty_message[TASKS_SOLVED] = 'Вы еще не решили ни одной задачи';
	$empty_message[TASKS_MY] = 'Вы не добавили ни одной задачи';
	$empty_message = $topic ? 'Нет задач по данном теме' : $empty_message[$mode];
	return $return_query ? $query : _tasks_list_display($query, $empty_message);
}

function _tasks_list_display($query, $empty_message = 'Нет задач', $pagesize = FALSE) {
	if (!$pagesize) $pagesize = variable_get('tasks_per_page', 10);
	$tasks = pager_query($query, $pagesize);
	$output = '';
	while ($task = db_fetch_object($tasks)) {
		$output .= node_view(node_load($task->nid), 1);
	}
	if ($output == '') {
		$output .= "<p>${empty_message}.</p>";
	}
	else {
		$output .= theme('pager', NULL, $pagesize);
	}
	return $output;

}
