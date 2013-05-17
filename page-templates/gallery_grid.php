<?php
/*
 * Template name: Gallery Plus Text
 */

get_header();

//Basically what the_content() does, without the echo
if (have_posts())
	the_post();
$content = get_the_content(null, false);
$content = apply_filters('the_content', $content);
$content = str_replace(']]>', ']]&gt;', $content);
$content = "<h2 class=\"entry-title\">" . get_the_title() . "</h2>" . $content;

$pages = get_pages( array(
			  'post_status' => 'publish',
			  'parent' => get_the_ID(),
			  'hierarchical' => 0
			  ));
$pages = priority_sort( $pages, true, false );

$content_put = false;
$count = 0;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="entry-content">
	<div id="tauko_boxes">
	<?php
	if (!empty($pages)) {
		while (count($pages) > 0) {
			$page = array_shift($pages);
		
			$img_str = get_the_post_thumbnail($page->ID, array(200, 9999), 
							  array('class' => "attachment-$size tauko_box-picture"));
			if  (++$count > 1 && !$content_put) {
				if (rand(0,1)) {
					tauko_box($content, array( 'class' => 'gallery_text', 
					                           'color' => get_page_color( get_the_ID() )));
					$content_put = true;
				}
			}
			if ( count($pages) == 1 && !$content_put) {
				tauko_box($img_str, array( 'link_href' => get_permalink($page->ID ), 
				                           'color' => get_page_color( get_the_ID() ),
							   'title' => get_the_title($page->ID)
  				                         ));
				tauko_box($content, array( 'class' => 'gallery_text', 'color' => get_page_color( get_the_ID()) ));
				$content_put = true;
				continue;
			}
			tauko_box($img_str, array( 'link_href' => get_permalink($page->ID ), 
			                           'color' => get_page_color( get_the_ID() ),
				                   'title' => get_the_title($page->ID)
						   ));
		}
	} else {
		tauko_box($content, array( 'class' => 'gallery_text', 'color' => get_page_color( get_the_ID()) ));
	}
	?>
	</div>
	 <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
  </div><!-- .entry-content -->
  <footer class="entry-meta">
    <?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
  </footer><!-- .entry-meta -->
</article><!-- #post -->

<?php get_footer();?>