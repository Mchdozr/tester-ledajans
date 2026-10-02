<?php
function modins_elementor_load_css_header(){
	if (!class_exists( 'Elementor\Core\Files\CSS\Post')){
		return;
	}
   
	$header_id = apply_filters('modins_get_header_layout', null);
   $footer_id = apply_filters('modins_get_footer_layout', null);

	if($header_id){
		$css_file = new Elementor\Core\Files\CSS\Post($header_id);
		$css_file->enqueue();
	}
   if($footer_id){
      $css_file = new Elementor\Core\Files\CSS\Post($footer_id);
      $css_file->enqueue();
   }
}

add_action('wp_enqueue_scripts', 'modins_elementor_load_css_header', 500);