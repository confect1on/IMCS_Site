<?php
// $Id: node.tpl.php,v 1.5 2007/10/11 09:51:29 goba Exp $
?>
<?php
//    if ($page!=0)drupal_add_js((path_to_theme()).'/vkl.js'); //временно
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">

<?php print $picture ?>

<?php if ($page == 0): ?>
<a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a>
<?php endif; ?>

<?php if ($page != 0): ?>
<?php /*if ($title): print '<h2'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h2>'; endif; */?>


  <div class="clear-block">
    
    <?php
        $Sotrudniki="<h2>Сотрудники</h2>
        <table class=\"views-table\"><thead><tr><th>Сотрудник</th><th>Ученая степень</th><th>Должность</th></tr></thead><tbody>";
        
          $db_sotrudniki = db_query("SELECT n.nid, n.title fio, n.status, post.field_person_post_value pos, v.field_person_ves_value ves,
                                    s.field_person_us_value us, s.field_person_uz_value uz
                                    FROM node n
                                    JOIN content_type_person s ON n.vid=s.vid
                                    JOIN content_field_person_department d ON n.vid=d.vid
                                    JOIN content_field_person_post post ON (post.vid=n.vid and post.delta=d.delta)
                                    JOIN content_field_person_ves v ON (v.vid=n.vid and v.delta=d.delta)
                                    WHERE (d.field_person_department_nid=$node->nid) and (n.status=1)
                                    ORDER BY ves DESC, fio");		
          $class=array("odd","even");
          $c=0;
          while ($sotrudnik = db_fetch_object($db_sotrudniki))
          {
            $cl=$class[($c++)%2];
            $fio="<a href=\"/node/$sotrudnik->nid\" title=\"\">$sotrudnik->fio</a>";
            $usuz=array();
            if ($sotrudnik->us!="")$usuz[]=$sotrudnik->us;
            //if ($sotrudnik->uz!="")$usuz[]=$sotrudnik->uz;
           
            $Sotrudniki.="<tr class=\"$cl\"><td>$fio</td><td>".join(", ",$usuz)."</td><td>$sotrudnik->pos</td></tr>";
        }
        $Sotrudniki.="</tbody></table>";

        $vkl_count=count($node->field_zagolovki);
        if ($node->field_zagolovki[0]['view']=="" || $node->field_vktext[0]['view']=="")$vkl_count=0;
        if($vkl_count==0)print $Sotrudniki;
        else
            {
				$tab = $_GET['tab'];
				if ($tab < 1 || $tab > $vkl_count + 1) $tab = 1;
      ?>
      <div id="vk">
        <ul>
      <?php
        
        for($i=1;$i<=$vkl_count;$i++)
        {
           print "<li class='" .($i==1?"first":'') . ($i==$tab?' vk-active':''). "'><a href=\"?tab=$i\" id=\"kl".$i."\">".drupal_strtolower($node->field_zagolovki[$i-1]['view'])."</a></li> ";
        }
        print "<li class=\"last" . ($tab == $vkl_count + 1 ? ' vk-active' : ''). "\"><a href=\"?tab=".($vkl_count+1)."\" id=\"kl".($vkl_count+1)."\">сотрудники</a></li>";
      ?>
        </ul>
      </div><div class="clear"></div>
      
      <div id="vkhide"><br />
      <?php
        for($i=1;$i<=$vkl_count;$i++)
        {
		  if ($i == $tab) {
			print "<div id=\"vkl".$i."\" class=\"hide\">".$node->field_vktext[$i-1]['view']."</div> ";
		  }
        }
		if ($tab == $vkl_count + 1) {
			print "<div id=\"vkl".($vkl_count+1)."\" class=\"hide\">".$Sotrudniki."</div>";
		}
      ?>
      </div>
    <?php   } print $content ?>
  </div>

<?php endif; ?>
  <div class="clear-block">
    <div class="meta">
    <?php if ($taxonomy): ?>
      <div class="terms"><?php print $terms ?></div>
    <?php endif;?>
    </div>

    <?php if ($links): ?>
      <div class="links"><?php print $links; ?></div>
    <?php endif; ?>
  </div>
    
</div>
