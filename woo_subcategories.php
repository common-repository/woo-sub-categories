<?php
/*
  Plugin Name: Woocommerce Sub Categories
  Plugin URI: www.test.com
  Description: Plugin will display child categories by a widget on category page.
  Version: 1.0.1
  Author: Manoj Dhiman
  Author URI: http://codingbin.com/
  License: A "Slug" license name e.g. GPL2
 */

// The widget class
define('WOO_SUB_VERSION', '1.0.0.');
define('WOO_SUB_DIR', __DIR__);
define('WOO_SUB_URL', plugins_url(basename(__DIR__)));

class woo_subcategories_Widget extends WP_Widget {

    // Main constructor
    public function __construct() {
        parent::__construct(
                'my_custom_widget', __('Woo Subcategories', 'wp_subcategories'), array(
            'customize_selective_refresh' => true,
                )
        );

        add_action('wp_enqueue_scripts', array($this, 'woo_subcat_scripts'));
    }

    // The widget form (for the backend )
    public function form($instance) {
        // Set widget defaults
        $defaults = array(
            'title' => '',
            'hide_empty' => false,
        );
        // Parse current settings with defaults
        extract(wp_parse_args((array) $instance, $defaults));
        ?>
        <?php // Widget Title   ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('hide_empty'); ?>"><?php _e('Hide Empty?', 'woo_subcategories'); ?></label>
            <select name="<?php echo $this->get_field_name('hide_empty'); ?>" id="<?php echo $this->get_field_id('hide_empty'); ?>" class="widefat">
                <?php
                // Your options array
                $options = array(
                    true => __('Yes', 'text_domain'),
                    false => __('No', 'text_domain'),
                );

                // Loop through options and add each one to the select dropdown
                foreach ($options as $key => $name) {
                    echo '<option value="' . esc_attr($key) . '" id="' . esc_attr($key) . '" ' . selected($hide_empty, $key, false) . '>' . $name . '</option>';
                }
                ?>
            </select>
        </p>
        <?php
    }

    // Update widget settings
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? wp_strip_all_tags($new_instance['hide_empty']) : '';
        return $instance;
    }

    public function woo_subcat_scripts() {
        wp_enqueue_style('woo_sub_style', WOO_SUB_URL . '/assets/woo_sub_style.css', array(), WOO_SUB_VERSION, false);
        wp_enqueue_script('woo_sub_script', WOO_SUB_URL . '/assets/woo_sub_script.js', array(), WOO_SUB_VERSION, false);
    }

    // Display the widget
    public function widget($args, $instance) {


        extract($args);
        $title = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        $hide_empty = isset($instance['hide_empty']) ? $instance['hide_empty'] : '';

        // WordPress core before_widget hook (always include )

        echo $before_widget;

        echo '<div class="woo_subcategories wp_widget_plugin_box">';
        $parentid = get_queried_object_id();


        if ($title) {
            echo $before_title . $title . $after_title;
        }

        $subcategories = $this->get_subcategories($parentid, $hide_empty);
        if (!empty($subcategories)) {
            //echo "<pre>"; print_r($subcategories); die;
            echo "<ul class='woo_subcategory woo_lvl_1'>";
            foreach ($subcategories as $key => $subcategory) {
                $isactive = $key == 0 ? 'woo_active' : '';
                echo "<li class='{$isactive} woo_subcategory_parent woo_subcategory $subcategory->slug'>"
                . "<a class='prnt_link' href='" . get_category_link($subcategory->term_id) . "'>{$subcategory->name}</a>";

                $subcats = $this->get_subcategories($subcategory->term_id, $hide_empty);
                if (!empty($subcats)) {
                    echo "<span class='acordion_sign woo_plus'>+</span><span class='acordion_sign woo_minus'>-</span>";
                    echo "<ul class='woo_nxt_lvl ul_labl_2 woo_subcategory woo_lvl_1'>";
                    foreach ($subcats as $key => $subcat) {
                        echo "<li class='woo_subcategory $subcat->slug'><a href='" . get_category_link($subcat->term_id) . "'>-{$subcat->name}</a>";
                        //check lbl 3
                        $subcats3 = $this->get_subcategories($subcat->term_id, $hide_empty);
                        if (!empty($subcats3)) {
                            echo "<ul class='woo_nxt_lvl ul_labl_3 woo_subcategory woo_lvl_1'>";
                            foreach ($subcats3 as $key => $subcat3) {
                                echo "<li class='woo_subcategory $subcat3->slug'><a href='" . get_category_link($subcat3->term_id) . "'>--{$subcat3->name}</a></li>";
                            }
                            echo "</ul>";
                        }
                        echo '</li>';
                    }
                    echo "</ul>";
                }
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<b>No subcategory found</b>";
        }
        echo '</div>';
    }

    protected function get_subcategories($parent, $hide_empty) {
        $cat_args = array(
            'parent' => $parent,
            'hide_empty' => $hide_empty,
        );
        $terms = get_terms('product_cat', $cat_args);
        
        return $terms;
    }

}

// Register the widget
function woo_subcategories_cb() {
    register_widget('woo_subcategories_Widget');
}

add_action('widgets_init', 'woo_subcategories_cb');
