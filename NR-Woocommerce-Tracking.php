<?php

/*

Plugin Name: NR-Woocommerce-Tracking

Plugin URI: https://blahblah

Description: Plugin to test ecommerce tracking.

Version: 1.0

Author: Some Guy

Author URI:

License: GPLv2 or later

Text Domain: Testing NR with Woocommerce

*/


add_action('admin_menu', 'test_ecom_nr_setup_menu');
function test_ecom_nr_setup_menu(){
    add_menu_page( 'Test Ecom NR Plugin Page', 'New Relic for Woo', 'manage_options', 'test-ecom-nr-plugin', 'test_ecom_nr_init' );
}
function test_ecom_nr_init(){
        ?>
<div class="wrap">

<style>
.button {
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 36px;
  margin: 4px 2px;
  cursor: pointer;
}

.button1 {background-color: #4CAF50;} /* Green */
.button2 {background-color: #008CBA;} /* Blue */
</style>

<h1><?php esc_html_e( 'New Relic for WordPress', 'NR-Woocommerce-Tracking' ) ?></h1>
    <button class="button button2">Link to New Relic Account</button>
</div>
<?php
}

/*
Example Woocommerce Data --> Send to NR for tracking store performance
*/
add_action('woocommerce_thankyou', 'send_to_nr', 10, 1);
function send_to_nr( $order_id ) {

    if ( ! $order_id )
        return;

    // Getting an instance of the order object
    $order = wc_get_order( $order_id );

    // grab the order total
    $order_total = $order->get_total();

    // grab line items from the order
    //$line_items = $order->get_items();

    //Grab Billing Email
    $order_email = $order->get_billing_email();

    //Get Order ID
    $order_id = $order->get_id();

    //Setting count variable for item count
    $item_count = 0;
    // Loop over line items to get product
    foreach ( $order->get_items() as $item_id => $item ) {

        // This will be a product
        $product = $item->get_product();

        //get product id
        $product_name = $product->get_id();

        //use id to get name
        $product_name = $item->get_name();
        $total = $order->get_line_total( $item, true, true );

        if ( $total > 0 ) {
                 $item_count++;
        }
        }



    if (extension_loaded('newrelic')) { // Ensure PHP agent is available

      //Send woocommerce data to NR
      newrelic_name_transaction("Successful_Checkout");
      newrelic_add_custom_parameter ('order_email', $order_email);
      newrelic_add_custom_parameter ('totalItems_in_cart', $item_count);
      newrelic_add_custom_parameter ('order_total', $order_total);
      newrelic_add_custom_parameter ('order_id', $order_id);
    }

}

//Track Google Session ID for customer performance tracking
add_action( 'init', 'gaCookie' );
function gaCookie() {
    if(isset($_COOKIE['_gid'])) {
        $googleSessionID = $_COOKIE['_gid'];
        newrelic_add_custom_parameter ('GoogleSessionID', $googleSessionID);
    }
}

?>
