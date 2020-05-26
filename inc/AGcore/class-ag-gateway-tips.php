<?php
/*-----------------------------------------------------------------------------------*/
/*	AG Gateway tips
/*-----------------------------------------------------------------------------------*/
defined( 'ABSPATH' ) or die( "No script kiddies please!" );


if ( class_exists( 'AG_gateway_tips' ) ) {
	return;
}


class AG_direct_gateway_tips {


    public static $instance = null;
    public static $args = array();
    
    /**
	 * run
	 */
	public static function run_instance( $args = array() ) {
		if ( self::$instance === null ) {
			self::$args            = $args;
			self::$instance = new self();
		}

		return self::$instance;
    }


    public static function setup_tips() {

        return array(
            'PCI' =>  array(
                'title'          =>  'What is PCI compliance and does it affect me?',
                'tip_url'        =>  'https://weareag.co.uk/what-is-pci-compliance-and-does-it-affect-me/',
                'dec'            =>  'If you plan to run an e-commerce site, you need to be familiar with PCI compliance. PCI stands for Payment Card Industry, which is an independent body that was created by the major payment card brands (Visa, MasterCard, American Express, Discover and JCB). PCI establishes a set of specific rules and requirements you need to […]',
                'tip_img'        =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/03/What-is-PCI-compliance-and-does-it-affect-me-1024x683.jpg',
            ),
            'payments_101' =>  array(
                'title'          =>  'Online Payment Processing Options 101',
                'tip_url'        =>  'https://weareag.co.uk/online-payment-processing-options-101/',
                'dec'            =>  'What is online payment processing? Online payment processing is the system that allows you to purchase goods or services on the internet without using cash. There are two main elements, the payment gateway and the payment processor. ',
                'tip_img'        =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/08/online-payment-processing-options-101.jpg',
            ),
            'for_you' =>  array(
                'title'          =>  'What Payment Gateway Solution is Best for You?',
                'tip_url'        =>  'https://weareag.co.uk/what-payment-gateway-solution-is-best-for-you/',
                'dec'            =>  'Growing a business is exciting. Seeing sales come pouring in is a shot of energy that helps you keep going even when things get tough. Choose the payment gateway that will work best with your business. Take time to do the research and read the reviews. Ask other online business owners what gateway they’ve used. ',
                'tip_img'        =>  'https://weareag.co.uk/wp/wp-content/uploads/2018/11/What-Payment-Gateway-Solution-is-Best-for-You-1024x576.jpg',
            ),
            'luhn' =>  array(
                'title'          =>  'The Luhn Algorithm',
                'tip_url'        =>  'https://weareag.co.uk/the-luhn-algorithm/',
                'dec'            =>  'Have you ever wondered what happens if you enter the wrong credit card information to a payment gateway? Does the number get rejected, does it try to take payment so you still get your products, or does some random person suddenly lose a chunk of money?',
                'tip_img'        =>  'https://weareag.co.uk/wp/wp-content/uploads/2019/02/The-Luhn-Algorithm.jpg',
            ),
        );
    }

    public static function get_defined_tips( $slugs = array() ) {

		$tips          = self::setup_tips();
        $selected_tips = array();
        $slugs = self::$args['tips'];
        

		foreach ( $slugs as $slug ) {
			$selected_tips[ $slug ] = $tips[ $slug ];
		}

		if ( empty( $selected_tips ) ) {
			return false;
		}

		return $selected_tips;
    }


    public static function output_tips() {
        $tips = self::get_defined_tips();
        foreach ( $tips as $tip ) { ?>

        <div class="tip-card">
            <div class="card-contents">
                <div class="card-header">
                    <a href="<?php echo $tip['tip_url']; ?>?utm_source=<?php echo self::$args['plugin_slug']; ?>&utm_medium=plugin_up_sell" target="_blank">
                        <img class="plugin-logo" src="<?php echo $tip['tip_img']; ?>">
                        <div class="ag-watermark">
                            <img src="https://weareag.co.uk/wp/wp-content/themes/AGv5/img/ag-logo.svg">
                        </div>
                    </a>
                </div>
                <div class="card-body">
                    <h3><?php echo $tip['title']; ?></h3>
                    <p><?php echo $tip['dec']; ?></p>
                    <a href="<?php echo $tip['tip_url']; ?>?utm_source=<?php echo self::$args['plugin_slug']; ?>&utm_medium=plugin_up_sell" target="_blank" class="ag-button">Find out more</a>
                </div>
            </div>
        </div>


		
        <?php } ?>

        <style>
        	.tip-card {
                -webkit-box-flex: 0 0 25%;
                -ms-flex: 0 0 25%;
                flex: 0 0 25%;
                max-width: 350px;
            }

            .tip-card {
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
            .tip-card .card-contents {
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

            .tip-card .card-header {
                overflow: hidden;
                border-radius: 0.25rem 0.25rem 0 0;
            }
            .tip-card .card-header a {
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
            .tip-card .card-header a .plugin-logo {
                
                width: 100%;
                max-height: 150px;
                object-fit: fill;
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
            .tip-card .card-header a .plugin-price {
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
            .tip-card .card-header a .plugin-tint {
                position: absolute;
                z-index: 2;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background-color: #323232;
                opacity: 0.95;
            }
            .tip-card .card-header a .plugin-background {
                position: relative;
                z-index: 1;
                display: block;
                width: 100%;
                height: auto;
            }
            .tip-card .card-body {
                -webkit-box-flex: 1;
                -ms-flex: 1;
                flex: 1;
                background-color: #ffffff;
                padding: 0.9375rem 0.9375rem 4rem 0.9375rem;
                border-radius: 0 0 0.25rem 0.25rem;
                position: relative;
            }
            .tip-card .card-body p { height: 150px; }
            .tip-card .card-body .ag-button {
                position: absolute;
                bottom: 0.9375rem;
                left: 0.9375rem;
                right: 0.9375rem;
            }
            .tip-card .card-contents .card-body .ag-button {
                display: block;
                text-align: center;
                color: #ffffff;
                background-color: #422450;
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

            .tip-card .card-contents .card-body .ag-button:hover { color: #fff; background-color: #543a60;}
        </style>
	<?php }
    

}

