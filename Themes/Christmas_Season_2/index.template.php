<?php
// Version: 2.0 RC2; index

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
	$settings['theme_version'] = '2.0 RC1';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = false;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = false;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = false;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = false;
}

// The main sub template above the content.
function template_main_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '><head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />
	<meta name="keywords" content="', $context['meta_keywords'], '" />
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// The ?rc2 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style.css?rc2" />
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/print.css?rc2" media="print" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="' . $scripturl . '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next pages of the same topic, respectively.
	// For some reason SMF developers thought that the best thing to put down as the "next page" is the next thread.
	// That's retarded, and thus now fixed.
	if (!empty($context['current_topic']))
	{
		$next_page = $context['start'] + $context['messages_per_page'];
		$prev_page = $context['start'] - $context['messages_per_page'];
		if($prev_page >= 0)
			echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.', $prev_page,'" />';
		if($next_page < $context['total_visible_posts'])
			echo '
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.', $next_page,'" />';
	}

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="' . $scripturl . '?board=' . $context['current_board'] . '.0" />';

	// We'll have to use the cookie to remember the header...
	if ($context['user']['is_guest'])
		$options['collapse_header'] = !empty($_COOKIE['upshrink']);

	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?rc2"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?rc2"></script>
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
	// ]]></script>';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

  // Include Google Analytics stuff at the beginning of <body>
	echo '
</head>
<body>

<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

  ga(\'create\', \'UA-48900853-2\', \'tfes.org\');
  ga(\'set\', \'anonymizeIp\', true);
  ga(\'send\', \'pageview\');

</script>';
/*}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;
*/

	// Because of the way width/padding are calculated, we have to tell Internet Explorer 4 and 5 that the content should be 100% wide. (or else it will assume about 108%!)
	echo '
	<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
			<td align="left" height="20" width="20"><img src="', $settings['images_url'], '/ht_ls.png"></td>
	      <td class="headtop" align="center" style="background:#332244;" height="20" width="100%"><img src="', $settings['images_url'], '/blank.gif" height="20" alt="" /></td>
	    <td align="right" height="20" width="20"><img src="', $settings['images_url'], '/ht_rs.png"></td>
	  </tr>
	</table>
	<div id="headerarea" style="padding: 12px 30px 4px 30px;', $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? ' width: 100%;' : '', '">';

echo '
      
 <table class="windowbg6" align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
   <td align="middle">
					<center><img src="', $settings['images_url'], '/northstar.gif" style="margin: 0px;" alt="" /></center></td>
</tr>
<td align="middle">
					<center><img src="', $settings['images_url'], '/img110.gif" style="margin: 0px;" alt="" /></center>
				</td></tr>';

	// The logo and the three info boxes.
	echo '
		<table class="windowbg6" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="position: relative;">
			<tr>
			<td class="top_ls">&nbsp;</td>
<td class="top" width="100%" colspan="3" valign="middle" align="left" style="padding-left: 80px; white-space: nowrap;">';

	// This part is the logo and forum name.  You should be able to change this to whatever you want...
	echo '

					<span align="center" valign="top" style="font-family: Georgia, sans-serif; font-size: 12pt;font-weight:bold;">', $context['forum_name_html_safe'], '</span>';


	echo '
	</td>
<td class="top_rs">&nbsp;</td>
			</tr></table>';
			echo '
<table width="100%" cellpadding="0" cellspacing="0" border="0">
     <tr>
				<td class="windowbg9" valign="top">
					<div class="titlebg5" style="margin-right: 5px; position: relative;"><img src="', $settings['images_url'], '/blank.gif" height="12" alt="" /></div>
					<div class="titlebg2" style="position: relative; margin-right: 5px; background:#E8E8E7;">
						<img src="', $settings['images_url'], '/', $context['user']['language'], '/userinfo.gif" style="position: absolute; left: ', $context['browser']['is_ie5'] || $context['browser']['is_ie4'] ? '0' : '-1px', '; top: -16px; clear: both;" alt="" />
						<table width="99%" cellpadding="5" cellspacing="5" border="0"><tr>';

	if (!empty($context['user']['avatar']))
		echo '<td class="windowbg4" valign="middle">', $context['user']['avatar']['image'], '</td>';

echo '	<td colspan="2" width="100%" valign="top" class="windowbg8"><span class="middletext">';

	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '
							', $txt['hello_member'], ' <b>', $context['user']['name'], '</b>';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm'])
			echo ', ', $txt['msg_alert_you_have'], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt['msg_alert_messages'] : $txt['message_lowercase'], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
		echo '.<br />';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
							<b>', $txt['maintain_mode_on'], '</b><br />';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
							', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';

		// Show the total time logged in?
		if (!empty($context['user']['total_time_logged_in']))
		{
			echo '
							', $txt['totalTimeLogged1'];

			// If days is just zero, don't bother to show it.
			if ($context['user']['total_time_logged_in']['days'] > 0)
				echo $context['user']['total_time_logged_in']['days'] . $txt['totalTimeLogged2'];

			// Same with hours - only show it if it's above zero.
			if ($context['user']['total_time_logged_in']['hours'] > 0)
				echo $context['user']['total_time_logged_in']['hours'] . $txt['totalTimeLogged3'];

			// But, let's always show minutes - Time wasted here: 0 minutes ;).
			echo $context['user']['total_time_logged_in']['minutes'], $txt['totalTimeLogged4'], '<br />';
		}

		echo '
							<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a><br />
							<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a><br />
							', $context['current_time'], '<br />';
		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
								<a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a>';
	}
	// Otherwise they're a guest - so politely ask them to register or login.
	else
	{
		echo '
							', sprintf($txt['welcome_guest'], $txt['guest_title']), '<br />
							', $context['current_time'], '<br />

							<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>

							<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" style="margin: 3px 1ex 1px 0;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
								<div style="text-align: right;">
									<input type="text" name="user" size="10" /> <input type="password" name="passwrd" size="10" />
									<select name="cookielength">
										<option value="60">', $txt['one_hour'], '</option>
										<option value="1440">', $txt['one_day'], '</option>
										<option value="10080">', $txt['one_week'], '</option>
										<option value="43200">', $txt['one_month'], '</option>
										<option value="-1" selected="selected">', $txt['forever'], '</option>
									</select>
									<input type="submit" value="', $txt['login'], '" /><br />
									', $txt['quick_login_dec'], '
									<input type="hidden" name="hash_passwrd" value="" />
								</div>
							</form>';
	}

	echo '
						</td></tr></table>
					</div>

					<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" class="middletext" style="margin: 0; margin-top: 7px;">
						<div>
							<b>', $txt['search'], ': </b><input type="text" name="search" value="" style="width: 190px;" />&nbsp;
							<input type="submit" name="submit" value="', $txt['search'], '" style="width: 8ex;" />&nbsp;';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
							<input type="checkbox" name="topic" value="', $context['current_topic'], '" />in current topic';

		// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
							<input type="checkbox" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />in current board';

	echo '
					 <a href="', $scripturl, '?action=search;advanced">', $txt['search_advanced'], '</a>
							<input type="hidden" name="advanced" value="0" />
						</div>
					</form>

				</td>
				<td class="windowbg9" width="360" style="padding-left: 5px;" valign="top">';

	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '
					<div class="titlebg5" style="width: 360px;"><img src="', $settings['images_url'], '/blank.gif" height="12" alt="" /></div>
					<div class="headerbodies" style="width: 360px; position: relative; background-image: url(', $settings['images_url'], '/box_bg.gif;">
						<img src="', $settings['images_url'], '/', $context['user']['language'], '/newsbox.gif" style="position: absolute; left: -1px; top: -16px;" alt="" />
						<div class="windowbg8" style="height: 50px; overflow: auto; padding: 5px;" class="middletext">', $context['random_news_line'], '</div>
					</div>';

	// The "key stats" box.
	echo '
					<div class="titlebg5" style="width: 360px;"><img src="', $settings['images_url'], '/blank.gif" height="12" alt="" /></div>
					<div class="headerbodies" style="width: 360px; position: relative; background-image: url(', $settings['images_url'], '/box_bg.gif);">
						<img src="', $settings['lang_images_url'], '/keystats.gif" style="position: absolute; left: -1px; top: -16px;" alt="" />
						<div class="windowbg8" style="', !$context['browser']['is_ie'] ? 'min-height: 35px;' : 'height: 4em;', ' padding: 5px;" class="middletext">
							<b>', $context['common_stats']['total_posts'], '</b> ', $txt['posts_made'], ' ', $txt['in'], ' <b>', $context['common_stats']['total_topics'], '</b> ', $txt['topics'], ' ', $txt['by'], ' <span style="white-space: nowrap;"><b>', $context['common_stats']['total_members'], '</b> ', $txt['members'], '</span><br />
							', $txt['latest_member'], ': <b> ', $context['common_stats']['latest_member']['link'], '</b>
						</div>
					</div>';

	echo '
				</td>
			</tr>

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
<td class="ice" width="100%" colspan="3" align="center" valign="top"><img src="', $settings['images_url'], '/blank.gif" height="36" alt="" /></td></tr>
<tr><td width="100%" colspan="3" align="center" valign="bottom">';


		// Show the menu here, according to the menu sub template.
		template_menu();
		
	echo '
				</td>
			</tr>
		</table>
	</div>'; 

	// The main content should go here.  A table is used because IE 6 just can't handle a div.
	echo '
       <table id="boardarea" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
		<td>';
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '	
	    </td></tr>
	</table>
		<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
			<td align="left" height="20" width="20"><img src="', $settings['images_url'], '/lbc.png"></td>
	      <td class="bcenter" align="center" style="background:#D8D8D7;" height="20" width="100%"><img src="', $settings['images_url'], '/blank.gif" height="20" alt="" /></td>
	    <td align="right" height="20" width="20"><img src="', $settings['images_url'], '/rbc.png"></td>
	  </tr>
	</table>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '

	<div id="footerarea" style="text-align: center; padding-bottom: 1ex;', $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? ' width: 100%;' : '', '">
		<table cellspacing="0" cellpadding="3" border="0" align="center" width="100%">
			<tr>
				<td width="28%" valign="middle" align="', !$context['right_to_left'] ? 'right' : 'left', '">
					<a href="http://www.mysql.com/" target="_blank" class="new_win"><img id="powered-mysql" src="', $settings['images_url'], '/powered-mysql.gif" alt="', $txt['powered_by_mysql'], '" width="54" height="20" style="margin: 5px 16px;" onmouseover="smfFooterHighlight(this, true);" onmouseout="smfFooterHighlight(this, false);" /></a>
					<a href="http://www.php.net/" target="_blank" class="new_win"><img id="powered-php" src="', $settings['images_url'], '/powered-php.gif" alt="', $txt['powered_by_php'], '" width="54" height="20" style="margin: 5px 16px;" onmouseover="smfFooterHighlight(this, true);" onmouseout="smfFooterHighlight(this, false);" /></a>
				</td>
				<td valign="middle" align="center" style="white-space: nowrap;">
				<small><font color="white">Christmas Season by <a href="http://globalbt-dev.info/forum/" target="_blank">TreetopClimber</a> &nbsp;|&nbsp;
					', theme_copyright(), '
				</font></td>
				<td width="28%" valign="middle" align="', !$context['right_to_left'] ? 'left' : 'right', '">
					<a href="http://validator.w3.org/check/referer" target="_blank" class="new_win"><img id="valid-xhtml10" src="', $settings['images_url'], '/valid-xhtml10.gif" alt="', $txt['valid_xhtml'], '" width="54" height="20" style="margin: 5px 16px;" onmouseover="smfFooterHighlight(this, true);" onmouseout="smfFooterHighlight(this, false);" /></a>
					<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank" class="new_win"><img id="valid-css" src="', $settings['images_url'], '/valid-css.gif" alt="', $txt['valid_css'], '" width="54" height="20" style="margin: 5px 16px;" onmouseover="smfFooterHighlight(this, true);" onmouseout="smfFooterHighlight(this, false);" /></a>
				</td>
			</tr>
		</table>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<span class="smalltext">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</span>';

	echo '
		</div>';

	// This is an interesting bug in Internet Explorer AND Safari.  Rather annoying, it makes overflows just not tall enough.
	if (($context['browser']['is_ie'] && !$context['browser']['is_ie4']) || $context['browser']['is_mac_ie'] || $context['browser']['is_safari'] || $context['browser']['is_firefox'])
	{
		// The purpose of this code is to fix the height of overflow: auto div blocks, because IE can't figure it out for itself.
		echo '
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[';

		// Unfortunately, Safari does not have a "getComputedStyle" implementation yet, so we have to just do it to code...
		if ($context['browser']['is_safari'])
			echo '
			window.addEventListener("load", smf_codeFix, false);

			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = 0; i < codeFix.length; i++)
				{
					if ((codeFix[i].className == "code" || codeFix[i].className == "post" || codeFix[i].className == "signature") && codeFix[i].offsetHeight < 20)
						codeFix[i].style.height = (codeFix[i].offsetHeight + 20) + "px";
				}
			}';
		elseif ($context['browser']['is_firefox'])
			echo '
			window.addEventListener("load", smf_codeFix, false);
			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = 0; i < codeFix.length; i++)
				{
					if (codeFix[i].className == "code" && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0))
						codeFix[i].style.overflow = "scroll";
				}
			}';
		else
		{
			echo '
			add_load_event(smf_codeFix);

			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = codeFix.length - 1; i > 0; i--)
				{
					if (codeFix[i].currentStyle.overflow == "auto" && (codeFix[i].currentStyle.height == "" || codeFix[i].currentStyle.height == "auto") && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0) && (codeFix[i].offsetHeight != 0 || codeFix[i].className == "code"))
						codeFix[i].style.height = (codeFix[i].offsetHeight + 36) + "px";
				}
			}';
		}

		echo '
		// ]]></script>';
	}
}
function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// The following will be used to let the user know that some AJAX process is running
	echo '
		<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>

	</body>
</html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
	global $context, $settings, $options;

	echo '<div align="left" class="nav" style="font-size: smaller; margin-bottom: 2ex; margin-top: 2ex;">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		// Show the | | |-[] Folders.
		if (!$settings['linktree_inline'])
		{
			if ($link_num > 0)
				echo str_repeat('<img src="' . $settings['images_url'] . '/icons/linktree_main.gif" alt="| " border="0" />', $link_num - 1), '<img src="' . $settings['images_url'] . '/icons/linktree_side.gif" alt="|-" border="0" />';
			echo '<img src="' . $settings['images_url'] . '/icons/folder_open.gif" alt="+" border="0" />&nbsp; ';
		}
		
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo '<b>', $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '" class="nav">' . $tree['name'] . '</a>' : $tree['name'], '</b>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo $settings['linktree_inline'] ? '&nbsp;<img src="' . $settings['images_url'] . '/icons/nav1.gif" alt="" border="0" />&nbsp;' : '<br />';
	}

	echo '</div>';
}

// Show the menu up top.  Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// We aren't showing all the buttons in this theme.
	$hide_buttons = array('pm', 'mlist');

	foreach ($context['menu_buttons'] as $act => $button)
		if (in_array($act, $hide_buttons))
			continue;
		else
			echo '
				<a href="', $button['href'], '">', $settings['use_image_buttons'] ? '<img src="' . $settings['lang_images_url'] . '/' . $act . '.gif" alt="' . $button['title'] . '" border="0" />' : $button['title'], '</a>', !empty($button['is_last']) ? '' : $context['menu_separator'];
}

	// Show the [home] and [help] buttons.
/*	<!--echo '
				<a href="', $scripturl, '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/home.gif" alt="' . $txt[103] . '" style="margin: 2px 0;" border="0" />' : $txt[103]), '</a>', $context['menu_separator'], '
				<a href="', $scripturl, '?action=help">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/help.gif" alt="' . $txt[119] . '" style="margin: 2px 0;" border="0" />' : $txt[119]), '</a>', $context['menu_separator'];

	// How about the [search] button?
	if ($context['allow_search'])
		echo '
				<a href="', $scripturl, '?action=search">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/search.gif" alt="' . $txt[182] . '" style="margin: 2px 0;" border="0" />' : $txt[182]), '</a>', $context['menu_separator'];

	// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo '
				<a href="', $scripturl, '?action=admin">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/admin.gif" alt="' . $txt[2] . '" style="margin: 2px 0;" border="0" />' : $txt[2]), '</a>', $context['menu_separator'];

	// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo '
				<a href="', $scripturl, '?action=profile">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/profile.gif" alt="' . $txt[79] . '" style="margin: 2px 0;" border="0" />' : $txt[467]), '</a>', $context['menu_separator'];

	// The [member] list button?
	if ($context['allow_memberlist'])
		echo '
					<a href="', $scripturl, '?action=mlist">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/memberlist.gif" alt="' . $txt[331] . '" style="margin: 2px 0;" border="0" />' : $txt[331]), '</a>', $context['menu_separator'];

	// The [calendar]!
	if ($context['allow_calendar'])
		echo '
				<a href="', $scripturl, '?action=calendar">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/calendar.gif" alt="' . $txt['calendar24'] . '" style="margin: 2px 0;" border="0" />' : $txt['calendar24']), '</a>', $context['menu_separator'];

	// If the user is a guest, show [login] and [register] buttons.
	if ($context['user']['is_guest'])
	{
		echo '
				<a href="', $scripturl, '?action=login">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/login.gif" alt="' . $txt[34] . '" style="margin: 2px 0;" border="0" />' : $txt[34]), '</a>', $context['menu_separator'], '
				<a href="', $scripturl, '?action=register">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/register.gif" alt="' . $txt[97] . '" style="margin: 2px 0;" border="0" />' : $txt[97]), '</a>';
	}
	// Otherwise, they might want to [logout]...
	else
		echo '
				<a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/logout.gif" alt="' . $txt[108] . '" style="margin: 2px 0;" border="0" />' : $txt[108]), '</a>';
}-->*/

// Generate a strip of buttons, out of buttons.
function template_button_strip($button_strip, $direction = 'top', $force_reset = false, $custom_td = '')
{
	global $settings, $buttons, $context, $txt, $scripturl;

	if (empty($button_strip))
		return '';

	// Create the buttons...
	foreach ($button_strip as $key => $value)
	{
		if (isset($value['test']) && empty($context[$value['test']]))
		{
			unset($button_strip[$key]);
			continue;
		}
		elseif (!isset($buttons[$key]) || $force_reset)
			$buttons[$key] = '<a href="' . $value['url'] . '" ' . (isset($value['custom']) ? $value['custom'] : '') . '>' . ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . ($value['lang'] ? $context['user']['language'] . '/' : '') . $value['image'] . '" alt="' . $txt[$value['text']] . '" border="0" />' : $txt[$value['text']]) . '</a>';

		$button_strip[$key] = $buttons[$key];
	}

	echo '
		<td ', $custom_td, '>', implode($context['menu_separator'], $button_strip) , '</td>';
}

?>