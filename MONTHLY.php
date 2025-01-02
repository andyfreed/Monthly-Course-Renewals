<?php
/**
 * Plugin Name: FLMS Monthly Courses by Meta
 * Plugin URI:  https://example.com
 * Description: Adds a [flms-monthly-courses] shortcode that displays published flms-courses matching the current month via a meta key (e.g., renewal_month).
 * Version:     1.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * License:     GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

/**
 * Register our [flms-monthly-courses] shortcode on 'init' (or 'plugins_loaded').
 */
add_action( 'init', 'flms_monthly_courses_by_meta_register_shortcode' );

function flms_monthly_courses_by_meta_register_shortcode() {
    add_shortcode( 'flms-monthly-courses', 'flms_monthly_courses_by_meta_shortcode_cb' );
}

/**
 * Shortcode callback for [flms-monthly-courses].
 * This DOES NOT rely on FLMS_Shortcodes->course_list().
 * Instead, it does a direct WP_Query on 'flms-courses' with a meta key = renewal_month.
 *
 * @param array $atts
 * @return string HTML
 */
function flms_monthly_courses_by_meta_shortcode_cb( $atts = array() ) {

    // 1) Current month, e.g. "December"
    $current_month = date( 'F' );

    // 2) Merge user atts with defaults
    //    meta_key is what you store in each course, e.g. 'renewal_month'
    //    layout is optional if you want a "grid" or something, but let's keep it simple.
    $defaults = array(
        'meta_key' => 'renewal_month',
        'layout'   => 'list',
        // You could allow columns or something, but we'll keep it simple.
    );
    $atts = shortcode_atts( $defaults, $atts, 'flms-monthly-courses' );

    // 3) Build the WP_Query. We look for post_type = 'flms-courses', post_status = 'publish',
    //    meta_key = e.g. 'renewal_month',
    //    meta_value = e.g. "December".
    $args = array(
        'post_type'      => 'flms-courses',
        'posts_per_page' => -1,  // or set to a limit if you want
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'   => $atts['meta_key'],
                'value' => $current_month,
            ),
        ),
    );

    $query = new WP_Query( $args );

    // 4) If no courses match, we can show a quick message or just return empty
    if ( ! $query->have_posts() ) {
        return '<p><em>No courses found for ' . esc_html( $current_month ) . '.</em></p>';
    }

    // 5) Display the results in a basic HTML list
    //    If you want to get fancier, you can do your own markup or call FLMS shortcodes, etc.
    ob_start();
    echo '<div class="flms-monthly-courses flms-layout-' . esc_attr( $atts['layout'] ) . '">';
    echo '<h3>Courses for ' . esc_html( $current_month ) . '</h3>';
    echo '<ul>';
    while ( $query->have_posts() ) {
        $query->the_post();
        echo '<li>';
        // Title
        echo '<a href="' . esc_url( get_permalink() ) . '">';
        the_title();
        echo '</a>';
        // Optional: excerpt, custom meta, etc.
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';

    wp_reset_postdata();
    return ob_get_clean();
}
