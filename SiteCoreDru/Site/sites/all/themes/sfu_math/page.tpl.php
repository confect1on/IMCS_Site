<?php
// $Id: page.tpl.php,v 1.18.2.1 2009/04/30 00:13:31 goba Exp $
/* <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
  <head>
    <?php print $head ?>
    <title><?= $is_front ? $site_name : $head_title ?></title>
    <?php print $styles ?>
	<link rel="stylesheet" type="text/css" href="http://search.sfu-kras.ru/navigation/?get=css&amp;background=%238CD9B3&amp;color=%2344775f&amp;referrer=math.sfu-kras.ru&amp;width=980px" /> 
	<?php print $scripts ?>
  </head>
  <body<?php print phptemplate_body_class($left, $right); ?>>
  
  <div id="wrapp">
    <div id="bag"><div id="pysh"></div></div>
    <div id="content">  
<?php print file_get_contents('http://search.sfu-kras.ru/navigation/?get=navigation&language=' . $lang . '&encoding=utf-8&referrer=math.sfu-kras.ru&divider=+|+&width=980px');?>

<?php /* а че так? перекинул сюда, а то скрол появляется  */ ?>
<?/*
	<div class="sfu_navigation">
	<table cellspacing='0' cellpadding='0'>
	<tr>
		<td class="sfu_navigation_links">
			<a href="http://www.sfu-kras.ru/" class="sfu_navigation_first">СФУ</a> | <a href="http://admissions.sfu-kras.ru/">Поступление</a> | <a href="http://research.sfu-kras.ru/">Наука</a> | <a href="http://edu.sfu-kras.ru/">Обучение</a> | <a href="http://gazeta.sfu-kras.ru/">Газета</a> | <a href="http://lib.sfu-kras.ru/">Библиотека</a> | <a href="http://sport.sfu-kras.ru/">Спорт</a> | <a href="http://www.sfu-kras.ru/structures" class="sfu_navigation_last">Институты</a>
		</td>
		<td class="sfu_navigation_search" align="right">
			<form method='get' action='http://search.sfu-kras.ru/'>
				<input type='text' name='q' size='27' />
				<select name='domain'>
				<option value='math.sfu-kras.ru'>этот сайт</option>
				<option value=''>сайты СФУ</option>
				</select>
				<input type='submit' class='sfu_navigation_submit' value='Поиск' />
			</form>
		</td>
	</tr>
	</table>
	</div>
*/?>	

	<?/*
      <div id="header-region" class="clear-block">
	<div id="linkRing">
	  <ul>
	     <li class="first"><a href="http://sfu-kras.ru/">СФУ</a></li>
	     <li><a href="http://admissions.sfu-kras.ru/">Поступление</a></li>
	     <li><a href="http://research.sfu-kras.ru/">Наука</a></li>
	     <li><a href="http://edu.sfu-kras.ru/">Обучение</a></li>
	     <li><a href="http://gazeta.sfu-kras.ru/">Газета</a></li>
	     <li><a href="http://lib.sfu-kras.ru/">Библиотека</a></li>
	     <li><a href="http://sport.sfu-kras.ru/">Спорт</a></li>
	     <li><a href="http://www.sfu-kras.ru/structures">Институты</a></li>
	     <li class="right"><form method="get" action="http://search.sfu-kras.ru/"><input type="text" name="q" size="27" />&nbsp;<select name="domain"><option value="math.sfu-kras.ru">этот сайт</option><option value="">сайты СФУ</option></select>&nbsp;<input type="submit" class="submit" value="Поиск" /></form></li>
	  </ul>
	</div>
      </div>
	  */?>
   
      <div id="header">
	<div id="logo-floater">
		<?if ($lang == 'en') {?>
			<a href="/en" onfocus="this.blur();"><img src="/sites/all/themes/sfu_math/img/logo.png" width="59px" height="50px"  alt="" />School of Mathematics and Computer Science of SibFU</a>
		<?} else {?>
			<a href="/" onfocus="this.blur();"><img src="/sites/all/themes/sfu_math/img/logo.png" width="59px" height="50px"  alt="" />Институт математики и фундаментальной информатики СФУ</a>
		<?}?>
	</div>
	<div id="photos">
	  <img src="/sites/all/themes/sfu_math/img/site_top.jpg" alt=""  />
	</div>
      </div> <!-- /header -->
	
      <div id="wrapper">
	<div id="container" class="clear-block" >
    
	    <div id="sidebar-left" class="sidebar">
	      <div id="sidebar1"></div>
	      <div id="sidebar2">  
			<?php print $left ?>
			<?if ($left == '') {?><div class='block'></div><?}?>
	      </div>
	    </div>
    
	  <div id="center"><div id="squeeze">
	      <div class="breadcrumb">
			<?php print $breadcrumb_line; ?>
			<?php print $breadcrumb; ?>
		  </div>
	      <?php if ($mission): print '<div id="mission">'. $mission .'</div>'; endif; ?>
	      <?php if ($tabs): print '<div id="tabs-wrapper" class="clear-block">'; endif; ?>
    
	      <?php if ($title && !$is_front): print '<h2'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h2>'; endif;  ?>
	      <?php if ($tabs): print '<ul class="tabs primary">'. $tabs .'</ul></div>'; endif; ?>
	      <?php if ($tabs2): print '<ul class="tabs secondary">'. $tabs2 .'</ul>'; endif; ?>
	      <?php if ($show_messages && $messages): print $messages; endif; ?>
	      <?php print $help; ?>
	      <div class="clear-block">
		<div class='page-content'><?php print $content ?></div>
	      </div>
	      <?php print $feed_icons ?>
	      
	  </div></div>
    
	</div> <!-- /container -->
      </div>
    </div> <!-- /content -->
    <?php print $closure ?>
    <div id="footer">
      <div id="copyright">
	&copy; <?php print date("Y"); ?> <?=$lang=='en'?'School of Mathematics and Computer Science of':'Институт математики и фундаментальной информатики'?> <a href='http://www.sfu-kras.ru/'><?=$lang=='en'?'SibFU':'СФУ'?></a><br />
	<a href='/<?=$lang=='en'?'en/':''?>contacts'><?=$lang=='en'?'Contacts':'Контакты'?></a> • +7 (391) 206-21-48 • <a href="mailto:math@sfu-kras.ru">math@sfu-kras.ru</a>
      </div>
    </div>
  </div><!-- /wrapp -->   

<?if (0 && !user_access('administer nodes')) {?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2488111-39']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script><?}?>

  </body>
</html>
