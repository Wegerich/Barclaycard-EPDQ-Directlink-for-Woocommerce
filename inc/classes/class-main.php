<?php
/*
 * Author: We are AG
 * Author URI: https://www.weareag.co.uk/
 * File: epdq-class.php
 * Project: barclaycard-epdq-direct-link-payment-gateway-woocommerce-premium
 * -----
 * Version: 2.1.5
 * WC requires at least: 3.0.0
 * WC tested up to: 3.7
 * License: GPL3
 */

/* Barclaycard ePDQ Direct Link Payment Gateway Class */

defined('ABSPATH') or die("No script kiddies please!");

class ag_epdq_checkout extends WC_Payment_Gateway
{


	/**
	 * Plugin Doc link
	 *
	 * @var string
	 */
	public static $AG_ePDQ_doc = "https://we-are-ag.helpscoutdocs.com/";


	/**
	 * Construct
	 */
	function __construct()
	{

		$this->id = "ag_epdq_checkout";
		$this->method_title = __("ePDQ Direct Link Checkout", 'ag_epdq_checkout');

		if (!AG_direct_licence::valid_licence()) {
			return;
		}

		$this->method_description = __("Barclaycard ePDQ Direct Link Payment Gateway for WooCommerce", 'ag_epdq_checkout');
		$this->title = __("Barclaycard ePDQ Direct Link", 'ag_epdq_checkout');
		$this->has_fields = true;
		$this->supports = array(
			'default_credit_card_form',
			'refunds',
			//'subscriptions',
			//'gateway_scheduled_payments',
			'products'
		);

		add_action('woocommerce_api_woocommerce_ag_epdq_checkout', array($this, 'successful_request'));
		add_action('woocommerce_receipt_ag_epdq_checkout', array($this, 'receipt_page'));
		add_action('woocommerce_api_ag_epdq_checkout', array($this, 'check_response'));
		add_action('wp_enqueue_scripts', array($this, 'ePDQ_CSS'));

		$this->notify_url = WC()->api_request_url('ag_epdq_checkout');
		$this->init_form_fields();
		$this->init_settings();


		// Turn these settings into variables we can use
		foreach ($this->settings as $setting_key => $value) {
			$this->$setting_key = $value;
		}

		$this->icon = apply_filters('woocommerce_ag_epdq_checkout_icon', '');

		add_action('admin_notices', array('AG_ePDQ_Direct_Helpers',	'do_ssl_check'));

		// Save settings
		if (is_admin()) {
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		}
	} // End __construct()


	/**
	 * ePDQ CSS
	 *
	 * @return void
	 */
	public function ePDQ_CSS()
	{
		if(is_checkout()) {
		
			wp_enqueue_style('ePDQ', AG_ePDQ_direct_path . 'assets/css/style.css');

		}
	}

	/**
	 * Get icon/s to use
	 *
	 * @return void
	 */
	function get_icon()
	{

		$icon = '';
		if (!$this->cardtypes) {
			// default behavior
			$icon = '<img src="' . AG_ePDQ_direct_path . 'img/new_cards.png" alt="' . $this->title . '" />';
		} elseif ($this->cardtypes) {
			// display icons for the selected card types
			$icon = '';
			foreach ($this->cardtypes as $cardtype) {
				$icon .= '<img style="margin-left:5px; margin-right: 5px;" src="' . AG_ePDQ_direct_path . 'img/new-card/' . strtolower($cardtype) . '.png" alt="' . strtolower($cardtype) . '" />';
			}
		}

		return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
	}

	/**
	 * Get plugin settings
	 *
	 * @return void
	 */
	public function init_form_fields()
	{
		$this->form_fields = AG_ePDQ_Direct_Settings::form_fields();
	}


	/**
	 * Build card form
	 *
	 * @param array $args
	 * @param array $fields
	 * @return void
	 */
	public function credit_card_form($args = array(), $fields = array())
	{

		if ( is_wc_endpoint_url( 'order-pay' ) && ! is_user_logged_in()  ) { 
			echo '<p><b>' . __('Please <a href="'. get_permalink( get_option('woocommerce_myaccount_page_id') ) .'">sign in</a> to your account and then make your payment.', 'ag_epdq_checkout') . '</b></p>';
			return false;
		}

		wp_enqueue_script('wc-credit-card-form');


		if ($this->environment == "yes") { ?>
		<p class="form-row form-row-wide"><br /><strong>TEST MODE ACTIVE</strong><br />In test mode you can run test payments using card number <strong>4444 3333 2222 1111</strong> with any CVC number and a valid expiration date. Check our doc's for more information <a target="_blank" href="https://www.weareag.co.uk/product/barclaycard-epdq-direct-link-payment-gateway-woocommerce/">here</a>.<br /><br />If 3DSecure is enabled on the account you can test this also, the password for testing is 11111.<br /><br /></p>
		<p>Click a button to prefill the card information below</p>
		<button id="visa-card">Visa test card</button> - <button id="3D-card">3DSv2 test card</button><br /><br />

	<?php }

$default_args = array(
	'fields_have_names' => true,
);

$args = wp_parse_args($args, apply_filters('woocommerce_credit_card_form_args', $default_args, $this->id));

$default_fields = array(
	'card-number-field' => '<p class="form-row form-row-wide">
	      <label for="' . esc_attr($this->id) . '-card-number">' . __('Card Number', 'woocommerce') . ' <span class="required">*</span></label>
	      <input id="' . esc_attr($this->id) . '-card-number" class="input-text wc-credit-card-form-card-number" type="tel" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="' . ($args['fields_have_names'] ? $this->id . '-card-number' : '') . '" />
	    </p>',
	'card-expiry-field' => '<p class="form-row form-row-first">
	      <label for="' . esc_attr($this->id) . '-card-expiry">' . __('Expiry (MM/YY)', 'woocommerce') . ' <span class="required">*</span></label>
	      <input id="' . esc_attr($this->id) . '-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="tel" autocomplete="off" placeholder="' . __('MM / YY', 'woocommerce') . '" name="' . ($args['fields_have_names'] ? $this->id . '-card-expiry' : '') . '" />
	    </p>',
	'card-cvc-field' => '<p class="form-row form-row-last">
	      <label for="' . esc_attr($this->id) . '-card-cvc">' . __('CVC', 'woocommerce') . ' <span class="required">*</span> <a href="#" data-tooltip="3-digit security code usually found on the back of your card. American Express cards have a 4-digit code located on the front."><small>What is this?</small></a></label>
	      <input id="' . esc_attr($this->id) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="tel" autocomplete="off" placeholder="' . __('CVC', 'woocommerce') . '" name="' . ($args['fields_have_names'] ? $this->id . '-card-cvc' : '') . '" />
	    </p>'
);

$fields = wp_parse_args($fields, apply_filters('woocommerce_credit_card_form_fields', $default_fields, $this->id));
?>

<script>
	jQuery(window).ready(function($) {

		$("#visa-card").on("click", function() {
			$("[name='ag_epdq_checkout-card-number']").val("4444333322221111");
			$("[name='ag_epdq_checkout-card-expiry']").val("1022");
			$("[name='ag_epdq_checkout-card-cvc']").val("123");
		});
		$("#3D-card").on("click", function() {
			$("[name='ag_epdq_checkout-card-number']").val("4330264936344675");
			$("[name='ag_epdq_checkout-card-expiry']").val("1022");
			$("[name='ag_epdq_checkout-card-cvc']").val("123");
		});

	});

	</script>

	<script type="text/javascript" language="javascript">


	function createHiddenInput(form, name, value) {
		var input = document.createElement("input");
		input.setAttribute("type", "hidden");
		input.setAttribute("name", name); 
		input.setAttribute("value", value);
		form.appendChild(input);
	}

	var AG3DSForms = document.getElementsByName("payment_method");
	if (AG3DSForms != null && AG3DSForms.length > 0) {
		var AG3DSForm = AG3DSForms[0];
		createHiddenInput(AG3DSForm, "browserUserAgent", navigator.userAgent);
		createHiddenInput(AG3DSForm, "browserColorDepth", screen.colorDepth);
		createHiddenInput(AG3DSForm, "browserJavaEnabled", navigator.javaEnabled());
		createHiddenInput(AG3DSForm, "browserLanguage", navigator.language);
		createHiddenInput(AG3DSForm, "browserScreenHeight", screen.height);
		createHiddenInput(AG3DSForm, "browserScreenWidth", screen.width);
		createHiddenInput(AG3DSForm, "browserTimeZone", new Date().getTimezoneOffset());
	}
	</script>

	<fieldset id="<?php echo $this->id; ?>-cc-form">
		<?php do_action('woocommerce_credit_card_form_start', $this->id); ?>
		<?php
		foreach ($fields as $field) {
			echo $field;
		}
		?>
		<?php do_action('woocommerce_credit_card_form_end', $this->id); ?>
		<div class="clear"></div>
	</fieldset>



<?php
}


/**
 * Check data sent back from ePDQ
 *
 * @return void
 */
public function check_response()
{
	ob_clean();
	header('HTTP/1.1 200 OK');

	$datacheck1 = array();
	$datatocheck = array();

	foreach ($_REQUEST as $key => $value) {
		if ($value == "") {
			continue;
		}
		$datatocheck[$key] = $value;
		$datacheck1[strtoupper($key)] = strtoupper($value);
	}
	//GJ line
	AG_ePDQ_Direct_Helpers::ag_log("Checking response from ePDQ  (_REQUEST) " . implode("|",$_REQUEST), 'debug', $this->debug);

	//When the data was sent the order number was hashed to give a ripemd160 hash
	//Re-generate that hash and compare against the value returned from ePDQ to serve as an extra security check.
	$nonce = $_REQUEST['COMPLUS'];
	$OrderIDtoVerify = $_REQUEST['orderID'];
	$InstanceofOrderObject = wc_get_order( $OrderIDtoVerify );
	$hash_fields = array($_REQUEST['orderID']);
	$encrypted_string = hash_hmac('ripemd160', implode($hash_fields), $this->sha_in);

	if (isset($_REQUEST['STATUS'])) {
	
		if (hash_equals($encrypted_string, $nonce)) {

			if (!empty($this->sha_out)) {

				$SHA_check = $this->SHA_check($datatocheck);

				if ($SHA_check) {
					$this->transaction_successfull($datacheck1, $_REQUEST['orderID']);
				} else {
					// SHA-Out check fail
					AG_ePDQ_Direct_Helpers::ag_log( '-----------' . $OrderIDtoVerify . 'Transaction was unsuccessful due to a SHA-Out issue. ' . self::$AG_ePDQ_doc . 'article/88-transaction-is-unsuccessful-due-to-a-sha-out-issue -------------', 'warning', $this->debug);
					$InstanceofOrderObject->add_order_note('ALERT: SHA-Out check failed for this order.');
					error_log("SHA-Out check failed for " . $OrderIDtoVerify, 0);
					wp_die('Security check fail. Please contact the site owner and give error code "SHA"');
				}
			} else {
				// SHA-Out not set
				AG_ePDQ_Direct_Helpers::ag_log( '-----------' . $OrderIDtoVerify . 'You don\'t have SHA-out set, for improved security we recommend you set this. ' . self::$AG_ePDQ_doc . 'article/88-transaction-is-unsuccessful-due-to-a-sha-out-issue -----------', 'warning', $this->debug);
				$this->transaction_successfull($datacheck1, $_REQUEST['id']);
			}

		} else {
			// Complus ripemd hash check fail
			AG_ePDQ_Direct_Helpers::ag_log( '-----------' . $OrderIDtoVerify . ' complus hash check fail. -----------', 'warning', $this->debug);
			$InstanceofOrderObject->add_order_note('ALERT: Complus hash check failed for this order.');
			error_log("Complus hash check failed for " . $OrderIDtoVerify, 0);
			wp_die('Security check fail. Please contact  the site owner and give error code "D12Complus"');
		}

	} else {
		//General failure
		AG_ePDQ_Direct_Helpers::ag_log( '-----------' . $OrderIDtoVerify . 'The transaction failed, ePDQ didn\'t send any data back. Please check you have setup the plugin correctly. -----------', 'warning', $this->debug);
		wp_die('No encryption result given. Please contact  the site owner and give error code "AGD13-generalFail"');
	}
}

/**
 * Check SHA data
 *
 * @param $datatocheck
 * @return void
 */
protected function SHA_check($datatocheck)
{
	$SHA_out = $this->sha_out;
	$origsig = $datatocheck['SHASIGN'];
	unset($datatocheck['SHASIGN']);
	unset($datatocheck['wc-api']);
	unset($datatocheck['id']);
	uksort($datatocheck, 'strcasecmp');


	$SHAsig = '';
	foreach ($datatocheck as $key => $value) {
		$SHAsig .= trim(strtoupper($key)) . '=' . utf8_encode(trim($value)) . $SHA_out;
	}

	if ($this->sha_method == 0) {
		$shasign_method = 'sha1';
	} elseif ($this->sha_method == 1) {
		$shasign_method = 'sha256';
	} elseif ($this->sha_method == 2) {
		$shasign_method = 'sha512';
	}

	$SHAsig = strtoupper(hash($shasign_method, $SHAsig));

	if (hash_equals($SHAsig, $origsig)) {
		return true;
	} else {
		return false;
	}
}


/**
 * Successful transaction
 *
 * @param $args
 * @return void
 */
function transaction_successfull($args, $id)
{
	global $woocommerce;

	extract($args);
	$order = new WC_Order($id);
	
	//Take barclaycard status number and create a note with it. 
	$note = 'ePDQ Status: - ' . AG_direct_errors::get_epdq_direct_status_code($STATUS) . '</p>';

	AG_ePDQ_Direct_functions::process_order_data($args, $order);

	switch ($STATUS):
		case '9':
			//AG_ePDQ_Direct_Helpers::ag_log('BarclayCard transaction confirmed.', 'debug', $this->debug);
			/**
			//Post note to order (not necessary because NCstatus gets posted as a note anyway.
			$noteTitle = 'Barclays ePDQ transaction is confirmed.';
			$order->add_order_note($noteTitle);
			$order->add_order_note($note);
			**/
			$order->payment_complete();
			break;
		case '91':
			$noteTitle = 'The authorisation will be processed offline. Please confirm the payment in the ePDQ back office.';
			$order->add_order_note($noteTitle);
			$order->add_order_note($noteTitle . " " . $note);
			AG_ePDQ_Direct_Helpers::ag_log('The data capture will be processed offline. This is the standard response if you have selected offline processing in your account configuration. Check the  the "Global transaction parameters" tab in the ePDQ back office.', 'debug', $this->debug);
			$order->update_status('on-hold');
			AG_ePDQ_Direct_Helpers::ag_log('GJ order data returned:' . $order , 'debug', $this->debug); //Allows logging of all incoming data 
			break;
		case '2': 
		case '4':
		case '41':
		case '5':
		case '51':
		case '93':
			$noteTitle = 'Barclays ePDQ transaction was refused.';
			AG_ePDQ_Direct_Helpers::ag_log('The authorisation has been refused by the financial institution. The customer can retry the authorisation process after selecting another card or another payment method.', 'debug', $this->debug);
			//$order->add_order_note($noteTitle);
			$order->add_order_note($note);
			$order->update_status('failed');
			//GJ//$woocommerce->cart->empty_cart();
			//AG_ePDQ_Direct_Helpers::ag_log('GJ order data returned:' . $order , 'debug', $this->debug); //Allows logging of all incoming data 
			break;
		case '52':
		case '92':
			$noteTitle = 'Barclays ePDQ payment uncertain.';
			AG_ePDQ_Direct_Helpers::ag_log('GJ Technical problem. Order data:' . $order , 'debug', $this->debug); //Allows logging of all incoming data
			$order->add_order_note($noteTitle . "\r\n" . $note);
			//$order->add_order_note($note);
			$order->update_status('failed');
			//GJ//$woocommerce->cart->empty_cart();
			break;
		case '1':
			$noteTitle = 'The customer has cancelled the transaction';
			AG_ePDQ_Direct_Helpers::ag_log('GJ order data returned:' . $order , 'debug', $this->debug); //Allows logging of all incoming data
			$order->add_order_note($noteTitle . "\r\n" . $note);
			//$order->add_order_note($note);
			$order->update_status('failed');
			//GJ//$woocommerce->cart->empty_cart();
			break;
		case '7':
			$noteTitle = 'The payment has been deleted';
			AG_ePDQ_Direct_Helpers::ag_log('GJ refund data returned:' . $order , 'debug', $this->debug); //Allows logging of all incoming data
			$order->add_order_note($noteTitle . "\r\n" . $note);
			//$order->add_order_note($note);
			//$order->update_status('refunded'); //Don't change status because refund may be partial
			//GJ//$woocommerce->cart->empty_cart();
			break;
		case '71':
			$noteTitle = 'The payment has been deleted';
			AG_ePDQ_Direct_Helpers::ag_log('GJ refund data returned:' . $order , 'debug', $this->debug); //Allows logging of all incoming data
			$order->add_order_note($noteTitle . "\r\n" . $note);
			//$order->add_order_note($note);
			//$order->update_status('refunded'); //Don't change status because refund may be partial
			//GJ//$woocommerce->cart->empty_cart();
			break;
		case '8':
			//added 2020-02-05//
			$noteTitle = 'The payment has been refunded';
			AG_ePDQ_Direct_Helpers::ag_log('GJ Rerfund data returned:' . $order , 'debug', $this->debug); //Allows logging of all incoming data
			$order->add_order_note($noteTitle . "\r\n" . $note);
			//$order->add_order_note($note);
			//$order->update_status('refunded'); //Don't change status because refund may be partial
			//GJ//$woocommerce->cart->empty_cart();
			break;
		default:
			$noteTitle = 'Unhandled status detected, value ' . $STATUS;
			AG_ePDQ_Direct_Helpers::ag_log('GJ order data returned:' . $order , 'debug', $this->debug); //Allows logging of all incoming data
			$order->add_order_note($noteTitle);
			$order->add_order_note($note);
			$order->update_status('failed');
			//GJ//$woocommerce->cart->empty_cart();
			break;

	endswitch;

	wp_redirect($order->get_checkout_order_received_url());

	exit;
}



/**
 * 3D Secure Redirect
 *
 * @return void
 */
public function receipt_page()
{
	$session = WC()->session->get('HTML_ANSWER'); 

	if($session === null) { 
	
		echo '<p>' . __('Please <a href="'. get_permalink( get_option('woocommerce_myaccount_page_id') ) .'">sign in</a> to make your account and make your payment. No monies have been taken at this time.', 'ag_epdq_checkout') . '</p>';

	} else {

		echo '<p>' . __('Thank you for your order, please authenticate your order.', 'ag_epdq_checkout') . '</p>';

		$html = base64_decode($session);

		print_r($html);
		
	}
}  




/**
 * Validate fields
 *
 * @return void
 */
public function validate_fields()
{

	$is_valid 			= 	parent::validate_fields();
	$account_number 	= 	str_replace(' ', '', AG_ePDQ_Direct_Helpers::AG_get_post_data('ag_epdq_checkout-card-number'));
	$cvc_number     	= 	AG_ePDQ_Direct_Helpers::AG_get_post_data('ag_epdq_checkout-card-cvc');
	$expiration 		= 	AG_ePDQ_Direct_Helpers::AG_get_post_data('ag_epdq_checkout-card-expiry');
	$expire_date 		=	str_replace(array(' ', '/'), '', $expiration);
	$exprie_month		=	substr($expire_date, 0, 2);
	$exprie_year		=	substr($expire_date, -2);
	$custEmailAddr 		= 	AG_ePDQ_Direct_Helpers::AG_get_post_data('billing_email');

	if ( is_wc_endpoint_url( 'order-pay' ) && ! is_user_logged_in() ) {
		wc_add_notice(__('Please <a href="'. get_permalink( get_option('woocommerce_myaccount_page_id') ) .'">sign in</a> to your account and then make your payment.', 'ag_epdq_checkout'), 'error');
		$is_valid = false;
	} else {
	
		//GJ Make a log entry to allow tracking of failed payment attempts.
		//AG_ePDQ_Direct_Helpers::ag_log('--------------------', 'debug', $this->debug); //Makes new orders easier to distinguish but makes an extra line per attempt.
		AG_ePDQ_Direct_Helpers::ag_log('' . $custEmailAddr . ' submitted' , 'debug', $this->debug);
		
		//GJ Check for Amex
		//if (substr($account_number, 0, 2) == '34' OR substr($account_number, 0, 2) == '37') {
		//	wc_add_notice(__('We are unable to accept American Express cards, sorry.', 'ag_epdq_checkout'), 'error');
		//	AG_ePDQ_Direct_Helpers::ag_log('Amex denied for: ' . $custEmailAddr , 'debug', $this->debug);
		//	$is_valid = false;
		//}
		
		// Check CVC
		if (empty($cvc_number)) {
			wc_add_notice(__('Card security code (CVC) is missing, you can find this on the back of your card.', 'ag_epdq_checkout'), 'error');
			AG_ePDQ_Direct_Helpers::ag_log('Card security code (CVC) is missing, you can find this on the back of your card.', 'warning', $this->debug);
			$is_valid = false;
		}
		if (!ctype_digit($cvc_number) && !empty($cvc_number)) {
			wc_add_notice(__('Card security code (CVC) is invalid (only numbers are allowed), please double check.', 'ag_epdq_checkout'), 'error');
			AG_ePDQ_Direct_Helpers::ag_log('Card security code (CVC) is invalid (only numbers are allowed), please double check.', 'warning', $this->debug);
			$is_valid = false;
		}
		if (strlen($cvc_number) < 3 && !empty($cvc_number) || strlen($cvc_number) > 4 && !empty($cvc_number)) {
			wc_add_notice(__('Card security code (CVC) is invalid (wrong length), please double check.', 'ag_epdq_checkout'), 'error');
			AG_ePDQ_Direct_Helpers::ag_log('Card security code (CVC) is invalid (wrong length), please double check.', 'warning', $this->debug);
			$is_valid = false;
		}

		// Check Date
		$current_year  = date('y');
		$current_month = date('m');

		if (
			!ctype_digit($exprie_month) || !ctype_digit($exprie_year) ||
			$exprie_month > 12 ||
			$exprie_month < 1 ||
			$exprie_year < $current_year || ($exprie_year == $current_year && $exprie_month < $current_month) ||
			$exprie_year > $current_year + 20
		) {
			wc_add_notice(__('Card expiry date is invalid, please double check.', 'ag_epdq_checkout'), 'error');
			AG_ePDQ_Direct_Helpers::ag_log('Card expiry date is invalid, please double check.', 'warning', $this->debug);
			$is_valid = false;
		}
		// Check Card Number
		if (empty($account_number) || !ctype_digit($account_number) || !AG_ePDQ_Direct_Helpers::luhn_algorithm_check($account_number)) {
			wc_add_notice(__('Card number is invalid, please double check', 'ag_epdq_checkout'), 'error');
			AG_ePDQ_Direct_Helpers::ag_log('Card number is invalid, please double check', 'warning', $this->debug);
			$is_valid = false;
		}
		
	}

	return $is_valid;
}



/**
 * Submit payment and handle response
 *
 * @param $order_id
 * @return void
 */
public function process_payment($order_id)
{

	global $woocommerce;

	$customer_order = new WC_Order($order_id);
	$declineUrl = esc_url($customer_order->get_checkout_payment_url());
	$environment = ($this->environment == "yes") ? 'TRUE' : 'FALSE';
	if (is_ssl()) {
		$environment_url = ("FALSE" == $environment)
			? 'https://payments.epdq.co.uk/ncol/prod/orderdirect.asp'
			: 'https://mdepayments.epdq.co.uk/ncol/test/orderdirect.asp';
	} else {
		$environment_url = 'https://mdepayments.epdq.co.uk/ncol/test/orderdirect.asp';
	}

	$fullName = 	remove_accents($customer_order->get_billing_first_name() . '%20' . str_replace("'", "", $customer_order->get_billing_last_name()));
	$amount = floatval(preg_replace('#[^\d.]#', '', $customer_order->get_total() * 100));

	$order_item = $customer_order->get_items();
	foreach ($order_item as $product) {
		$prodct_name[] = str_replace('&', 'and', $product['name']) . " x" . $product['qty'];
	}
	$product_list_string = implode(',', $prodct_name);

	// If the items in the cart add to more than the character limit set by ePDQ then switch to product id.
	if (strlen($product_list_string) < 99) {
		$product_list = $product_list_string;
	} else {
		foreach ($order_item as $product) {
			$prodct_names[] = str_replace('&', 'and', $product['product_id']) . " x" . $product['qty'];
		}
		$product_list = implode(',', $prodct_names);
	}


	$data_post = array();
	if (get_woocommerce_currency() != 'GBP' && defined('ePDQ_apiLogin')) {
		$data_post['PSPID'] = ePDQ_apiLogin;
		$data_post['USERID'] = ePDQ_apiUser;
		$data_post['PSWD'] = ePDQ_apiPassword;
	} else {
		$data_post['PSPID'] = $this->api_login;
		$data_post['USERID'] = $this->api_user;
		$data_post['PSWD'] = $this->api_password;
	}

	// Hash the order number to create a ripemd160 hash to use as a double check when the data is sent back from barclaycard.
	$hash_fields = array($customer_order->get_order_number());
	$encrypted_string = hash_hmac('ripemd160', implode($hash_fields), $this->sha_in);
	
	$CustomerOrderNumber = $customer_order->get_id();
	$ExtraBitForNotifyURL = '?id=' . $CustomerOrderNumber;
	
	
	$data_post['ORDERID'] = $customer_order->get_order_number();
	$data_post['COMPLUS'] = $encrypted_string;
	$data_post['AMOUNT'] = $amount;
	$data_post['CURRENCY'] = get_woocommerce_currency();
	$data_post['CARDNO'] = str_replace(array(' ', '-'), '', $_POST['ag_epdq_checkout-card-number']);
	$data_post['ED'] = str_replace(array('/', ' '), '', $_POST['ag_epdq_checkout-card-expiry']);
	$data_post['CVC'] = (isset($_POST['ag_epdq_checkout-card-cvc'])) ? $_POST['ag_epdq_checkout-card-cvc'] : '';
	$data_post['EMAIL'] = $customer_order->get_billing_email();
	$data_post['CN'] = $fullName;
	$data_post['COM'] = $product_list;
	$data_post['OWNERADDRESS'] = substr(preg_replace('/[^A-Za-z0-9\. -]/', '', $customer_order->get_billing_address_1()) . ' ' . preg_replace('/[^A-Za-z0-9\. -]/', '', $customer_order->get_billing_address_2()), 0, 34);
	$data_post['OWNERZIP'] = $customer_order->get_billing_postcode();
	$data_post['OWNERTOWN'] = substr(preg_replace('/[^A-Za-z0-9\. -]/', '', $customer_order->get_billing_city()), 0, 34);
	$data_post['OWNERCTY'] = substr(preg_replace('/[^A-Za-z0-9\. -]/', '', $customer_order->get_billing_country()), 0, 34);
	$data_post['OWNERTELNO'] = $customer_order->get_billing_phone();
	$data_post['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
	$data_post['WIN3DS'] = $this->secure_3d;
	$data_post['FLAG3D'] = 'Y';
	$data_post['PARAMVAR'] = $ExtraBitForNotifyURL;
	$data_post['ACCEPTURL'] = $this->notify_url . $ExtraBitForNotifyURL;
	$data_post['DECLINEURL'] = $this->notify_url . $ExtraBitForNotifyURL;
	$data_post['EXCEPTIONURL'] = $declineUrl;
	$data_post['LANGUAGE'] = get_bloginfo('language');
	$data_post['AIVATAMNT'] = $customer_order->get_total_tax(); //this is working and reporting the correct VAT but I don't know the correct parameter for ePDQ to use this VAT.
	$data_post['OPERATION'] = $this->operation; 
	
	
	//Check accept URL
	//$MyAcceptURL =	$data_post['ACCEPTURL'];
	//AG_ePDQ_Direct_Helpers::ag_log('Accept URL is: ' . $MyAcceptURL, 'debug', $this->debug);
	
	


	// New PSD2 parameter requirements  
	$data_post['BROWSERCOLORDEPTH']	=	$_POST['browserColorDepth'];
	$data_post['BROWSERJAVAENABLED']	=	$_POST['browserJavaEnabled'];
	$data_post['BROWSERLANGUAGE']		=	$_POST['browserLanguage'];
	$data_post['BROWSERSCREENHEIGHT']	=	$_POST['browserScreenHeight'];
	$data_post['BROWSERSCREENWIDTH']	=	$_POST['browserScreenWidth'];
	$data_post['BROWSERTIMEZONE']	=	$_POST['browserTimeZone'];
	$data_post['BROWSERUSERAGENT']	=	$_POST['browserUserAgent'];
	$data_post['BROWSERACCEPTHEADER']	=	'*/*';
	//GJ need to check these
	//$data_post['ECOM_SHIPTO_POSTAL_POSTALCODE'] = $customer_order->get_shipping_postcode();
	//$data_post['ECOM_SHIPTO_POSTAL_CITY'] = $customer_order->get_shipping_city();
	//$data_post['ECOM_SHIPTO_POSTAL_STREET_LINE1'] = $customer_order->get_shipping_address_1();


	if (isset($this->sha_in)) {

		ksort($data_post);

		if ($this->sha_method == 0) {
			$shasign_method = 'sha1';
		} elseif ($this->sha_method == 1) {
			$shasign_method = 'sha256';
		} elseif ($this->sha_method == 2) {
			$shasign_method = 'sha512';
		}

		$SHAsig = hash($shasign_method, implode($this->sha_in, $data_post) . $this->sha_in);
		$data_post['SHASIGN'] = $SHAsig;
	}

	$post_string = array();
	foreach ($data_post as $key => $value) {
		$post_string[] = $key . '=' . $value;
	}
	$actual_string = '';
	$actual_string = implode('&', $post_string);
	$result = wp_remote_post($environment_url, array(
		'method' => 'POST',
		'timeout'     => 9,
		'redirection' => 5,
		'sslverify' => true,
		'body' => $actual_string,
	));

    // Check for error
	if ( is_wp_error( $result ) ) {
		return;
	}

	$xml = simplexml_load_string($result['body'], "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$data_back = json_decode($json, TRUE);
	$order_data = $data_back['@attributes'];
	//AG_ePDQ_Direct_Helpers::ag_log($order_id . " order data before 3DS step" . implode("|",$order_data), 'debug', $this->debug);
	$STATUS = $order_data['STATUS'];
	$NCERROR = $order_data['NCERROR'];

	$note = '<p>ePDQ Status: - ' . AG_direct_errors::get_epdq_direct_status_code($STATUS) . '</p>';
	$errornote = '<p>ePDQ NCERROR: - ' . AG_direct_errors::get_epdq_direct_ncerror($NCERROR) . '</p>';
	//$note .= '<p>Order ID: - ' . $order_data['id'] . '</p>';//Not sure where this section is outputting to but this line was giving an error:
	//[07-Jan-2020 23:51:17 UTC] PHP Notice:  Undefined index: id in /home/reagqpqw/public_html/wp/wp-content/plugins/barclaycard-epdq-direct-link-payment-gateway-woocommerce-premium/inc/classes/class-main.php on line 707
	$note .= '<p>ePDQ PAYID: - ' . $order_data['PAYID'] . '</p>';


	// Check order status and process		
	if (in_array($STATUS, array(4, 5, 9))) {
		
		// 3DS v2 Frictionless flow
		$noteTitle = 'Barclays ePDQ transaction is confirmed.';
		AG_ePDQ_Direct_Helpers::ag_log('Barclays ePDQ transaction is confirmed (The transaction was a 3DS v2 Frictionless transaction)', 'debug', $this->debug);
		$customer_order->add_order_note($noteTitle);
		$customer_order->add_order_note($note);
		$customer_order->payment_complete();

		$orderdata = array(
			'OrderID'         	=> 	$order_data['id'] = $order_data['id'] ?? '',
			'Status'         	=> 	AG_direct_errors::get_epdq_direct_status_code($STATUS),
			'PAYID'				=>	$order_data['PAYID'] = $order_data['PAYID'] ?? '',
	
		);
		AG_ePDQ_Direct_Helpers::update_order_meta_data($order_data['orderID'], $orderdata);

		$redirect = $customer_order->get_checkout_order_received_url();
		return array(
			'result'    => 'success',
			'redirect'    => $redirect
		);
		
	} elseif (in_array($STATUS, array(41, 51, 91))) {

		$noteTitle = 'The authorisation will be processed offline. Please confirm the payment in the ePDQ back office.';
		$customer_order->add_order_note($noteTitle);
		AG_ePDQ_Direct_Helpers::ag_log('The data capture will be processed offline. This is the standard response if you have selected offline processing in your account configuration. Check the  the "Global transaction parameters" tab in the ePDQ back office.', 'debug', $this->debug);
		$customer_order->update_status('on-hold');

	} elseif ($STATUS == 46) {
		// 3DS v2 Challenge flow
		WC()->session->set('HTML_ANSWER', $data_back['HTML_ANSWER'] = $data_back['HTML_ANSWER'] ?? '');
		WC()->session->set('PaReq', $data_back['PaReq'] = $data_back['PaReq'] ?? '');
		WC()->session->set('Order_ID', $order_id);
		$redirect = $customer_order->get_checkout_payment_url(true);
		return array(
			'result'    => 'success',
			'redirect'    => $redirect
		);

	} elseif ($STATUS == 2 || $STATUS == 93) {
			
		$noteTitle = 'Barclays ePDQ transaction was refused.';
		$customer_order->add_order_note($noteTitle);
		$customer_order->add_order_note($errornote);
		AG_ePDQ_Direct_Helpers::ag_log('The authorisation has been refused by the financial institution. The customer can retry the authorisation process after selecting another card or another payment method.', 'debug', $this->debug);
		$customer_order->update_status('failed');
		$woocommerce->cart->empty_cart();
		
		wc_add_notice( $errornote, 'error' );
		$redirect = esc_url($customer_order->get_checkout_payment_url());
		return array(
			'result'    => 'success',
			'redirect'    => $redirect
		);

	} elseif ($STATUS == 52 || $STATUS == 92) {
		
		$noteTitle = 'Barclays ePDQ payment uncertain.';
		$customer_order->add_order_note($noteTitle);
		$customer_order->add_order_note($errornote);
		AG_ePDQ_Direct_Helpers::ag_log('A technical problem arose during the authorisation/payment process, giving an unpredictable result.', 'debug', $this->debug);
		$customer_order->update_status('failed');
		$woocommerce->cart->empty_cart();
		
		wc_add_notice( $errornote, 'error' );
		$redirect = esc_url($customer_order->get_checkout_payment_url());
		return array(
			'result'    => 'success',
			'redirect'    => $redirect
		);

	} elseif ($STATUS == 1) {
		
		//GJ Added 2 log lines
		AG_ePDQ_Direct_Helpers::ag_log("Status 0 or NULL", 'debug', $this->debug);
		
		$noteTitle = 'The customer has cancelled the transaction';
		$customer_order->add_order_note($noteTitle);
		$customer_order->add_order_note($errornote);
		$customer_order->update_status('failed');
		$woocommerce->cart->empty_cart();
		
		wc_add_notice( $errornote, 'error' );
        $redirect = esc_url($customer_order->get_checkout_payment_url());
		return array(
			'result'    => 'success',
			'redirect'    => $redirect
		);
	
	} elseif ($STATUS == 0 || $STATUS === NULL) {
		
		//GJ Added 2 log lines
		AG_ePDQ_Direct_Helpers::ag_log("Status 0 or NULL", 'debug', $this->debug);
		
		$noteTitle = 'Incomplete or invalid';
		$customer_order->add_order_note($noteTitle);
		$customer_order->add_order_note($errornote);
		$customer_order->update_status('failed');
		$woocommerce->cart->empty_cart();
		wc_add_notice($errornote, 'error' );
		

		$redirect = esc_url($customer_order->get_checkout_payment_url());
		return array(
			'result'    => 'success',
			'redirect'    => $redirect
		);
	} else
					
		//GJ Added 2 log lines
		AG_ePDQ_Direct_Helpers::ag_log("Status 0 or NULL", 'debug', $this->debug);
		
		$noteTitle = 'Incomplete or invalid';
		$customer_order->add_order_note($noteTitle);
		$customer_order->add_order_note($errornote);
		$customer_order->update_status('failed');
		//$woocommerce->cart->empty_cart();
		wc_add_notice($errornote, 'error' );
		

		$redirect = esc_url($customer_order->get_checkout_payment_url());
		return array(
			'result'    => 'success',
			'redirect'    => $redirect
		);
	

	wp_redirect($customer_order->get_checkout_order_received_url());
	

}


/**
 * Process refund
 *
 * @param $order_id
 * @param $amount
 * @param string $reason
 * @return void
 */
public function process_refund($order_id, $amount = NULL, $reason = '')
{

	$order 			   = new WC_Order($order_id);
	$environment = ($this->environment == "yes") ? 'TRUE' : 'FALSE';

	if (is_ssl()) {
		$environment_url = ("FALSE" == $environment)
			? 'https://payments.epdq.co.uk/ncol/prod/maintenancedirect.asp'
			: 'https://mdepayments.epdq.co.uk/ncol/test/maintenancedirect.asp';
	} else {
		$environment_url = 'https://mdepayments.epdq.co.uk/ncol/test/maintenancedirect.asp';
	}

	$Refundamount = $amount * 100;
	if (!$Refundamount) {
		return new WP_Error('error', __('Refund failed: Amount invalid.', 'ag_epdq_checkout'));
	}
	
	$payid = get_post_meta($order_id, 'PAYID', true);
	if (!$payid) {
		return new WP_Error('error', __('PAYID is missing from this order, you will need to manually refund this order in the ePDQ back office.', 'ag_epdq_checkout'));
	}

	$data_post = array();
	$data_post['AMOUNT'] = $Refundamount;
	$data_post['OPERATION'] = 'RFD';
	$data_post['PAYID'] = get_post_meta($order_id, 'PAYID', true);

	if (get_woocommerce_currency() != 'GBP' && defined('ePDQ_apiLogin')) {
		$data_post['PSPID'] = ePDQ_apiLogin;
		$data_post['USERID'] = ePDQ_apiUser;
		$data_post['PSWD'] = ePDQ_apiPassword;
	} else {
		$data_post['PSPID'] = $this->api_login;
		$data_post['USERID'] = $this->api_user;
		$data_post['PSWD'] = $this->api_password;
	}

	$post_string = array();
	foreach ($data_post as $key => $value) {
		$post_string[] = $key . '=' . $value;
	}

	$actual_string = '';
	$actual_string = implode('&', $post_string);
	$result = wp_remote_post($environment_url, array(
		'method' => 'POST',
		'timeout'     => 6,
		'redirection' => 5,
		'body' => $actual_string,
	));

	$lines = preg_split('/\r\n|\r|\n/', $result['body']);
	$response = array();
	foreach ($lines as $line) {
		$key_value = preg_split('/=/', $line, 2);
		if (count($key_value) > 1) {
			$response[trim($key_value[0])] = trim($key_value[1]);
		}
	}

	$accepted = array(8, 81, 85); // OK
	$status = preg_replace('/[^a-zA-Z0-9\s]/', '', $response['STATUS']);
	$fullError = preg_replace('/[^a-zA-Z0-9\s]/', '', $response['NCERRORPLUS']);
	$string = implode('|', $response);

	if (!is_wp_error($result) && $result['response']['code'] >= 200 && $result['response']['code'] < 300) {

		if (in_array($status, $accepted)) {
			$order->add_order_note(
				__('Refund successful', 'ag-direct') . '<br />' .
					__('Refund Amount: ', 'ag-direct') . $amount . '<br />' .
					__('Refund Reason: ', 'ag-direct') . $reason . '<br />' .
					__('ePDQ Status: ', 'ag-direct') . AG_direct_errors::get_epdq_direct_status_code($status) . ' '
			);
			return true;
		} else {
			$order->add_order_note(__('Refund failed', 'ag_epdq_checkout') . '<br />' . __('ePDQ Status: ', 'ag_epdq_checkout') . AG_ePDQ_Direct_Helpers::get_epdq_status_code($status) . '<br />');
			$order->add_order_note(__('Refund Note', 'ag_epdq_checkout') . '<br /><strong>' . __('Error: ', 'ag_epdq_checkout') . $fullError . '</strong><br />');
		}
		// Log refund error
		AG_ePDQ_Direct_Helpers::ag_log($string, 'warning', $this->debug);
	} else {
		// Log refund error
		AG_ePDQ_Direct_Helpers::ag_log($string, 'warning', $this->debug);
	}
}
} // End of ag_epdq_checkout
