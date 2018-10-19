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
	$settings['require_theme_strings'] = true;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>
<meta charset="' . $context['character_set'] . '"/>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/bootstrap.css?fin20" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/font-awesome.css?fin20" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Favicon
	echo
	'
	<link rel="shortcut icon" href="/favicon.ico" />';

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
	<script type="text/javascript" src="//assets.tfes.org/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/redsy.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/bootstrap.min.js?fin20"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$("input[type=button]").attr("class", "btn btn-default btn-sm");
		$(".button_submit").attr("class", "btn btn-danger btn-sm");
		$("#advanced_search input[type=\'text\'], #search_term_input input[type=\'text\']").removeAttr("size");
		$(".table_grid").addClass("table table-striped");
		$("img[alt=\'', $txt['new'], '\'], img.new_posts").replaceWith("<span class=\'label label-warning\'>', $txt['new'], '</span>");
		$("#profile_success").removeAttr("id").removeClass("windowbg").addClass("alert alert-success");
		$("#profile_error").removeAttr("id").removeClass("windowbg").addClass("alert alert-danger");
	});
	</script>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
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

	echo '
	<style type="text/css">
	@media (min-width: 768px)
	{
		.container {
			width: ' . $settings['forum_width'] . ';
		}
	}
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />';
	//Theme colour for mobile browsers
	echo '
	<meta name="theme-color" content="#4c798e">';

	//Here comes SexWarrior`s meta description bloat!
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
	// Robots, no roboting!
	if(!empty($context['robot_no_index']))
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
	if (!empty($context['current_topic']) && empty($context['current_action']))
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
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];
	// Include Google Analytics at the start of <body>
	echo '
</head>
<body', !empty($settings['redsy_navbar']) ? ' style="padding-top: 50px;"' :  '' ,'>

<script type="text/javascript">
	(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

	ga(\'create\', \'UA-48900853-2\', \'tfes.org\');
	ga(\'set\', \'anonymizeIp\', true);
	ga(\'send\', \'pageview\');
</script>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo'
<div id="wrapper">
	<nav class="navbar navbar-default ', !empty($settings['redsy_navbar']) ? 'navbar-fixed-top' :  'navbar-static-top' ,'">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<h1><a class="navbar-brand" href="' , $scripturl , '">', empty($context['header_logo_url_html_safe']) ? $context['forum_name'] : '<img class="logo" src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name'] . '" />', '</a></h1>
			</div>
			<div class="collapse navbar-collapse">
				<button type="button" class="navbar-toggle collapsed collapsemenu" id="upshrink">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>';
				if(!empty($context['user']['is_logged']))
				{
				echo'
					<ul class="nav navbar-nav navbar-right" id="profile_button">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<img class="avatar img-circle" src="', !empty($context['user']['avatar']['href']) ? $context['user']['avatar']['href'] : $settings['images_url']. '/noavatar.png' ,'" alt="*" />
								<span id="profile_username">', $context['user']['name'], '</span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="' , $scripturl , '?action=profile"><i class="fa fa-user fa-fw"></i>' , $txt['profile'] , '</a></li>
								<li class="divider"></li>
								<li><a href="' , $scripturl , '?action=profile;area=forumprofile;"><i class="fa fa-gear fa-fw"></i>' , $txt['edit_profile'] , '</a></li>
								<li><a href="' , $scripturl , '?action=profile;area=account;"><i class="fa fa-wrench fa-fw"></i>' , $txt['profile_account'] , '</a></li>
								<li><a href="' , $scripturl , '?action=unread;"><i class="fa fa-comment fa-fw"></i>' , $txt['new_post'] , '</a></li>
								<li><a href="' , $scripturl , '?action=unreadreplies;"><i class="fa fa-comments fa-fw"></i>' , $txt['new_replies'] , '</a></li>
								<li class="divider"></li>
								<li><a href="' , $scripturl , '?action=logout;sesc=', $context['session_id'], '"><i class="fa fa-sign-out fa-fw"></i>' , $txt['logout'] , '</a></li>
							</ul>
						</li>
					</ul>';
				} else { //If we're not logged in, let the user do so easily
				echo '
					<ul class="nav navbar-nav navbar-right" id="profile_button">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle">
								<img class="avatar img-circle" src="' . $settings['images_url']. '/noavatar.png' ,'" alt="*" />
								<span id="profile_username">', $txt['guest_title'] , '</span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
								<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
									<li><i id="usericon" class="fa fa-user fa-fw"></i><input type="text" class="userpass" name="user" placeholder="' , $txt['username'] , '"/></li>
									<li><i id="passicon" class="fa fa-lock fa-fw"></i><input type="password" class="userpass" name="passwrd" placeholder="', $txt['password'], '"/></li>
									<li id="always_logged_in"><label><input type="checkbox" id="stayloggedin" name="cookieneverexp"/>', $txt['always_logged_in'], '</label></li>
									<li id="quick_login_button"><input type="submit" value="', $txt['login'], '" class="button_submit" /> <a href="' , $scripturl , '?action=register" class="button_submit" style="color:#ffffff">' , $txt['register'] , '</a></li>
									<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
									<input type="hidden" name="hash_passwrd" value="" />
								</form>
							</ul>
						</li>
					</ul>';
				}
				echo'
				<ul class="nav-notification navbar-right">
					<li class="search-list">
						<div class="search-input-wrapper">
							<div class="search-input">
								<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">';
									// Search within current topic?
									if (!empty($context['current_topic']))
									{
										echo '
											<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
										$placeholder = "Search in this thread...";
									}
									// If we're on a certain board, limit it to this board ;).
									elseif (!empty($context['current_board']))
									{
										echo '
											<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';
										$placeholder = "Search in this board...";
									}
									else
										$placeholder = "Search...";
									echo '
									<input name="search" type="search" id="desktop_quicksearch" class="form-control input-sm inline-block" placeholder="' . $placeholder . '">
									<a href="#" class="input-icon text-normal">
										<i class="fa fa-search"></i>
									</a>';
									echo '
								</form>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</nav>';

	template_menu();

	// Define the upper_section toggle in JavaScript.
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			var oMainHeaderToggle = new smc_Toggle({
				bToggleEnabled: true,
				bCurrentlyCollapsed: ', empty($options['collapse_header']) ? 'false' : 'true', ',
				aSwappableContainers: [
					\'menu\'
				],
				aSwapTriggers: [
					{
						sId: \'upshrink\',
					}
				],
				oThemeOptions: {
					bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
					sOptionName: \'collapse_header\',
					sSessionVar: ', JavaScriptEscape($context['session_var']), ',
					sSessionId: ', JavaScriptEscape($context['session_id']), '
				},
				oCookieOptions: {
					bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
					sCookieName: \'upshrink\'
				}
			});
		// ]]></script>';

		echo'
	<header id="header">
		<div class="container">';
			pages_titlesdesc();
			theme_linktree();
		echo'
		</div>
	</header>';

	// The main content should go here.
	echo '
	<div class="container"><div id="content_section">
		<div id="main_content_section">';

	// Custom banners and shoutboxes should be placed here, before the linktree.

}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
		</div>
	</div></div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<footer><div class="container">
		<ul class="social">';
			if(!empty($settings['facebook_check']))
			echo'
			<li>
				<a href="', !empty($settings['facebook_text']) ? $settings['facebook_text'] : 'http://www.facebook.com ' ,'" title="', $txt['rs_facebook'], '"><i class="fa fa-twitter fa-2x"></i></a>
			</li>';
			if(!empty($settings['twitter_check']))
			echo'
			<li>
				<a href="', !empty($settings['twitter_text']) ? $settings['twitter_text'] : 'http://www.twitter.com' ,'"><i class="fa fa-facebook fa-2x"></i></a>
			</li>';
			if(!empty($settings['youtube_check']))
			echo'
			<li>
				<a href="', !empty($settings['youtube_text']) ? $settings['youtube_text'] : 'http://www.youtube.com' ,'"><i class="fa fa-youtube fa-2x"></i></a>
			</li>';
			if(!empty($settings['rss_check']))
			echo'
			<li>
				<a href="', !empty($settings['rss_text']) ? $settings['rss_text'] : $scripturl .'?action=.xml;type=rss' ,'"><i class="fa fa-rss fa-2x"></i></a>
			</li>';
			echo'
		</ul>
		<ul class="reset">
			<li>', theme_copyright(), '</li>
			<li>Theme by Blanko, based on Redsy by <a href="http://smftricks.com/" rel="nofollow">SMFTricks</a></li>
			<li>&copy; ', !empty($settings['redsy_copyright']) ? $settings['redsy_copyright'] : $context['forum_name'] ,'</li>
		</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

	echo '
	</div></footer>
	<div class="action-bar-mobile">
					<div class="search-list">
						<div class="search-input-wrapper">
							<div class="search-input">
								<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">';
									// Search within current topic?
									if (!empty($context['current_topic']))
									{
										echo '
											<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
										$placeholder = "Search in thread...";
									}
									// If we're on a certain board, limit it to this board ;).
									elseif (!empty($context['current_board']))
									{
										echo '
											<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';
										$placeholder = "Search in board...";
									}
									else
										$placeholder = "Search...";
									echo '
									<input name="search" type="search" id="mobile_quicksearch" class="form-control input-sm inline-block" placeholder="' . $placeholder . '">
									<div class="input-icon text-normal">
										<i class="fa fa-search mobile"></i>
									</div>';
									echo '
								</form>
							</div>
						</div>
					</div>
	</div>
	<a href="#" class="scroll-to-top hidden-print"><i class="fa fa-chevron-up fa-lg"></i></a>
	<a href="' , $scripturl , '" class="link-to-home"><i class="fa fa-home"></i></a>
</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
		<ol class="breadcrumb">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		echo '
			</li>';
	}
	echo '
		</ol>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<div id="menu">
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<div class="container">
				<div class="row">
					<ul class="nav navbar-nav">';

			foreach ($context['menu_buttons'] as $act => $button)
			{
				echo '
						<li id="button_', $act, '" class="', $button['active_button'] ? 'active ' : '', '', !empty($button['sub_buttons']) ? 'dropdown' : '', '">
							<a ', !empty($button['sub_buttons']) ? 'class="dropdown-toggle"' : '', ' href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
								', $button['title'], '', !empty($button['sub_buttons']) ? ' <span class="caret"></span>' : '' ,'
							</a>';
				if (!empty($button['sub_buttons']))
				{
					echo '
							<ul class="dropdown-menu" role="menu">';

					foreach ($button['sub_buttons'] as $childbutton)
					{
						echo '
								<li>
									<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
										<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
									</a>';
						// 3rd level menus :)
						if (!empty($childbutton['sub_buttons']))
						{
							echo '
									<ul>';

							foreach ($childbutton['sub_buttons'] as $grandchildbutton)
								echo '
										<li>
											<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
												<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
											</a>
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
				</div>
			</div>
		</div>
	</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><i class="fa fa-'.$key.' fa-fw"></i><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul class="nav nav-pills">',
				implode('', $buttons), '
			</ul>
		</div>
		<div class="buttonlist-mobile">
			<button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown">Actions</button>
			<ul class="dropdown-menu">',
				implode('', $buttons), '
			</ul>
		</div>';
}
function pages_titlesdesc()
{
	global $txt, $context, $scripturl, $settings, $modSettings, $options, $sourcedir, $topic, $topicinfo, $board_info, $board, $category;

	if(!empty($topic))
	{
		echo '
		<h2>', $context['subject'], '</h2>';
	}
	elseif(!empty($board))
	{
		echo '
		<h2>',$board_info['name'],'</h2>';
	}
	else
	{
		echo '
		<h2>',$context['page_title'],'</h2>';
	}
}
?>
