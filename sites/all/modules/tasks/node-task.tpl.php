<?php
$solution = _tasks_get_solution($node);

// оформление вывода "источника"
$source = $node->field_task_source[0]['view'];
$source_link = trim($node->field_task_source_link[0]['view']);
if ($source == '') $source = $source_link;
if ($source != '' && $source_link != '') {
	if (substr($source_link, 0, 7) != 'http://' && substr($source_link, 0, 8) != 'https://') $source_link = "http://$source_link/";
	$source = "<a href=\"$source_link\">$source</a>";
}
if ($source != '' && !$node->field_task_source_visible[0]['value'] && !$solution->solved) {
	$source = '<span title="показывается после решения задачи">скрыт</span>';
}

?>
<div id="node-<?=$node->nid?>" class="task node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?> clear-block">

	<?php if (!$page): ?>
		<h3><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h3>
	<?php endif; ?>

	<div class="content">

		<div class="task-task">
			<?=$node->field_task[0]['view']?>
			<?if (!$teaser) {?>
				<div class="task-author">
					<?if ($node->field_task_author[0]['value'] != '') {?>
						<div><span class="task-label">Автор задачи:</span> <span class="task-value"><?=$node->field_task_author[0]['view']?></span></div>
					<?}?>
					<?if ($source != '') {?>
						<div><span class="task-label">Источник:</span> <span class="task-value"><?=$source?></span></div>
					<?}?>
					<div><span class="task-label">Добавил задачу:</span> <span class="task-value"><?=$name?></span></div>
				</div>
			<?}?>
		</div>

		<div class='task-meta'>
		<table cellpadding='0' cellspacing='0'>
		<tr valign='top'><td>
			<?if ($node->field_task_topic[0]['value']) {?>
				<span class='task-label'>Тема:</span>&nbsp;<a class='task-value' href="/tasks/<?=arg(0) == 'tasks' && arg(1) == 0 && arg(1) != '' ? (arg(1) . '/') : ''?><?=$node->field_task_topic[0]['value']?>"><?=$node->field_task_topic[0]['view']?></a>
			<?}?>
			<span class='task-label'>Баллы:</span>&nbsp;<span class='task-value'><?=$node->field_task_score[0]['view']?></span>
			<span class='task-label'>Решили:</span>&nbsp;<span class='task-value'><?=_tasks_solutions_number($node)?></span>
			<?if ($solution->attempts > 0) {?><span class='task-label'>Ваших попыток:</span>&nbsp;<span class='task-value'><?=$solution->attempts?></span><?}?>
		</td><td>
			<?if ($solution->solved) {?>
				<span class="task-solved">Решена&nbsp;на&nbsp;<?=$solution->score?></span>
			<?} elseif ($teaser && tasks_can_attempt($node)) {?>
				<a class="task-solve" href="/node/<?=$node->nid?>">Отправить&nbsp;решение</a>
			<?}?>
		</td></tr>
		</table>
		</div>

	</div>

	<?=$links?>

	<?if (!$teaser && tasks_can_attempt($node)) {?>
		<?=drupal_get_form('tasks_attempt_form', $node)?>
	<?}?>

</div>
