<?php
/*-----------------------------------------------------------------------------------*/
/*	AG ePDQ subscription
/*-----------------------------------------------------------------------------------*/
defined('ABSPATH') or die("No script kiddies please!");

class epdq_direct_checkout_subscription extends ag_epdq_checkout
{

    	/**
	 * Constructor
	*/
	public function __construct() {

		parent::__construct();

		if ( class_exists( 'WC_Subscriptions_Order' ) ) {

            add_action('wcs_renewal_order_created', array($this, 'link_recurring_child'), 10, 2);

			add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );

		}
	}


	/**
	 * Check if an order contains a subscription
	 */
	public function order_contains_subscription( $order_id ) {
		return function_exists( 'wcs_order_contains_subscription' ) && ( wcs_order_contains_subscription( $order_id ) || wcs_order_contains_renewal( $order_id ) );

	}


	/**
	 * Process a trial subscription order with 0 total
	 */
	public function process_payment($order_id)
    {

        $order = wc_get_order($order_id);

        // Check for trial subscription order with 0 total
        if ($this->order_contains_subscription($order) && $order->get_total() == 0) {

            $order->payment_complete();

            $order->add_order_note('This subscription has a free trial, reason for the 0 amount');

            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            );
        } else {

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
	
			$fullName = 	remove_accents($customer_order->get_billing_first_name() . ' ' . str_replace("'", "", $customer_order->get_billing_last_name()));
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
	
			// New hash - replaces the WP nonce
			$hash_fields = array($this->api_user, date('Y:m:d'), $customer_order->get_order_number(), $this->sha_in);
			$encrypted_string = hash_hmac('ripemd160', implode($hash_fields), $this->sha_in);
			//if(isset($this->encryption)) {
			//	$sodium = ePDQ_Direct_crypt::sodium_crypt($hash_fields, $this->encryption, '256');
			//	WC()->session->set('epdq_direct_hash', $sodium);
			//}
	
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
			$data_post['ACCEPTURL'] = $this->notify_url;
			$data_post['DECLINEURL'] = $this->notify_url;
			$data_post['EXCEPTIONURL'] = $declineUrl;
			$data_post['LANGUAGE'] = get_bloginfo('language');
			$data_post['OPERATION'] = $this->operation;
				
			//$price_per_period = WC_Subscription::get_total();
			$billing_period = WC_Subscriptions_Order::get_subscription_period( $customer_order );
	
			switch( strtolower( $billing_period ) ) {
				case 'day':
					$billing_period = 'd';
					$subscription_interval = WC_Subscriptions_Order::get_subscription_interval( $customer_order );
					break;
				case 'week':
					$billing_period = 'ww';
					$subscription_interval = WC_Subscriptions_Order::get_subscription_interval( $customer_order );
					break;
				case 'year':
					$billing_period = 'm';
					$subscription_interval='12';
					break;
				case 'month':
				default:
					$billing_period = 'm';
					$subscription_interval = WC_Subscriptions_Order::get_subscription_interval( $customer_order );
					break;
			}
				
			// Recurring payment
			$data_post['SUBSCRIPTION_ID'] = $customer_order->get_id();
			$data_post['SUB_AMOUNT'] = $customer_order->get_total() *100;
			$data_post['SUB_COM'] = 'order description';
			$data_post['SUB_COMMENT'] = 'Recurring payment';
			$data_post['SUB_ORDERID'] = $customer_order->get_id();
			$data_post['SUB_PERIOD_MOMENT'] = date('d');
			$data_post['SUB_PERIOD_NUMBER'] = $subscription_interval;
			$data_post['SUB_PERIOD_UNIT'] = $billing_period;
			$data_post['SUB_STARTDATE'] = date('Y-m-d');
			$data_post['SUB_STATUS'] = '1';
	
				
			if (isset($this->sha_in)) {
	
				$shasign_arg = array();
				ksort($data_post);
	
				if ($this->sha_method == 0) {
					$shasign_method = 'sha1';
				} elseif ($this->sha_method == 1) {
					$shasign_method = 'sha256';
				} elseif ($this->sha_method == 2) {
					$shasign_method = 'sha512';
				}
	
				$SHAsig = hash($shasign_method, implode($this->sha_in, $shasign_arg) . $this->sha_in);
				$data_post['SHASIGN'] = $SHAsig;
			}
	
			$post_string = array();
			foreach ($data_post as $key => $value) {
				$post_string[] = $key . '=' . $value;
			}
			$actual_string = '';
			$actual_string = implode('&', $post_string);

			// Comment to check data getting sent.
			//AG_ePDQ_Direct_Helpers::ag_log($actual_string, 'Fail', 'yes');


			$result = wp_remote_post($environment_url, array(
				'method' => 'POST',
				'timeout'     => 6,
				'redirection' => 5,
				'body' => $actual_string,
			));
	
			$xml = simplexml_load_string($result['body'], "SimpleXMLElement", LIBXML_NOCDATA);
			$json = json_encode($xml);
			$data_back = json_decode($json, TRUE);
			$order_data = $data_back['@attributes'];
			$status = $order_data['STATUS'];
	
			$note = 'ePDQ Status: - ' . AG_direct_errors::get_epdq_direct_status_code($order_data['STATUS']) . '</p>';
	
			// Check order status and process		
			switch ($status): case '4':
				case '5':
				case '9':
					$noteTitle = 'Barclay ePDQ transaction is confirmed.';
					$customer_order->add_order_note($noteTitle);
					$customer_order->add_order_note($note);
					$customer_order->payment_complete();
					break;
	
				case '41':
				case '51':
				case '91':
					$noteTitle = 'The authorisation will be processed offline. Please confirm the payment in the ePDQ back office.';
					$customer_order->add_order_note($noteTitle);
					AG_ePDQ_Direct_Helpers::ag_log('The data capture will be processed offline. This is the standard response if you have selected offline processing in your account configuration. Check the  the "Global transaction parameters" tab in the ePDQ back office.', 'Fail', $this->debug);
					$customer_order->update_status('on-hold');
					break;
	
				case '46':
					WC()->session->set('HTML_ANSWER', $data_back['HTML_ANSWER'] = $data_back['HTML_ANSWER'] ?? '');
					WC()->session->set('PaReq', $data_back['PaReq'] = $data_back['PaReq'] ?? '');
					WC()->session->set('Order_ID', $order_id);
					$redirect = $customer_order->get_checkout_payment_url(true);
					return array(
						'result'    => 'success',
						'redirect'    => $redirect
					);
					break;
	
				case '2':
				case '93':
					$noteTitle = 'Barclay ePDQ transaction was refused.';
					$customer_order->add_order_note($noteTitle);
					AG_ePDQ_Direct_Helpers::ag_log('The authorisation has been refused by the financial institution. The customer can retry the authorisation process after selecting another card or another payment method.', 'Fail', $this->debug);
					$customer_order->update_status('failed');
					$woocommerce->cart->empty_cart();
					break;
	
				case '52':
				case '92':
					$noteTitle = 'Barclay ePDQ payment uncertain.';
					$customer_order->add_order_note($noteTitle);
					AG_ePDQ_Direct_Helpers::ag_log('A technical problem arose during the authorisation/payment process, giving an unpredictable result.', 'Fail', $this->debug);
					$customer_order->update_status('failed');
					$woocommerce->cart->empty_cart();
					break;
	
				case '1':
					$noteTitle = 'The customer has cancelled the transaction';
					$customer_order->add_order_note($noteTitle);
					$customer_order->update_status('failed');
					$woocommerce->cart->empty_cart();
					break;
	
			endswitch;
	
			wp_redirect($customer_order->get_checkout_order_received_url());

        }
	}
	


    /**
     * Scheduled subscription payment
     *
     * @param $amount_to_charge
     * @param $renewal_order
     * @return void
     */
    public function scheduled_subscription_payment($amount_to_charge, $renewal_order)
    {

        $response = $this->process_subscription_payment($renewal_order, $amount_to_charge);

        if (is_wp_error($response)) {

            $renewal_order->update_status('failed', sprintf('ePDQ Transaction Failed (%s)', $response->get_error_message()));
        }
    }

    /**
     * Process subscription payment
     *
     * @param string $order
     * @param integer $amount
     * @return void
     */
    public function process_subscription_payment($order = '', $amount = 0)
    {

        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
        $transaction_id = get_post_meta($order_id, 'PAYID', true);

        WC_Subscriptions_Manager::process_subscription_payments_on_order( $order_id );
			        $order->payment_complete();

        if ($this->status == 'test')    $environment_url = 'https://mdepayments.epdq.co.uk/ncol/test/querydirect.asp';
        if ($this->status == 'live')    $environment_url = 'https://payments.epdq.co.uk/ncol/prod/querydirect.asp';

        if ($transaction_id) {


            $data_post = array();
            //$data_post['ORDERID'] = $order->get_id();
            $data_post['PAYID'] = $transaction_id;
            $data_post['PAYIDSUB'] = '';
            $data_post['PSPID'] = $this->access_key;
            $data_post['PSWD'] = $this->api_password;
            $data_post['USERID'] = $this->api_user;


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

            if (!is_wp_error($result) && 200 == wp_remote_retrieve_response_code($result)) {

                $lines = preg_split('/\r\n|\r|\n/', $result['body']);
                $response = array();
                foreach ($lines as $line) {
                    $key_value = preg_split('/=/', $line, 2);
                    if (count($key_value) > 1) {
                        $response[trim($key_value[0])] = trim($key_value[1]);
                    }
                }

                $accepted = array(4, 5, 9); // OK
                $status = preg_replace('/[^a-zA-Z0-9\s]/', '', $response['STATUS']);

                if (in_array($status, $accepted)) {


                    update_post_meta($order_id, '_epdq_status', $response['STATUS']);
                    update_post_meta($order_id, '_epdq_acceptance', $response['ACCEPTANCE']);
                    update_post_meta($order_id, '_epdq_amount', $response['amount']);
                    update_post_meta($order_id, '_epdq_PAYID', $response['PAYID']);
                    update_post_meta($order_id, '_epdq_PAYIDSUB', $response['PAYIDSUB']);
                    update_post_meta($order_id, '_epdq_NCERROR', $response['NCERROR']);


                    $order->payment_complete($response['PAYID']);
                    $message = sprintf('Subscription payment via ePDQ successful (<strong>Transaction Reference:</strong> %s)', $response['PAYID']);
                    $order->add_order_note($message);

                    return true;


                } else {
                    $order_note = 'Subscription payment via ePDQ fail.';
                    $order->add_order_note($order_note);
                    $note = 'ePDQ Status Code: ' . $status . ' - ' . AG_direct_errors::get_epdq_status_code($status) . '</p>';
                    $order->update_status('on-hold', $note);

                    return new WP_Error( 'epdq_error', 'ePDQ payment failed. ' . AG_direct_errors::get_epdq_status_code($status) );

                }
            }
        }
        AG_ePDQ_Direct_Helpers::ag_log('This subscription can\'t be renewed automatically.', 'warning', 'yes');
        return new WP_Error('epdq_error', 'This subscription can\'t be renewed automatically.');
    }
 
}
