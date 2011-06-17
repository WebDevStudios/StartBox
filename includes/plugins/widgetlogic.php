<?php
/*
Plugin Name: Widget Logic
Plugin URI: http://freakytrigger.co.uk/wordpress-setup/
Description: Control widgets with WP's conditional tags is_home etc
Author: Alan Trewartha
Version: 0.47
Author URI: http://freakytrigger.co.uk/author/alan/
*/

// Check to see if current theme supports Widget Logic && Widget Logic is not active
if( !current_theme_supports('sb-widgetlogic') || function_exists('widget_logic_widget_update_callback') )
	return;

if (!function_exists('widget_logic_widget_update_callback')) {
	add_filter( 'widget_update_callback', 'widget_logic_widget_update_callback', 10, 3); 

	// new in 2.8 - ajaxy update of a single widget
	function widget_logic_widget_update_callback($instance, $new_instance, $this_widget)
	{	$widget_id=$this_widget->id;
		if ( isset($_POST[$widget_id.'-widget_logic']))
		{	if((!$wl_options = get_option('widget_logic')) || !is_array($wl_options) ) $wl_options = array();
			$wl_options[$widget_id]=$_POST[$widget_id.'-widget_logic'];
			update_option('widget_logic', $wl_options);
		}
		return $instance;
	}

	add_action( 'sidebar_admin_setup', 'widget_logic_expand_control'); 

	function widget_logic_expand_control()
	{	global $wp_registered_widgets, $wp_registered_widget_controls;

		if((!$wl_options = get_option('widget_logic')) || !is_array($wl_options) ) $wl_options = array();

		// if we're just updating the widgets, just read in the widget logic settings - makes this WP2.5+ only i think
		if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) )
		{	foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id )
				if (isset($_POST[$widget_id.'-widget_logic']))
					$wl_options[$widget_id]=$_POST[$widget_id.'-widget_logic'];

			// clean up empty options (in PHP5 use array_intersect_key)
			$regd_plus_new=array_merge(array_keys($wp_registered_widgets),array_values((array) $_POST['widget-id']),array('widget_logic-options-filter', 'widget_logic-options-wp_reset_query'));
			foreach (array_keys($wl_options) as $key)
				if (!in_array($key, $regd_plus_new))
					unset($wl_options[$key]);
		}

		foreach ( $wp_registered_widgets as $id => $widget )
		{	if (!$wp_registered_widget_controls[$id])
					wp_register_widget_control($id,$widget['name'], 'widget_logic_empty_control');

			if (!array_key_exists(0,$wp_registered_widget_controls[$id]['params'])  || is_array($wp_registered_widget_controls[$id]['params'][0]))
				$wp_registered_widget_controls[$id]['params'][0]['id_for_wl']=$id;
			else
			{	// some older widgets put number in to params directly (which messes up the 'templates' in WP2.5)
				array_push($wp_registered_widget_controls[$id]['params'],$id);	
				$wp_registered_widget_controls[$id]['height']+=40;					// this is really a pre2.5 thing - discard?
			}

			// do the redirection
			$wp_registered_widget_controls[$id]['callback_wl_redirect']=$wp_registered_widget_controls[$id]['callback'];
			$wp_registered_widget_controls[$id]['callback']='widget_logic_extra_control';		
		}


		// check the 'widget content' filter option
		if ( isset($_POST['widget_logic-options-submit']) )
		{	$wl_options['widget_logic-options-filter']=$_POST['widget_logic-options-filter'];
			$wl_options['widget_logic-options-wp_reset_query']=$_POST['widget_logic-options-wp_reset_query'];
		}
		update_option('widget_logic', $wl_options);
	}


	add_action( 'sidebar_admin_page', 'widget_logic_options_filter');

	function widget_logic_options_filter()
	{
		if((!$wl_options = get_option('widget_logic')) || !is_array($wl_options) ) $wl_options = array();
		?><div class="wrap">
			<form method="POST">
				<h2>Widget Logic options</h2>
				<p style="line-height: 30px;">

				<label for="widget_logic-options-filter" title="Adds a new WP filter you can use in your own code. Not needed for main Widget Logic functionality.">Use 'widget_content' filter
				<input id="widget_logic-options-filter" name="widget_logic-options-filter" type="checkbox" value="checked" class="checkbox" <?php echo $wl_options['widget_logic-options-filter'] ?> /></label>
					&nbsp;&nbsp;
				<label for="widget_logic-options-wp_reset_query" title="Resets a theme's custom queries before your Widget Logic is checked.">Use 'wp_reset_query' fix
				<input id="widget_logic-options-wp_reset_query" name="widget_logic-options-wp_reset_query" type="checkbox" value="checked" class="checkbox" <?php echo $wl_options['widget_logic-options-wp_reset_query'] ?> /></label>

				<span class="submit"><input type="submit" name="widget_logic-options-submit" id="widget_logic-options-submit" value="Save" /></span></p>
			</form>
		</div>
		<?php
	}


	function widget_logic_empty_control() {}

	function widget_logic_extra_control()
	{	global $wp_registered_widget_controls;
		$params=func_get_args();

		// find the widget id that we have sneaked into the params
		$id=(is_array($params[0]))?$params[0]['id_for_wl']:array_pop($params);	
		$id_disp=$id;

		if((!$wl_options = get_option('widget_logic')) || !is_array($wl_options) ) $wl_options = array();

		$callback=$wp_registered_widget_controls[$id]['callback_wl_redirect'];
		if (is_callable($callback))
			call_user_func_array($callback, $params);		// go to the original control function

		$value=htmlspecialchars(stripslashes($wl_options[$id]),ENT_QUOTES);

		// dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
		if (is_array($params[0]) && isset($params[0]['number'])) $number=$params[0]['number'];
		if ($number==-1) {$number="%i%"; $value="";}
		if (isset($number)) $id_disp=$wp_registered_widget_controls[$id]['id_base'].'-'.$number;

		// output our extra widget logic field
		echo "<p><label for='".$id_disp."-widget_logic'>Widget logic <input type='text' name='".$id_disp."-widget_logic' id='".$id_disp."-widget_logic' value='".$value."' /></label></p>";

	}


	// intercept  registered widgets - redirect them and put each ID on the end of the params
	// perhaps there is a way to just intercept the ones that are used??
	add_action('wp_head', 'widget_logic_redirect_callback');
	function widget_logic_redirect_callback()
	{	global $wp_registered_widgets;
		foreach ( $wp_registered_widgets as $id => $widget )
		{	if (!isset($wp_registered_widgets[$id]['callback_wl_redirect']) || $wp_registered_widgets[$id]['callback_wl_redirect']==false)
			{	array_push($wp_registered_widgets[$id]['params'],$id);
				$wp_registered_widgets[$id]['callback_wl_redirect']=$wp_registered_widgets[$id]['callback'];
				$wp_registered_widgets[$id]['callback']='widget_logic_redirected_callback';
			}
		}
	}

	// the redirection comes here
	function widget_logic_redirected_callback()
	{	global $wp_registered_widgets, $wp_reset_query_is_done;

		$params=func_get_args();											// get all the passed params
		$id=array_pop($params);												// take off the widget ID
		$callback=$wp_registered_widgets[$id]['callback_wl_redirect'];		// find the real callback

		$wl_options = get_option('widget_logic');							// do we want the widget?
		$wl_value=(isset($wl_options[$id])&&$wl_options[$id]!='')?stripslashes($wl_options[$id]):"true";
		$wl_value=(stristr($wl_value, "return"))?$wl_value:"return (".$wl_value.");";

		// before we execute the condtional code, perhaps we want to wp_reset_query...
		if (isset($wl_options['widget_logic-options-wp_reset_query']) && $wl_options['widget_logic-options-wp_reset_query']=='checked' && !$wp_reset_query_is_done)
		{	wp_reset_query(); $wp_reset_query_is_done=true;	}

		$wl_value=(eval($wl_value) && is_callable($callback));
		if ( $wl_value )
		{	if (isset($wl_options['widget_logic-options-filter']) && $wl_options['widget_logic-options-filter']!='checked')
				call_user_func_array($callback, $params);					// if so callback with original params!
			else
			{	ob_start();
				call_user_func_array($callback, $params);					// if so callback with original params!
				$widget_content = ob_get_contents();
				ob_end_clean();
				echo apply_filters( 'widget_content', $widget_content, $id);
			}
		}
	}
}
?>