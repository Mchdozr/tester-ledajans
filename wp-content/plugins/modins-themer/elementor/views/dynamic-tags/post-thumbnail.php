<?php
   if (!defined('ABSPATH')) {
      exit; 
   }
   global $modins_post;
   if (!$modins_post){
      return;
   }
?>

<?php 
   $thumbnail_size = $settings['modins_image_size'];

   if(has_post_thumbnail($modins_post)){
      echo get_the_post_thumbnail($modins_post, $thumbnail_size);
   }
?>

