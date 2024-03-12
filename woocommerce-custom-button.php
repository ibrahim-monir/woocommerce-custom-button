<?php
/*
Plugin Name: WooCommerce Custom Button
Description: Adds custom button to WooCommerce.
Version: 1.0.0
Author: Ibrahim Monir
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


// Add custom input field to product dashboard
add_action('woocommerce_product_options_general_product_data', 'add_custom_product_field');
function add_custom_product_field() {
    woocommerce_wp_text_input( 
        array( 
            'id' => '_custom_link', 
            'label' => __('Live Preview', 'woocommerce' ), 
            'placeholder' => __('Enter preview link...', 'woocommerce' ),
            'desc_tip' => 'true', 
            'description' => __( 'Enter the live preview link for this product.', 'woocommerce' ), 
        )
    );
}

//Make this field mandatory
add_action('woocommerce_admin_process_product_object', 'make_custom_field_mandatory', 10, 1);
function make_custom_field_mandatory($product) {
    if (isset($_POST['_custom_link']) && empty(sanitize_text_field($_POST['_custom_link']))) {
        throw new Exception(__('The Live Preview link cannot be empty.', 'woocommerce'));
    } elseif (isset($_POST['_custom_link'])) {
        $product->update_meta_data('_custom_link', sanitize_text_field($_POST['_custom_link']));
    }
}

function enqueue_custom_admin_js($hook) {
    global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'product' === $post->post_type ) {     
            wp_enqueue_script('my_custom_admin_js', plugin_dir_url(__FILE__) . 'js/custom-admin.js', array('jquery'), '', true);
        }
    }
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_js');


// Save custom product field
add_action('woocommerce_process_product_meta', 'save_custom_product_field');
function save_custom_product_field($post_id) {
    $custom_link = isset($_POST['_custom_link']) ? $_POST['_custom_link'] : '';
    update_post_meta($post_id, '_custom_link', sanitize_text_field($custom_link));
}

// Add custom button after shop loop item
add_action('woocommerce_after_shop_loop_item', 'add_custom_button', 15);
function add_custom_button() {
    global $product;

    // Ensure the product is defined, simple, and in stock
    if ($product && $product->is_type('simple') && $product->is_in_stock()) {
        $custom_link = get_post_meta($product->get_id(), '_custom_link', true);
        
        // Check if a custom link exists
        if (!empty($custom_link)) {
            // Output the custom button HTML
            echo '<hr class="live-preview-border">';
            echo '<a href="' . esc_url($custom_link) . '" class="preview-button">Live Preview</a>';
        }
    }
}
