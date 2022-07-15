<?php

function _tasks_rating_page() {
	$pagesize = variable_get('tasks_rating_per_page', 50);
	$users = pager_query("SELECT uid, score, solved FROM tasks_users ORDER BY score DESC, changed", $pagesize);
	$rows = array();
	$n = $pagesize * $_GET['page'];
	while ($db_user = db_fetch_object($users)) {
		$user = user_load($db_user->uid);
		$rows[] = array('data' => array(++$n, theme('username', $user), $db_user->score, $db_user->solved));
		if ($user->uid == $GLOBALS['user']->uid) $rows[count($rows) - 1]['class'] = 'tasks-self';
	}
	if (!$rows) {
		$output .= "<p>Рейтинг пока пуст.</p>";
	}
	else {
		$output .= theme('table', array('№', 'Участник', 'Баллы', 'Решено задач'), $rows);
		$output .= theme('pager', NULL, $pagesize);
	}
	return $output;
}
