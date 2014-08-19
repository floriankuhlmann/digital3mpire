<!doctype html>
<html class="no-js" <?php language_attributes(); ?> >
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php if ( is_category() ) {
			echo 'Category Archive for &quot;'; single_cat_title(); echo '&quot; | '; bloginfo( 'name' );
		} elseif ( is_tag() ) {
			echo 'Tag Archive for &quot;'; single_tag_title(); echo '&quot; | '; bloginfo( 'name' );
		} elseif ( is_archive() ) {
			wp_title(''); echo ' Archive | '; bloginfo( 'name' );
		} elseif ( is_search() ) {
			echo 'Search for &quot;'.esc_html($s).'&quot; | '; bloginfo( 'name' );
		} elseif ( is_home() || is_front_page() ) {
			bloginfo( 'name' ); echo ' | '; bloginfo( 'description' );
		}  elseif ( is_404() ) {
			echo 'Error 404 Not Found | '; bloginfo( 'name' );
		} elseif ( is_single() ) {
			wp_title('');
		} else {
			echo wp_title( ' | ', 'false', 'right' ); bloginfo( 'name' );
		} ?></title>


		<link href='http://fonts.googleapis.com/css?family=Megrim' rel='stylesheet' type='text/css'>
		<!-- <link href='http://fonts.googleapis.com/css?family=Exo+2:400,100,100italic,200,200italic,300,300italic,400italic,500,500italic,600,600italic,700,800,800italic,900,900italic,700italic' rel='stylesheet' type='text/css'>-->
	<link href='http://fonts.googleapis.com/css?family=Cousine:400,400italic,700,700italic&effect=shadow-multiple|3d-float|neon' rel='stylesheet' type='text/css'>	
		<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ; ?>/css/app.css" />
		<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ; ?>/css/digital3mpire.css" />
		
		<link rel="icon" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/favicon_2.png" type="image/png">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-144x144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-114x114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-72x72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-precomposed.png">
		
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	<?php do_action('foundationPress_after_body'); ?>
	
	<div class="off-canvas-wrap" data-offcanvas>
	<div class="inner-wrap">
	
	<?php do_action('foundationPress_layout_start'); ?>
	
	<nav class="tab-bar show-for-small-only">
		<section class="left-small">
			<a class="left-off-canvas-toggle menu-icon" ><span></span></a>
		</section>
		<section class="middle tab-bar-section">
			
			<h4 class="title"><?php bloginfo( 'name' ); ?></h4>

		</section>
	</nav>

	<?php get_template_part('parts/off-canvas-menu'); ?>

	<?php get_template_part('parts/top-bar'); ?>

<section class="container" role="document">
	<?php do_action('foundationPress_after_header'); ?>

<header role="banner">
	<div class="row">
		<!-- <div class="small-10 medium-10 large-10 columns">
			<div class="green font-effect-shadow-multiple font-effect-neon"><h3><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h3>
			</div>
			<h5 class="subheader"><?php bloginfo('description'); ?></h5>
		</div>

		<div class="large-12 medium-12 columns">
			<ul class="inline-list">
				<li><a href="https://www.facebook.com/0floriankuhlmann1"><img src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/fb_s.png"></a></li>
				<li><a href="https://twitter.com/fkuhlmann"><img src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/twitter_s.png"></a></li>
				<li><a href="http://lsdsl.tumblr.com/"><img src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/tumblr_s.png"></a></li>
				<li><a href=""><img src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/rss_s.png"></a></li>
				<li><a href="https://twitter.com/fkuhlmann"><img src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/google_s.png"></a></li>
			</ul>
		</div> 

		<div class="floatingyeti show-for-medium-up">
			<br><br><br>
			 <img alt="digital3empire" src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/paint2.png">
		</div>-->
	</div>

</header>
