<?php
/**
 * Functions to handle boards in the Sitemap
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
function BoardsDisplay()
{
	global $txt, $mbname, $scripturl;

	// Create the list
	$listOptions = array(
		'no_items_label' => $txt['sitemap_board_none'],
		'get_items' => array(
			'function' => 'list_getBoards',
		),
		'columns' => array(
			'board' => array(
				'header' => array(
					'value' => $txt['board'],
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="' . $scripturl . '?board=%d.0">%s</a>',
						'params' => array(
							'id_board' => false,
							'name' => false,
						),
					),
				),
			),
			'subject' => array(
				'header' => array(
					'value' => $txt['subject'],
				),
				'data' => array(
					'db' => 'description',
				),
			),
			'topics' => array(
				'header' => array(
					'value' => $txt['topics'],
				),
				'data' => array(
					'db' => 'num_topics',
					'style' => 'text-align: center;',
				),
			),
			'posts' => array(
				'header' => array(
					'value' => $txt['posts'],
				),
				'data' => array(
					'db' => 'num_posts',
					'style' => 'text-align: center;',
				),
			),
		),
	);

	return $listOptions;
}

function BoardsXML()
{
	global $modSettings, $scripturl;

	// Get our information from the database
	if (($boards = cache_get_data('xml_boards', $modSettings['sitemap_cache_ttl'])) == null)
	{
		$temp_boards = list_getBoards(0, 0, 'm.poster_time DESC');
		// And assign it to an array
		$boards = array();
		foreach ($temp_boards as $row)
		{
			$boards[] = array(
				'url' => fix_possible_url($scripturl . '?board=' . $row['id_board'] . '.0'),
				'time' => date_iso8601($row['poster_time']),
				'priority' => priority($row['poster_time']),
			);
		}
		cache_put_data('xml_boards', $boards, $modSettings['sitemap_cache_ttl']);
	}

	return $boards;
}

function list_getBoards($start, $items_per_page, $sort)
{
	global $smcFunc;

	$request = $smcFunc['db_query']('','
		SELECT b.id_board, b.id_parent, b.name, b.description, b.num_topics, b.num_posts, m.poster_time
		FROM {db_prefix}boards as b
			LEFT JOIN {db_prefix}messages as m ON (m.id_msg = b.id_last_msg)
		WHERE {query_see_board}
		ORDER BY {raw:sort}',
		array(
			'sort' => $sort == '1=1' ? 'b.board_order' : $sort,
		)
	);

	$boards = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$boards[] = $row;
	$smcFunc['db_free_result']($request);

	return $boards;

}