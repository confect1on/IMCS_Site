<?
	if ($node->taxonomy[15] && (arg(0) == 'front' || arg(0) == 'news' || arg(0) == 'node' && arg(1) == $node->nid)) {
		$title = "Заседание учёного совета: $title";
		if ($page) drupal_set_title($title);
	}

  if(arg(0) == 'front' && $node->taxonomy[9]) { ?>
  <div class="content clear-block news-line">
		<a href='<?=$node_url?>'><?=$title?></a> <span class="submitted"><?php print $submitted; ?></span>
  </div>
<? } else {?>

<div id="node-<?php print $node->nid; ?>" class="<?=$node->type?> <?=$teaser?'teaser':'full'?> node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">

<?php print $picture ?>

<?php if ($page == 0) { ?>

	<h3><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a>
		<?php if ($submitted && ($node->type != 'news' || $node->taxonomy[8] || $node->taxonomy[9])): ?>
			<span class="submitted"><?php print $submitted; ?></span>
		<?php endif; ?>
	</h3>

<?php } else { ?>
		<?php if ($submitted && ($node->type != 'news' || $node->taxonomy[8] || $node->taxonomy[9])): ?>
			<span class="submitted"><?php print $submitted; ?></span>
		<?php endif; ?>
<? } ?>

<div class="content clear-block">
    <?if ($node->field_news_img[0]['view'])
    {
      if($page==0) print "<div class='" . ($_GET['q'] == 'front' ? 'left' : 'right') . "'><a href=\"$node_url\" title=\"$title\">" . $node->field_news_img[0]['view']  . "</a></div>";
      else print "<div class='right'>" . $node->field_news_img[0]['view']  . "</div>";
    }
    ?>
 	<?php print $content ?>
  </div>

  <div class="clear-block">
    <div class="meta">
    <?php if ($taxonomy && $node->type != 'news' && $node->type != 'forum'): ?>
      <div class="terms"><?php print $terms ?></div>
    <?php endif;?>
    </div>

	<?if ($links || $node->type == 'news' && !$teaser) {?>
		<table cellpadding='0' cellspacing='0' class='node-links-table'>
		<tr>
			<?php if ($node->type == 'news' && !$teaser && function_exists('ru_share')):?><td><?=ru_share($node)?></td><?endif;?>
			<?php if ($links): ?><td><div class="links"><?php print $links; ?></div></td><?endif;?>
		</tr>
		</table>
	<?}?>
  </div>
</div>

<?}?>
