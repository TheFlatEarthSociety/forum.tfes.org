<?php
// Version 2.2.0; Sitemap

function template_xml_list()
{
	global $context, $scripturl;

	echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
	http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
	<url>
		<loc>', fix_possible_url($scripturl), '</loc>
		<lastmod>', $context['sitemap']['main']['time'], '</lastmod>
		<changefreq>always</changefreq>
		<priority>1.0</priority>
	</url>';

	foreach ($context['sitemap']['items'] as $item)
		echo '
	<url>
		<loc>', $item['url'], '</loc>
		<lastmod>', $item['time'], '</lastmod>
		<changefreq>', (empty($item['changefreq']) ? 'daily' : $item['changefreq']), '</changefreq>
		<priority>', $item['priority'], '</priority>
	</url>';

	echo '
</urlset>';
}

?>