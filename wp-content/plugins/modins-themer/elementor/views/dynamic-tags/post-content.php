<?php
   if (!defined('ABSPATH')) {
      exit; 
   }
   global $modins_post;
   if (!$modins_post){
      return;
   }
   ?>
   
   <div class="post-content">
         <?php 
            echo $modins_post->post_content;
         ?>
   </div>      

