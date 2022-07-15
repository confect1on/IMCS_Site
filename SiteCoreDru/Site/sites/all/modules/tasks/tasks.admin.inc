<?php

// форма для админки
function tasks_admin_form() {
	$form['tasks'] = array(
		'#type' => 'markup',
		'#value' => _tasks_admin_list(),
	);
	// удаление результатов по задаче
	$form['task'] = array(
		'#type' => 'select',
		'#title' => 'Задача',
		'#options' => _tasks_admin_options(),
	);
	$form['task-clear'] = array(
		'#type' => 'submit',
		'#value' => 'Аннулировать решения выбранной задачи',
	);
	// удаление результатов по пользователю
	$form['user'] = array(
		'#type' => 'select',
		'#title' => 'Пользователь',
		'#options' => _tasks_admin_users(),
	);
	$form['user-clear'] = array(
		'#type' => 'submit',
		'#value' => 'Аннулировать решения выбранного пользователя',
	);
	return $form;
}

// список попыток в виде таблицы
function _tasks_admin_list() {
	// выборка попыток
	$query = "SELECT n.title, field_task_number_value number, a.uid, a.task_nid, a.attempt, a.created, a.answer, s.solved, s.attempts, s.score FROM tasks_attempts a JOIN tasks_solutions s ON a.uid=s.uid AND a.task_nid=s.task_nid JOIN node n ON a.task_nid=n.nid JOIN content_type_task t ON n.vid=t.vid ORDER BY a.created DESC";
	$tasks = pager_query($query, 50);
	while ($task = db_fetch_object($tasks)) {
		$user = theme('username', user_load($task->uid));
		if ($task->number) $task->title = "№ $task->number. $task->title";
		$title = l($task->title, "node/$task->task_nid");
		$date = format_date($task->created, 'short');
		$answer = htmlspecialchars($task->answer);
		$attempt = "<a title='ответ: $answer'>№ $task->attempt</a>";
		if ($task->solved && $task->attempt == $task->attempts) $attempt .= " +$task->score баллов";
		$rows[] = array($user, $title, $attempt, $date);
	}

	// рендеринг таблицы с попытками
	if (!$rows) {
		//$output .= "<p>Нет ни одного решения.</p>";
	}
	else {
		$header = array('Участник', 'Задача', 'Попытка', 'Дата');
		$output .= theme('table', $header, $rows);
		$output .= theme('pager', NULL, $pagesize);
	}
	return $output;
}

// список опций для выбора задачи
function _tasks_admin_options() {
	$tasks = db_query("SELECT n.nid, n.title, SUM(s.attempts) attempts FROM node n LEFT JOIN tasks_solutions s ON n.nid=s.task_nid WHERE n.type='task' GROUP BY n.nid ORDER BY n.created DESC");
	$options[0] = '- нет -';
	while ($task = db_fetch_object($tasks)) {
		if ($task->attempts) $options[$task->nid] = drupal_substr($task->title, 0, 60) . " (попыток: $task->attempts)";
	}
	return $options;
}

// список опций для выбора пользователя
function _tasks_admin_users() {
	$users = db_query("SELECT uid FROM tasks_users");
	$options[0] = '- нет -';
	while ($user = db_fetch_object($users)) {
		$options[$user->uid] = strip_tags(theme('username', user_load($user->uid)));
	}
	return $options;
}

// обработка операций очистки
function tasks_admin_form_submit($form, &$form_state) {
	if ($form_state['values']['op'] == 'Аннулировать решения выбранной задачи' && $form_state['values']['task']) {
		tasks_remove(node_load($form_state['values']['task']));
		drupal_set_message('Все решения задачи удалены.');
	}
	if ($form_state['values']['op'] == 'Аннулировать решения выбранного пользователя' && $form_state['values']['user']) {
		tasks_remove_user($form_state['values']['user']);
		drupal_set_message('Все решения пользователя удалены.');
	}
}
