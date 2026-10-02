<?php
use Elementor\Plugin;
use Elementor\Core\Settings\Page\Manager as PageManager;

function modins_themer_path_demo_content(){
  return (__DIR__.'/demo-data/');
}
add_filter('wbc_importer_dir_path', 'modins_themer_path_demo_content');

//Way to set menu, import revolution slider, and set home page.
function modins_themer_import_sample($demo_active_import , $demo_directory_path){

	reset($demo_active_import);
	$current_key = key($demo_active_import);
	
	if ( class_exists( 'RevSlider' ) ) {
		$wbc_sliders_array = array( 'slider-1.zip', 'slider-2.zip', 'slider-3.zip' );
		$slider = new RevSlider();
		foreach ($wbc_sliders_array as $s) {
			if(file_exists( modins_themer_path_demo_content() . 'main/'. $s )){
				$slider->importSliderFromPost(true, true, modins_themer_path_demo_content().'main/'.$s);
			}
		}
	}

	//Setting Menus
	$wbc_menu_array = array( 'main' );
	if( isset( $demo_active_import[$current_key]['directory'] ) && !empty( $demo_active_import[$current_key]['directory'] ) && in_array( $demo_active_import[$current_key]['directory'], $wbc_menu_array ) ) {
		$top_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		if ( isset( $top_menu->term_id ) ) {
			set_theme_mod( 'nav_menu_locations', array(
				'primary' => $top_menu->term_id
			));
	 	}
	}

	//Set HomePage
	$wbc_home_pages = array(
		'main' => 'Home 1'
	);
	
	if ( isset( $demo_active_import[$current_key]['directory'] ) && !empty( $demo_active_import[$current_key]['directory'] ) && array_key_exists( $demo_active_import[$current_key]['directory'], $wbc_home_pages ) ) {
		$page = get_page_by_title( $wbc_home_pages[$demo_active_import[$current_key]['directory']] );
		if (isset($page->ID)) {
			update_option( 'page_on_front', $page->ID );
			update_option( 'show_on_front', 'page' );
		}
	}

	// Import Settings of Elementor
	$options_elementor = maybe_unserialize('a:12:{s:13:"system_colors";a:4:{i:0;a:3:{s:3:"_id";s:7:"primary";s:5:"title";s:7:"Primary";s:5:"color";s:7:"#00CDE5";}i:1;a:3:{s:3:"_id";s:9:"secondary";s:5:"title";s:9:"Secondary";s:5:"color";s:7:"#2B2B5E";}i:2;a:3:{s:3:"_id";s:4:"text";s:5:"title";s:4:"Text";s:5:"color";s:7:"#82828A";}i:3;a:3:{s:3:"_id";s:6:"accent";s:5:"title";s:7:"Heading";s:5:"color";s:7:"#2B2B5E";}}s:13:"custom_colors";a:3:{i:0;a:3:{s:3:"_id";s:7:"9e78dc2";s:5:"title";s:10:"Gray Color";s:5:"color";s:7:"#F0EFF4";}i:1;a:3:{s:3:"_id";s:7:"23e11f5";s:5:"title";s:12:"Border Color";s:5:"color";s:7:"#E2DFEB";}i:2;a:3:{s:3:"_id";s:7:"d78bc54";s:5:"title";s:5:"Black";s:5:"color";s:7:"#2B2B5E";}}s:17:"system_typography";a:4:{i:0;a:5:{s:3:"_id";s:7:"primary";s:5:"title";s:7:"Primary";s:21:"typography_typography";s:6:"custom";s:22:"typography_font_family";s:6:"Roboto";s:22:"typography_font_weight";s:3:"600";}i:1;a:5:{s:3:"_id";s:9:"secondary";s:5:"title";s:9:"Secondary";s:21:"typography_typography";s:6:"custom";s:22:"typography_font_family";s:11:"Roboto Slab";s:22:"typography_font_weight";s:3:"400";}i:2;a:5:{s:3:"_id";s:4:"text";s:5:"title";s:4:"Text";s:21:"typography_typography";s:6:"custom";s:22:"typography_font_family";s:6:"Roboto";s:22:"typography_font_weight";s:3:"400";}i:3;a:5:{s:3:"_id";s:6:"accent";s:5:"title";s:6:"Accent";s:21:"typography_typography";s:6:"custom";s:22:"typography_font_family";s:6:"Roboto";s:22:"typography_font_weight";s:3:"500";}}s:17:"custom_typography";a:0:{}s:21:"default_generic_fonts";s:10:"Sans-serif";s:9:"site_name";s:42:"Modins - Insurance Company WordPress Theme";s:16:"site_description";s:27:"Just another WordPress site";s:15:"container_width";a:3:{s:4:"unit";s:2:"px";s:4:"size";i:1200;s:5:"sizes";a:0:{}}s:19:"page_title_selector";s:14:"h1.entry-title";s:15:"activeItemIndex";i:1;s:11:"viewport_md";i:768;s:11:"viewport_lg";i:1025;}', true);
	$active_kit_id = Elementor\Plugin::$instance->kits_manager->get_active_id();
	update_post_meta($active_kit_id, '_elementor_page_settings', $options_elementor);
	update_option('use_extendify_templates', '0');

	update_option( 'elementor_experiment-e_dom_optimization', 'inactive' );
	update_option( 'elementor_experiment-a11y_improvements', 'inactive' );
   update_option( 'elementor_editor_break_lines', '1' );
   update_option( 'elementor_unfiltered_files_upload', '1' );
   update_option( 'elementor_experiment-container', 'inactive' );
   update_option( 'elementor_experiment-e_optimized_assets_loading', 'inactive' );
   update_option( 'elementor_experiment-additional_custom_breakpoints', 'inactive' );
   update_option( 'elementor_experiment-e_swiper_latest', 'inactive' );
   update_option( 'elementor_experiment-e_optimized_css_loading', 'inactive' );
   update_option( 'elementor_experiment-e_font_icon_svg', 'inactive' );

	// Import Settings of Event
	$options_event = maybe_unserialize('a:36:{s:8:"did_init";b:1;s:19:"tribeEventsTemplate";s:7:"default";s:16:"tribeEnableViews";a:3:{i:0;s:4:"list";i:1;s:5:"month";i:2;s:3:"day";}s:10:"viewOption";s:4:"list";s:14:"schema-version";s:7:"6.0.6.2";s:21:"previous_ecp_versions";a:2:{i:0;s:1:"0";i:1;s:5:"6.0.5";}s:18:"latest_ecp_version";s:7:"6.0.6.2";s:18:"dateWithYearFormat";s:6:"F j, Y";s:24:"recurrenceMaxMonthsAfter";i:60;s:22:"google_maps_js_api_key";s:39:"AIzaSyDNsicAsP6-VuGtAb1O9riI3oc_NOb7IOU";s:24:"front_page_event_archive";b:0;s:13:"earliest_date";s:19:"2023-01-11 08:00:00";s:11:"latest_date";s:19:"2023-09-11 17:00:00";s:21:"earliest_date_markers";a:1:{i:0;N;}s:19:"latest_date_markers";a:1:{i:0;N;}s:39:"last-update-message-the-events-calendar";s:7:"6.0.6.2";s:15:"stylesheet_mode";s:5:"tribe";s:16:"monthEventAmount";s:1:"3";s:12:"postsPerPage";s:2:"12";s:12:"showComments";b:0;s:20:"tribeDisableTribeBar";b:0;s:21:"dateWithoutYearFormat";s:3:"F j";s:18:"monthAndYearFormat";s:3:"F Y";s:16:"datepickerFormat";s:1:"1";s:17:"dateTimeSeparator";s:3:" @ ";s:18:"timeRangeSeparator";s:3:" - ";s:14:"multiDayCutoff";s:5:"00:00";s:32:"tribe_events_timezones_show_zone";b:0;s:26:"tribe_events_timezone_mode";s:5:"event";s:21:"defaultCurrencySymbol";s:1:"$";s:19:"defaultCurrencyCode";s:3:"USD";s:23:"reverseCurrencyPosition";b:0;s:15:"embedGoogleMaps";b:1;s:19:"embedGoogleMapsZoom";s:2:"10";s:21:"tribeEventsBeforeHTML";s:0:"";s:20:"tribeEventsAfterHTML";s:0:"";}', true);
	update_option('tribe_events_calendar_options', $options_event);

	if (function_exists('is_plugin_active') && is_plugin_active( 'elementor/elementor.php' )) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}

add_action('wbc_importer_after_content_import', 'modins_themer_import_sample', 10, 2);
