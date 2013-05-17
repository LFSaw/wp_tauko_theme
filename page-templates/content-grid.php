<?php
/*
 * Template name: Content-Grid
 */

get_header();

//Basically what the_content() does, without the echo
if (have_posts())
	the_post();
$content = get_the_content(null, false);
$content = apply_filters('the_content', $content);
$content = str_replace(']]>', ']]&gt;', $content);
$content = "<h2 class=\"entry-title\">" . get_the_title() . "</h2>" . $content;

//Ideally we could use something like this, but I'm not yet sure
//how add_image_size works.
//$images = get_multi_images_src('medium', 'lightbox-size');
//So there.
$images = get_multi_images_src('medium', 'large');
$image_ids = get_images_ids();
$content_put = false;
$count = 0;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="entry-content">
	<div id="tauko_boxes">
	<?php
	if (!empty($images)) {
		while (count($images) > 0) {
			$img = array_shift($images);
			$img_id = array_shift($image_ids);
			$img_obj = get_post( $img_id );

			$lbox_text = $img_obj->post_content;
			$lbox_text = apply_filters('the_content', $lbox_text);
			//$lbox_text = str_replace(']]>', ']]&gt;', $lbox_text);

			// Black magic.
			//$lbox_text = str_replace('<', '&amp;lt;', $lbox_text);
			//$lbox_text = str_replace('>', '&amp;gt;', $lbox_text);
			$lbox_text = str_replace('"', '&amp;quot;', $lbox_text);


			$img_str = sprintf("<a href=\"%s\" rel=\"lightbox-%s\" title=\"%s\"><img title=\" \" width=\"%s\" src=\"%s\" /></a>", 
					   $img[1][0], 
					   get_the_ID(),
					   $lbox_text,
					   200,
					   $img[0][0]);
			if  (++$count > 1 && !$content_put) {
				if (rand(0,1)) {
					tauko_box($content, array( 'class' => 'content_grid_text' ));
					$content_put = true;
				}
			}
			if ( count($images) == 1 && !$content_put) {
				tauko_box($img_str);
				tauko_box($content, array( 'class' => 'content_grid_text' ));
				$content_put = true;
				continue;
			}
			tauko_box($img_str);
		}
	} else {
		tauko_box($content, array( 'class' => 'content_grid_text' ));
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