<?php
/**
 * @Package Wordpress
 * @SubPackage Widgets
 *
 * Plugin Name: StageHQ Widget
 * Description: Displays a StageHQ ticket form
 * Version: 1.0.0
 * Author: SYN-ACK
 * Author URI: http://syn-ack.se
 *
 */

defined('ABSPATH') or die("Cannot access pages directly.");

/**
 * Initializing
 *
 * The directory separator is different between linux and microsoft servers.
 * Thankfully php sets the DIRECTORY_SEPARATOR constant so that we know what
 * to use.
 */
defined("DS") or define("DS", DIRECTORY_SEPARATOR);

/**
 * Actions and Filters
 *
 * Register any and all actions here. Nothing should actually be called
 * directly, the entire system will be based on these actions and hooks.
 */
add_action( 'widgets_init', create_function( '', 'register_widget("StageHQ_Widget");' ) );


/**
 * StageHQ widget class
 */
class StageHQ_Widget extends WP_Widget {

  function __construct() {

    $locale = get_locale();
    if( !empty( $locale ) ) {
      $mofile = dirname(__FILE__) . "/lang/" .  $locale . ".mo";
      if(@file_exists($mofile) && is_readable($mofile))
        load_textdomain('synack', $mofile);
    }

    $widget_ops = array('classname' => 'widget_stagehq', 'description' => __('Displays a StageHQ ticket form', 'synack'));

    $control_ops = array('width' => 250, 'height' => 150);

    parent::__construct('stagehq', __('StageHQ'), $widget_ops, $control_ops);
  }

  function widget( $args, $instance ) {
    extract($args);
    $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
    $stagehq_account = apply_filters( 'widget_stagehq_account', $instance['stagehq_account'], $instance );
    $stagehq_id = apply_filters( 'widget_stagehq_id', $instance['stagehq_id'], $instance );

    if ( !empty( $stagehq_account ) && !empty( $stagehq_id ) ) {

      echo $before_widget;

      if ( !empty( $title ) ) {
        echo $before_title . $title . $after_title;
      }
    ?>
    <div class="widget-content">
      <iframe src="https://<?php echo $stagehq_account; ?>.stagehq.com/events/<?php echo $stagehq_id; ?>/external" frameborder="0" scrolling="auto"></iframe>
    </div>
    <?php
      echo $after_widget;
    }
  }

  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['stagehq_account'] = strip_tags($new_instance['stagehq_account']);
    $instance['stagehq_id'] = strip_tags($new_instance['stagehq_id']);
    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'stagehq_account' => '', 'stagehq_id' => '' ) );
    $title = strip_tags($instance['title']);
    $stagehq_account = strip_tags($instance['stagehq_account']);
    $stagehq_id = strip_tags($instance['stagehq_id']);
?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" placeholder="<?php _e('Buy tickets now', 'synack'); ?>">
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('stagehq_account'); ?>"><?php _e('StageHQ account slug'); ?></label><br>
      <input class="widefat" id="<?php echo $this->get_field_id('stagehq_account'); ?>" name="<?php echo $this->get_field_name('stagehq_account'); ?>" type="text" value="<?php echo esc_attr($stagehq_account); ?>" placeholder="XXXXXX"><br>
      <small>Extract your account slug from the StageHQ booking URL, i.e. <em><strong>XXXXXX</strong>.stagehq.com.</em></small>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('stagehq_id'); ?>"><?php _e('StageHQ event ID'); ?></label><br>
      <input class="widefat" id="<?php echo $this->get_field_id('stagehq_id'); ?>" name="<?php echo $this->get_field_name('stagehq_id'); ?>" type="text" value="<?php echo esc_attr($stagehq_id); ?>" placeholder="NNNN"><br>
      <small>Extract the four-digit ID number from the StageHQ booking URL, i.e. <em>/events/<strong>NNNN</strong>/booking/new.</em></small>
    </p>
<?php
  }
}
