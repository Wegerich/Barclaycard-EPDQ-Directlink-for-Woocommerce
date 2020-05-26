<?php 
/*-----------------------------------------------------------------------------------*/
/*	GDPR new bits
/*-----------------------------------------------------------------------------------*/

class AG_ePDQ_Direct_gdpr {


    public function __construct() {

        add_action( 'admin_init', array($this, 'plugin_add_suggested_privacy_content'), 20 );
        add_action( 'woocommerce_privacy_before_remove_order_personal_data', array($this, 'remove_epdq_personal_data') );


    }


    
    public function remove_epdq_personal_data( $order ) {

        $epdq_meta_to_remove = apply_filters( 'woocommerce_privacy_remove_order_personal_data_meta', array(
            'Acceptance'      => 'text',
            'BRAND'           => 'text',
            'IP'              => 'text',
            'NCERROR'         => 'text',
            'PAYID'           => 'text',
            'PaymentMethod'   => 'text',
            'Status'          => 'text',
            'OrderAmount'     => 'text',
            'TRXDATE'         => 'text',
            'OrderCurrency'   => 'text',
            'OrderID'         => 'text',
            '_transaction_id' => 'text',
        ) );
        if ( !empty($epdq_meta_to_remove) && is_array( $epdq_meta_to_remove ) ) {
            foreach ( $epdq_meta_to_remove as $meta_keys => $data_types ) {
                $value = $order->get_meta( $meta_keys );
                if ( empty($value) || empty($data_types) ) {
                    continue;
                }
                $anon_value = ( function_exists( 'wp_privacy_anonymize_data' ) ? wp_privacy_anonymize_data( $data_types, $value ) : '' );
                $anon_value = apply_filters(
                    'woocommerce_privacy_remove_order_personal_data_meta_value',
                    $anon_value,
                    $meta_keys,
                    $value,
                    $data_types,
                    $order
                );
                
                if ( $anon_value ) {
                    $order->update_meta_data( $meta_keys, $anon_value );
                } else {
                    $order->delete_meta_data( $meta_keys );
                }
            
            }
        }
    }

    
    public function plugin_get_default_privacy_content()
    {
        return '<p>' . __( 'In the WooCommerce payments section you should list Barclays ePDQ as a payment processor.<br />
    <br />
    We accept payments through Barclays ePDQ. When processing payments, some of your data will be passed to Barclays, including information required to process or support the payment, such as the purchase total and billing information.<br /><br />
    Once an order has been placed some of the data sent back from Barclays is stored to support the payment and order, this data will remain part of the order until it is deleted.
    <br/><br />
    Please see Barclays ePDQ Privacy Policy for more details.' ) . '</p>';
    }

    function plugin_add_suggested_privacy_content()
    {
        $content = $this->plugin_get_default_privacy_content();
        wp_add_privacy_policy_content( __( 'AG ePDQ Server Gateway' ), $content );
    }

 

}