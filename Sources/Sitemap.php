<?php
/**********************************************************************************
* Version 2.2.0 Sitemap.php                                                       *
***********************************************************************************
* Modification by:                Matt Zuba (http://www.mattzuba.com)			  *
* Copyright 2009-2010 by:      AirRideTalk.com (http://www.airidetalk.com)		  *
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/

// No Direct Access!
if (!defined('SMF'))
	die('Hacking attempt...');

// Main function that determines what we will view
function ShowSiteMap()
{
	global $context, $scripturl, $settings, $txt, $user_info, $modSettings, $smcFunc, $sourcedir, $mbname;

	// Set the page title
	$context['page_title'] = $txt['sitemap'];

	$context['linktree'][] = array(
		'url' => $scripturl . '?action=sitemap',
		'name' => $txt['sitemap'],
	);

	// Load integration info...
	if (($context['sitemap']['extensions'] = cache_get_data('sitemap_extensions', 3600)) == null)
	{
		$ext_dir = $sourcedir . '/Sitemap-Ext';
		$context['sitemap']['extensions'] = array();
		if (is_readable($ext_dir))
		{
			$dh = opendir($ext_dir);
			while ($filename = readdir($dh))
			{
				// Skip these
				if (in_array($filename, array('.', '..')) || preg_match('~^sitemap_([a-zA-Z_-]+)\.php~', $filename, $match) == 0)
					continue;

				if (@include_once($ext_dir . '/' . $filename))
					$context['sitemap']['extensions'][$match[1]] = array($filename, 'has_display' => function_exists(ucwords($match[1]) . 'Display'), 'has_xml' => function_exists(ucwords($match[1]) . 'XML'));
			}
		}

		cache_put_data('sitemap_extensions', $context['sitemap']['extensions'], 3600);
	}

	$subAction = isset($_REQUEST['sa']) && !empty($context['sitemap']['extensions'][$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'boards';

	// Check to see if we're viewing topics or the boards or xml sitemap
	if (isset($_REQUEST['xml']))
		return XMLDisplay();
	else
	{
		if (!$context['sitemap']['extensions'][$subAction]['has_display'])
			fatal_lang_error('invalid_sitemap_subaction');

		include_once($sourcedir . '/Sitemap-Ext/' . $context['sitemap']['extensions'][$subAction][0]);
		$displayFunction = ucwords($subAction) . 'Display';
		$listOptions = $displayFunction();
	}

	addAdditionalRows($listOptions);

	$listOptions['id'] = 'sitemap_list';
	$listOptions['title'] = '<a href="' . $scripturl . '">' . $mbname . ' - ' . $txt['sitemap'] . '</a>';
	$listOptions['base_href'] = $scripturl . '?action=sitemap' . ($subAction != 'boards' ? ';sa=' . $subAction : '');

	require_once($sourcedir . '/Subs-List.php');
	createList($listOptions);
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'sitemap_list';
}

function XMLDisplay()
{
	global $context, $user_info, $modSettings, $smcFunc, $sourcedir, $scripturl;

	// Load the proper template
	loadtemplate('Sitemap');
	$context['sub_template'] = 'xml_list';

	require_once($sourcedir . '/News.php');

	// Setup the main forum url...
	$context['sitemap']['main'] = array('time' => date_iso8601());

	// Fixup the query_see_board so it only displays what guests would see
	$old_see_board = $user_info['query_see_board'];
	$user_info['query_see_board'] = '(FIND_IN_SET(-1, b.member_groups) != 0)';

	$context['sitemap']['items'] = array();

	// Extensions?
	foreach ($context['sitemap']['extensions'] as $ext => $info)
	{
		if (!$info['has_xml'])
			continue;

		include_once($sourcedir . '/Sitemap-Ext/' . $info[0]);
		$xmlFunction = ucwords($ext) . 'XML';
		$context['sitemap']['items'] = array_merge($context['sitemap']['items'], $xmlFunction());
	}

	$user_info['query_see_board'] = $old_see_board;

    // Prettify any URLs
    if (!empty($modSettings['pretty_enable_filters']))
    {
        $context['pretty']['search_patterns'][] = '~(<loc>)([^#<]+)~';
        $context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';
    }
}

function addAdditionalRows(&$listOptions)
{
	global $scripturl, $txt, $modSettings, $context;

	$buttonList = array();

	foreach ($context['sitemap']['extensions'] as $subAction => $dummy)
	{
		if ($dummy['has_display'])
			$buttonList[] = '<li><a href="' . $scripturl . '?action=sitemap;sa=' . $subAction . '"><span>' . $txt['sitemap_' . $subAction] . '</span></a></li>';
	}

	if (!empty($modSettings['sitemap_xml']) || $context['user']['is_admin'])
		$buttonList[] = '<li><a class="active" href="' . $scripturl . '?action=sitemap;xml"><span>XML</span></a></li>';

	$listOptions['additional_rows'] = array(
		array(
			'position' => 'bottom_of_list',
			'value' => '
				<div class="buttonlist">
					<ul>' . implode('
						', $buttonList) . '
					</ul>
				</div>',
		),
	);
}

function date_iso8601($timestamp = '')
{
	$timestamp = empty($timestamp) ? time() : $timestamp;
	$gmt = substr(date("O", $timestamp), 0, 3) . ':00';
	return date('Y-m-d\TH:i:s', $timestamp) . $gmt;
}

function priority($timestamp)
{
	global $modSettings;
	// Get the last day the topic/board was updated
	$diff = floor((time() - $timestamp)/60/60/24);
	if ($diff <= 30)
		return $modSettings['sitemap_30day_priority'];
	else if ($diff <= 60)
		return $modSettings['sitemap_60day_priority'];
	else if ($diff <= 90)
		return $modSettings['sitemap_90day_priority'];
	else
		return $modSettings['sitemap_91day_priority'];
}

function changefreq($timestamp, $replies)
{
	// How often is it updated?  How about the difference between now and when it was first started
	// divided by how many posts it has...
	// Seconds per post
	$freq = floor((time() - $timestamp)) / ($replies+1);
	if ($freq < (24*60*60))
		return 'hourly';
	elseif ($freq < (24*60*60*7))
		return 'daily';
	elseif ($freq < (24*60*60*7*(52/12)))
		return 'weekly';
	elseif ($freq < (24*60*60*365))
		return 'monthly';
	else
		return 'yearly';
}