<?php
header("Content-Type: text/css");

$top_pages = get_automatic_pages();

foreach ($top_pages as $page) {
	printf("page-item-%d { background-color: %s }\n", $page->ID, get_page_tauko_color($page->ID));
}

?>

