<?php
   if (!defined('ABSPATH')) {
      exit; 
   }
   global $modins_post;
   if (!$modins_post){
      return;
   }
   $html_tag = $settings['html_tag'];
?>

<div class="modins-post-title">
   <<?php echo esc_attr($html_tag) ?> class="post-title">
      <span><?php echo get_the_title($modins_post) ?></span>
   </<?php echo esc_attr($html_tag) ?>>
</div>   