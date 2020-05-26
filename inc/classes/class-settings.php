<?php
/*-----------------------------------------------------------------------------------*/
/*	AG ePDQ settings
/*-----------------------------------------------------------------------------------*/
defined('ABSPATH') or die("No script kiddies please!");


if (class_exists('AG_ePDQ_Direct_Settings')) {
        return;
}

class AG_ePDQ_Direct_Settings
{

        /**
         * Plugin settings
         *
         * @return void
         */
        public static function form_fields()
        {

                return array(
                        'enabled' => array(
                                'title'                => __('Enable / Disable', 'ag_epdq_checkout'),
                                'label'                => __('Enable this payment gateway', 'ag_epdq_checkout'),
                                'type'                => 'checkbox',
                                'default'        => 'no',
                        ),
                        'title' => array(
                                'title'                => __('Title', 'ag_epdq_checkout'),
                                'type'                => 'text',
                                'desc_tip'        => __('Payment title the customer will see during the checkout process.', 'ag_epdq_checkout'),
                                'default'        => __('Credit card', 'ag_epdq_checkout'),
                        ),
                        'description' => array(
                                'title'                => __('Description', 'ag_epdq_checkout'),
                                'type'                => 'textarea',
                                'desc_tip'        => __('Payment description the customer will see during the checkout process.', 'ag_epdq_checkout'),
                                'default'        => __('Pay securely using your credit card.', 'ag_epdq_checkout'),
                                'css'                => 'max-width:350px;'
                        ),
                        'api_login' => array(
                                'title'                => __('PSPID', 'ag_epdq_checkout'),
                                'type'                => 'text',
                                'desc_tip'        => __('The PSPID for your Barclays account. This is the id which you use to login the admin panel of the Barclays bank.', 'ag_epdq_checkout'),
                        ),
                        'api_user' => array(
                                'title'                => __('API User ID', 'ag_epdq_checkout'),
                                'type'                => 'text',
                                'desc_tip'        => __('', 'ag_epdq_checkout'),
                        ),
                        'api_password' => array(
                                'title'                => __('API User Password', 'ag_epdq_checkout'),
                                'type'                => 'password',
                                'desc_tip'        => __('', 'ag_epdq_checkout'),
                        ),
                        'operation' => array(
                                'title' => __('Operation', 'ag_epdq_checkout'),
                                'type' => 'select',
                                'css'  => 'height: 35px;',
                                'options' => array('RES' => 'Authorization', 'SAL' => 'Sale'),
                                'description' => __('Operation Type For Transaction ', 'ag_epdq_checkout'),
                                'default' => '',
                                'desc_tip' => false,
                        ),
                        'sha_in' => array(
                                'title' => __('SHA-IN Passphrase', 'ag_epdq_checkout'),
                                'type' => 'password',
                                'description' => __('The SHA-IN signature will encode the data passed to the payment processor to ensure better security.', 'ag_epdq_checkout'),
                                //'desc_tip'      => true
                        ),
                        'sha_out' => array(
                                'title' => __('SHA-OUT Passphrase', 'ag_epdq_checkout'),
                                'type' => 'password',
                                'description' => __('The SHA-OUT signature will encode the data passed back from the payment processor to ensure better security.', 'ag_epdq_checkout'),
                                //'desc_tip'      => true
                        ),
                        'sha_method' => array(
                                'title' => __('SHA encryption method', 'ag_epdq_checkout'),
                                'type' => 'select',
                                'options' => array(0 => 'SHA-1', 1 => 'SHA-256', 2 => 'SHA-512'),
                                'description' => __('Sha encryption method - this needs match what you have set in the eePDQ back office.', 'ag_epdq_checkout'),
                                'default' => '',
                                'desc_tip' => true,
                        ),
                        'secure_3d' => array(
                                'title' => __('Display 3D secure method', 'ag_epdq_checkout'),
                                'type' => 'select',
                                'options' => array('MAINW' => 'Default', 'POPUP' => 'Popup window'),
                                'description' => __('This options allows you to select how to display 3D secure to the customer.', 'ag_epdq_checkout'),
                                'desc_tip' => true,
                        ),
                        //'alias' => array(
                        //        'title' => __('Enable/Disable Alias Manager', 'ag_epdq_checkout'),
                        //        'type' => 'checkbox',
                        //        'description' => 'If enabled, customers will be able to pay with a saved card during checkout. Card details are saved on ePDQ servers, not on your store.',
                        //        'label' => __('Enable ePDQ  Alias Manager', 'ag_epdq_checkout'),
                        //       'default' => 'no'
                        //),
                        'environment' => array(
                                'title'                => __('Test Mode', 'ag_epdq_checkout'),
                                'label'                => __('Enable Test Mode', 'ag_epdq_checkout'),
                                'type'                => 'checkbox',
                                'description' => __('Place the payment gateway in test mode.', 'ag_epdq_checkout'),
                                'default'        => 'no',
                        ),
                        'cardtypes'        => array(
                                'title'                 => __('Accepted Cards', 'ag_epdq_checkout'),
                                'type'                         => 'multiselect',
                                'class'                        => 'chosen_select',
                                'css'                 => 'width: 350px;',
                                'description'         => __('Select which card types to accept. This is to show card icons on the checkout only.', 'ag_epdq_checkout'),
                                'default'                 => '',
                                'options'                 => array(
                                        'mastercard'                => __('MasterCard', 'ag_epdq_checkout'),
                                        'amex'         => __('American Express', 'ag_epdq_checkout'),
                                        'maestro'                        => __('Maestro', 'ag_epdq_checkout'),
                                        'visa'                                => __('Visa', 'ag_epdq_checkout'),
                                        'jcb'                                => __('JCB', 'ag_epdq_checkout'),
                                ),
                        ),
                        //'encryption' => array(
			//	'title'		=> __( 'AG Encryption key', 'ag-epdq_checkout' ),
			//	'type'		=> 'password',
			//	'description'	=> 'Please enter an encryption key, this is needed to add an extra layer of security for the plugin (This is not part of the ePDQ system and is seen and an option only, its not required).<br />We have a random generator which you can use <strong>'. ePDQ_Direct_crypt::encrypt_password_gen() .'</strong> It will regenerate on each load giving you multiple options.',
			//	'desc_tip' => false,
			//),
                        'debug' => array(
                                'title' => __('Enable Debug', 'ag_epdq_checkout'),
                                'type' => 'checkbox',
                                'label' => 'Enable debug reporting',
                                'default' => 'no',
                                'description' => 'To view the log go <a href="' . site_url() . '/wp-admin/admin.php?page=wc-status&tab=logs">here</a> and find <strong>barclaycard-epdq-direct-link-payment-gateway-woocommerce-premium</strong> in the WooCommerce logs',
                                'desc_tip' => false
                        ),


                );
        }
}
