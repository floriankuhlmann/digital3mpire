<?php get_header(); ?>

<div class="row">
    <div class="small-12 index-headline columns" role="main">
        <h2 class="green font-effect-shadow-multiple font-effect-neon">
           Publish, connect & present:
        </h2>
    </div>
</div>
<div class="row ">
    <div class="small-12 medium-3 columns" role="main">
        <a href="<?php echo home_url() ?>/category/shows/">
        <div class="menubox">
            <h2 class="green font-effect-shadow-multiple font-effect-neon">
                <i class="fas fa-capsules"></i> Hosted IRL Shows
            </h2>
            <p>Since 2012 we have been hosting shows, performances and happenings with a focus on the digital arts community in Düsseldorf.</p>
        </div>
        </a>
    </div>
    <div class="small-12 medium-3 columns" role="main">
        <a href="<?php echo home_url() ?>/category/collaborations/">
            <div class="menubox">
                <div class="menubox-bg-coops">
                <h2 class="green font-effect-shadow-multiple font-effect-neon">
                    <i class="fas fa-hands-helping"></i> Our collabs
                </h2>
                <p>In recent years we have carried out collaborative projects with various institutions, galleries and festivals, both nationally and internationally.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="small-12 medium-3 columns" role="main">
        <a href="<?php echo home_url() ?>/category/nft-label/">
            <div class="menubox">
                <h2 class="green font-effect-shadow-multiple font-effect-neon">
                    <i class="fas fa-comments-dollar"></i> NFT label
                </h2>
                <p>Our latest project is the label for NFT-based aesthetic digital things. We also use it as a platform for experimental research in the crypto area.</p>
            </div>
        </a>
    </div>
    <div class="small-12 medium-3 columns" role="main">
        <a href="<?php echo home_url() ?>/about-the-namespace/">
            <div class="menubox">
                <h2 class="green font-effect-shadow-multiple font-effect-neon">
                    <i class="fas fa-tablet-alt"></i> NAMESPACE
                </h2>
                <p>NAMESPACE is our virtual showroom in the matrix. A digital metaspace to present creative aesthetic productions of groups and individuals. Please talk to us.</p>
            </div>
        </a>
    </div>
</div>
<br><br>
<div class="row">
    <div class="small-12 index-headline columns" role="main">
        <h2 class="green font-effect-shadow-multiple font-effect-neon">
            News and updates:
        </h2>
    </div>
</div>
<div class="row">
    <div class="small-12 medium-10 columns small-centered" role="main">

	<?php if ( have_posts() ) : ?>

		<?php do_action('foundationPress_before_content'); ?>

		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', get_post_format() ); ?>
		<?php endwhile; ?>

	<?php else : ?>
		<?php get_template_part( 'content', 'none' ); ?>
		<?php do_action('foundationPress_before_pagination'); ?>
	<?php endif;?>


	<?php if ( function_exists('FoundationPress_pagination') ) { FoundationPress_pagination(); } else if ( is_paged() ) { ?>
		<nav id="post-nav">
			<div class="post-previous"><?php next_posts_link( __( '&larr; Older posts', 'FoundationPress' ) ); ?></div>
			<div class="post-next"><?php previous_posts_link( __( 'Newer posts &rarr;', 'FoundationPress' ) ); ?></div>
		</nav>
	<?php } ?>

	<?php do_action('foundationPress_after_content'); ?>

	</div>
	<?php /* get_sidebar(); */ ?>
</div>
<?php get_footer(); ?>
