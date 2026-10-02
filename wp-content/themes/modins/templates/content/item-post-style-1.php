<?php 
   global $post;

   $thumbnail = (isset($thumbnail_size) && $thumbnail_size) ? $thumbnail_size : 'post-thumbnail';
   $excerpt_words = (isset($excerpt_words) && $excerpt_words) ? $excerpt_words : '0';

   $desc = modins_limit_words($excerpt_words, get_the_excerpt(), '');

   $meta_classes = 'post-one__meta';
   if(empty(get_the_date())){
      $meta_classes = 'post-one__meta schedule-date';
   }
   $content_classes = 'post-one__content';
   $thumbnail_check = has_post_thumbnail() ? ' has-thumbnail' : ' has-no-thumbnail';
   $content_classes .= $thumbnail_check;
?>

   <article <?php post_class('post post-one__single'); ?>>
      <?php 
         if(has_post_thumbnail()){
            echo '<div class="post-one__thumbnail">';
               echo '<a href="' . esc_url( get_permalink() ) . '">';
                  the_post_thumbnail( $thumbnail, array( 'alt' => get_the_title() ) );
               echo '</a>';
               if( get_the_date() ){
                  echo '<div class="entry-date">';
                     echo '<span class="date">' . esc_html( get_the_date('d')) . '</span>';
                     echo '<span class="month">' . esc_html( get_the_date('M')) . '</span>';
                  echo '</div>';
               } 
            echo '</div>';
         }
      ?>
       
      <div class="<?php echo esc_attr($content_classes) ?>">
         <div class="post-one__content-inner">
            
            <div class="<?php echo esc_attr($meta_classes) ?>">
               <?php modins_posted_on(); ?>
            </div> 
            <h3 class="post-one__title"><a href="<?php echo esc_url( get_permalink() ) ?>" rel="bookmark"><?php the_title() ?></a></h3>
            <?php 
               if($desc){ 
                  echo '<div class="post-one__desc">';
                     echo esc_html($desc);
                  echo '</div>';   
               } 
            ?>
            <div class="post-one__bottom">
               <a class="post-one__read-more" href="<?php echo esc_url( get_permalink() ) ?>">
                 <?php echo esc_html__('Read More', 'modins'); ?>
               </a>
               <a class="post-one__arrow" href="<?php echo esc_url( get_permalink() ) ?>">
                 <i class="micon__right-arrow2"></i>
               </a>
            </div>
         </div>
      </div>
   </article>   

  