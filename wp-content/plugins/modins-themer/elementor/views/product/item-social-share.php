<?php
   if (!defined('ABSPATH')){ exit; }

   global $modins_post;
   if( !$modins_post || $modins_post->post_type != 'product' ||  !$modins_post->post_excerpt ){ return; }
   
   $post_id = $modins_post->ID;
   $this->add_render_attribute('block', 'class', [ 'cf-item-social-share' ]);
?>

<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
   <?php wpcf_function()->template('include/social-share'); ?>
</div>