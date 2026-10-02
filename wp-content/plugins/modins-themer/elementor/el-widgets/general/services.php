<?php
if(!defined('ABSPATH')){ exit; }

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;

class GVAElement_Services extends GVAElement_Base{
  	const NAME = 'gva-services';
  	const TEMPLATE = 'general/services/';
  	const CATEGORY = 'modins_general';

  	public function get_name() {
	 	return self::NAME;
  	}

  	public function get_categories() {
	 	return array(self::CATEGORY);
  	}

	public function get_title() {
		return esc_html__('Services Grid/Carousel', 'modins-themer');
	}

	public function get_keywords() {
		return [ 'services', 'content', 'carousel', 'grid' ];
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

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Content', 'modins-themer'),
			]
		);
		$this->add_control( // xx Layout
			'layout_heading',
			[
				'label'   => esc_html__('Layout', 'modins-themer'),
				'type'    => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'layout',
			[
				'label'   => esc_html__('Layout Display', 'modins-themer'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'carousel',
				'options' => [
					 'grid'      => esc_html__('Grid', 'modins-themer'),
					 'carousel'  => esc_html__('Carousel', 'modins-themer')
				]
			]
	  	);
	  	$this->add_control(
			'style',
			[
				'label' => esc_html__('Style', 'modins-themer'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'style-1' 	=> esc_html__('Style 01', 'modins-themer'),
					'style-2' 	=> esc_html__('Style 02', 'modins-themer'),
					'style-3' 	=> esc_html__('Style 03', 'modins-themer'),
					 'style-4' 	=> esc_html__('Style 04', 'modins-themer')
				],
				'default' => 'style-1',
			]
	  	);
		$this->add_control(
			'last_style',
			[
				'label' 			=> esc_html__('Last Item Special Style', 'modins-themer'),
				'type' 			=> Controls_Manager::SWITCHER,
				'placeholder' 	=> esc_html__('Active', 'modins-themer'),
				'default' 		=> 'no'
			]
		);
		$this->add_responsive_control(
			'min_height',
			[
				'label' 		=> esc_html__('Min Height', 'modins-themer'),
				'type' 		=> Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .service-one__content' => 'min-height: {{SIZE}}{{UNIT}};',
					
				],
			]
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__('Title', 'modins-themer'),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Add your Title',
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'desc',
			[
				'label'       => esc_html__('Description', 'modins-themer'),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => 'Lorem ipsum dolor sit amet, sed consectetur elit.',
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'image',
			[
				'label'      => esc_html__('Choose Image', 'modins-themer'),
				'default'    => [
					'url' => GAVIAS_MODINS_PLUGIN_URL . 'elementor/assets/images/image-3.jpg',
				],
				'type'       => Controls_Manager::MEDIA,
				'show_label' => false,
			]
		);
		$repeater->add_control(
			'selected_icon',
			[
				'label'      => esc_html__('Choose Icon', 'modins-themer'),
				'type'       => Controls_Manager::ICONS,
				'default' => [
				  'value' => 'fas fa-home',
				  'library' => 'fa-solid',
				]
			]
		);
		$repeater->add_control(
			'link',
			[
				'label'     => esc_html__('Link', 'modins-themer'),
				'type'      => Controls_Manager::URL,
				'placeholder' => esc_html__('https://your-link.com', 'modins-themer'),
				'label_block' => true
			]
		);
		$repeater->add_control(
         'active',
			[
	         'label'       => __('Active', 'modins-themer'),
	         'type'        => Controls_Manager::SWITCHER,
	         'default'	  => 'no'
	     	]
	   );
		$this->add_control(
			'services_content',
			[
				'label'       => esc_html__('Service Content Item', 'modins-themer'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
				  	array(
					 	'title'  => esc_html__('Car insurance', 'modins-themer'),
					 	'image'  => [
                     'url' => GAVIAS_MODINS_PLUGIN_URL . 'elementor/assets/images/image-3.jpg'
                  ]
				  	),
				  	array(
					 	'title'  => esc_html__('Life insurance', 'modins-themer'),
					 	'image'  => [
                     'url' => GAVIAS_MODINS_PLUGIN_URL . 'elementor/assets/images/image-4.jpg'
                  ]
				  	),
				  	array(
					 	'title'  => esc_html__('Home insurance', 'modins-themer'),
					 	'image'  => [
                     'url' => GAVIAS_MODINS_PLUGIN_URL . 'elementor/assets/images/image-5.jpg'
                  ]
				  	),
				  	array(
					 	'title'  => esc_html__('Health insurance', 'modins-themer'),
					 	'image'  => [
                     'url' => GAVIAS_MODINS_PLUGIN_URL . 'elementor/assets/images/image-6.jpg'
                  ]
				  	)
				)
			]
		);
		
		$this->add_control(
			'image_size',
			[
				'label'     => __('Image Size', 'modins-themer'),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $this->get_thumbnail_size(),
				'default'   => ''
			]
		);

		$this->add_control(
			'btn_label',
			[
				'label' => __( 'Button label last item', 'modins-themer' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Get a Quote', 'modins-themer' ),
				'placeholder' => __( 'Enter your label', 'modins-themer' ),
				'label_block' => true,
				'condition' => [
					'style' => ['style-1']
				]
			]
		);

		$this->end_controls_section();

		$this->add_control_carousel(false, array('layout' => 'carousel'));

		$this->add_control_grid(array('layout' => 'grid'));

		// Icon Styling
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__('Icon', 'modins-themer'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__('Icon Color', 'modins-themer'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
				  '{{WRAPPER}} .service-one__icon i, {{WRAPPER}} .service-two__icon i, {{WRAPPER}} .service-three__icon ' => 'color: {{VALUE}};',
				  '{{WRAPPER}} .service-one__icon svg, {{WRAPPER}} .service-two__icon svg, {{WRAPPER}} .service-three__icon svg' => 'fill: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__('Size', 'modins-themer'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
				  'px' => [
					 'min' => 20,
					 'max' => 80, 
				  ],
				],
				'selectors' => [
				  '{{WRAPPER}} .service-one__icon i, {{WRAPPER}} .service-two__icon i, {{WRAPPER}} .service-three__icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				  '{{WRAPPER}} .service-one__icon svg, {{WRAPPER}} .service-two__icon svg, {{WRAPPER}} .ervice-three__icon svg' => 'width: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_section();

	 	$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'modins-themer'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label' => esc_html__('Title', 'modins-themer'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'title_bottom_space',
			[
				'label' => esc_html__('Spacing', 'modins-themer'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
				  'px' => [
					 'min' => 0,
					 'max' => 100,
				  ],
				],
				'selectors' => [
				  '{{WRAPPER}} .service-one__title, {{WRAPPER}} .service-two__title, {{WRAPPER}} .service-three__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		); 

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Color', 'modins-themer'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
				  '{{WRAPPER}} .service-one__title, {{WRAPPER}} .service-one__title a, {{WRAPPER}} .service-two__title, {{WRAPPER}} .service-two__title a, {{WRAPPER}} .service-three__title, {{WRAPPER}} .service-three__title a' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .service-one__title, {{WRAPPER}} .service-two__title, {{WRAPPER}} .service-three__title',
			]
		);

		$this->add_control(
			'heading_description',
			[
				'label' => esc_html__('Description', 'modins-themer'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__('Color', 'modins-themer'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
				  '{{WRAPPER}} .service-one__desc, {{WRAPPER}} .service-two__desc, {{WRAPPER}} .service-three__desc' => 'color: {{VALUE}};'
				],
			]
		);

	  	$this->add_group_control(
		 	Group_Control_Typography::get_type(),
		 	[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .service-one__desc, {{WRAPPER}} .service-two__desc, {{WRAPPER}} .service-three__desc'
			]
	  	);

		$this->end_controls_section();
	 }

	 protected function render() {
		$settings = $this->get_settings_for_display();
		printf('<div class="gva-element-%s gva-element">', $this->get_name() );
		if( !empty($settings['layout']) ){
			include $this->get_template(self::TEMPLATE . $settings['layout'] . '.php');
		}
		print '</div>';
	 }

}

$widgets_manager->register(new GVAElement_Services());
