<?php
/**
 * Plugin Name: Lncj Variations Price
 * Plugin URI: http://zonzon09.com/
 * Description:       Add Variation with price
 * Version:           1.0
 * Requires at least: 5.2
 * Author:            lncj
 * Author URI:        https://profiles.wordpress.org/longseej/
 * License:           GPL v2 or later
 * Text Domain:       lncj-variations
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

function lncj_attr_price_checkbox_assets() {
    wp_enqueue_script( 'wc-variation-js',  plugin_dir_url( __FILE__ ).'assets/js/add-to-cart-variation.js', array( 'jquery', 'wp-util' ));
    wp_enqueue_style( 'wc-variation-css', plugin_dir_url( __FILE__ ).'assets/css/add-to-cart-variation.css' );
}

add_action('wp_footer', 'lncj_attr_price_checkbox_assets');

add_action( 'plugins_loaded', 'lncj_attr_price', 10 );
function lncj_attr_price(){
    add_action('woocommerce_dropdown_variation_attribute_options_html','lncj_attr_price_checkbox', 10, 2);
    function lncj_attr_price_checkbox($html, $args){
        $radio = '';
        $product   = $args['product'];
        $id        = $args['id'] ? $args['id'] : sanitize_title( $args['attribute'] );
        $attribute = $args['attribute'];
        $name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );

        if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
        }
        
        if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
                $i = 0;
                $checked = '';
                $active = '';
                foreach($product->get_available_variations() as $variation ){
                    if( $i == 0 ){
                        $checked =  " checked='checked'";
                        $active = "active";
                    }else{
                        $checked = "";
                        $active = "";
                    }
                    $term = get_term_by('slug', $variation['attributes'][$name], $id);
                    $radio .= '<div class="item-variation ' . $active . '"><div class="in"><input ' .$checked. ' id="' . $id . '" data-attribute_name="' . $name . '" type="radio" name="' . $name . '" value="' . esc_attr( $variation['attributes'][$name] ) . '" ' . checked( sanitize_title( $args['selected'] ), $variation['attributes'][$name], false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name_woo_attr', $term->name, $term, $attribute, $product ) );
                    $radio .= '<label>' . wc_price($variation['display_price']) . '</label></div></div>';
                    $i ++;
                }
            }else {
				foreach ( $options as $option ) {
					$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
                    $radio    .= '<input name="' . $name . '" type="radio" value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name_woo_attr', $option, null, $attribute, $product ) );
				}
			}
        }
        return $radio;
    }

}
