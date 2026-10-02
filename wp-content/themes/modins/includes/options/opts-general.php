<?php
Redux::setSection( $opt_name, array(
	'title' => esc_html__('General Options', 'modins'),
	'icon' => 'el-icon-wrench',
	'fields' => array(
		array(
			'id'           => 'page_layout',
			'type'         => 'button_set',
			'title'        => esc_html__('Page Layout', 'modins'),
			'subtitle'     => esc_html__('Select the page layout type', 'modins'),
			'options'      => array(
				'boxed'     => esc_html__('Boxed', 'modins'),
				'fullwidth' => esc_html__('Fullwidth', 'modins')
			),
			'default' => 'fullwidth'
		),
      array(
        'id' => 'map_api_key',
        'type' => 'text',
        'title' => esc_html__('Google Map API key', 'modins'),
        'default' => ''
      ),

      // Logo Default Settings
      array(
         'id'     => 'logo_default',
         'type'   => 'info',
         'icon'   => true,
         'raw'    => '<h3 class="margin-bottom-0">' . esc_html__('Logo Default', 'modins') . '</h3>',
      ),
      array(
        'id'      => 'header_logo', 
        'type'    => 'media',
        'url'     => true,
        'title'   => esc_html__('Logo in header default', 'modins'), 
        'default' => ''
      ), 
         
		// Breadcrumb Default Settings
		array(
         'id'     => 'breadcrumb_default',
         'type'   => 'info',
         'icon'   => true,
         'raw'    => '<h3 class="margin-bottom-0">' . esc_html__('Breadcrumb Settings Without Elementor', 'modins') . '</h3>',
      ),
		array(
         'id'        => 'breadcrumb_title',
         'type'      => 'button_set',
         'title'     => esc_html__('Breadcrumb Title', 'modins'),
         'options'   => array(
            1 => esc_html__('Enable', 'modins'),
            0 => esc_html__('Disable', 'modins')
         ),
         'default'   => 1
      ),
      array(
         'id'        => 'breadcrumb_bg_color',
         'type'      => 'color',
         'title'     => esc_html__('Background Overlay Color', 'modins'),
         'default'   => ''
      ),
      array(
         'id'        => 'breadcrumb_bg_opacity',
         'type'      => 'slider',
         'title'     => esc_html__('Breadcrumb Ovelay Color Opacity', 'modins'),
         'default'   => 50,
         'min'       => 0,
         'max'       => 100,
         'step'      => 2,
         'display_value' => 'text',
      ),
      array(
         'id'        => 'breadcrumb_bg_image',
         'type'      => 'media',
         'url'       => true,
         'title'     => esc_html__('Breadcrumb Background Image', 'modins'),
         'default'   => '',
      ),
      array(
         'id'        => 'breadcrumb_text_stype',
         'type'      => 'select',
         'title'     => esc_html__('Breadcrumb Text Stype', 'modins'),
         'options'   => 
         array(
            'text-light'     => esc_html__('Light', 'modins'),
            'text-dark'      => esc_html__('Dark', 'modins')
         ),
         'default' => 'text-light'
      ),
	)
));