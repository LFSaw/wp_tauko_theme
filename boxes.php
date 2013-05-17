<?php

function tauko_box( $content, $settings = array() )
{
	if ( empty($content) )
		return;
	if ( isset($settings['title']))
		$content .= sprintf("\n<span class=\"tauko_box_head\" style=\"background-color: %s\">%s</span>",
			(isset($settings['color'])) ? $settings['color'] : get_page_color(get_the_ID()),
			$settings['title']);

	$style = '';
	if ( !isset($settings['color']) )
		$style .= sprintf("border-color: %s;", get_page_color(get_the_ID()));
	else
		$style .= sprintf("border-color: %s;", $settings['color']);
	if ( isset($settings['width']) )
		$style .= sprintf("width: %spx;", $settings['width']);
				 

	$class = 'tauko_box';
	if ( isset($settings['class']) )
		$class .= sprintf(" %s", $settings['class']);
	
	
	if ( isset($settings['link_href']) )
		printf("<a href=\"%s\">", $settings['link_href']);
	printf("<div class=\"%s\" style=\"%s\">", $class, $style);
	echo $content;
	printf("</div>");
	if ( isset($settings['link_href']) )
		echo "</a>";
}

new theme_customizer();
class theme_customizer
{
    public function __construct()
    {
	    //add_action ('admin_menu', array(&$this, 'customizer_admin'));
        add_action( 'customize_register', array(&$this, 'customize_manager' ));
    }
    /**
     * Add the Customize link to the admin menu
     * @return void
     */
    public function customizer_admin() {
        add_theme_page( 'Customize', 'Customize', 'edit_theme_options', 'customize.php' );
    }
    /**
     * Customizer manager demo
     * @param  WP_Customizer_Manager $wp_manager
     * @return void
     */
    public function customize_manager( $wp_manager )
    {
        $this->customize_section( $wp_manager );
    }
    public function customize_section( $wp_manager )
    {
        $wp_manager->add_section( 'boxes_settings_section', array(
            'title'          => 'Boxes',
            'priority'       => 35,
        ) );

	$wp_manager->add_setting( 'box_count', array(
	    'default' => 10
	) );
	$wp_manager->add_control( 'box_count', array(
	    'label' => 'Box count',
	    'section' => 'boxes_settings_section',
	    'type' => 'text',
	    'priority' => 1
	) );
	/*
	 * Pointless now.
	$wp_manager->add_setting( 'box_style', array(
	    'default' => 'color_boxes'
	) );
	$wp_manager->add_control( 'box_style', array(
	    'label' => 'Box style',
	    'section' => 'boxes_settings_section',
	    'type' => 'radio',
	    'choices' => array('color_boxes' => __('Color boxes'),
			       'pict_boxes' => __('Picture boxes')),
	    'priority' => 2
	) );
	*/
    }
}

?>
