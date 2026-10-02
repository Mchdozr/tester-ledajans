<?php
   if (!defined('ABSPATH')){ exit; }

   global $modins_post, $post;
   if( !$modins_post ){ return; }
   if( $modins_post->post_type != 'product' ){ return; }
   $post_id = $modins_post->ID;
   if(\Elementor\Plugin::$instance->editor->is_edit_mode() || $post->post_type == 'gva__template'){
      global $product;
      $product = wc_get_product($post_id);
   }
   $this->add_render_attribute('block', 'class', ['product-item-meta']);
?>

<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
   <div class="content-inner">
   <span class="title-meta"><?php echo esc_html__('Product Meta', 'modins-theme') ?></span>
   <?php woocommerce_template_single_meta(); ?>
</div>
</div>