<?php

/////////////////////// ПОЛУЧЕНИЕ ИНФОРМАЦИИ О РЕШЕНИЯХ ///////////////////////

// решена ли задача пользователем
function _tasks_is_solved($task, $uid = NULL) {
	return _tasks_get_solution($task, $uid)->solved;
}

// информации о решении задачи пользователем: поле solved (решена?), поле attempts (кол-во попыток), поле score (начисленные баллы)
function _tasks_get_solution($task, $uid = NULL) {
	static $cache;
	if (empty($uid)) $uid = $GLOBALS['user']->uid;
	if (isset($cache[$task->nid][$uid])) return $cache[$task->nid][$uid];
	$solution = db_fetch_object(db_query("SELECT solved, attempts, score FROM tasks_solutions WHERE uid=%d AND task_nid=%d", $uid, $task->nid));
	$solution->solved = $solution->solved != 0;
	$solution->attempts += 0;
	$solution->score += 0;
	$cache[$task->nid][$uid] = $solution;
	return $solution;
}

// кол-во решивших задачу
function _tasks_solutions_number($task) {
	return db_result(db_query("SELECT COUNT(*) FROM tasks_solutions WHERE task_nid=%d AND solved=1", $task->nid)) + 0;
}

// название задачи с номером, если он есть (без экранирования)
function _tasks_title($task) {
	$title = $task->title;
	$number = $task->field_task_number[0]['value'];
	if ($number != '') $title = "№ ${number}. $title";
	return $title;
}

/////////////////////// ПОПЫТКИ ///////////////////////

// разрешено ли пользователю отправить решение задачи
function tasks_can_attempt($task) {
	return user_access('tasks solve') && ($task->status || user_access('tasks admin')) && !_tasks_is_solved($task);
}

// была ли уже попытка с таким ответом
function tasks_has_attempt($task, $answer) {
	return db_result(db_query("SELECT COUNT(*) FROM tasks_attempts WHERE uid=%d AND task_nid=%d AND answer='%s'", $GLOBALS['user']->uid, $task->nid, $answer)) > 0;
}

// засчитывает попытку, возвращает TRUE, если ответ верный
function tasks_attempt($task, $answer) {
	$uid = $GLOBALS['user']->uid;
	$solution = _tasks_get_solution($task);
	$solved = ($answer === $task->field_task_solution[0]['value'] ? 1 : 0);
	// добавляем баллов участнику
	$score = 0;
	if ($solved) {
		$score = $task->field_task_score[0]['value'] - $solution->attempts;
		if ($score < 0) $score = 0;
		if (db_result(db_query("SELECT COUNT(*) FROM tasks_users WHERE uid=%d", $uid))) {
			db_query("UPDATE tasks_users SET score=score+%d, solved=solved+1, changed=%d WHERE uid=%d", $score, time(), $uid);
		}
		else {
			db_query("INSERT INTO tasks_users (uid, score, solved, changed) VALUES (%d, %d, 1, %d)", $uid, $score, time());
		}
	}
	// записываем информацию о кол-ве попыток и отметку о решении
	if ($solution->attempts > 0) {
		db_query("UPDATE tasks_solutions SET attempts=attempts+1, solved=%d, score=%d WHERE uid=%d AND task_nid=%d", $solved, $score, $uid, $task->nid);
	}
	else {
		db_query("INSERT INTO tasks_solutions (uid, task_nid, attempts, solved, score) VALUES (%d, %d, 1, %d, %d)", $uid, $task->nid, $solved, $score);
	}
	// сохраняем информацию о попытке
	db_query("INSERT INTO tasks_attempts (uid, task_nid, attempt, created, answer) VALUES (%d, %d, %d, %d, '%s')", $uid, $task->nid, $solution->attempts + 1, time(), $answer);
	return $solved;
}

/////////////////////// ОБНОВЛЕНИЯ ПРИ ИЗМЕНЕНИЯХ ЗАДАЧ ///////////////////////

// удаление всех результатов по задаче (с вычитанием баллов)
function tasks_remove($task) {
	// обнуляем баллы за задачу
	$solutions = db_query("SELECT uid, score FROM tasks_solutions WHERE task_nid=%d AND solved=1", $task->nid);
	while ($solution = db_fetch_object($solutions)) {
		db_query("UPDATE tasks_users SET score=score-%d, solved=solved-1 WHERE uid=%d", $solution->score, $solution->uid);
	}
	// удаляем все сведения о попытках и решениях задачи
	db_query("DELETE FROM tasks_attempts WHERE task_nid=%d", $task->nid);
	db_query("DELETE FROM tasks_solutions WHERE task_nid=%d", $task->nid);
}

// пересчёт баллов и обновление воспомогательных таблиц при изменении максимального балла за задачу
function tasks_update_score($task) {
	$new_score = $task->field_task_score[0]['value'];
	$solutions = db_query("SELECT uid, score, attempts FROM tasks_solutions WHERE task_nid=%d AND solved=1", $task->nid);
	while ($solution = db_fetch_object($solutions)) {
		$score = $new_score - $solution->attempts + 1;
		if ($score < 0) $score = 0;
		db_query("UPDATE tasks_users SET score=score+(%d) WHERE uid=%d", -$solution->score + $score, $solution->uid);
		db_query("UPDATE tasks_solutions SET score=%d WHERE uid=%d AND task_nid=%d", $score, $solution->uid, $task->nid);
	}
}

// удаление всех решений и аннулирование баллов пользователя (за провинность)
function tasks_remove_user($uid) {
	db_query("DELETE FROM tasks_attempts WHERE uid=%d", $uid);
	db_query("DELETE FROM tasks_solutions WHERE uid=%d", $uid);
	db_query("DELETE FROM tasks_users WHERE uid=%d", $uid);
}

/////////////////////// УВЕДОМЛЕНИЯ ///////////////////////

// уведомление админов о новой задаче
function _tasks_notify_admins($task) {
	$roles = user_roles(TRUE, 'tasks admin');
	foreach ($roles as $rid => $role) {
		$db_users = db_query("SELECT u.uid, u.mail FROM users_roles r JOIN users u ON r.uid=u.uid WHERE r.rid=%d", $rid);
		while ($user = db_fetch_object($db_users)) {
			if (!$users[$user->uid]) {
				$users[$user->uid] = TRUE;
				drupal_mail('tasks', 'new_task', $user->mail, 'ru', array('task' => $task));
			}
		}
	}
}

// формы писем
function tasks_mail($key, &$message, $params) {
	switch ($key) {
		case 'new_task':
			$sitename = variable_get('site_name', '');
			$task = $params['task'];
			$message['subject'] = "Новая задача на сайте \"${sitename}\"";
			$link = url("node/$task->nid", array('absolute' => TRUE));
			$user = strip_tags(theme('username', user_load($task->uid)));
			$message['body']  = "На сайте «${sitename}» в банк задач была добавлена новая задача. Если задача входит в область ваших профессиональных интересов, то просьба проверить её, отредактировать и опубликовать, если она по-вашему мнению достойна публикации.\n\n";
			$message['body'] .= "Добавил пользователь:\n${user}\n\nНазвание задачи:\n$task->title\n\nУсловие задачи:\n" . $task->field_task[0]['value'] . "\n\nРешение:\n" . $task->field_task_solution[0]['value'] . "\n\nСсылка:\n$link\n\n";
			$message['body'] .= "---\nАдминистрация сайта «${sitename}»";
			break;
	}
}
