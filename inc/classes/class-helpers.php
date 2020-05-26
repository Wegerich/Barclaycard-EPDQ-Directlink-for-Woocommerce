<?php
/*-----------------------------------------------------------------------------------*/
/*	AG ePDQ Helper
/*-----------------------------------------------------------------------------------*/
defined('ABSPATH') or die("No script kiddies please!");


if (class_exists('AG_ePDQ_Direct_Helpers')) {
	return;
}


class AG_ePDQ_Direct_Helpers
{

	/**
	 * Log errors in debug file
	 *
	 * @param $message
	 * @param $level
	 * @param $log
	 * @return void
	 */
	public static function ag_log($message, $level, $log)
	{
		if ($log == 'yes' || WP_DEBUG === true ) {
	
			$logger = wc_get_logger();

			// Log PHP version
			if (version_compare(phpversion(), '7', '>')) {
				$php = 'PHP version is fine';
				$php_level = 'debug';
			} elseif (version_compare(phpversion(), '7', '===')) {
				$php = 'You are using a old version of PHP, it is important that you update to something above 7.1';
				$php_level = 'warning';
			} else {
				$php = 'You are using a very old version of PHP, it is important that you update to something above 7.1 as this current version is not supported';
				$php_level = 'warning';
			}
			//$logger->$php_level( $php, array( 'source' => 'barclaycard-epdq-direct-link-payment-gateway-woocommerce-premium' ) );

			// Log errors in WooCommerce logs
			$logger->$level( $message, array( 'source' => 'barclaycard-epdq-direct-link-payment-gateway-woocommerce-premium' ) );


			// Known plugin conflict
			//if( class_exists( 'WOOF' ) ) {
			//	$woof = 'If you are having issues please make sure you have updated the WOOF - WooCommerce Products Filter plugin. This plugin is blocking the data getting sent back from ePDQ';
			//	$logger->debug( $woof, array( 'source' => 'barclaycard-epdq-direct-link-payment-gateway-woocommerce-premium' ) );
			//}
			//if( class_exists( 'ITSEC_Core' ) ) {
			//	$ITSEC = 'If you are having issues please make sure you have updated the iThemes Security. Login to the settings for iThemes security and deactivate the "Filter long URL strings" option.';
			//	$logger->debug( $ITSEC, array( 'source' => 'barclaycard-epdq-direct-link-payment-gateway-woocommerce-premium' ) );
			//}

		
		}
	}


	/**
	 * Loop through returned order data and store.
	 *
	 * @param $post_id
	 * @param $meta
	 * @return void
	 */
	public static function update_order_meta_data($post_id, $meta)
	{
		if (!get_post($post_id) || !is_array($meta)) {
			return false;
		}

		foreach ($meta as $meta_key => $meta_value) {
			if (!empty($meta_value)) {
				update_post_meta($post_id, $meta_key, $meta_value);
			}
		}
	}

	/**
	 * Loop through returned order data and set as notes.
	 *
	 * @param $customer_order
	 * @param $meta
	 * @return void
	 */
	public static function update_order_notes($customer_order, $meta)
	{
		if (!is_array($meta)) {
			return false;
		}

		$order_notes = array();
		foreach ($meta as $key => $value) {
			if (!empty($value) && !empty($key)) {
				if ($value == '') continue;
				$order_notes[] =  $key . ' ' . $value . '<br />';
			}
		}
		$data_back = implode('', $order_notes);
		$customer_order->add_order_note($data_back);
	}


	/**
	 * Get post data if set
	 *
	 * @param string $name name of post argument to get
	 * @return mixed post data, or null
	 */
	public static function AG_get_post_data( $name ) {
		if ( isset( $_POST[ $name ] ) ) {
			return trim( $_POST[ $name ] );
		}
		return null;
	}


	/**
	 * Luhn check
	 *
	 * @param $account_number
	 * @return void
	 */
	public static function luhn_algorithm_check( $account_number ) {
		
		$sum = 0;

		// Loop through each digit and do the maths
		for ( $i = 0, $ix = strlen( $account_number ); $i < $ix - 1; $i++) {
			$weight = substr( $account_number, $ix - ( $i + 2 ), 1 ) * ( 2 - ( $i % 2 ) );
			$sum += $weight < 10 ? $weight : $weight - 9;
		}

		// If the total mod 10 equals 0, the number is valid
		return substr( $account_number, $ix - 1 ) == ( ( 10 - $sum % 10 ) % 10 );

	}

	/**
	 * SSL check
	 *
	 * @return void
	 */
	public static function do_ssl_check() {
		if( is_ssl() == false ) {
			echo "<div class=\"error\"><p>". sprintf( __( "<strong>%s</strong> is enabled, but you dont have an SSL certificate on your website. Please ensure that you have a valid SSL certificate.<br /><strong>ePDQ Direct Link will only work in test mode while there is no SSL</strong>" ), 'ePDQ Direct Link Checkout', admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) ."</p></div>";
		}
	}

}
