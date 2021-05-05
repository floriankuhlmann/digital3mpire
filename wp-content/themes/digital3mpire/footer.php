</section>
<footer class="row">
	<?php do_action('foundationPress_before_footer'); ?>
	<?php dynamic_sidebar("footer-widgets"); ?>
	<?php do_action('foundationPress_after_footer'); ?>
</footer>
<a class="exit-off-canvas"></a>

	<?php do_action('foundationPress_layout_end'); ?>
	</div>
</div>
<?php wp_footer(); ?>
<?php do_action('foundationPress_before_closing_body'); ?>

<script src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/js/jquery.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/js/foundation.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/js/menu/velocity.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/js/menu/main.js"></script>

<script>
    $(document).foundation();
</script>
</body>
</html>
