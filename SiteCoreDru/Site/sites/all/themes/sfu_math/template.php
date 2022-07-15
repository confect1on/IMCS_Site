<?php

function i18n_get_lang() {
	static $lang;
	if ($lang) {
		return $lang;
	}
	$lang = 'ru';
	$offset  = (variable_get('clean_url', 0) ? 0 : 3) + strlen(base_path());
	$uri = substr(request_uri(), $offset);
	if (substr($uri, 2, 1) == '/' || substr($uri, 2, 1) == '?') {
		$uri = substr($uri, 0, 2);
	}
	if ($uri == 'en') {
		$lang = 'en';
	}
	elseif ($uri == 'cn') {
		$lang = 'zh-hans';
	}
	elseif ($uri == 'de') {
		$lang = 'de';
	}
	elseif ($uri == 'es') {
		$lang = 'es';
	}
	return $lang;
}

// $Id: template.php,v 1.16.2.2 2009/08/10 11:32:54 goba Exp $

/**
 * Sets the body-tag class attribute.
 *
 * Adds 'sidebar-left', 'sidebar-right' or 'sidebars' classes as needed.
 */
function phptemplate_body_class($left, $right) {
  if ($left != '' && $right != '') {
    $class = 'sidebars';
  }
  else {
    if ($left != '') {
      $class = 'sidebar-left';
    }
    if ($right != '') {
      $class = 'sidebar-right';
    }
  }

  if (isset($class)) {
    print ' class="'. $class .'"';
  }
}

/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb) && count($breadcrumb)>1) {
    return implode(' › ', $breadcrumb);
  }
}

/**
 * Override or insert PHPTemplate variables into the templates.
 */
function phptemplate_preprocess_page(&$vars) {
  $vars['tabs2'] = menu_secondary_local_tasks();
  $lang = $vars['lang'] = i18n_get_lang();
  if ($vars['lang'] == 'en') {
	  $vars['head_title'] = 'School of Mathematics and Computer Science of SibFU';
	  if ($vars['node']->nid != 1128) {
		  $vars['head_title'] = $vars['title'] . ' | ' . $vars['head_title'];
	  }
  }
  // Hook into color.module
  if (module_exists('color')) {
    _color_page_alter($vars);
  }
}

function phptemplate_preprocess_node(&$vars) {
    $vars['template_files'][] = 'node-'. $vars['node']->nid;
}
/**
 * Add a "Comments" heading above comments except on forum pages.
 */
function sfu_math_preprocess_comment_wrapper(&$vars) {
  if ($vars['content'] && $vars['node']->type != 'forum') {
    $vars['content'] = '<h2 class="comments">'. t('Comments') .'</h2>'.  $vars['content'];
  }
}

/**
 * Returns the rendered local tasks. The default implementation renders
 * them as tabs. Overridden to split the secondary tasks.
 *
 * @ingroup themeable
 */
function phptemplate_menu_local_tasks() {
  return menu_primary_local_tasks();
}

function phptemplate_comment_submitted($comment) {
  return t('!username //&nbsp;!datetime',
    array(
      '!username' => theme('username', $comment),
      '!datetime' => format_date($comment->timestamp)
    ));
}

function phptemplate_node_submitted($node) {
	if ($node->type == 'forum') {
		return t('!username //&nbsp;!datetime',
			array(
				'!username' => theme('username', $node),
				'!datetime' => format_date($node->created),
			));
	} else {
		return '//&nbsp;' . format_date($node->created);
	}
	/*
  return t('!username // !datetime',
    array(
      '!username' => theme('username', $node),
      '!datetime' => format_date($node->created),
    ));
	*/
}

/**
 * Generates IE CSS links for LTR and RTL languages.
 */
function phptemplate_get_ie_styles() {
  global $language;

  $iecss = '<link type="text/css" rel="stylesheet" media="all" href="'. base_path() . path_to_theme() .'/fix-ie.css" />';
  if ($language->direction == LANGUAGE_RTL) {
    $iecss .= '<style type="text/css" media="all">@import "'. base_path() . path_to_theme() .'/fix-ie-rtl.css";</style>';
  }

  return $iecss;
}

function phptemplate_upload_attachments($files) {
  $header = array(t('Attachments'), "","");
  $rows = array();
  $Arows=array();
  foreach ($files as $file) {
    $file = (object)$file;
    if ($file->list && empty($file->remove)) {
      $href = file_create_url($file->filepath);
      $text = $file->description ? $file->description : $file->filename;
      preg_match("/.*(\..*)$/",$file->filename,$xxx);
      preg_match("/^(\[#\])(.*)/i",$text,$arh);
      if($arh[1]=='[#]')$Arows[] = array(l($arh[2], $href), strtolower($xxx[1]), str_replace(" ","&nbsp;",format_size($file->filesize)));
      else
      $rows[] = array(l($text, $href), strtolower($xxx[1]), str_replace(" ","&nbsp;",format_size($file->filesize)));
    }
  }
  $s="";
  if (count($rows)) {
     $s.='<table id="attachments" class="sticky-enabled"><thead><tr><th width="500px">Прикрепленные файлы</th><th></th><th></th></tr></thead><tbody>';
     $cl=array("odd","even"); $c=0;
     foreach ($rows as $row)
     {
        $s.="<tr class=\"".$cl[($c++)%2]."\"><td width=\"500px\">".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td></tr>";
     }
     $s.="</tbody></table>";
  }
  if (count($Arows)) {
     //drupal_add_js((path_to_theme()).'/js.js');
     $s.='<br /><a href="#" id="plusminus" class="plusminus">Архив файлов</a>';
     $s.='<script>document.write("<style>#pmhide{display:none;}</style>");</script><div id="pmhide"><table>';
     $cl=array("odd","even"); $c=0;
     foreach ($Arows as $row)
     {
        $s.="<tr class=\"".$cl[($c++)%2]."\"><td width=\"500px\">".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td></tr>";
     }
     $s.="</table></div>";
  }

  return $s;
}

/*
// вывод кнопок "сохранить" и "предпросмотр" вверху форм у нод
function sfu_math_theme() {
   return array(
    'node_form' => array(
       'arguments' => array('form' => NULL),
     ),
   );
}

function sfu_math_node_form($form) {
  $output = '<div id="my-node-form"><!--input type="submit" name="op" value="Сохранить"  class="form-submit" />
<input type="submit" name="op" value="Предпросмотр"  class="form-submit" /-->';
     //print_r($form);

  if ($form['#node']->type != 'forum') {
	$form['topbuttons']['submit']=$form['buttons']['submit'];
	$form['topbuttons']['preview']=$form['buttons']['preview'];
	$form['topbuttons']['#weight']=-200;
  }

  $form['buttons']['#weight']=300;

  $output .= drupal_render($form) ."</div>";
  return $output;
}
*/

function phptemplate_username($object) {
	//_d($object);
	//drupal_set_message('<pre>'.print_r($object,1).'</pre>');
	if ($object->uid) {
		$object = user_load($object->uid);
		if ($object->profile_fullname != '') $object->name = $object->profile_fullname;
	}
  if ($object->uid && $object->name) {
    // Shorten the name when it is too long or it will break many tables.
    if (drupal_strlen($object->name) > 40) {
      $name = drupal_substr($object->name, 0, 30) .'...';
    }
    else {
      $name = $object->name;
    }

    if (user_access('access user profiles')) {
      $output = l($name, 'user/'. $object->uid, array('attributes' => array('title' => t('View user profile.'))));
    }
    else {
      $output = check_plain($name);
    }
  }
  else if ($object->name) {
    // Sometimes modules display content composed by people who are
    // not registered members of the site (e.g. mailing list or news
    // aggregator modules). This clause enables modules to display
    // the true author of the content.
    if (!empty($object->homepage)) {
      $output = l($object->name, $object->homepage, array('attributes' => array('rel' => 'nofollow')));
    }
    else {
      $output = check_plain($object->name);
    }

    $output .= ' ('. t('not verified') .')';
  }
  else {
    $output = check_plain(variable_get('anonymous', t('Anonymous')));
  }

  return $output;
}
