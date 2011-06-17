<?php
// Style Options
class sbc_style_settings extends sb_settings {
	public $name = 'Style Options';
	public $slug = 'style_options';
	public $page = 'sb_style';
	public $location = 'primary';
	public $priority = 'high';
	public $options = array(
			'styleselect' => array(
					'type'		=> 'select',
					'label'		=> 'Select Stylesheet',
					'options'	=> array(
						'default'	=> 'Default',
						'red'		=> 'Red',
						'green'		=> 'Green',
						'blue'		=> 'Blue'
						),
					'default'	=> 'default'
				)
		);
	
	// Use this method to control output. Note: you can add as many other functions as you need.
	public function output() {
		$style = sb_get_option('styleselect');
		if ($style == 'red') { wp_enqueue_style( 'child-red', THEME_URI . '/styles/red.css' ); }
		elseif ($style == 'green') { wp_enqueue_style( 'child-green', THEME_URI . '/styles/green.css' ); }
		elseif ($style == 'blue') { wp_enqueue_style( 'child-blue', THEME_URI . '/styles/blue.css' ); }
	}
	
	// For this method to hook all your functions somewhere. Uncomment the line below to add output to sb_header
	public function hooks() {
		add_action( 'template_redirect', array( $this, 'output' ) ); // 'output' is the name of the function we want to add.
	}

}	
//sb_register_settings('sbc_style_settings');
?>