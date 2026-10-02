<?php
function modins_register_meta_boxes(){
	$prefix = 'modins_';
	global $meta_boxes;
	$meta_boxes = array();

	/* ====== Metabox Template ====== */
	$meta_boxes[] = array(
		'id'    => 'gavias_metaboxes_page',
		'title' => esc_html__('Page Meta', 'modins'),
		'pages' => array( 'gva__template'),
		'priority'   => 'high',
		'fields' => array(
			array(
				'name' => esc_html__('Template Type', 'modins'),
				'id'   => "gva_template_type",
				'type' => 'text'
			),
		)
	);

	/* ====== Metabox Page ====== */
	$meta_boxes[] = array(
		'id'    => 'gavias_metaboxes_page',
		'title' => esc_html__('Page Meta', 'modins'),
		'pages' => array( 'page'),
		'priority'   => 'high',
		'fields' => array(
			array(
            'name' => esc_html__('Full Width', 'modins'),
            'id'   => "{$prefix}page_full_width",
            'type' => 'switch',
            'desc' => esc_html__('Turn on to have the main area display at 100% width according to the window size. Turn off to follow site width.', 'modins'),
            'std' => 0,
         ),
			array(
				'name' => esc_html__('Header Layout', 'modins'),
				'id'   => "{$prefix}header_layout",
				'type' => 'select',
				'options' => modins_list_header_layout(),
				'multiple' => false,
				'std'  => '_default_active',
			),
			array(
				'name' => esc_html__('Footer Layout', 'modins'),
				'id'   => "{$prefix}footer_layout",
				'type' => 'select',
				'options' => modins_list_footer_layout(),
				'multiple' => false,
				'std'  => '_default_active',
			),
			array(
				'name' => esc_html__('Extra page class', 'modins'),
				'id' => $prefix . 'extra_page_class',
				'desc' => esc_html__("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'modins'),
				'clone' => false,
				'type'  => 'text',
				'std' => '',
			),
		)
	);

	/* ====== Metabox Page Title ====== */
	$meta_boxes[] = array(
		'id' => 'gavias_metaboxes_page_heading',
		'title' => esc_html__('Page Title & Breadcrumb', 'modins'),
		'pages' => array( 'post', 'page', 'product', 'portfolio', 'tribe_events'),
		'context' => 'normal',
		'priority'   => 'high',
		'fields' => array(
		  	array(
				'name' => esc_html__('Remove Title of Page', 'modins'),   
				'id'   => "{$prefix}disable_page_title",
				'type' => 'switch',
				'std'  => 0,
		  	),
		  	array(
			 	'name' => esc_html__('Disable Breadcrumbs', 'modins'),
			 	'id'   => "{$prefix}no_breadcrumbs",
			 	'type' => 'switch',
			 	'desc' => esc_html__('Disable the breadcrumbs from under the page title on this page.', 'modins'),
			 	'std' => 0,
		  	),
		  	array(
			 	'name' => esc_html__('Breadcrumb Layout', 'modins'),
			 	'id'   => "{$prefix}breadcrumb_layout",
			 	'type' => 'select',
			 	'options' => array(
				 	'layout_options'    => esc_html__('Default Options in Layout Template', 'modins'),
				 	'page_options'      => esc_html__('Individuals Options This Page', 'modins')
			 	),
			 	'multiple' => false,
			 	'std'  => 'theme_options',
			 	'desc' => esc_html__('You can use breadcrumb settings default in Layout Template or individuals this page', 'modins')
		  	),
		  	array(
			 	'name' 	=> esc_html__( 'Background Overlay Color', 'modins' ),
			 	'id'   	=> "{$prefix}modins_breacrumb_bg_color",
			 	'desc' 	=> esc_html__( "Set an overlay color for hero heading image.", 'modins' ),
			 	'type' 	=> 'color',
			 	'class' => 'breadcrumb_setting',
			 	'std'  	=> '',
		  	),
		  	array(
			 	'name'       => esc_html__( 'Overlay Opacity', 'modins' ),
			 	'id'         => "{$prefix}breacrumb_bg_opacity",
			 	'desc'       => esc_html__( 'Set the opacity level of the overlay. This will lighten or darken the image depening on the color selected.', 'modins' ),
			 	'clone'      => false,
			 	'type'       => 'slider',
			 	'prefix'     => '',
			 	'class'   	  => 'breadcrumb_setting',
			 	'js_options' => array(
				  	'min'  => 0,
				  	'max'  => 100,
				  	'step' => 1,
			 	),
			 	'std'   => '50'
		  	),
		  	array(
			 	'name'  	=> esc_html__('Breadcrumb Background Image', 'modins'),
			 	'id'    	=> "{$prefix}modins_breacrumb_image",
			 	'type'  	=> 'image_advanced',
			 	'class'   	=> 'breadcrumb_setting',
			 	'max_file_uploads' => 1
		  	),
		)
	);

	$meta_boxes[] = array(
    	'id'    		=> 'metaboxes_give_forms',
    	'title' 		=> esc_html__('Give Forms Settings', 'modins'),
    	'pages' 		=> array( 'give_forms' ),
    	'priority' 	=> 'high',
    	'fields' 	=> array(
	     	array (
	        	'name'   => esc_html__('Gallery Images', 'modins'),
	        	'id'    	=> "{$prefix}give_gallery_images",
	        	'type'             => 'image_advanced',
	        	'max_file_uploads' => 50,
	      ),
	      array(
	        	'name' => esc_html__('Video URL', 'modins'),
	        	'id' 	 => $prefix . 'give_video_url',
	        	'type' => 'text'
	      ),
	      array(
	        	'name' => esc_html__('Featured', 'modins'),   
	        	'id'   => "{$prefix}give_featured",
	        	'type' => 'checkbox',
	        	'std'  => 0,
	      ),
	      array(
			 	'name' 	=> esc_html__( 'Color', 'modins' ),
			 	'id'   	=> "{$prefix}give_color",
			 	'type' 	=> 'color'
		  	),
		  	array(
				'name' => esc_html__('Layout Page', 'modins'),
				'id'   => "{$prefix}template_layout",
				'type' => 'select',
				'options' => modins_get_donation_layout(),
				'multiple' => false,
				'std'  => '_default_active',
			),
    	)
  	);

	return $meta_boxes;
 }  
  /********************* META BOX REGISTERING ***********************/
  add_filter( 'rwmb_meta_boxes', 'modins_register_meta_boxes' , 99 );

