<?php
namespace Blorm;

class DisplayWidget extends \WP_Widget
{

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'blorm_widget', // Base ID
            esc_html__( 'Blorm show Posts', 'text_domain' ), // Name
            array( 'description' => esc_html__( 'A Foo Widget', 'text_domain' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {


        wp_enqueue_script( 'blorm-widget-init', plugins_url( 'blorm/assets/js/blorm_init_wp_widget.js'));

        $options = get_option( 'blorm_plugin_options_frontend' );

        if (isset( $options['display_config'] )) {
            if( $options['display_config'] ==  "do-not-show" ||
                $options['display_config'] ==  "display_config_loop" ||
                $options['display_config'] ==  "display_config_category" ||
                $options['display_config'] ==  "display_config_loop_and_category"
            ) return;
        }

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        if ( ! empty( $instance['numberOfPosts'] ) ) {
            $posts = $this->generateWidgetOutput($instance);
        }

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
            $cssClassWidget = ! empty( $instance['cssClassWidget'] ) ? $instance['cssClassWidget'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'cssClassWidget' ) ); ?>"><?php esc_attr_e( 'Css-class name widget:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cssClassWidget' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cssClassWidget' ) ); ?>" type="text" value="<?php echo esc_attr( $cssClassWidget ); ?>">
        </p>
        <?php
            $cssClassPost = ! empty( $instance['cssClassPost'] ) ? $instance['cssClassPost'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'cssClassPost' ) ); ?>"><?php esc_attr_e( 'Css-class name single post:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cssClassPost' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cssClassPost' ) ); ?>" type="text" value="<?php echo esc_attr( $cssClassPost ); ?>">
        </p>
        <?php
            $numberOfPosts = ! empty( $instance['numberOfPosts'] ) ? $instance['numberOfPosts'] : esc_html__( 'Number of Posts to display', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'numberOfPosts' ) ); ?>"><?php esc_attr_e( 'Show number of posts:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'numberOfPosts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'numberOfPosts' ) ); ?>" type="number" value="<?php echo esc_attr( $numberOfPosts ); ?>">
        </p>
        <?php
            $showImage = ! empty( $instance['showImage'] ) ? "checked" : "";
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'showImage' ) ); ?>"><?php esc_attr_e( 'Show image of posts:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'showImage' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'showImage' ) ); ?>" type="checkbox" <?php echo esc_attr( $showImage ); ?>>
        </p>
        <?php
            $showExcerpt = ! empty( $instance['showExcerpt'] ) ? "checked" : "";
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'showExcerpt' ) ); ?>"><?php esc_attr_e( 'Show excerpt of posts:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'showExcerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'showExcerpt' ) ); ?>" type="checkbox" <?php echo esc_attr( $showExcerpt ); ?>>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['numberOfPosts'] = ( ! empty( $new_instance['numberOfPosts'] ) ) ? sanitize_text_field( $new_instance['numberOfPosts'] ) : '';
        $instance['cssClassWidget'] = ( ! empty( $new_instance['cssClassWidget'] ) ) ? sanitize_text_field( $new_instance['cssClassWidget'] ) : '';
        $instance['cssClassPost'] = ( ! empty( $new_instance['cssClassPost'] ) ) ? sanitize_text_field( $new_instance['cssClassPost'] ) : '';
        $instance['showImage'] = $new_instance['showImage'];
        $instance['showExcerpt'] = $new_instance['showExcerpt'];

        return $instance;
    }


    private function generateWidgetOutput($instance) {

        $blormposts = get_posts(array('post_type' => 'blormpost','numberposts' => $instance['numberOfPosts']));

        $cssClassWidget = "";
        $cssClassPost = "";

        if ( !empty($instance['cssClassWidget'])) $cssClassWidget = $instance['cssClassWidget'];
        if ( !empty($instance['cssClassPost'])) $cssClassPost = $instance['cssClassPost'];

        echo "<div class='blorm-display-posts-widget ".$cssClassWidget."'>";
        foreach ($blormposts as $blormpost) {

            $a = get_post_meta($blormpost->ID);

            $acivityId = "";
            $showImage = "off";
            $showExcerpt = "off";
            if (isset($a["blorm_reblog_activity_id"])) $acivityId = $a['blorm_reblog_activity_id'][0];
            if (isset($a["blorm_reblog_teaser_url"])) $post_url = $a['blorm_reblog_teaser_url'][0];
            if (isset($instance['showImage'])) $showImage = $instance['showImage'];
            if (isset($instance['showExcerpt'])) $showExcerpt = $instance['showExcerpt'];

            //echo $blormpost->post_content;
            echo "<div class='blorm-display-posts-widget-element ".$cssClassPost."' data-postid='".$blormpost->ID."' data-activityid='".$acivityId."'>";
            echo "<div class='blorm-display-posts-widget-element-title'><span class=\"material-icons\">content_copy</span><a href='".$post_url."'>".get_the_title($blormpost)."</a></div>";

            if ($showImage == "on" ) {
                echo "<div class='blorm-display-posts-widget-element-image'><a href='".$post_url."'>".get_the_post_thumbnail($blormpost)."</a></div>";
            }

            if ($showExcerpt == "on" ) {
                echo "<div class='blorm-display-posts-widget-element-excerpt'><a href='".$post_url."'>".get_the_excerpt($blormpost) . "</a></div>";
            }

            echo "</div>";
        }
        echo "</div>";
    }


}

add_action( 'widgets_init', function(){
    register_widget( 'Blorm\DisplayWidget' );
});