<?php
	if (!defined('ABSPATH')) {
		exit; 
	}
	global $modins_post;
	if (!$modins_post){
		return;
	}
	?>
	
	<div class="post-category">
		<?php 
			if($settings['show_icon']){ 
				echo '<i class="fas fa-folder-open"></i>';
			}
			echo '<span>' . get_the_category_list( ", ", '', $modins_post->ID ) . '</span>';
		?>
	</div>      

