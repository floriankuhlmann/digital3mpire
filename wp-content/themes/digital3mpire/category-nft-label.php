<?php get_header(); ?>

<div class="row">
    <div class="small-12 columns" role="main">
        <h2 class="green font-effect-shadow-multiple font-effect-neon">
            The NFT-label
        </h2>
<span class="text-bg-light">
        <p>the digital3mpire was and is always changing. as a hybrid dynamic and fluid entity, it is continually reinventing itself. at the beginning, the physical space and the shows there were the central location and part of the platform. In the course of the last few years this has changed, the focus of the activity is now on the network.
        and so in addition to the gallery and the cooperation projects, the NFT label digital3mpire was added.</p>
        <h3>Create with us</h3>
        <p>we are creating and minting netbased aesthetic entities and artworks in cooperation with progressive creators who have a strong digital mindset.<br>
        <br>we currently mint the releases on the <a href="https://opensea.io/accounts/digital3mpire">opensea</a> or the <a href="https://www.hicetnunc.xyz/tz/tz1LnELzbJ4mSoyNuxBSnkGZ6jmvjtck8ohp">
                hicetnunc.xyz</a> plattform and publish infos here and on our social channels.</p>
</span>
    </div>
</div>


<div class="row">
    <div class="small-12 columns" role="main">
        <h2 class="green font-effect-shadow-multiple font-effect-neon">
           NFT related updates
        </h2>
    </div>
</div>
<div class="row">

<!-- Row for main content area -->
	<div class="small-12 medium-10 columns small-centered" role="main">
	<?php if ( have_posts() ) : ?>

		<?php /* Start the Loop */ ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', get_post_format() ); ?>
		<?php endwhile; ?>

		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>

	<?php endif; // end have_posts() check ?>

	<?php /* Display navigation to next/previous pages when applicable */ ?>
	<?php if ( function_exists('FoundationPress_pagination') ) { FoundationPress_pagination(); } else if ( is_paged() ) { ?>
		<nav id="post-nav">
			<div class="post-previous"><?php next_posts_link( __( '&larr; Older posts', 'FoundationPress' ) ); ?></div>
			<div class="post-next"><?php previous_posts_link( __( 'Newer posts &rarr;', 'FoundationPress' ) ); ?></div>
		</nav>
	<?php } ?>

	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
