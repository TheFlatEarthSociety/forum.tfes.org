<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = false;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />';

	//favicon pls
	//hi i am pplanet i know how comments
	echo 
	'
	<link rel="shortcut icon" href="/favicon.ico" />';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>
	<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';
	//If it has a defined description, use that.
	if (!empty($context['description']))
	{
		$descr = $context['description'];
	}
	//If it's a topic (which we can actually read), try to describe it.
	else if (!empty($context['current_topic']) && empty($context['current_action']) && !empty($context['get_message']))
	{
		//Grab first post of current page.
		$descr = $context['get_message']()['body'];
		//Strip "Quote from: [...]" lines
		$descr = preg_replace('/<div class=\"topslice_quote\">.*?<\/div>/', '', $descr);
		//Strip <a> tags whose text isn't meaningful for a descritpion
		$descr = preg_replace('/<a[^>]*>http:\/\/[^<]*<\/a>/', '', $descr);
		//Strip all other HTML tags.
		$descr = preg_replace('/(<\/?(strong|em|span|del)[^>]*>)+/', '', $descr);
		$descr = preg_replace('/((<[^>]*>|&nbsp;))+/', ' ', $descr);
		//Clean up whitespace
		$descr = trim(preg_replace('/\s+/', ' ', $descr));
		//Truncate it to <160 characters (reasonable length for meta description)
		if(strlen($descr) > 160)
		{
			$descr = substr($descr, 0, 156);
			$descr = substr($descr, 0, strrpos($descr, " "));
			$descr .= '...';
		}
		//Reset counter so that the first post doesn't get omitted in the actual display.
		$context['get_message'](true);
	}
	//Default description
	else
	{
		$descr = 'This is the forum of the world-famous Flat Earth Society, a place for free thinkers and the intellectual exchange of ideas.';
	}
	echo '
	<meta name="description" content="'. $descr  .'" />
	<meta property="og:description" content="'. $descr  .'" />';
	echo !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<meta http-equiv="content-language" content="en-us" />
	<meta property="og:site_name" content="The Flat Earth Society" />
	<meta property="og:title" content="', $context['page_title_html_safe'], '" />
	<meta property="og:image" content="http://forum.tfes.org/logo.png" />
	<meta property="og:locale" content="en_US" />
	<title>', $context['page_title_html_safe'], '</title>';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<meta property="og:url" content="', $context['canonical_url'], '" />
	<link rel="canonical" href="', $context['canonical_url'], '" />';
	// Don't respect SMF's noindex value if we have a perfectly good canonical URL to work with.
	else if(!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';


	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next pages of the same topic, respectively.
	// For some reason SMF developers thought that the best thing to put down as the "next page" is the next thread.
	// That's retarded, and thus now fixed.
	if (!empty($context['current_topic']) && empty($context['current_action']) && !empty($context["start"]) && !empty($context["messages_per_page"]) && !empty($context["total_visible_posts"]))
	{
		$next_page = $context['start'] + $context['messages_per_page'];
		$prev_page = $context['start'] - $context['messages_per_page'];
		if($prev_page >= 0)
			echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.', $prev_page,'" />';
		else
      echo '
	<link rel="first" />';
		if($next_page < $context['total_visible_posts'])
			echo '
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.', $next_page,'" />';
		else
      echo '
	<link rel="last" />';
	}

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo !empty($settings['forum_width']) ? '
<div id="wrapper" style="width: ' . $settings['forum_width'] . '">' : '', '
	 <div id="header">
			<div id="head-l">
				 <div id="head-r">
					  <div id="userarea" class="smalltext">';
		 if (!empty($context['user']['avatar']))
		 echo '<div id="my-avatar" class="clearfix">'.$context['user']['avatar']['image'].'</div>';
		 if ($context['user']['is_logged'])
  {
	echo '
		<ul class="reset">
			<li><b>', $txt['hello_member'], ' ', $context['user']['name'], '</b></li><li>';

	// Only tell them about their messages if they can read their messages!
	if ($context['allow_pm'])
	 echo $txt['msg_alert_you_have'], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt['msg_alert_messages'] : $txt['message_lowercase'], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'] , '.</li>';

	echo '
			  <li><a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a></li>
			  <li><a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a></li>
		</ul>';

  }

	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	else
  {
		echo sprintf($txt['welcome_guest'], $txt['guest_title']);

	  echo '
			<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" style="margin: 4px 0;">
				<input type="text" name="user"  size="10" />
				<input type="password" name="passwrd"  size="10" />
				<input type="submit" value="', $txt['login'], '" class="button_submit" />
				  </form>', $context['current_time'],'<br />';
  }
	
	echo '
		</div>';

	echo '
		 <div id="searcharea" class="smalltext">';
			  echo '
		  <form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
		  <input class="inputbox" type="text" name="search" value="', $txt['search'], '..." onfocus="this.value = \'\';" onblur="if(this.value==\'\') this.value=\'', $txt['search'], '...\';" />';

	  // Search within current topic?
		 if (!empty($context['current_topic']))
		 echo '<br /><input type="checkbox" name="topic" value="', $context['current_topic'], '" />in current thread';
  
	  // If we're on a certain board, limit it to this board ;).
		  elseif (!empty($context['current_board']))
		 echo '<br /><input type="checkbox" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />in current board';
		echo '
				</form>';
				echo '
						</div>';

	  // Show a random news item? (or you could pick one from news_lines...)
		 if (!empty($settings['enable_news']))
	{
			echo '<div id="news">
			<br /><b>', $txt['news'], ':</b> ', $context['random_news_line'], '</div>';
	}
	echo '
			 <div id="logo">
					<a href="'.$scripturl.'" title=""></a>
						  </div>';
					echo '
						</div>
					</div>
				</div>
				<div id="toolbar">
					',template_menu(),'
				</div>
				<div id="bodyarea">';

					// Show the navigation tree.
					theme_linktree(false, true);
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

			echo '
				</div>';


	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
<div id="footer">
	 <div id="foot-l">
		  <div id="foot-r">
				<div id="footerarea">
					 <div id="footer_section">
					<ul class="reset">
						<li class="copyright">', theme_copyright(), '</li>
						<li><b>Anecdota</b> by <a href="http://www.jpr62.com/theme/" target="_blank" class="new_win" title=""><span><b>Crip</b></span></a></li>
						<li class="last"><a id="button_wap2" href="', $scripturl , '?action=forum;wap2" class="new_win"><span>', $txt['wap2'], '</span></a></li>
					</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

	echo '
				 </div>
			 </div>
		 </div>
	 </div>
</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</div>
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false, $header = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	// Reverse the linktree in right to left mode.
	if ($context['right_to_left'])
		$context['linktree'] = array_reverse($context['linktree'], true);

	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last">' . ($header ? '<h1>' : '' ) : '>' . ($header ? '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><h2>' : '');

		// Don't show a separator for the last one (RTL mode)
		if ($link_num != count($context['linktree']) - 1 && $context['right_to_left'])
			echo '&#171;&nbsp;';

			// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"' . ($header ? 'itemprop="url"' : '') . '><span ' . ($header ? 'itemprop="title"' : '') . '>' . $tree['name'] . '</span></a>' : '<span ' . ($header ? 'itemprop="title"' : '') . '>' . $tree['name'] .'</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1 && !$context['right_to_left'])
			echo '&nbsp;&#187;';

		echo '
			' . ($header ? (($link_num == count($context['linktree']) - 1) ? '</h1>' : '</h2></div>') : '') . '</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<div id="topnav">
			<ul>';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', '" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '><span>', $button['title'], '</span></a>';

		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</a>';

				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>', $grandchildbutton['title'], '</a>
								</li>';

					echo '
						</ul>';
				}

				echo '
						</li>';
			}
			echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>
		</div><br class="clear" />';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '<a ' . (isset($value['active']) ? 'class="active" ' : '') . 'href="' . $value['url'] . '" ' . (isset($value['custom']) ? $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' align_' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>
				<li>', implode('</li><li>', $buttons), '</li>
			</ul>
		</div>';
}

?>
