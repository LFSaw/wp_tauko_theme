<?php
/*
 * Package: Tauko
 */
?>
<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() . "/js/nav_menu.js";?>"></script>
<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() . "/js/jquery.js";?>"></script>
<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() . "/js/slimbox2.js";?>"></script>
<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() . "/js/jquery.isotope.min.js";?>"></script>
<script type="text/javascript">
  $('#tauko_boxes').isotope({
  // options
  itemSelector : '.tauko_box',
  layoutMode : 'masonry',
  masonryHorizontal : {
  rowHeight : 410
  }
});
</script>

<?php wp_footer(); ?>
</div><!--/jelly-->
</body>
