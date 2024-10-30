<?php

/**
 * @link              https://itech-softsolutions.com
 * @since             1.0.0
 * @package           WooCommerce Catalog for Logged Out Users
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Catalog for Logged Out Users
 * Plugin URI:        
 * Description:       This plugin will hide add to cart button and price for logged out users
 * Version:           1.0.0
 * Author:            itechtheme 
 * Author URI:        https://itech-softsolutions.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       catlou
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Load Textdomain

class catlou_text_domain{

  	function __construct() {
    	add_action( 'plugins_loaded', array( $this,'load_textdomain' ) );
  	}

  	function load_textdomain() {
    	load_plugin_textdomain( 'catlou', true, plugin_dir_url( __FILE__ ) . "/languages" );
  	}
}

new catlou_text_domain(); 

class catlou_settings_tab {

    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_catlou', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_catlou', __CLASS__ . '::update_settings' );
    }
    
    
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_catlou'] = __( 'Catalog for Logged Out Users', 'catlou' );
        return $settings_tabs;
    }


    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }


    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }


    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {

        $settings = array(
            'section_title' => array(
                'name'     => __( 'Catalog for Logged Out Users', 'catlou' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_catlou_section_title'
            ),
            'button_label' => array(
                'name' => __( 'Button Label', 'catlou' ),
                'type' => 'text',
                'desc' => __( 'Please Enter Button Label', 'catlou' ),
                'id'   => 'wc_settings_tab_catlou_form_label',
                'default'   => 'Please login to purchase'
            ),
            'button_link' => array(
                'name' => __( 'Button Link', 'catlou' ),
                'type' => 'text',
                'desc' => __( 'Please Enter Button Link', 'catlou' ),
                'id'   => 'wc_settings_tab_catlou_form_link',
                'default'   => '/my-account'
            ),
            'style_section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_style_settings_tab_catlou_section_end'
            )
        );

        return apply_filters( 'catlou_settings_tab_settings', $settings );
    }

}

catlou_settings_tab::init();

add_action( 'init', 'catlou_hide_price_add_cart_not_logged_in' );
  
function catlou_hide_price_add_cart_not_logged_in() {   
   if ( ! is_user_logged_in() ) {      
      remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
      remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );   
      add_action( 'woocommerce_single_product_summary', 'catlou_print_login_to_see', 31 );
      add_action( 'woocommerce_after_shop_loop_item', 'catlou_print_login_to_see', 11 );
   }
}
  
function catlou_print_login_to_see() {
    $labels = get_option( 'wc_settings_tab_catlou_form_label' );
    $link = get_option( 'wc_settings_tab_catlou_form_link' );
?>
    <a href="<?php echo $link ?>"><?php echo $labels ?></a>
   <?php
}