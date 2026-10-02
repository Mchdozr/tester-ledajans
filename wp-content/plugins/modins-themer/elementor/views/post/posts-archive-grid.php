<?php
		global $wp_query;

		if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$args = [
				 'post_type' 		=> 'post',
				 'post_status' 		=> 'publish', 
				 'posts_per_page' 	=> get_option('posts_per_page', '6')
			];
			$wp_query = new WP_Query($args);
		}

	$this->add_render_attribute('wrapper', 'class', ['gva-posts-grid clearfix gva-archive-posts']);

	//add_render_attribute grid 
	$this->get_grid_settings();
	 
	 $this->add_render_attribute('grid', 'class', ['post-masonry-style post-masonry-index masonry']);

?>

<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
	<div class="gva-content-items"> 
		<?php if(have_posts()){ ?>
			
			<div <?php echo $this->get_render_attribute_string('grid') ?>>
				<?php
					//global $post;
					 while (have_posts()){ 
						the_post(); 
						echo '<div class="item-columns item-masory">';
							$this->modins_get_template_part('templates/content/item', $settings['style'], array(
								'thumbnail_size' => $settings['image_size'],
								'excerpt_words'  => $settings['excerpt_words']
							));
						echo '</div>';
					}
				?>
			</div>

		<?php }else{ ?>
			<div class="search-no-results-content">
				<div class="message"><?php echo esc_html__( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'modins-themer' ); ?></div>
				<?php get_search_form() ?>
			</div>
		<?php } ?>   
	</div>

	<div class="pagination">
		<?php echo $this->pagination($wp_query); ?>
	</div>
</div>

<?php wp_reset_postdata();