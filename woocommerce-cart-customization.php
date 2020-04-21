<?php
/**
 * Plugin Name: Cart Customization
 * Plugin URI: https://magento360.com/
 * Description: A plugin Customizing WooCommerce Cart And Products.
 * Author:  Kavita Mehta.
 * Author URI: https://magento360.com/
 * Version: 1.0
 */
if ( ! class_exists( 'WC_Cart_Customization' ) ) :
class WC_Cart_Customization {
  /**
  * Construct the plugin.
  */
  public function __construct() {
    add_action( 'plugins_loaded', array( $this, 'init' ) );
  }
  /**
  * Initialize the plugin.
  */
  public function init() {
    // Checks if WooCommerce is installed.
    if ( class_exists( 'WC_Integration' ) ) {
      // Include our integration class.
      include_once 'class-wc-integration-cart-customization.php';
      // Register the integration.
      add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
    }
    // Set the plugin slug
    define( 'CART_CUSTOMIZATION_SLUG', 'wc-settings' );
    // Setting action for plugin
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'WC_cart_customization_action_links' );
    function cc_validate_plugin_settings(){
        $settings= cc_get_plugin_settings();
        $enable = $settings['enable'];
        if($enable == 'yes'){
          return 1;
        }
    }
    function cc_get_plugin_settings(){
      $settings= get_option( 'woocommerce_cart_customization_integration_settings' );
      $settings['if_sku'] = explode(",",$settings['if_sku']);
      $settings['result_sku'] = explode(",",$settings['result_sku']);
      return $settings;
    }
    function cc_get_product_id_by_sku($sku){
      $productId= wc_get_product_id_by_sku($sku);
      return $productId;
    }
    function cc_search_product_in_cart_by_id($product_id){
      $cart_product_id = WC()->cart->generate_cart_id( $product_id );
      $is_in_cart = WC()->cart->find_product_in_cart( $cart_product_id );
      return $is_in_cart;
    }

    //For Price Customization on Cart Page
    add_action( 'woocommerce_before_calculate_totals', 'cc_search_for_product_in_cart' );
    function cc_search_for_product_in_cart($cart_object){
      $validation = cc_validate_plugin_settings();
      if($validation){
        $settings = cc_get_plugin_settings();
        $check_sku = $settings['if_sku'];
        $customise_sku = $settings['result_sku'];
        $customise_product_ids = array();
        foreach($customise_sku as $result){
          array_push($customise_product_ids,cc_get_product_id_by_sku($result));
        }
        foreach ( $cart_object->cart_contents as $key => $value ){
               $cart_sku=$value['data']->get_sku();
               foreach($customise_product_ids as $customise_product_id){
                 $is_in_cart=cc_search_product_in_cart_by_id($customise_product_id);
                 foreach($check_sku as $value){
                   if($cart_sku == $value && $is_in_cart){
                     foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                          if ( $cart_item['product_id'] == $customise_product_id ){
                               $product = WC()->cart->get_cart_item($cart_item_key);
                               $product['data']->set_price($settings['special_price']);
                          }
                     }
                   }
                 }
               }
        }
      }
    }
//For price cusstomization on Shop/Product Page
    add_filter('woocommerce_get_price', 'cc_return_custom_price', 10, 2);
      function cc_return_custom_price($price,$product){
        $validation = cc_validate_plugin_settings();
        global $post;
        if($validation){
            $settings = cc_get_plugin_settings();
            $customise_sku = $settings['result_sku'];
            $check_sku = $settings['if_sku'];
            $check_product_id =array();
            $post_id = $post->ID;
            $customise_product_ids = array();
            foreach($customise_sku as $result){
              array_push($customise_product_ids,cc_get_product_id_by_sku($result));
            }
            foreach($check_sku as $value){
                array_push($check_product_id, cc_get_product_id_by_sku($value));
            }
          foreach($customise_product_ids as $customise_product_id){
            foreach($check_product_id as $data){
              $is_in_cart = cc_search_product_in_cart_by_id($data);
              if($is_in_cart && $post_id == $customise_product_id){
                $price = $settings['special_price'];
              }
            }
          }
          return $price;
        }
        else{
          return $price;
        }
      }
  }
  /**
   * Add a new integration to WooCommerce.
   */
  public function add_integration( $integrations ) {
    $integrations[] = 'WC_Cart_Customization_Integration';
    return $integrations;
  }
}
$WC_Cart_Customization = new WC_Cart_Customization( __FILE__ );
function WC_cart_customization_action_links( $links ) {

    $links[] = '<a href="'. menu_page_url( CART_CUSTOMIZATION_SLUG, false ) .'&tab=integration">Settings</a>';
    return $links;
  }

endif;
 ?>
