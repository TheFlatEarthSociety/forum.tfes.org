<?php
/**
 * Functions to handle topics in the Sitemap
 * 
 * @author Matt Zuba
 * @website http://www.mattzuba.com
 * @mod Core
 */


/**
 * This function creates an array of $listOptions which will then be passed
 * to the list creator in SMF's Subs-List createList function.
 * The indicies that will be overloaded are 'id', 'title', 'base_href' and
 * 'additional_rows'.  You may included any others you like/need.
 *
 * @return array Compiled array of list options to pass to the list creator
 */
function TopicsDisplay()
{
	global $scripturl, $txt, $mbname;

	// Create the list
	$listOptions = array(
		'items_per_page' => 100,
		'no_items_label' => $txt['sitemap_topic_none'],
		'get_items' => array(
			'function' => 'list_getTopics',
		),
		'get_count' => array(
			'function' => 'list_getNumTopics',
		),
		'columns' => array(
			'subject' => array(
				'header' => array(
					'value' => $txt['topic'],
				),
				'data' => array(
					'sprintf' => array(
						'format' => '[<a href="' . $scripturl . '?board=%d.0">%s</a>] <a href="' . $scripturl . '?topic=%d.0">%s</a>',
						'params' => array(
							'id_board' => false,
							'name' => false,
							'id_topic' => false,
							'subject' => false,
						),
					),
				),
			),
			'poster' => array(
				'header' => array(
					'value' => $txt['started_by'],
				),
				'data' => array(
					'db' => 'poster',
				),
			),
			'views' => array(
				'header' => array(
					'value' => $txt['views'],
				),
				'data' => array(
					'db' => 'num_views',
					'style' => 'text-align: center;',
				),
			),
			'replies' => array(
				'header' => array(
					'value' => $txt['replies'],
				),
				'data' => array(
					'db' => 'num_replies',
					'style' => 'text-align: center;',
				),
			),
		),
	);

	return $listOptions;
}

/**
 * This function creates an array of items which will then be passed
 * to the xml output template.  Each item of the array returned should
 * have the following information:
 *	array(
 *		'url' => the URL to the item in question,
 *		'time' => the last time this was updated in iso8601 date format,
 *		'priority' => the priority of this item,
 *		'changefreq' => the frequency this item changes (Optional),
 *	);
 *
 * date_iso8601() and priority() both take unix timestamps as arguments
 * changefreq() takes a unix timestamp and number of updates as arguments
 *
 * It is heavily recommended to cache this data.  This data should also only
 * reflect what a guest would see.  $user_info['query_see_board'] is already
 * set to what a guest would have.
 *
 * @return array Compiled array of options
 */
function TopicsXML()
{
	global $modSettings, $scripturl;

	if (($topics = cache_get_data('xml_topics', $modSettings['sitemap_cache_ttl'])) == null)
	{
		$temp_topics = list_getTopics(0, $modSettings['sitemap_topic_count'], 'm.poster_time DESC');
		// Assign it to the array
		$topics = array();
		foreach ($temp_topics as $row)
		{
			$topics[] = array(
				'url' => fix_possible_url($scripturl . '?topic=' . $row['id_topic'] . '.0'),
				'time' => date_iso8601($row['poster_time']),
				'priority' => priority($row['poster_time']),
				'changefreq' => changefreq($row['first_time'], $row['num_replies']),
			);
		}
		cache_put_data('xml_topics', $topics, $modSettings['sitemap_cache_ttl']);
	}

	return $topics;
}

function list_getTopics($start, $items_per_page, $sort)
{
	global $smcFunc;

	$query_limit = !empty($items_per_page) ? 'LIMIT {int:start}, {int:per_page}' : '';
	$query_limit_params = !empty($items_per_page) ? array('start' => $start, 'per_page' => $items_per_page) : array();

	$request = $smcFunc['db_query']('','
		SELECT t.id_topic, t.num_replies, t.num_views, t.id_board,
		m.subject, IFNULL(mem.real_name, m.poster_name) as poster, b.name,
		m.poster_time as first_time, mes.poster_time
		FROM {db_prefix}topics as t
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = t.id_first_msg)
			INNER JOIN {db_prefix}boards as b ON (b.id_board = t.id_board)
			INNER JOIN {db_prefix}messages as mes ON (mes.id_msg = t.id_last_msg)
			LEFT JOIN {db_prefix}members as mem ON (mem.id_member = t.id_member_started)
		WHERE {query_see_board}
		ORDER BY {raw:sort}
		' . $query_limit,
		array_merge($query_limit_params, array(
			'sort' => $sort == '1==1' ? 't.id_topic DESC' : $sort,
		))
	);

	$topics = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$topics[] = $row;
	$smcFunc['db_free_result']($request);

	return $topics;
}

function list_getNumTopics()
{
	global $smcFunc;

	// Get the total topics ($modSettings['totalTopics'] isn't reliable for us) and create the page index
	$request = $smcFunc['db_query']('','
		SELECT COUNT(*)
		FROM {db_prefix}topics as t
			INNER JOIN {db_prefix}boards as b ON (b.id_board = t.id_board)
		WHERE {query_see_board}'
	);

	list($numTopics) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	return $numTopics;
}
