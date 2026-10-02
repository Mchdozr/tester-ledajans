<?php
   if (!defined('ABSPATH')) {
      exit; 
   }
   global $modins_post;
   if (!$modins_post){
      return;
   }
   ?>
   
   <div class="post-date">
         <?php 
            if($settings['show_icon']){ 
               echo '<i class="far fa-calendar"></i>';
            }
            echo get_the_date( get_option('date_format'), $modins_post->ID);
         ?>
   </div>      

