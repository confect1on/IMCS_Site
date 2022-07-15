<?php
function hide_email($s)
{
	$s2="";
	for($i=0;$i<strlen($s);$i++)$s2.="&#".ord($s[$i]).";'+'";
	return "<script type=\"text/javascript\">
		<!--
			a='".$s2."';
			document.write('<a href=\"'+'&#109'+';&#'+'97;'+'&#'+'10'+'5;'+'&#'+'108;&'+'#116;'+'&#11'+'1;&'+'#58;'+a+'\">'+a+'</a>');
		//-->
		</script>";
}

	// научная степень и звание
	$titles = array();
	if ($node->field_person_us[0]['view']) {
		$titles[] = $node->field_person_us[0]['view'];
	}
	if ($node->field_person_uz[0]['view']) {
		$titles[] = $node->field_person_uz[0]['view'];
	}

	//стаж
	if ($node->field_person_stazhe_all[0]['value'] != '') $stazhe = "Общий: " . ($node->field_person_stazhe_all[0]['view']+date("Y")-2017) . "<br> По специальности: " . ($node->field_person_stazhe_spec[0]['view']+date("Y")-2017);

	// место работы и должность
	/*
	$job = array();
	foreach ($node->field_person_post as $post) {
		if ($post['value'] != '') $posts[] = $post['view'];
	}
	foreach ($node->field_person_department as $department) {
		if ($department['nid']){
			$departments[] = $department['view'];
		}
	}
	$job = array();
	if ($departments) $job[] = join(", ", $departments);
	if ($posts) $job[] = join(", ", $posts);
	*/
	$job="";
	for($i=0;$i<count($node->field_person_department);$i++)
	{
		if ($node->field_person_department[$i]['nid'])
		{
			$job.=$node->field_person_department[$i]['view'];
			if ($node->field_person_post[$i]['value'])$job.=", ".$node->field_person_post[$i]['view'];
			$job.="<br />";
		}
	}
	// контакты
	$contacts = array();
	if ($node->field_person_phone[0]['value'] != '') $contacts[] = "<strong>тел.:</strong> " . $node->field_person_phone[0]['view'];
	if ($node->field_person_fax[0]['value'] != '') $contacts[] = "<strong>факс:</strong> " . $node->field_person_fax[0]['view'];
	if ($node->field_person_email[0]['value'] != '') $contacts[] = "<strong>e-mail:</strong> " . hide_email($node->field_person_email[0]['view']);
	if ($node->field_person_site[0]['value'] != '') $contacts[] = "<strong>веб-сайт:</strong> <a href='" . $node->field_person_site[0]['view'] . "'>" . truncate_utf8($node->field_person_site[0]['view'], 30, false, true) . "</a>";
	if ($node->field_person_address[0]['value'] != '') $contacts[] = "<strong>адрес:</strong> " . str_replace(". ", ".&nbsp;", $node->field_person_address[0]['view']);
	if ($node->field_person_birthday[0]['value'] != '') $contacts[] = "<strong>год рождения:</strong> " . $node->field_person_birthday[0]['view'];
	// образование



	$education = "";
	if ($node->field_person_education['data'])
	foreach ($node->field_person_education['data'] as $edu) {
		$name = $edu[0];
		if ($name == '') continue;
		if ($edu[1] != '' || $edu[2] != '') {
			$name .= " — ";
			if ($edu[1] != '') $name .= $edu[1];
			if ($edu[2] != '') {
				$year = is_numeric($edu[2]) ? ($edu[2] . " г.") : $edu[2];
				$name .= $edu[1] == '' ? $year : ", $year";
			}
			if ($edu[3] != '') {
				if ($edu[1] == '' && $edu[2] == '') $name .= " — ";
				$name .= ", тема дипломной работы: «" . $edu[3] . '»';
			}
		}
		$education = "<li>".$name."</li>".$education;
	}
	// диссертации
	$dissertations = array();
	if ($node->field_person_dissertation['data'][0][0]!='') {
		$row = array();
		if ($node->field_person_dissertation['data'][0][0] != '') $row[] = $node->field_person_dissertation['data'][0][0];
		if ($node->field_person_dissertation['data'][0][1] != '') $row[] = is_numeric($node->field_person_dissertation['data'][0][1]) ? ($node->field_person_dissertation['data'][0][1] . " г."): $node->field_person_dissertation['data'][0][1];
		$dissertations['Кандидатская диссертация'] = $row;
	}
	if ($node->field_person_dissertation['data'][1][0]!='') {
		$row = array();
		if ($node->field_person_dissertation['data'][1][0] != '') $row[] = $node->field_person_dissertation['data'][1][0];
		if ($node->field_person_dissertation['data'][1][1] != '') $row[] = is_numeric($node->field_person_dissertation['data'][1][1]) ? ($node->field_person_dissertation['data'][1][1] . " г."): $node->field_person_dissertation['data'][1][1];
		$dissertations['Докторская диссертация'] = $row;
	}
	// проекты, гранты
	$projects = array();
	if ($node->field_person_projects) {
		if($node->field_person_projects[data])
		foreach ($node->field_person_projects[data] as $project) {
			$row = array();
			$name = $project[1];
			if ($name == '') continue;
			if ($project[0] != '') $name .= " — " . $project[0];
			if ($project[3] != '') $name .= ($project[0] == '' ? ' — ' : ', ') . $project[3] . (is_numeric($project[3]) ? " г." : "");
			if ($project[2] != '') $name .= " (грант № ". $project[2] . ")";
			$projects[] = $name;
		}
	}
?>

<div id="node-<?=$node->nid?>" class="node<?
	if ($sticky) print " sticky";
	if (!$status) print " node-unpublished";
	if ($teaser) print " teaser";
	echo " $node->type";
?>">
	<?php
/*
	print"<pre>";
print_r($node);
print"</pre>";
*/
	?>

	<?/* {?><h2><?=$title?></h2><?} */?>

	<?if ($node->field_person_photo[0]['view']) print "<div class='right'>" . $node->field_person_photo[0]['view']  . "</div>";?>

	<?if ($titles) {?><div class='form-item'><?=join(", ", $titles)?></div><?}?>

	<?if ($contacts) {?><div class='form-item'><?=join("<br />", $contacts)?></div><?}?>

	<?if ($job!="") {?>
		<h3>Место работы и должность</h3>
		<ul><li><?= $job ?></li></ul>
	<?}?>

	<?if (false && $node->field_person_photo[0]['view']) {?><div class='clear'></div><?}?>

	<?if ($education!="") {?>
		<h3>Образование</h3>
		<ul><?= $education ?></ul>
	<?}?>

	<?if ($stazhe!="") {?>
		<h3>Стаж работы (полных лет)</h3>
		<ul><?= $stazhe ?></ul>
	<?}?>


	<?if ($node->field_person_sciences[0]['value']) {?>
		<h3>Научные направления, профессиональные интересы</h3>
		<ul><?foreach ($node->field_person_sciences as $science) print "<li>" . $science['view'] . "</li>";?></ul>
	<?}?>

	<?if ($dissertations) {?>
		<h3>Диссертации</h3>
		<dl>
			<?foreach ($dissertations as $title => $info) print "<dt>$title</dt><dd>" . join(" — ", $info) . "</dd>";?>
		</dl>
	<?}?>

	<?if ($node->field_person_disciplines[0]['value']) {?>
		<h3>Преподаваемые дисциплины</h3>
		<ul><?foreach ($node->field_person_disciplines as $discipline) print "<li>" . $discipline['view'] . "</li>";?></ul>
	<?}?>

	<?if ($node->field_person_rewards[0]['value']) {?>
		<h3>Награды</h3>
		<ul><?foreach ($node->field_person_rewards as $reward) print "<li>" . $reward['view'] . "</li>";?></ul>
	<?}?>

	<?if ($node->field_person_membership[0]['value']) {?>
		<h3>Членство в научных, профессиональных, общественных организациях</h3>
		<ul><?foreach ($node->field_person_membership as $member) print "<li>" . $member['view'] . "</li>";?></ul>
	<?}?>

	<?
	$external_publications = _sfu_structure_show_load_publications(@$node->field_person_sfu_account[0]['value'] != '' ? @$node->field_person_sfu_account[0]['value'] . '@sfu-kras.ru' : @$node->field_person_email[0]['value'], 5);
	if ($node->field_person_publications_count[0]['value'] >0 || $external_publications != '' || $node->field_person_publications[0]['value'] != '' || $node->field_person_googlescholar[0]['value'] != '') {?>
		<h3>Публикации</h3>
			<?
			if ($external_publications != '') {
				print "<p>Последние публикации:</p>$external_publications
					<p><small>Список публикаций сформирован в автоматическом режиме. <a href='http://scholar.sfu-kras.ru/feedback?subject=%D0%9F%D1%83%D0%B1%D0%BB%D0%B8%D0%BA%D0%B0%D1%86%D0%B8%D0%B8+%D0%B2+%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D0%B5+%D1%81%D0%BE%D1%82%D1%80%D1%83%D0%B4%D0%BD%D0%B8%D0%BA%D0%B0+%D0%BD%D0%B0+%D1%81%D0%B0%D0%B9%D1%82%D0%B5+%D0%A1%D0%A4%D0%A3' target='_blank'>Сообщите</a>, если заметили неточности.</small></p>";
			}
			?>

			<?if ($node->field_person_publications[0]['value'] != '' /*&& $external_publications == ''*/) {?>
				<p>Наиболее значимые публикации:</p>
				<ul><?foreach ($node->field_person_publications as $publication) {?><li><?=$publication['value']?></li><?}?></ul>
			<?}?>

			<?
			if ($node->field_person_publications_count[0]['value'] != '') {
				if (is_numeric($node->field_person_publications_count[0]['value'])) {
					print "<p>Всего публикаций: " . $node->field_person_publications_count[0]['value'] . ".</p>";
				}	else {
					print $node->field_person_publications_count[0]['value'];
				}
				if ($node->field_person_googlescholar[0]['value'] != '') {
					//print "<br>";
				}
			}
			?>
			<?
			if (user_access('access administration pages')) {
				if ($node->field_person_wos[0]['value'] != '') {
						print "<p><a href='".$node->field_person_wos[0]['value']."'>Ссылка на профиль в Web of Science</a></p>";
					}	
			}
			if ($node->field_person_scopus[0]['value'] != '') {
					print "<p><a href='".$node->field_person_scopus[0]['value']."'>Ссылка на профиль в Scopus</a></p>";
				}	
			if ($node->field_person_googlescholar[0]['value'] != '') {
					print "<p><a href='".$node->field_person_googlescholar[0]['value']."'>Ссылка на профиль в Академии Google</a></p>";
				}
			if ($node->field_person_rci[0]['value'] != '') {
					print "<p><a href='".$node->field_person_rci[0]['value']."'>Ссылка на профиль в РИНЦ</a></p>";
				}		

			?>

	<?}?>

	<?if ($node->field_person_grants_count[0]['value'] >0 || $projects) {?>
		<h3>Участие в грантах, проектах</h3>
			<?if ($projects) {?>
				<ul><li><?=join("</li><li>", $projects)?></li></ul>
			<?}?>
			<?if ($node->field_person_grants_count[0]['value'] != '') {?>
				<?if (is_numeric($node->field_person_grants_count[0]['value'])) {?>Всего грантов: <?}?>
				<?=$node->field_person_grants_count[0]['value']?>.
			<?}?>
	<?}?>

	<?if ($node->field_person_raising_skill[data][0][0]) {?>
		<h3>Повышение квалификации, стажировки</h3>
		<ul>
			<? foreach ($node->field_person_raising_skill[data] as $skill) if ($skill[0]){?><li><?=$skill[0] . ($skill[1] == '' ? '' : ', ' . $skill[1]) . ($skill[2] == '' ? '' : ', ' . $skill[2]) . ($skill[3] == '' ? '' : ' — ' . (is_numeric($skill[3]) ? $skill[3] . " г." : $skill[3]))?></li><?}?>
		</ul>
	<?}?>

	<?if ($node->field_person_activity[0]['value']) {?>
		<h3>Научно-педагогическая и общественная деятельность</h3>
		<ul><?foreach ($node->field_person_activity as $activity) print "<li>" . $activity['view'] . "</li>";?></ul>
	<?}?>

	<?if ($node->field_person_more_text1[0]['value'] != '') {?>
		<?if ($node->field_person_more_h1[0]['value'] != '') {?><h3><?=$node->field_person_more_h1[0]['view']?></h3><?}?>
		<?=$node->field_person_more_text1[0]['view']?>
	<?}?>

	<?if ($node->field_person_more_text2[0]['value'] != '') {?>
		<?if ($node->field_person_more_h2[0]['value'] != '') {?><h3><?=$node->field_person_more_h2[0]['view']?></h3><?}?>
		<?=$node->field_person_more_text2[0]['view']?>
	<?}?>

	<?=$node->field_person_description[0]['view']?>

	<?=$node->content['files']['#value']?>

</div>
