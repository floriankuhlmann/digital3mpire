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

        <script src="https://kit.fontawesome.com/d2dcff0c12.js" crossorigin="anonymous"></script>
		<link href='https://fonts.googleapis.com/css?family=Megrim' rel='stylesheet' type='text/css'>
		<!-- <link href='https://fonts.googleapis.com/css?family=Exo+2:400,100,100italic,200,200italic,300,300italic,400italic,500,500italic,600,600italic,700,800,800italic,900,900italic,700italic' rel='stylesheet' type='text/css'>-->
	<link href='https://fonts.googleapis.com/css?family=Cousine:400,400italic,700,700italic&effect=shadow-multiple|3d-float|neon' rel='stylesheet' type='text/css'>	
		<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ; ?>/css/app.css" />
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ; ?>/css/menu-style.css"> <!-- Resource style -->
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ; ?>/css/digital3mpire.css" />


        <script src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/js/menu/modernizr.js"></script> <!-- Modernizr -->


        <link rel="icon" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/favicon_2.png" type="image/png">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-144x144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-114x114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-72x72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-precomposed.png">
		
		<?php //wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	<?php do_action('foundationPress_after_body'); ?>
    <div class="toppaint">
        <img src="<?php bloginfo('template_url'); ?>/assets/img/paint2.png">
    </div>
    <div>
        <div class="inner-wrap">


            <nav>
                <ul class="cd-primary-nav">
                    <?php foundationPress_mobile_off_canvas(); ?>
                </ul>
            </nav>
            <?php do_action('foundationPress_layout_start'); ?>
            <div class="top-bar-container">
                <div class="row">
                    <!--<div class="large-10 medium-10 small-12 columns small-centered">-->
                    <div class="large-2 small-12 columns">
                            <a href="#0" class="cd-nav-trigger">Menu<span class="cd-icon"></span></a>
                            <div class="cd-overlay-content">
                                <span></span>
                            </div> <!-- cd-overlay-content -->
                    </div>
                    <div class="large-10 small-12 columns">
                        <h3 class="text-right">
                            <a href="<?php echo get_home_url(); ?>">DIGITAL3MPIRE</a> &nbsp;&nbsp;
                            <a href="https://instagram.com/digital.3mpire"><i class="fab fa-instagram"></i></a>
                            <a href="https://facebook.com/digital3mpire"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/digital_3mpire"><i class="fab fa-twitter"></i></a>
                        </h3>
                    </div>
                    <div class="large-4 columns">
                        <div class="cd-overlay-nav">
                            <span></span>
                        </div> <!-- cd-overlay-nav -->
                    </div>
                </div>
            </div>






