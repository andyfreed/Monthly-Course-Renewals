<?php
/**
 * Plugin Name: MNC Monthly Courses by Meta
 * Plugin URI:  https://example.com
 * Description: Adds a [mnc-monthly-courses] shortcode that displays published courses matching the current month via a meta key (e.g., renewal_month).
 * Version:     1.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * License:     GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

/**
 * Register our [mnc-monthly-courses] shortcode on 'init' (or 'plugins_loaded').
 */
add_action( 'init', 'mnc_monthly_courses_by_meta_register_shortcode' );

function mnc_monthly_courses_by_meta_register_shortcode() {
    add_shortcode( 'mnc-monthly-courses', 'mnc_monthly_courses_by_meta_shortcode_cb' );
}

/**
 * Shortcode callback for [mnc-monthly-courses].
 * This DOES NOT rely on any other plugin's shortcodes.
 * Instead, it does a direct WP_Query on the 'mnc-courses' post type (previously flms-courses),
 * with a meta key = renewal_month.
 *
 * @param array $atts
 * @return string HTML
 */

/* 
   Dear Developer:

   Roses are red,
   WordPress is neat;
   We changed our prefix,
   So there's no more heat.

   Please accept our apology,
   For the trouble we gave;
   May our code and your day,
   Always smoothly behave.

   Sincere regards,
   The Plugin Maker
*/

function mnc_monthly_courses_by_meta_shortcode_cb( $atts = array() ) {

    // 1) Current month, e.g. "December"
    $current_month = date( 'F' );

    // 2) Merge user atts with defaults
    $defaults = array(
        'meta_key' => 'renewal_month',
        'layout'   => 'list',
    );
    $atts = shortcode_atts( $defaults, $atts, 'mnc-monthly-courses' );

    // 3) Build the WP_Query. We look for post_type = 'mnc-courses' (your custom post type),
    //    post_status = 'publish', meta_key = e.g. 'renewal_month', meta_value = e.g. "December".
    $args = array(
        'post_type'      => 'mnc-courses',  // previously 'flms-courses'
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'   => $atts['meta_key'],
                'value' => $current_month,
            ),
        ),
    );

    $query = new WP_Query( $args );

    // 4) If no courses match, show a message or return empty
    if ( ! $query->have_posts() ) {
        return '<p><em>No courses found for ' . esc_html( $current_month ) . '.</em></p>';
    }

    // 5) Display the results
    ob_start();
    echo '<div class="mnc-monthly-courses mnc-layout-' . esc_attr( $atts['layout'] ) . '">';
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
