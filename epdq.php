<?php
/*
 * Author: We are AG
 * Author URI: https://www.weareag.co.uk/
 * File: epdq.php
 * Project: barclaycard-epdq-direct-link-payment-gateway-woocommerce-premium
 * -----
 * Version: 2.1.10
 * WC requires at least: 3.0.0
 * WC tested up to: 3.8
 * License: GPL3
 
Plugin Name: AG Barclaycard ePDQ Direct Link  - WooCommerce Gateway
Plugin URI: https://www.weareag.co.uk/
Description: Extends WooCommerce by Adding the Barclaycard ePDQ Direct Link Payment Gateway.
*/
defined('ABSPATH') or die("No script kiddies please!");
define('AG_ePDQ_Direct_debug', plugin_dir_path(__FILE__) );


class AG_ePDQ_direct
{


	public static $AGversion = "2.1.4";

	public static $AG_ePDQ_Direct_slug = "barclaycard-epdq-direct-link-payment-gateway-woocommerce";


	public function __construct()
	{

		load_plugin_textdomain('ag-epdq-direct', false, dirname(plugin_basename(__FILE__)) . '/languages');
		$this->ePDQ_define_constants();
		$this->AG_ePDQ_classes();

		add_action('plugins_loaded', array($this, 'woocommerce_ag_epdq_direct_init'), 0);
		add_filter('woocommerce_payment_gateways', array($this, 'woocommerce_add_epdq_direct_gateway'));

		if (!AG_direct_licence::valid_licence()) {
			return;
		}
	}


	public function woocommerce_add_epdq_direct_gateway($methods)
	{

		//
		// TODO - Not quite ready for this release.
		//
		 
		//if ( class_exists( 'WC_Subscriptions_Order' ) && class_exists( 'WC_Payment_Gateway_CC' ) ) {
		//	$methods[] = 'epdq_direct_checkout_subscription';
		//} else {
			$methods[] = 'ag_epdq_checkout';
		//}
		return $methods;
	}

	private function ePDQ_define_constants() {
		define('AG_ePDQ_direct_path', plugin_dir_url(__FILE__));
		define('ePDQ_path', plugin_dir_path(__FILE__));
		define('ePDQ_class', ePDQ_path . 'inc/classes/');
		define('ePDQ_core', ePDQ_path . 'inc/AGcore/');
		define('ePDQ_admin', admin_url() );

	}

	public function woocommerce_ag_epdq_direct_init()
	{

		if (!class_exists('WC_Payment_Gateway')) {
			return;
		}

		require_once ePDQ_class . 'class-main.php';
		require_once ePDQ_class . 'class-epdq-error-codes.php';
		require_once ePDQ_class . 'class-gdpr.php';
		require_once ePDQ_class . 'class-helpers.php';
		require_once ePDQ_class . 'class-settings.php';
		require_once ePDQ_class . 'class-crypt.php';
		require_once ePDQ_class . 'class-functions.php';

		if ( class_exists( 'WC_Subscriptions_Order' ) && class_exists( 'WC_Payment_Gateway_CC' ) ) {
			require_once ePDQ_class . 'class-wc-subscriptions.php';
		}
		
	}

	public function AG_ePDQ_classes()
	{

		include_once ePDQ_core . 'class-ag-welcome.php';
		include_once ePDQ_core . 'class-ag-start-here-docs.php';
		include_once ePDQ_core . 'class-ag-up-sell.php';
		include_once ePDQ_core . 'class-ag-gateway-tips.php';
		include_once ePDQ_core . 'class-ag-licence.php';

		AG_direct_licence::run_instance(array(
			'basename' => plugin_basename(__FILE__),
			'urls'     => array(
				'product'  => 'https://weareag.co.uk/product/barclaycard-epdq-direct-link-payment-gateway-woocommerce/',
				'welcome' => admin_url('admin.php?page=' . self::$AG_ePDQ_Direct_slug),
				'account'  => admin_url('admin.php?page=' . self::$AG_ePDQ_Direct_slug),
			),
			'paths'    => array(
				'plugin' => plugin_dir_path(__FILE__),
			),
			'freemius' => array(
				'id'         => '2189',
				'slug'       => self::$AG_ePDQ_Direct_slug,
				'public_key' => 'pk_a0106212950855f65d7636c1442cd',
				'trial'            => array(
					'days'               => 7,
					'is_require_payment' => true,
				),
				'has_affiliation'     => 'customers',
				'menu'       => array(
					'slug'    => self::$AG_ePDQ_Direct_slug,
					'first-path'     => 'admin.php?page=AG_plugins',
					'parent'         => array(
						'slug' => 'AG_plugins',
					),
				),
			),
			'update'    => array(
				'plugin' => 'barclaycard-epdq-direct-link-payment-gateway-woocommerce-premium/epdq.php',
				'name' => '',
				'update_notice' => false,
				'message'	=>	'',
			),
		));

		AG_direct_welcome_screen::run_instance(array(
			'parent_slug'   => self::$AG_ePDQ_Direct_slug,
			'main_slug'   => self::$AG_ePDQ_Direct_slug,
			'collection' => '21-barclaycard-epdq-direct-link',
			'plugin_title'         => 'Barclaycard ePDQ Direct Link for WooCommerce',
			'plugin_version'       => self::$AGversion,
		));

		AG_direct_start_here_docs::run_instance(array(
			'start_here' => 'category/24-category',
			'troubleshooting' => 'category/32-category',
			'plugin_slug'   => self::$AG_ePDQ_Direct_slug
		));

		AG_direct_up_sell::run_instance(array(
			'plugins'   => array(
				'sagepay_direct',
				'pay360',
				'auth',
				'lloyds'
			),
			'plugin_slug'   => self::$AG_ePDQ_Direct_slug,
		));


		AG_direct_gateway_tips::run_instance(array(
			'tips'   => array(
				'for_you',
				'PCI',
				'payments_101',
				'luhn',
			),
			'plugin_slug'   => self::$AG_ePDQ_Direct_slug,
		));
	}

	public static function activate()
	{
		set_transient('AG_welcome_screen_activation', true, 30);
	}
}

$AG_ePDQ_direct = new AG_ePDQ_direct();
register_activation_hook(__FILE__, array($AG_ePDQ_direct, 'activate'));




