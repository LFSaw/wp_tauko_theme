<?php
/*
 * @package Tauko
 */

$pages = get_pages( array(
			  'post_status' => 'publish',
			  'parent' => get_the_ID(),
			  'hierarchical' => 0
			  ));

$pages = priority_sort( $pages, true, false );

tauko_boxes($pages);

?>