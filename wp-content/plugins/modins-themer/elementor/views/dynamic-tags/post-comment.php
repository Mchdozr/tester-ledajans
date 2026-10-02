<?php
   if (!defined('ABSPATH')){ exit; }

   global $modins_post, $post;

   if(!$modins_post){ return; }
   $post = $modins_post;
?>
   
<div class="post-comment">
   <?php
      if(comments_open($modins_post->ID)){
         comments_template();
      }else{
         if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
            echo '<div class="alert alert-info">' . esc_html__('This Post Disabled Comment', 'modins-themer') . '</div>';
         }
      }
   ?>
</div>      

