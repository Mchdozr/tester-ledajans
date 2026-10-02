<?php
/**
 *
 * @author     Gaviasthemes Team     
 * @copyright  Copyright (C) 2023 Gaviasthemes. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
*/ 
  	use Elementor\Plugin;
  	$protocol = is_ssl() ? 'https' : 'http';
	$header_id = apply_filters('modins_get_header_layout', null );
	$document = Plugin::instance()->documents->get($header_id);
  	$header_position = $document->get_settings('modins_header_position');
  	
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="<?php echo esc_attr($protocol) ?>://gmpg.org/xfn/11">
	<?php wp_head(); ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WDLJTMSV');</script>
<!-- End Google Tag Manager -->
</head>

<body <?php body_class() ?>>
	<?php wp_body_open(); ?>
   <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WDLJTMSV"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
  <div class="modins-page-loading"></div>
	
	<div class="wrapper-page"> <!--page-->
		<?php do_action( 'modins_before_header' );  ?>
	 
		<header class="wp-site-header header-builder-frontend header-position-<?php echo esc_attr($header_position) ?>">
			<div class="header_default_screen">
				<div class="header-builder-inner">
					<?php if($header_id && class_exists('GVA_Layout_Frontend')){
						echo '<div class="header-main-wrapper">' .  GVA_Layout_Frontend::getInstance()->element_display($header_id)  . '</div>'; 
					}else{
						get_template_part('header-default');
					}?>
				</div> 
			</div> 
	  </header>

		<?php do_action( 'modins_after_header' );  ?>
	 
		<div id="page-content"> <!--page content-->