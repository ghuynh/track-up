<?php 
/*
Plugin Name: Track Up
Plugin URI: http://twitter.com/gwhuynh
Description: track feeds and other things using google analytics.
Version: 1.0
Author: George Huynh
Author URI: http://twitter.com/gwhuynh
*/

// Create the default key and status
/*
add_option('trackup_status', 'enabled');
add_option('trackup_uid', 'XX-XXXX-X');
add_option('trackup_feed', 'enabled');
add_option('trackup_adsense', '');
*/
// Create a option page for settings
add_action('admin_init', 'trackup_admin_init');

// Initialize the options
function trackup_admin_init() {
	// Register out options so WordPress knows about them
	if ( function_exists('register_setting') ) {
		register_setting('track-up', 'trackup_status', '');
		register_setting('track-up', 'trackup_uid', '');
		register_setting('track-up', 'trackup_feed', '');
		register_setting('track-up', 'trackup_adsense', '');
	}
}

// Initialize outbound link tracking
add_action('init', 'trackup_feed_links');
add_action('wp_head', 'add_trackup_adsense');
add_action('loop_start', 'get_feed_keyword', 2);
add_action('get_footer', 'add_trackup_main');

function get_feed_keyword($post)  {
	if(is_search())
		$post_keyword = $_REQUEST['s'];
	else
	{
		$feed_post_id = $post->post->ID;

		$post_keyword = get_post_meta($feed_post_id, "mapping", true);
		if(!$post_keyword)
		{
			$categories = get_the_category($feed_post_id);
			if(!empty($categories))
			{
				$post_keyword = $categories[0]->name;
			}
		}
	}
	if($post_keyword == '')
		$post_keyword = 'unknown';
?>
<script type="text/javascript">
	var keywordName = '<?php echo $post_keyword; ?>';
</script>
<?php	
}

/**
 * Adds the Analytics Adsense tracking code to the header if the main Analytics tracking code is in the footer.
 **/
function add_trackup_adsense() {
	$uid = stripslashes(get_option('trackup_uid'));
	if (  (get_option('trackup_status') == 'enabled' ) && ( $uid != "XX-XXXXX-X" )) {
		if ( !is_admin() ) {
			if ( trim(get_option('trackup_adsense')) != '' ) {
				echo '	<script type="text/javascript">window.google_analytics_uacct = "' . get_option('trackup_adsense') . "\";</script>\n\n";
			}
		}
	}
}

// main part of the Google Analytics script
function add_trackup_main() {	
	$uid = stripslashes(get_option('trackup_uid'));
	if (  (get_option('trackup_status') == 'enabled' ) && ( $uid != "XX-XXXXX-X" )) {

		if ( !is_admin() ) {
			// Pick the HTTP connection
			echo "	<script type=\"text/javascript\" src=\"http://www.google-analytics.com/ga.js\"></script>\n\n";

			echo "	<script type=\"text/javascript\">\n";
			echo "	try {\n";
			echo "		var pageTracker = _gat._getTracker(\"$uid\");\n";
			// Initialize the tracker
			echo "		pageTracker._initData();\n";
			echo "		pageTracker._trackPageview();\n";
			echo "	} catch(err) {}</script>\n";
		
			// Include the feed tracking
			$event_tracking = get_option('trackup_feed');
			if($event_tracking == 'enabled')
			{
				$feed_type = get_option('feed_type');
				?>
				<script type="text/javascript">
					var eventName = '<?php echo $feed_type; ?>';
					var analyticsEventTracking = '<?php echo $event_tracking; ?>';
				</script>
				<?php
			}
		}
	}
}

/**
 * Adds outbound link tracking to Google Analyticator
 **/
function trackup_feed_links()
{
	$uid = stripslashes(get_option('trackup_uid'));
	if (  (get_option('trackup_status') == 'enabled' ) && ( $uid != "XX-XXXXX-X" )) {
		if ( get_option('trackup_feed') == 'enabled' ) {
			if ( !is_admin() ) {
					add_action('wp_print_scripts', 'trackup_feed_tracking_js');
			}
		}
	}
}

/**
 * Adds the scripts required for feed tracking
 **/
function trackup_feed_tracking_js()
{
	wp_enqueue_script('trackup-feed-tracking', plugins_url('/track-up/feed-tracking.js'), array('jquery'), '1.0');
}

?>