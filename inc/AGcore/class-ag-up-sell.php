<?php
defined( 'ABSPATH' ) or die( "No script kiddies please!" );
/*-----------------------------------------------------------------------------------*/
/*	AG up sell
/*-----------------------------------------------------------------------------------*/

class AG_direct_up_sell {

    public static $single_instance = null;
    public static $args = array();
    
    
    /**
	 * run
	 */
	public static function run_instance( $args = array() ) {
		if ( self::$single_instance === null ) {
			self::$args            = $args;
			self::$single_instance = new self();
		}

		return self::$single_instance;
    }


 
    

    public static function setup_plugins() {

        return array(
           'sagepay_direct' =>  array(
               'title'          =>  'SagePay Direct For WooCommerce',
               'plugin_url'     =>  'https://weareag.co.uk/product/sagepay-direct-woocommerce/',
               'dec'            =>  '63% of consumers feel more reassured about their purchase when a SagePay logo is shown on a website.',
               'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/10/ag-sagepay.png',
               'plugin_color'   =>  '#323232',  
           ), 
           'sagepay_server' =>  array(
                'title'          =>  'SagePay Server For WooCommerce',
                'plugin_url'     =>  'https://weareag.co.uk/product/sage-pay-server-woocommerce/',
                'dec'            =>  '63% of consumers feel more reassured about their purchase when a SagePay logo is shown on a website.',
                'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/10/ag-sagepay.png',
                'plugin_color'   =>  '#323232',  
           ),
           'visa_checkout'  =>  array(
                'title'          =>  'Visa Checkout For WooCommerce',
                'plugin_url'     =>  'https://weareag.co.uk/product/ag-visa-checkout-for-woocommerce/',
                'dec'            =>  'Visa Checkout customers make 30% more transactions per person compared to other customers.',
                'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/10/ag-visa.png',
                'plugin_color'   =>  '#1a1e5a',  
           ),
           'epdq_direct'    =>  array(
                'title'          =>  'Barclaycard ePDQ Direct For WooCommerce',
                'plugin_url'     =>  'https://weareag.co.uk/product/barclaycard-epdq-direct-link-payment-gateway-woocommerce/',
                'dec'            =>  'Barclaycard is one of the most established & trusted merchant payment companies in the UK.',
                'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/10/ag-barclays.png',
                'plugin_color'   =>  '#543a60', 
           ),
           'epdq_server'    =>  array(
                'title'          =>  'Barclaycard ePDQ Server For WooCommerce',
                'plugin_url'     =>  'https://weareag.co.uk/product/ag-barclays-epdq-payment-gateway-woocommerce/',
                'dec'            =>  'Industry researches have proved that credit card payments on-line increase sales up to 23% because products and services become more easily available to customers.',
                'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/10/ag-barclays.png',
                'plugin_color'   =>  '#543a60', 
           ),
           'adyen'          =>  array(
                'title'          =>  'Adyen HPP For WooCommerce',
                'plugin_url'     =>  'https://weareag.co.uk/product/ag-adyen-hpp-woocommerce-gateway/',
                'dec'            =>  'Adyen serves more than 4,500 businesses, including 8 of the 10 largest U.S. Internet companies. Customers include Facebook, Uber, Netflix, Spotify, L’Oreal and Burberry.',
                'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/10/ag-adyen.png',
                'plugin_color'   =>  '#071a40', 
           ),
           'pay360'         =>  array(
                'title'          =>  'Pay360 For WooCommerce',
                'plugin_url'     =>  'https://weareag.co.uk/product/ag-woocommerce-pay360-payment-gateway/',
                'dec'            =>  'Pay360 is a leading online payment provider, our Pay360 plugin focuses on the hosted cashier API.',
                'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/10/ag-pay360.png',
                'plugin_color'   =>  '#287470', 
           ),
           'safecharge'         =>  array(
                'title'          =>  'SafeCharge For WooCommerce',
                'plugin_url'     =>  'https://weareag.co.uk/product/safecharge-payment-gateway-for-woocommerce/',
                'dec'            =>  'SafeCharge Checkout page is a ready to use, customisable payment page designed to give your customers a smooth payment experience, online and on mobile.',
                'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2019/01/AG-safecharge-WooCommerce-e1548292046998.png',
                'plugin_color'   =>  '#016080', 
            ),
            'lloyds'         =>  array(
                'title'          =>  'Lloyds Cardnet Connect For WooCommerce',
                'plugin_url'     =>  'https://weareag.co.uk/product/lloyds-cardnet-connect-for-woocommerce/',
                'dec'            =>  'Lloyds Bank is one of the leading names in European banking, so it comes as no surprise it provides merchant account services to thousands of firms across the continent and beyond.',
                'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2019/01/ag-cardnet-1.png',
                'plugin_color'   =>  '#006a4e', 
            ),
            'auth'         =>  array(
                'title'          =>  'Authorize.net Hosted For WooCommerce',
                'plugin_url'     =>  'https://weareag.co.uk/product/authorize-net-hosted-for-woocommerce/',
                'dec'            =>  'Authorize.Net has been working with merchants and small businesses since 1996 and is trusted by more than 430,000 merchants.',
                'plugin_img'     =>  'https://weareag.co.uk/wp/wp-content/uploads/2019/03/ag-Authorize.net-Hosted-1.png',
                'plugin_color'   =>  '#1c3141', 
            ),
        );

    }


    public static function get_defined_plugins( $slugs = array() ) {
	
		$plugins          = self::setup_plugins();
        $selected_plugins = array();
        $slugs = self::$args['plugins'];
        

		foreach ( $slugs as $slug ) {
			$selected_plugins[ $slug ] = $plugins[ $slug ];
		}

		if ( empty( $selected_plugins ) ) {
			return false;
		}

		return $selected_plugins;
    }
    

    public static function output_up_sells() {
        $upsells = self::get_defined_plugins();
        foreach ( $upsells as $upsell ) { ?>

        <div class="product-card">
            <div class="card-contents">
                <div class="card-header">
                    <a href="<?php echo $upsell['plugin_url']; ?>?utm_source=<?php echo self::$args['plugin_slug']; ?>&utm_medium=plugin_up_sell" target="_blank">
                        <img class="plugin-logo" src="<?php echo $upsell['plugin_img']; ?>">
                        <div class="ag-watermark">
                            <img src="https://weareag.co.uk/wp/wp-content/themes/AGv5/img/ag-logo.svg">
                        </div>
                        <div class="plugin-tint" style="background-color:<?php echo $upsell['plugin_color']; ?>; opacity:0.95;"></div>
                        <img class="plugin-background" src="https://weareag.co.uk/wp/wp-content/themes/AGv5/img/plugin-background.jpg">
                    </a>
                </div>
                <div class="card-body">
                    <h3><?php echo $upsell['title']; ?></h3>
                    <p><?php echo $upsell['dec']; ?></p>
                    <a href="<?php echo $upsell['plugin_url']; ?>?utm_source=<?php echo self::$args['plugin_slug']; ?>&utm_medium=plugin_up_sell" target="_blank" class="ag-button">Find out more</a>
                </div>
            </div>
        </div>


		
        <?php } ?>

        <style>
        	.product-card {
                -webkit-box-flex: 0 0 25%;
                -ms-flex: 0 0 25%;
                flex: 0 0 25%;
                max-width: 350px;
            }

            .product-card {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                float: left;
                position: relative;
                min-height: 1px;
                padding-right: 14px;
                padding-left: 14px;
                padding-bottom: 28px;
                padding-top: 30px;
            }
            .product-card .card-contents {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                -moz-flex-direction: column;
                -ms-flex-direction: column;
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                flex-direction: column;
                border: 0.0625rem solid rgba(56, 56, 56, 0.1);
                border-radius: 0.25rem;
                -webkit-box-shadow: 3px 3px 8px 0px rgba(0, 0, 0, 0.05);
                box-shadow: 3px 3px 8px 0px rgba(0, 0, 0, 0.05);
            }

            .product-card .card-header {
                overflow: hidden;
                border-radius: 0.25rem 0.25rem 0 0;
            }
            .product-card .card-header a {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                -moz-align-items: center;
                -ms-align-items: center;
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center;
                -moz-justify-content: center;
                -ms-justify-content: center;
                -webkit-box-pack: center;
                justify-content: center;
                -ms-flex-pack: center;
                position: relative;
            }
            .product-card .card-header a .plugin-logo {
                position: absolute;
                z-index: 4;
            }
            .ag-watermark {
                bottom: 0.75rem;
                left: 0.875rem;
            }
            .ag-watermark img {
                width: 1.5625rem;
            }
            .ag-watermark, .ag-watermark-lg, .ag-watermark-xl {
                position: absolute;
                z-index: 3;
            }
            .ag-watermark img, .ag-watermark-lg img, .ag-watermark-xl img {
                height: auto;
            }
            .product-card .card-header a .plugin-price {
                position: absolute;
                z-index: 3;
                top: 0;
                right: 0;
                background-color: rgba(0, 0, 0, 0.88);
                color: #ffffff;
                font-family: "MegalopolisExtra", sans-serif;
                font-weight: 700;
                font-size: 1.1875rem;
                line-height: 1;
                padding: 0.6875rem 0.625rem;
                border-radius: 0 0 0 0.25rem;
            }
            .product-card .card-header a .plugin-tint {
                position: absolute;
                z-index: 2;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background-color: #323232;
                opacity: 0.95;
            }
            .product-card .card-header a .plugin-background {
                position: relative;
                z-index: 1;
                display: block;
                width: 100%;
                height: auto;
            }
            .product-card .card-body {
                -webkit-box-flex: 1;
                -ms-flex: 1;
                flex: 1;
                background-color: #ffffff;
                padding: 0.9375rem 0.9375rem 4rem 0.9375rem;
                border-radius: 0 0 0.25rem 0.25rem;
                position: relative;
            }
            .product-card .card-body p { height: 80px; }
            .product-card .card-body .ag-button {
                position: absolute;
                bottom: 0.9375rem;
                left: 0.9375rem;
                right: 0.9375rem;
            }
            .ag-button {
                display: block;
                text-align: center;
                color: #ffffff;
                background-color: #e2037d;
                text-decoration: none;
                line-height: 1;
                padding: 1.0625rem 1.5rem 1.125rem 1.5rem;
                border: none;
                border-radius: 0.25rem;
                font-family: "Ubuntu", sans-serif;
                font-size: 1.1875rem;
                font-weight: 700;
                -webkit-transition: background-color .2s ease;
                transition: background-color .2s ease;
            }

            .ag-button:hover { color: #fff; background-color: #970253;}
        </style>
	<?php }

}



