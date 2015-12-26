<?php

if (!defined('SMF'))
	die('Hacking attempt...');

function addnobbcButton(&$bbc_tags)
{
	global $txt;

	$temp = array();
	foreach ($bbc_tags[1] as $tag)
	{
		$temp[] = $tag;

		if (isset($tag['image']) && $tag['image'] == 'code')
		{
			$temp[] = array(
				'image' => 'nobbc',
				'code' => 'nobbc',
				'before' => '[nobbc]',
				'after' => '[/nobbc]',
				'description' => $txt['nobbc'],
				'fa-icon' => 'fa-code',
			);

			$temp[] = array();
		}
	}

	$bbc_tags[1] = $temp;
}

?>