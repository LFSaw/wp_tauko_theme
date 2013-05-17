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

$smallest_width = 0;

echo "<div id=\"tauko_boxes\">";

foreach ( $pages as $page ) {
?>
	<a href="<?php echo get_permalink( $page->ID ); ?>">
		  <div class="tauko_box" style="border-color: <?php echo get_page_color( $page->ID ); ?>;">
		<?php echo get_the_post_thumbnail($page->ID, array(200, 9999), array(
		    'class' => "attachment-$size tauko_box-picture"
		    )); ?>
		  </div>
		</a>
<?php
}
echo "</div>";


//tauko_boxes($pages);

?>