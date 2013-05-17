<?php
/*
 * @package Tauko
 */

$box_count = get_theme_mod('box_count');

$pages = get_pages( array(
			  'post_status' => 'publish'
			  ));

$pages = priority_sort( $pages, true, false );

$pages = array_slice($pages, 0, $box_count);

echo "<div id=\"tauko_boxes\">";

foreach ( $pages as $page ) {
	$content = get_the_post_thumbnail($page->ID, array(200, 9999), array(
		    'class' => "attachment-$size tauko_box-picture"));
	tauko_box($content, array( 'color' => get_page_color( $page->ID ),
			           'link_href' => get_permalink( $page->ID ),
				   'title' => get_the_title( $page->ID)));
}

echo "</div>"

?>