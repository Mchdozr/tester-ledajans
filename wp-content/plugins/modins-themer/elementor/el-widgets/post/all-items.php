<?php
if(!defined('ABSPATH')){ exit; }

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;


class GVAElement_Posts extends GVAElement_Base{
	 const NAME = 'gva-posts';
	 const TEMPLATE = 'post/';
	 const CATEGORY = 'modins_post';

	 public function get_categories(){
		  return array(self::CATEGORY);
	 }
	 
	 public function get_name(){
		  return self::NAME;
	 }

	 public function get_title() {
		  return __('Posts All Items', 'modins-themer');
	 }

	 public function get_keywords() {
		  return [ 'post', 'content', 'carousel', 'grid' ];
	 }

	 public function get_script_depends() {
		  return [
				'swiper',
				'gavias.elements',
		  ];
	 }

	 public function get_style_depends() {
		  return array('swiper');
	 }

	 private function get_categories_list(){
		  $categories = array();

		  $categories['none'] = __('None', 'modins-themer');
		  $taxonomy = 'category';
		  $tax_terms = get_terms( $taxonomy );
		  if ( ! empty( $tax_terms ) && ! is_wp_error( $tax_terms ) ){
				foreach( $tax_terms as $item ) {
					 $categories[$item->term_id] = $item->name;
				}
		  }
		  return $categories;
	 }

	 private function get_posts() {
		  $posts = array();

		  $loop = new \WP_Query( array(
				'post_type' => array('post'),
				'posts_per_page' => -1,
				'post_status'=>array('publish'),
		  ) );

		  $posts['none'] = __('None', 'modins-themer');

		  while ( $loop->have_posts() ) : $loop->the_post();
				$id = get_the_ID();
				$title = get_the_title();
				$posts[$id] = $title;
		  endwhile;

		  wp_reset_postdata();

		  return $posts;
	 }

	 protected function register_controls() {
		  $this->start_controls_section(
				'section_query',
				[
					 'label' => __('Query & Layout', 'modins-themer'),
					 'tab'   => Controls_Manager::TAB_CONTENT,
				]
		  );

		  $this->add_control( // xx Layout
				'layout_heading',
				[
					 'label'   => __('Layout', 'modins-themer'),
					 'type'    => Controls_Manager::HEADING,
				]
		  );
			$this->add_control(
				'layout',
				[
					 'label'   => __('Layout Display', 'modins-themer'),
					 'type'    => Controls_Manager::SELECT,
					 'default' => 'carousel',
					 'options' => [
						  'standard'      => __('Standard', 'modins-themer'),
						  'grid'          => __('Grid', 'modins-themer'),
						  'carousel'      => __('Carousel', 'modins-themer'),
						  'list'    		=> __('List', 'modins-themer'),
						  'sticky'        => __('Post Large & List', 'modins-themer')
					 ]
				]
		  );
		  $this->add_control(
				'style',
				[
					 'label'     => __('Style', 'modins-themer'),
					 'type'      => \Elementor\Controls_Manager::SELECT,
					 'default' => 'post-style-1',
					 'options' => [
						  'post-style-1'         => __('Item Post Style I', 'modins-themer'),
						  'post-style-2'         => __('Item Post Style II', 'modins-themer'),
						  'post-style-3'         => __('Item Post Style III', 'modins-themer')
					 ],
					 'condition' => [
						  'layout' => array('grid', 'carousel')
					 ]
				]
		  );
		  	$this->add_control(
				'image_size',
				[
					'label'     => __('Image Style', 'modins-themer'),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => $this->get_thumbnail_size(),
					'default'   => 'modins_medium'
				]
		  	);
		  	$this->add_control(
				'excerpt_words',
				[
					'label'     => __('Excerpt Words', 'modins-themer'),
					'type'      => 'number',
					'default'   => 15
				]
		  	);

		  	$this->add_control( // xx Layout
				'query_heading',
				[
					'label'   => __('Query', 'modins-themer'),
					'type'    => Controls_Manager::HEADING,
				]
		  	);
		  	$this->add_control(
				'category_ids',
				[
					'label' => __('Select By Category', 'modins-themer'),
					'type' => Controls_Manager::SELECT2,
					'multiple'    => true,
					'default' => '',
					'label_block' => true,
					'options'   => $this->get_categories_list()
				]
		  	);

		  	$this->add_control(
				'post_ids',
				[
					'label' => __('Select Individually', 'modins-themer'),
					'type' => Controls_Manager::SELECT2,
					'default' => '',
					'multiple'    => true,
					'label_block' => true,
					'options'   => $this->get_posts()
				]  
		  	);

		  	$this->add_control(
				'posts_per_page',
				[
					'label' => __('Posts Per Page', 'modins-themer'),
					'type' => Controls_Manager::NUMBER,
					'default' => 6,
				]
		  	);

		  	$this->add_control(
				'orderby',
				[
					'label'   => __('Order By', 'modins-themer'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'post_date',
					'options' => [
					  	'post_date'  => __('Date', 'modins-themer'),
					  	'post_title' => __('Title', 'modins-themer'),
					  	'menu_order' => __('Menu Order', 'modins-themer'),
					  	'rand'       => __('Random', 'modins-themer'),
					]
				]
		  	);

		  	$this->add_control(
				'order',
				[
					'label'   => __('Order', 'modins-themer'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => [
					  'asc'  => __('ASC', 'modins-themer'),
					  'desc' => __('DESC', 'modins-themer'),
					]
				]
		  	);

		  	$this->add_control(
				'pagination',
				[
					'label'     => __('Pagination', 'modins-themer'),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'no',
					'condition' => [
						'layout' => ['grid', 'standard']
					]
				]
		  	);

		  	$this->end_controls_section();

		  	$this->add_control_carousel(false, array('layout' => 'carousel'));

		  	$this->add_control_grid(array('layout' => 'grid'));

		  	// Styling
		  	$this->start_controls_section(
				'section_styling_blog_sticky',
				[
					'label' => __('Right List Posts Style', 'modins-themer'),
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => [
						'layout' => 'stick'
					]
				]
		  	);

		  	$this->add_responsive_control(
				'stick_list_padding',
				[
					'label' => __('Padding List Posts', 'modins-themer'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .gva-posts-stick .column-right > .content-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					]
				]
		  	);

		  	$this->add_control(
				'stick_list_background',
				[
					'label' => __('Background List Posts', 'modins-themer'),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .gva-posts-stick .column-right > .content-inner' => 'background-color: {{VALUE}};',
					]
				]
		  	);

		  	$this->add_control(
				'stick_list_post_title_color',
				[
					'label' => __('List Posts Title Color', 'modins-themer'),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .post-block-small .post-content .content-inner .entry-title a' => 'color: {{VALUE}};',
					]
				]
		  	);

		  	$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'stick_list_post_title_typography',
					'selector' => '{{WRAPPER}} .post-block-small .post-content .content-inner .entry-title a',
				]
		  	);

		  	$this->add_control(
				'stick_list_post_meta_color',
				[
					'label' => __('List Posts Meta Color', 'modins-themer'),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .post-block-small .post-content .entry-meta .entry-date' => 'color: {{VALUE}};',
					]
				]
		  	);

		  	$this->end_controls_section();

		  	// Styling Posts Grid & Carousel
		  	$this->start_controls_section(
				'section_styling_post_content',
				[
					'label' => __('Post Content', 'modins-themer'),
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => [
						'layout' => ['grid', 'carousel']
					]
				]
		  	);

		  	$this->add_responsive_control(
				'post_box_padding',
				[
					'label' => __('Padding Post Content', 'modins-themer'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .gva-posts .entry-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					]
				]
		  	);

		  $this->add_control(
				'post_box_background',
				[
					 'label' => __('Background Post Content', 'modins-themer'),
					 'type' => Controls_Manager::COLOR,
					 'default' => '',
					 'selectors' => [
						  '{{WRAPPER}} .gva-posts .entry-content' => 'background-color: {{VALUE}};',
					 ]
				]
		  );

		  $this->end_controls_section();

		  // Styling post title
		  $this->start_controls_section(
				'section_styling_post_title',
				[
					 'label' => __('Post Title', 'modins-themer'),
					 'tab'   => Controls_Manager::TAB_STYLE,
					 'condition' => [
						  'layout' => ['grid', 'carousel']
					 ],
				]
		  );

		  $this->add_control(
				'post_box_title_color',
				[
					 'label' => __('Color Title', 'modins-themer'),
					 'type' => Controls_Manager::COLOR,
					 'default' => '',
					 'selectors' => [
						  '{{WRAPPER}} .gva-posts .entry-content .entry-title a' => 'color: {{VALUE}};',
					 ],
				]
		  );

		  $this->add_control(
				'post_box_title_color_hover',
				[
					 'label' => __('Color Title Hover', 'modins-themer'),
					 'type' => Controls_Manager::COLOR,
					 'default' => '',
					 'selectors' => [
						  '{{WRAPPER}} .gva-posts .entry-content .entry-title a:hover' => 'color: {{VALUE}};',
					 ],
				]
		  );

		  $this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					 'name' => 'post_box_title_typography',
					 'selector' => '{{WRAPPER}} .gva-posts .entry-content .entry-title a',
				]
		  );

		  $this->add_control(
				'post_box_meta_color',
				[
					 'label' => __('List Posts Meta Color', 'modins-themer'),
					 'type' => Controls_Manager::COLOR,
					 'default' => '',
					 'selectors' => [
						  '{{WRAPPER}} .gva-posts .post .entry-meta, {{WRAPPER}} .gva-posts .post .entry-meta a' => 'color: {{VALUE}};',
					 ],
				]
		  );

		  $this->end_controls_section();

		  // Styling post description
		  $this->start_controls_section(
				'section_styling_post_description',
				[
					 'label' => __('Post Description', 'modins-themer'),
					 'tab'   => Controls_Manager::TAB_STYLE,
					 'condition' => [
						  'layout' => ['grid', 'carousel']
					 ],
				]
		  );

		  $this->add_control(
				'post_box_description_color',
				[
					 'label' => __('Color', 'modins-themer'),
					 'type' => Controls_Manager::COLOR,
					 'default' => '',
					 'selectors' => [
						  '{{WRAPPER}} .gva-posts .entry-content .entry-description' => 'color: {{VALUE}};',
					 ],
				]
		  );

		  $this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					 'name' => 'post_box_description_typography',
					 'selector' => '{{WRAPPER}} .gva-posts .entry-content .entry-description',
				]
		  );

		  $this->end_controls_section();

	 }

	 public static function get_query_args(  $settings ) {
		  $defaults = [
				'post_ids'          => '',
				'category_ids'      => '',
				'orderby'           => 'date',
				'order'             => 'desc',
				'posts_per_page'    => 3,
				'offset'            => 0
		  ];

		  $settings = wp_parse_args( $settings, $defaults );
		  $cats = $settings['category_ids'];
		  $ids = $settings['post_ids'];

		  $query_args = [
				'post_type' => 'post',
				'posts_per_page' => $settings['posts_per_page'],
				'orderby' => $settings['orderby'],
				'order' => $settings['order'],
				'ignore_sticky_posts' => 1,
				'post_status' => 'publish', // Hide drafts/private posts for admins
		  ];

		  if($cats){
				if( is_array($cats) && count($cats) > 0 ){
					 $field_name = is_numeric($cats[0]) ? 'term_id':'slug';
					 $query_args['tax_query'] = array(
						  array(
							 'taxonomy' => 'category',
							 'terms' => $cats,
							 'field' => $field_name,
							 'include_children' => false
						  )
					 );
				}
		  }
		  if( $ids ){
			 if( is_array($ids) && count($ids) > 0 ){
				$query_args['post__in'] = $ids;
				$query_args['orderby'] = 'post__in';
			 }
		  }

		  if(is_front_page()){
				$query_args['paged'] = (get_query_var('page')) ? get_query_var('page') : 1;
		  }else{
				$query_args['paged'] = (get_query_var('paged')) ? get_query_var('paged') : 1;
		  }
 
		  return $query_args;
	 }


	 public function query_posts() {
		  $query_args = $this->get_query_args( $this->get_settings() );

		  return new WP_Query( $query_args );
	 }


	 protected function render() {
		  $settings = $this->get_settings_for_display();
		  printf('<div class="gva-element-%s gva-element">', $this->get_name() );
		  if(!empty($settings['layout'])){
				include $this->get_template('post/allitems/' . $settings['layout'] . '.php');
		  }
		  print '</div>'; 

	 }

}

$widgets_manager->register(new GVAElement_Posts());
