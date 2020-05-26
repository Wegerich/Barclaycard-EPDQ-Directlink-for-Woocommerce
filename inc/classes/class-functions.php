<?php

/*-----------------------------------------------------------------------------------*/
/*	AG ePDQ functions
/*-----------------------------------------------------------------------------------*/
defined('ABSPATH') or die("No script kiddies please!");


if (class_exists('AG_ePDQ_Direct_functions')) {
	return;
}


class AG_ePDQ_Direct_functions {


    public static function process_order_data($args, $order) {
			
		$order_notes = array(
			'Order  '				=>	$args['ORDERID'] = $args['ORDERID'] ?? '',
			'Amount: '				=>	$args['AMOUNT'] = $args['AMOUNT'] ?? '',
			'Order Currency: '		=>	$args['CURRENCY'] = $args['CURRENCY'] ?? '',
			'Payment Method: '		=>	$args['PM'] = $args['PM'] ?? '',
			'Acceptance Code: '		=>	$args['ACCEPTANCE'] = $args['ACCEPTANCE'] ?? '',
			'PAYID: '				=>	$args['PAYID'] = $args['PAYID'] ?? '',
			'Error Code: '			=>	$args['NCERROR'] = $args['NCERROR'] ?? '',
			'Card Brand: '			=>	$args['BRAND'] = $args['BRAND'] ?? '',
			'Transaction Date: '	=>	$args['TRXDATE'] = $args['TRXDATE'] ?? '',
			'Cardholder/Customer Name: '	=>	$args['CN'] = $args['CN'] ?? '',
			'Customer IP: '			=>	$args['IP'] = $args['IP'] ?? '',
			'Is 3D Secure payment: '=>	$args['3D_Secure'] = $args['3D_Secure'] ?? '',

		);
		
		//This is now in the 3DS bit to reduce the number of order notes.
		//AG_ePDQ_Direct_Helpers::update_order_notes($order, $order_notes);
	
		$order_data = array(
			//'OrderID'         	=> 	$args['ORDERID'] = $args['ORDERID'] ?? '',
			'Status'         	=> 	AG_direct_errors::get_epdq_direct_status_code($args['STATUS']),
			'OrderAmount'       => 	$args['AMOUNT'] = $args['AMOUNT'] ?? '',
			'OrderCurrency'		=>	$args['CURRENCY'] = $args['CURRENCY'] ?? '',
			'PaymentMethod'		=>	$args['PM'] = $args['PM'] ?? '',
			'Acceptance'		=>	$args['ACCEPTANCE'] = $args['ACCEPTANCE'] ?? '',
			'PAYID'				=>	$args['PAYID'] = $args['PAYID'] ?? '',
			'NCERROR'			=>	$args['NCERROR'] = $args['NCERROR'] ?? '',
			'BRAND'				=>	$args['BRAND'] = $args['BRAND'] ?? '',
			'TRXDATE'			=>	$args['TRXDATE'] = $args['TRXDATE'] ?? '',
			//'IP'				=>	$args['IP'] = $args['IP'] ?? '',
			'3DS Status'		=>	$args['ECI'] = $args['ECI'] ?? '',
			'AAVADDRESS'		=>	$args['AAVADDRESS'] = $args['AAVADDRESS'] ?? '',
			//'AAVCHECK'		=>	$args['AAVCHECK'] = $args['AAVCHECK'] ?? '',
			'AAVZIP'			=>	$args['AAVZIP'] = $args['AAVZIP'] ?? '',
		);

		
		AG_ePDQ_Direct_Helpers::update_order_meta_data($args['ORDERID'], $order_data);


		// 3D secure bits 
		$secure_notes = array(
			'Order: '			=>	$args['ORDERID'] = $args['ORDERID'] ?? '',
			'PAYID: '			=>	$args['PAYID'] = $args['PAYID'] ?? '',
			'Card Number: '		=>	$args['CARDNO'] = $args['CARDNO'] ?? '',
			'3DS Result: '		=>	$args['ECI'] = $args['ECI'] ?? '',
			'Address check: '	=>	$args['AAVADDRESS'] = $args['AAVADDRESS'] ?? '',
			'Postcode check: '	=>	$args['AAVZIP'] = $args['AAVZIP'] ?? '',
			'CVC check: '		=>	$args['CVCCHECK'] = $args['CVCCHECK'] ?? '',
			'Acceptance Code: '	=>	$args['ACCEPTANCE'] = $args['ACCEPTANCE'] ?? '',
			'Error Code: '		=>	$args['NCERROR'] = $args['NCERROR'] ?? '',
		);
		AG_ePDQ_Direct_Helpers::update_order_notes($order, $secure_notes);

    }

    

}