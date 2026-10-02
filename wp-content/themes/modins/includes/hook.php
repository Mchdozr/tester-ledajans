<?php
add_theme_support( 'wp-block-styles' );

function modins_breadcrumb(){
	$title = get_the_title();
  $post_id = modins_id();

	if(get_post_type() == 'post'){
	}
	$title = is_search() ? esc_html__('Search', 'modins') : $title;
	$title = is_archive() ? get_the_archive_title() : $title;
	$title = is_home() ? esc_html__('Latest posts', 'modins') : $title;
	$show_title = modins_get_option('breadcrumb_show_title', '1');
	$bg_color = modins_get_option('breadcrumb_background_color', '1');;
	$bg_color_opacity = modins_get_option('breadcrumb_background_opacity', '1');
	$breadcrumb_image = modins_get_option('breadcrumb_bg_image', array('id'=> 0));
	$text_style = modins_get_option('breadcrumb_text_stype', 'text-light');
	if(get_post_meta($post_id, 'modins_breadcrumb_layout', true) == 'page_options'){
		$breadcrumb_image = get_post_meta($post_id, 'modins_modins_breacrumb_image', true);
		$bg_color_opacity = get_post_meta($post_id, 'modins_breacrumb_bg_opacity', true);
		$bg_color = get_post_meta($post_id, 'modins_modins_breacrumb_bg_color', true);
	}

	if(function_exists('is_shop') && is_shop()){
		$title = get_the_title($post_id);
	}
	
	$styles = $styles_inner = $classes = array();
	$styles_overlay = '';
	$classes[] = $text_style;

	if($bg_color){
		$rgba_color = modins_convert_hextorgb($bg_color);
		$styles_overlay = 'background-color: rgba(' . esc_attr($rgba_color['r']) . ',' . esc_attr($rgba_color['g']) . ',' . esc_attr($rgba_color['b']) . ', ' . ($bg_color_opacity/100) . ')';
	}

	if(isset($breadcrumb_image['url'])){
		$styles[] = 'background-image: url(\'' . $breadcrumb_image['url'] . '\')';
	}
	if(is_numeric($breadcrumb_image)){
    	$image = wp_get_attachment_image_src( $breadcrumb_image, 'full');
    	if(isset($image[0]) && $image[0]){
    		$styles[] = 'background-image: url(\'' . $image[0] . '\')';
    	}
  	}

	$css = count($styles) ? 'style="' . implode(';', $styles) . '"' : '';
	$css_inner = count($styles_inner) > 0 ? 'style="' . implode(';', $styles_inner) . '"' : '';
?>

	<div class="custom-breadcrumb breadcrumb-default <?php echo implode(' ', $classes); ?>" <?php echo trim($css) ?>>
		<?php if($styles_overlay){ ?>
			<div class="breadcrumb-overlay" style="<?php echo esc_attr($styles_overlay); ?>"></div>
		<?php } ?>
		<div class="breadcrumb-main">
		  	<div class="container">
			 	<div class="breadcrumb-container-inner align-center" <?php echo trim($css_inner) ?>>
			 		<?php 
			 			if($title && $show_title){ 
							echo '<h2 class="heading-title">' . html_entity_decode($title) . '</h2>';
						} 
					 	modins_general_breadcrumbs();
					?>
			 	</div>  
		  	</div>   
		</div>  
	</div>

<?php }

add_action( 'modins_page_breacrumb', 'modins_breadcrumb', '10' );

/**
 * Hook to select footer of page
 */
function modins_get_footer_layout(){
	
	if(!class_exists('GVA_Layout_Frontend')){
		return false;
	}

	$post_id = false;
	if(class_exists('WooCommerce') && is_shop()){
		$post_id = wc_get_page_id('shop');
	}else{
		$post = get_post();
		if( $post && isset($post->ID) && $post->ID ){
			$post_id = $post->ID;
		}
	}

	$frontend = new GVA_Layout_Frontend();
	if(get_post_type($post_id) == 'page'){
		$footer_id = get_post_meta($post_id, 'modins_footer_layout', true);
		if(empty($footer_id) || $footer_id == '_default_active'){
			$footer_id = $frontend->get_template_default('footer_layout');
		}
		return $footer_id;
	}

	$frontend = new GVA_Layout_Frontend();
	$template_id = $frontend->template_default_active_id();

	$post_meta_template = get_post_meta($post_id, 'modins_template_layout', true);
	if( !empty($post_meta_template) && $post_meta_template != '_default_active' && $post_meta_template != '_without_layout' && is_numeric($post_meta_template) ){
		$template_id = $post_meta_template;
	}

	$footer_id = 0;
	if($template_id){
		$footer_id = get_post_meta($template_id, 'footer_layout', true);
	}

	if(!$footer_id){
		$footer_id = $frontend->template_default_active_id('footer_layout');
	}

	return $footer_id;
} 
add_filter( 'modins_get_footer_layout', 'modins_get_footer_layout' );
 
/**
 * Hook to select header of page
 */
function modins_get_header_layout(){
	if(!class_exists('GVA_Layout_Frontend')){
		return false;
	}
	$post_id = false;

	if(class_exists('WooCommerce') && is_shop()){
		$post_id = wc_get_page_id('shop');
	}else{
		$post = get_post();
		if( $post && isset($post->ID) && $post->ID ){
			$post_id = $post->ID;
		}
	}

	$frontend = new GVA_Layout_Frontend();
	if(get_post_type($post_id) == 'page'){
		$header_id = get_post_meta($post_id, 'modins_header_layout', true);
		if(empty($header_id) || $header_id == '_default_active'){
			$header_id = $frontend->get_template_default('header_layout');
		}
		return $header_id;
	}
	
	$template_id = $frontend->template_default_active_id();
	$post_meta_template = get_post_meta($post_id, 'modins_template_layout', true);

	if( !empty($post_meta_template) && $post_meta_template != '_default_active' && is_numeric($post_meta_template) ){
		$template_id = $post_meta_template;
	}

	$header_id = 0;
	if($template_id){
		$header_id = get_post_meta($template_id, 'header_layout', true);
	}

	if(!$header_id){
		$header_id = $frontend->template_default_active_id('header_layout');
	}
	
	return $header_id;
} 
add_filter( 'modins_get_header_layout', 'modins_get_header_layout' );

function modins_main_menu(){
	if(has_nav_menu( 'primary' )){
		$modins_menu = array(
			'theme_location'    => 'primary',
			'container'         => 'div',
			'container_class'   => 'navbar-collapse',
			'container_id'      => 'gva-main-menu',
			'menu_class'        => ' gva-nav-menu gva-main-menu',
			'walker'            => new Modins_Walker()
		);
		wp_nav_menu($modins_menu);
	}  
}
add_action( 'modins_main_menu', 'modins_main_menu', 10 );
 
function modins_mobile_menu(){
	if(has_nav_menu( 'primary' )){
		$modins_menu = array(
			'theme_location'    => 'primary',
			'container'         => 'div',
			'container_class'   => 'navbar-collapse',
			'container_id'      => 'gva-mobile-menu',
			'menu_class'        => 'gva-nav-menu gva-mobile-menu',
			'walker'            => new Modins_Walker()
		);
		wp_nav_menu($modins_menu);
	}  
}
add_action( 'modins_mobile_menu', 'modins_mobile_menu', 10 );

function modins_header_mobile(){
	get_template_part('templates/parts/header', 'mobile');
}
add_action('modins_header_mobile', 'modins_header_mobile', 10);

function modins_offcanvas_mobile(){
	get_template_part('templates/parts/canvas-mobile', 'mobile');
}
add_action('modins_offcanvas_mobile', 'modins_offcanvas_mobile', 10);

add_filter('gavias-elements/map-api', 'modins_googlemap_api');
function modins_googlemap_api( $key = '' ){
   return modins_get_option('map_api_key', '');
}

add_filter('gavias-post-type/slug-portfolio', 'modins_slug_portfolio');
function modins_slug_portfolio( $key = '' ){
	return modins_get_option('slug_portfolio', '');
}

function modins_setup_admin_setting(){
  	global $pagenow; 
  	if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
	 	update_option( 'thumbnail_size_w', 175 );  
	 	update_option( 'thumbnail_size_h', 175 );  
	 	update_option( 'thumbnail_crop', 1 );  
	 	update_option( 'medium_size_w', 600 );  
	 	update_option( 'medium_size_h', 540 ); 
	 	update_option( 'medium_crop', 1 );  
  }
}
add_action( 'init', 'modins_setup_admin_setting'  );

function modins_page_class_names($classes) {
	$post_id = modins_id();
 	$class_el = get_post_meta($post_id, 'modins_extra_page_class', true);
 	if($class_el) $classes[] = $class_el;
 	$classes[] = 'modins-body-loading';
 	return $classes;
}
add_filter( 'body_class', 'modins_page_class_names' );

function modins_post_classes($classes, $class, $post_id){
   if(is_single($post_id)){
    	$classes[] = 'post-single-content';
   }
   return $classes;
}
add_filter( 'post_class', 'modins_post_classes', 10, 3 );
