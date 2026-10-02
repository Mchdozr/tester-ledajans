<?php 
   if(!isset($index)){
      $index = 2;
   }
   $thumbnail = (isset($thumbnail_size) && $thumbnail_size) ? $thumbnail_size : 'modins_medium';
   if(!isset($excerpt_words)){
      $excerpt_words = modins_get_option('blog_excerpt_limit', 20);
   }
?>

<article id="post-<?php echo esc_attr(get_the_ID()); ?>" class="post post-five__single">
   <div class="post-five__thumbnail">
      <a href="<?php echo esc_url( get_permalink() ) ?>">
         <?php the_post_thumbnail( $thumbnail, array( 'alt' => get_the_title() ) ); ?>
      </a>
   </div>

   <div class="post-five__content">
      <div class="post__meta post-five__meta">
         <?php modins_posted_on(); ?>
      </div> 
      <h2 class="post-five__title">
         <a href="<?php echo esc_url( get_permalink() ) ?>" rel="bookmark"><?php the_title() ?></a>
      </h2>
   </div>
</article>

  