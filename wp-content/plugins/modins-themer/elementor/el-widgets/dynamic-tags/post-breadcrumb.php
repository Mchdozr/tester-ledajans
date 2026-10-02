<?php
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class GVAElement_Post_Breadcrumb extends GVAElement_Base{
	const NAME = 'gva_post_breadcrumb';
	const TEMPLATE = 'dynamic-tags/post-breadcrumb';
	const CATEGORY = 'modins_post';

	public function get_categories(){
		 return array(self::CATEGORY);
	}
	 
	public function get_name(){
		return self::NAME;
	}

	public function get_title(){
		return esc_html__('Breadcrumb', 'modins-themer');
	}

	public function get_keywords() {
		return [ 'post', 'breadcrumb'];
	}
	 
	protected function register_controls(){
		  //--
		$this->start_controls_section(
			self::NAME . '_content',
			[
				'label' => esc_html__('Content', 'modins-themer'),
			]
		);

		$this->add_control(
			'heading_background',
			[
				'label'     => __('Background & Color ------', 'modins-themer'),
				'type'      => Controls_Manager::HEADING,
			]
	  	);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __( 'Background', 'plugin-domain' ),
				'types' => [ 'classic', 'image', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .custom-breadcrumb',
			]
		);

		$this->add_control(
			'bg_overlay_color',
			[
				'label' => esc_html__( 'Background Overlay Color', 'modins-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom-breadcrumb .breadcrumb-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_padding',
			[
				'label'     => __('Padding ------', 'modins-themer'),
				'type'      => Controls_Manager::HEADING,
			]
	  	);

	  	$this->add_responsive_control(
			'padding_top',
			[
				'label' => __( 'Breadcrumb Padding Top', 'modins-themer' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .custom-breadcrumb .breadcrumb-container-inner' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_bottom',
			[
				'label' => __( 'Breadcrumb Padding Bottom', 'modins-themer' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .custom-breadcrumb .breadcrumb-container-inner' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_content',
			[
				'label'     => __('Content & Style ------', 'modins-themer'),
				'type'      => Controls_Manager::HEADING,
			]
	  	);
		$this->add_control(
			'show_title',
			[
				'label'     => __('Show Titlte', 'modins-themer'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
	  	);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'modins-themer' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom-breadcrumb .heading-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .modins-post-tags .title',
			]
		);

		$this->add_control(
			'show_links',
			[
				'label'     => __('Show Breadcrumb Links', 'modins-themer'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				
			]
	  	);

		$this->add_control(
			'align',
			[
				'label' => __( 'Alignment Text', 'modins-themer' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'modins-themer' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'modins-themer' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'modins-themer' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
			]
		);


		$this->end_controls_section();
	}

	protected function render(){
		parent::render();

		$settings = $this->get_settings_for_display();
		printf( '<div class="gva-element-%s gva-element">', $this->get_name() );
			include $this->get_template(self::TEMPLATE . '.php');
		print '</div>';
	}

	public function convert_hextorgb($hex, $alpha = false) {
	 	$hex = str_replace('#', '', $hex);
	 	if ( strlen($hex) == 6 ) {
			$rgb['r'] = hexdec(substr($hex, 0, 2));
			$rgb['g'] = hexdec(substr($hex, 2, 2));
			$rgb['b'] = hexdec(substr($hex, 4, 2));
	 	}else if ( strlen($hex) == 3 ) {
			$rgb['r'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
			$rgb['g'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
			$rgb['b'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
	 	}else {
			$rgb['r'] = '0';
			$rgb['g'] = '0';
			$rgb['b'] = '0';
	  	}
	  if ( $alpha ) {
			$rgb['a'] = $alpha;
	 	}
	 	return $rgb;
  	}

	public function breadcrumbs(){
	 	$delimiter = '';
	 	$home = esc_html__('Home', 'modins-themer');
	 	$before = '<li class="active">';
	 	$after = '</li>';
	 	$breadcrumb = '';
	 	if(!is_home() && !is_front_page() || is_paged()) {

			$breadcrumb .= '<ol class="breadcrumb">';

			global $post;
			$breadcrumb .= '<li><a href="' . esc_url(home_url()) . '">' . $home . '</a> ' . $delimiter . '</li> ';

			if(is_category()){
			  
			  	global $wp_query;
			  	$cat_obj = $wp_query->get_queried_object();
			  	$thisCat = $cat_obj->term_id;
			  	$thisCat = get_category($thisCat);
			  	$parentCat = get_category($thisCat->parent);
			  	if ($thisCat->parent != 0) $breadcrumb .= (get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
			  	$breadcrumb .= $before . single_cat_title('', false) . $after;
		  
			}elseif(is_day()){
			  
			  	$breadcrumb .= '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a>' . ' ' . $delimiter . ' ' . '</li>';
			  	$breadcrumb .= '<li><a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . get_the_time('F') . '</a>' . ' ' . $delimiter . ' ' . '</li>';
			  	$breadcrumb .= $before . get_the_time('d') . $after;
		  
			}elseif(is_month()){
			 
			  	$breadcrumb .= '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a>' . ' ' . $delimiter . ' ' . '</li>';
			  	$breadcrumb .= $before . get_the_time('F') . $after;
		  
			}elseif(is_year()){
			 
			  	$breadcrumb .= $before . get_the_time('Y') . $after;
			
			}elseif( is_search() || get_query_var('s')){

			  	$breadcrumb .= $before . esc_html__('Search results for', 'modins-themer') . '"' . htmlentities(get_search_query()) . '"' . $after;

			}elseif(is_single() && !is_attachment()){
			  	
			  	if ( get_post_type() != 'post' ) {
				 	if(get_the_title()){
						$breadcrumb .= $before . get_the_title() . $after;
				 	}
			  	}else{
				 	$cat = get_the_category(); $cat = $cat[0];
				 	$breadcrumb .= $before . get_category_parents($cat, TRUE, '') . $after;
			  	}

			}elseif(!is_single() && !is_page() && get_post_type() != 'post' && !is_404()){
			  
			  	$post_type = get_post_type_object(get_post_type());
			  	if( $post_type ){
				 	$breadcrumb .= $before . $post_type->labels->singular_name . $after;
			  	}

			}elseif(is_attachment()){

			  	$parent = get_post($post->post_parent);
			  	$cat = get_the_category($parent->ID); 
			  	if(isset($cat[0]) && $cat[0]){
				 	$cat = $cat[0];
				 	$breadcrumb .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
			  	}
			  	$breadcrumb .= '<li><a href="' . esc_url(get_permalink($parent)) . '">' . $parent->post_title . '</a></li> ' . $delimiter . ' ';
			  	$breadcrumb .= $before . get_the_title() . $after;

			}elseif(is_page() && !$post->post_parent){
			  
			  	$breadcrumb .= $before . get_the_title() . $after;

			}elseif(is_page() && $post->post_parent){

			  	$parent_id  = $post->post_parent;
			  	$breadcrumbs = array();
			  	while ($parent_id) {
				 	$page = get_page($parent_id);
				 	$breadcrumbs[] = '<li><a href="' . esc_url(get_permalink($page->ID)) . '">' . get_the_title($page->ID) . '</a></li>';
				 	$parent_id  = $page->post_parent;
			  	}
			  	$breadcrumbs = array_reverse($breadcrumbs);
			  	foreach ($breadcrumbs as $crumb) $breadcrumb .= ($crumb) . ' ' . $delimiter . ' ';
			  	$breadcrumb .= $before . get_the_title() . $after;

			}elseif(is_tag()){

			  $breadcrumb .= $before . esc_html__('Posts tagged', 'modins-themer') . '"' . single_tag_title('', false) . '"' . $after;

			}elseif(is_author()){

			  	global $author;
			  	$userdata = get_userdata($author);
			  	if($userdata){
				 	$breadcrumb .= $before . esc_html__('Articles posted by', 'modins-themer') . $userdata->display_name . $after;
			  	} 

			}elseif(is_404()){
			  	$breadcrumb .= $before . esc_html__('Error 404', 'modins-themer') . $after;
			}

			if(get_query_var('paged')){
			  	$breadcrumb .= $before . esc_html__('Page','modins-themer') . ' ' . get_query_var('paged') . $after;
			}

			$breadcrumb .= '</ol>';
			echo html_entity_decode($breadcrumb);
	 	}
	} 	

}

$widgets_manager->register(new GVAElement_Post_Breadcrumb());
