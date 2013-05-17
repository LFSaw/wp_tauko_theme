<?php
/*
 * @package Tauko
 */

add_theme_support('post-thumbnails', array('page'));
set_post_thumbnail_size(200, 9999, false);

add_image_size('lightbox-size', 800,650, false);

function home_icon( $items )
{
	$home_icon = '<div style="display: block; position: absolute; bottom: -25px;"> <a href="' . home_url() . '"><img height="100" src="' . get_stylesheet_directory_uri() . '/img/homeicon.png" /></a></div>';
	
	$items = $home_icon . $items;

	return $items;
}
add_filter('wp_page_menu', 'home_icon', 10, 1);

function uppercase_title( $title )
{
	$title = strtoupper($title);

	return $title;
}
add_filter('the_title', 'uppercase_title');

/*
 ************************ COLOR
 */

function color_metabox() {
	add_meta_box( 'color_metabox', 
		      'Tauko custom color', 
		      'color_metabox_innards', 
		      'page');
}
add_action( 'add_meta_boxes', 'color_metabox');

function color_metabox_innards( $post ) {

	wp_create_nonce( plugin_basename( __FILE__ ) );
	wp_nonce_field( plugin_basename( __FILE__ ), 'tauko_nonce' );

	$value = get_post_meta( $post->ID, '_page_color', true );
	echo '<label for="color_field">';
	echo 'Set a color for this page and its children.';
	echo '</label> ';
	echo '<input type="text" id="color_field" name="color_field" value="'.esc_attr($value).'" size="25" />';
}

function color_save( $post_id ) {

  // First we need to check if the current user is authorised to do this action. 
  if ( 'page' == $_POST['post_type'] ) {
    if ( ! current_user_can( 'edit_page', $post_id ) )
        return;
  }

  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['tauko_nonce'] ) || ! wp_verify_nonce( $_POST['tauko_nonce'], plugin_basename( __FILE__ ) ) )
      return;


  // Thirdly we can save the value to the database

  //if saving in a custom table, get post_ID
  $post_ID = $_POST['post_ID'];
  //sanitize user input
  $mydata = sanitize_text_field( $_POST['color_field'] );

  add_post_meta($post_ID, '_page_color', $mydata, true) or
    update_post_meta($post_ID, '_page_color', $mydata);
}
add_action( 'save_post', 'color_save');

function get_page_color( $post_id ) {
	$color = get_post_meta( $post_id, '_page_color', true );

	$page_ancestors = get_post_ancestors( $post_id );

	if ( empty( $color ) && 
	     empty( $page_ancestors )) {
		return "#000";
	} else if ( ! empty( $page_ancestors )) {
		$page_ancestors = array_reverse($page_ancestors);
		return get_page_color( $page_ancestors[0] );
	} else {
		return $color;
	}
}

function page_color_alt_shade( $color ) {
	/*
	 * Should somehow decide wether or not the color should be brightened, or dimmed.
	 * Probably based on some heuristic about when 'too much information has been lost'.
	 */
	
	$c = str_split($color);
	$INC = 3;

	for ($i = 0; $i < count($c); ++$i) {
		$h = 0;
		while ($h++ < $INC) {
			if ($c[$i] == '9')
				$c[$i] = 'a';
			else if ($c[$i] == 'f' ||
				 $c[$i] == 'F')
				continue;
			else
				$c[$i]++;

		}
	}

	$color = implode("", $c);
	
	return $color;
}

//Sort of hackish way of adding color to site nav
function page_item_colors()
{
	$top_pages = get_automatic_pages();

	echo "<style type=\"text/css\">\n";
	foreach ($top_pages as $page) {
		printf("li.page-item-%d { background-color: %s; }\n", $page->ID, get_page_color($page->ID));
		printf(".page-item-%d li:hover > ul.children > li { background-color: %s; }\n", $page->ID, get_page_color($page->ID));
		printf(".page-item-%d ul.children > li:hover { background-color: %s; }\n", $page->ID, get_page_color($page->ID));
		printf(".page-item-%d ul.children > li { background-color: %s; }\n", 
		       $page->ID, 
		       page_color_alt_shade(get_page_color($page->ID)));
	}
	echo "</style>\n";
}
add_action('wp_head', 'page_item_colors');

include_once("page_priority.php");
include_once("boxes.php");

?>
