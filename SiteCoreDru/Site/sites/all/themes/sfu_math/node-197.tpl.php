<?php
// $Id: node.tpl.php,v 1.5 2007/10/11 09:51:29 goba Exp $
//    if ($page!=0)drupal_add_js((path_to_theme()).'/vkl2.js'); 
	$by_abc = $_GET['by'] == 'abc';
?>
<div id="node-197">


<?php if ($page == 0): ?>
<h2><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
<?php endif; ?>

<?php if ($page != 0): ?>
<?php /* if ($title): print '<h2'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h2>'; endif; */?>
<?php endif; ?>


<?php
        $DEP=array();
        $FIO=array();

        $db_sotrudniki = db_query("SELECT d.nid did, d.title dep, de.field_department_ves_value dves,
                                        p.nid, p.status, p.title fio, v.field_person_ves_value ves,
                                        pr.field_person_us_value us, pr.field_person_uz_value uz
                                FROM node p
                                JOIN content_type_person pr ON (pr.vid=p.vid)
                                JOIN content_field_person_department dp ON (dp.vid=pr.vid)
                                JOIN content_field_person_ves v ON (v.vid=p.vid and v.delta=dp.delta)
                                JOIN node d ON (d.nid=dp.field_person_department_nid)
                                JOIN content_type_department de ON (de.vid=d.vid)
								WHERE (p.status = 1)
                                ORDER BY fio
                                "); //   :(


        while ($sotrudnik = db_fetch_object($db_sotrudniki))
        {
          $DEP[$sotrudnik->did]['nid']=$sotrudnik->did;
          $DEP[$sotrudnik->did]['name']=$sotrudnik->dep;
          $DEP[$sotrudnik->did]['v']=$sotrudnik->dves;
          $DEP[$sotrudnik->did]['p'][]=array("nid"=>$sotrudnik->nid, "name"=>$sotrudnik->fio,"v"=>$sotrudnik->ves, "us"=>$sotrudnik->us, "uz"=>$sotrudnik->uz);
          $FIO[$sotrudnik->nid]=array("nid"=>$sotrudnik->nid, "name"=>$sotrudnik->fio, "d"=>$sotrudnik->did, "us"=>$sotrudnik->us, "uz"=>$sotrudnik->uz);
          //print_r($sotrudnik);
        }
        function my_cmp($a,$b)
        {
            if ($a['p'] && $a['v']!=$b['v']) return $a['v']<$b['v'];
            return $a['name']>$b['name'];
        }
        usort($DEP,my_cmp);
        
        
		if ($by_abc) {
			// по алфавиту
			$Fs="<ul class=\"person\">";
			$Q="";
			$quick_links = '';
			$quick_links_number = 0;
			foreach ($FIO as $F)
			{
				$q=drupal_substr($F['name'],0,1);
				if($q!=$Q)
				{
					$Q=$q;
					$quick_links_number++;
					$Fs.="<li class=\"bukva\" id='go$quick_links_number'>$Q<br /></li>";
					$quick_links .= "<a href='#go$quick_links_number'>$Q</a>";
				}
				$Fs.="<li><a href=\"/node/".$F['nid']."\">".$F['name']."</a>";
				if ($F['us']!="")$Fs.=", ".$F['us'];
				if ($F['uz']!="")$Fs.=", ".$F['uz'];
				$Fs.="</li>";
			}
			$Fs.="</ul>";
			$Fs = "<div class='quick-links'>Первая буква фамилии: &nbsp; $quick_links</div>$Fs";
		}
		
		else {
			// по подразделениям
			$Depname=array();
			$Ds="";
			$c=1;
			$quick_links = '';
			$quick_links_number = 0;
			foreach ($DEP as $D)
			{
				$c++;
				$quick_links_number++;
				$Ds.="<h3 id='go$quick_links_number'>".$D['name']."</h3><div id=\"dep$c\" class=\"nohide\"><ul class=\"person\">";
				$quick_links .= "<li><a href='#go$quick_links_number'>$D[name]</a></li>";
				usort($D['p'],my_cmp);
				foreach ($D['p'] as $P)
				{
					$Ds.="<li><a href=\"/node/".$P['nid']."\">".$P['name']."</a>";
					if ($P['us']!="")$Ds.=", ".$P['us'];
					if ($P['uz']!="")$Ds.=", ".$P['uz'];
					$Ds.="</li>";
				}
				$Ds.="</ul></div>";
			}
			$Ds = "<div class='quick-links'>Выберите подразделение для перехода к списку сотрудников:<ul>$quick_links</ul></div><br />$Ds";
		}

      ?>


    <div class="clear-block">
		<?=$node->body?>
        <div id="vk">
            <ul id="vkul">
                <li class="first<?if (!$by_abc) print ' vk-active'?>"><a href="?by=departments">по подразделениям</a> </li>
                <li class="last<?if ($by_abc) print ' vk-active'?>"> <a href="?by=abc">по алфавиту</a></li>
            </ul>
        </div><div class="clear"></div>
        <div id="vkhide">
			<?if ($by_abc) {?>
				<div id="vkl2">
					<?php print $Fs ?>
				</div>
			<?} else {?>
				<div id="vkl1">
					<?php print $Ds ?>
				</div>
			<?}?>
        </div>
    </div>
</div>
