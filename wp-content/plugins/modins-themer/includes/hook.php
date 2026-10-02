<?php
add_action('init','modins_themer_disable_kses_if_allowed');
function modins_themer_disable_kses_if_allowed() {
   if (current_user_can('unfiltered_html')) {
      // Disables Kses only for textarea saves
      foreach (array('pre_term_description', 'pre_link_description', 'pre_link_notes', 'pre_user_description') as $filter) {
         remove_filter($filter, 'wp_filter_kses');
      }
   }

   // Disables Kses only for textarea admin displays
   foreach (array('term_description', 'link_description', 'link_notes', 'user_description') as $filter) {
      remove_filter($filter, 'wp_kses_data');
   }
}

function modins_themer_mime_types($mimes) {
   $mimes['svg'] = 'image/svg+xml';
   return $mimes;
}
add_filter('upload_mimes', 'modins_themer_mime_types');

add_action( 'init', 'modins_init_options', 1 );
function modins_init_options(){
   if( empty(get_option( 'tribeEventsTemplate', '' )) ){
      update_option('tribeEventsTemplate', 'default');
   }
   if( empty(get_option( 'views_v2_enabled', '' )) ){
      update_option('views_v2_enabled', '0');
   }
}

// Disables the block editor from managing widgets in the Gutenberg plugin.
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
// Disables the block editor from managing widgets.
add_filter( 'use_widgets_block_editor', '__return_false' );


function modins_themer_disable_woocommerce_block_styles() {
  wp_dequeue_style( 'wc-blocks-style' );
  wp_dequeue_script( 'wc-blocks' );
}
add_action( 'wp_enqueue_scripts', 'modins_themer_disable_woocommerce_block_styles' );

add_filter('wpcf7_autop_or_not', '__return_false');
