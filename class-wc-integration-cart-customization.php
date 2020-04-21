<?php
/*
**
 * Integration Rule.
 *
 * @package   Woocommerce  plugin Integration
 * @category Integration
 * @author   Kavita Mehta
 */
if ( ! class_exists( 'WC_Cart_Customization_Integration' ) ) :
class WC_Cart_Customization_Integration extends WC_Integration {
  /**
   * Init and hook in the integration.
   */
  public function __construct() {
    global $woocommerce;
    $this->id                 = 'cart_customization_integration';
    $this->method_title       = __( 'Cart Customization Integration');
    $this->method_description = __( 'Cart Customization Integration to Customize WooCommerce Cart And Products .');
    // Load the settings.
    $this->init_form_fields();
    $this->init_settings();
    // Define user set variables.
    add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
  }
  /**
   * Initialize integration settings form fields.
   */
  public function init_form_fields() {

    $this->form_fields = array(
      'enable' => array(
        'title'             => __( 'Enable'),
        'type'              => 'select',
        'options'           =>array(
          'yes'        => __( 'Yes', 'woocommerce' ),
          'no'       => __( 'No', 'woocommerce' )
        ),
        'description'       => __( 'Choose Yes to Enable Plugin Functionality'),
        'desc_tip'          => false,
        'default'           => '',
        'css'      => 'width:170px;',
      ),
      'if_sku' => array(
        'title'             => __( 'IF SKU'),
        'type'              => 'text',
        'description'       => __( 'Enter Comma Separated Vales'),
        'desc_tip'          => false,
        'default'           => '',
        'css'      => 'width:170px;',
      ),
      'result_sku' => array(
        'title'             => __( 'Result SKU'),
        'type'              => 'text',
        'description'       => __( 'Enter Comma Separated Vales'),
        'desc_tip'          => false,
        'default'           => '',
        'css'      => 'width:170px;',
      ),
      'special_price' => array(
        'title'             => __( 'Special Price'),
        'type'              => 'text',
        'desc_tip'          => false,
        'default'           => '',
        'css'      => 'width:170px;',
      ),
    );
  }
}
endif;

 ?>
