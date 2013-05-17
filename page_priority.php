<?php

function page_priority_activate()
{
	global $wpdb;
	$query_base = "UPDATE %s
                       SET menu_order = %d
                       WHERE ID = %d";
	
	$all_pages = get_all_pages();

	$prio = 1;
	foreach ($all_pages as $page) {
		//delete_post_meta($page->ID, '_page_priority');
		/*
		if (get_page_priority($page->ID))
			continue;
		
		$prio = valid_priority($page->ID, $prio);
		*/
		add_post_meta($page->ID, '_page_priority', $prio, true);
		$query = sprintf($query_base, $wpdb->posts, $prio++, $page->ID);
		$wpdb->query($query);
	}
}
//register_activation_hook( __FILE__, 'page_priority_activate' );
//Maybe this is enough?
add_action('setup_theme', 'page_priority_activate');

function page_priority_deactivate()
{
	global $wpdb;
	$query_base = "UPDATE %s
                       SET menu_order = %d
                       WHERE ID = %d";

	$all_pages = get_all_pages();
	foreach ($all_pages as $page) {
		delete_post_meta($page->ID, '_page_priority');
		$query = sprintf($query_base, $wpdb->posts, 0, $page->ID);
		$wpdb->query($query);
	}
}
//register_deactivation_hook( __FILE__, 'page_priority_deactivate' );

function page_priority_box()
{
	add_meta_box( 'page_priority_box', 
		      'Page priority', 
		      'page_priority_box_innards', 
		      'page');
}
add_action( 'add_meta_boxes', 'page_priority_box' );

function page_priority_box_innards( $post )
{

	wp_create_nonce( plugin_basename( __FILE__ ) );
	wp_nonce_field( plugin_basename( __FILE__ ), 'page_priority_nonce' );

	$value = get_post_meta( $post->ID, '_page_priority', true );
	echo '<label for="page_priority_field">';
	echo 'Set page priority.';
	echo '</label> ';
	echo '<input type="text" id="page_priority_field" name="page_priority_field" value="'.esc_attr($value).'" size="25" />';
}

function get_automatic_pages()
{
	$top_pages = get_pages( array(
				      'post_status' => 'publish',
				      'parent' => 0));

	return $top_pages;
}

function get_other_pages()
{
	$all_pages = get_pages( array('post_status' => 'publish'));
	$top_pages = get_automatic_pages();
	$other_pages = array();

	foreach ($all_pages as $page) {
		$include = true;
		for ($i = 0; $i < count($top_pages); ++$i) {
			if ((int)$page->ID == (int)$top_pages[$i]->ID)
				$include = false;
		}
		if ($include)
			array_push($other_pages, $page);
	}

	return $other_pages;
}

function get_all_pages()
{
	$top_pages = get_automatic_pages();
	$other_pages = get_other_pages();

	$all_pages = array_merge($top_pages, $other_pages);

	return $all_pages;
}

function valid_priority( $post_ID, $priority) {
	if ( prio_is_automatic($post_ID) ) {
		$HIGHEST_PRIORITY = 1;
		$LOWEST_PRIORITY = get_lowest_automatic_priority();
		if ($LOWEST_PRIORITY == 0)
			$LOWEST_PRIORITY = 1;
	} else {
		$HIGHEST_PRIORITY = get_lowest_automatic_priority() + 1;
		$LOWEST_PRIORITY = 1000;
	}

	if ($priority < $HIGHEST_PRIORITY)
		return $HIGHEST_PRIORITY;
	else if ($priority > $LOWEST_PRIORITY)
		return $LOWEST_PRIORITY;

	return $priority;
}

function get_lowest_automatic_priority()
{
	$top_pages = get_automatic_pages();

	if ( empty($top_pages))
		return 0;
	else {
		$top_pages = priority_sort($top_pages, false);
		$prio = get_page_priority($top_pages[0]->ID);
		return $prio;
	}
}

function prio_is_automatic($post_ID)
{
	$page = get_page($post_ID);

	if ( $page->post_parent == 0 )
		return true;

	return false;
}

function page_priority_save( $post_id )
{
  global $wpdb;
  $query_base = "UPDATE %s
                 SET menu_order = %d
                 WHERE ID = %d";


  // First we need to check if the current user is authorised to do this action. 
  if ( 'page' == $_POST['post_type'] ) {
    if ( ! current_user_can( 'edit_page', $post_id ) )
        return;
  }

  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['page_priority_nonce'] ) || ! wp_verify_nonce( $_POST['page_priority_nonce'], plugin_basename( __FILE__ ) ) )
      return;


  // Thirdly we can save the value to the database

  //if saving in a custom table, get post_ID
  $post_ID = $_POST['post_ID'];
  //sanitize user input
  $mydata = sanitize_text_field( $_POST['page_priority_field'] );

  if ( $_POST['post_status'] == 'publish' ) {
	  $all_pages = priority_sort(get_all_pages(), true, false);

	  if ( empty( $mydata)) {
		  if ( empty( $all_pages ))
			  $mydata = 1;
		  else {
			  $prio = 10000;
			  $prio = valid_priority($post_ID, $prio);
			  if (priority_taken($post_ID, $prio)) {
				  delete_post_meta($post_ID, '_page_priority');
				  free_priority($prio);
			  }
		  }
	  } else {
		  $prio = valid_priority($post_ID, $mydata);
		  if (priority_taken($post_ID, $prio)) {
			  delete_post_meta($post_ID, '_page_priority');
			  free_priority($prio);
		  }
	  }

	  add_post_meta($post_ID, '_page_priority', $prio, true) or
		  update_post_meta($post_ID, '_page_priority', $prio);

	  $query = sprintf($query_base, $wpdb->posts, $prio, $post_ID);
	  $wpdb->query($query);
  }
}
add_action( 'save_post', 'page_priority_save' );

function priority_taken($post_ID, $priority)
{
	$all_pages = get_all_pages();

	foreach ($all_pages as $page) {
		$prio = get_page_priority($page->ID);
		if (!$prio)
			continue;
		if ( $prio == $priority &&
		     $page->ID == $post_ID)
			return false;
		if ( $prio == $priority &&
		     $page->ID != $post_ID)
			return true;
	}
}

function free_priority($priority)
{
	$all_pages = priority_sort(get_all_pages());
	$passed = false;
	global $wpdb;
	$query_base = "UPDATE %s
                       SET menu_order = %d
                       WHERE ID = %d";

	foreach ($all_pages as $page) {
		$prio = get_page_priority($page->ID);
		if (!$prio || $prio < $priority)
			continue;
	        if ($passed && (int)$prio == (int)$prev_prio) {
			update_post_meta($page->ID, '_page_priority', ++$prio);
			$query = sprintf($query_base, $wpdb->posts, $prio, $page->ID);
			$wpdb->query($query);
		} else if ((int)$prio == (int)$priority) {
			$passed = true;
			update_post_meta($page->ID, '_page_priority', ++$prio);
			$query = sprintf($query_base, $wpdb->posts, $prio, $page->ID);
			$wpdb->query($query);
		}

		$prev_prio = $prio;
	}
}

function page_priority_trashed( $post_id )
{
	global $wpdb;
	$query_base = "UPDATE %s
                       SET menu_order = %d
                       WHERE ID = %d";

	$prio = get_page_priority( $post_id );
	if ( ! $prio )
		return;
	else {
		delete_post_meta( $post_id, '_page_priority' );
		$query = sprintf($query_base, $wpdb->posts, 0, $post_ID);
		$wpdb->query($query);
	}
}
add_action( 'trashed_post', 'page_priority_trashed' );

function page_priority_untrashed( $post_ID )
{
	global $wpdb;
	$query_base = "UPDATE %s
                       SET menu_order = %d
                       WHERE ID = %d";
	$untrashed_page = get_page( $post_ID );

	// Priorities are only given to published pages.
	if ( $untrashed_page->post_status != 'publish' )
		return;

	$prio = valid_priority($post_ID, 10000);
	if (priority_taken($post_ID, $prio)) {
		free_priority($prio);
		add_post_meta($post_ID, '_page_priority', $prio, true);
		$query = sprintf($query_base, $wpdb->posts, $prio, $post_ID);
		$wpdb->query($query);
	}
}
add_action( 'untrashed_post', 'page_priority_untrashed' );

function get_page_priority( $page_id )
{
	$prio = get_post_meta( $page_id, '_page_priority', true );

	if ( empty( $prio ))
		return 0;
	else
		return (int) $prio;
}

function priority_sort( $pages, $asc = true, $discard_invalid = false)
{
	$invalid_prios = array();
	$i = 0; 

	while ( $i++ < count($pages) ) {
		$atom = array_shift($pages);
		if ( ! get_page_priority( $atom->ID ))
			array_push( $invalid_prios, $atom );
		else
			array_push( $pages, $atom );
	}

	if ( ! empty($pages))
		for ($i = 0; $i < count($pages); ++$i) {
			for ($h = $i + 1; $h < count($pages); ++$h) {
				if ( get_page_priority($pages[$i]->ID) > get_page_priority($pages[$h]->ID)) {
					$tmp = $pages[$i];
					$pages[$i] = $pages[$h];
					$pages[$h] = $tmp;
				}
			}
		}

	if ( ! $asc )
		$pages = array_reverse( $pages );

	if ( ! empty($invalid_pages) && 
	     ! $discard_invalid)
		$pages = array_merge( $pages, $invalid_prios );

	return $pages;
}

?>