<?php
/*-----------------------------------------------------------------------------------*/
/*	AG Welcome/Account Screen
/*-----------------------------------------------------------------------------------*/
defined( 'ABSPATH' ) or die( "No script kiddies please!" );

if ( class_exists( 'AG_direct_welcome_screen' ) ) {
	return;
}

/**
 * AG welcome screen
 */
class AG_direct_welcome_screen {



	/**
	 * Doc url
	 * @var string
	 */
	public static $AG_doc_url = 'https://we-are-ag.helpscoutdocs.com/';


	/**
	 * AG SVG
	 * @var string
	 */
	public static $ag_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIzLjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAxODIgMTI1IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxODIgMTI1OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+Cgkuc3Qwe2ZpbGw6IzM4MzgzODt9Cjwvc3R5bGU+CjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik03OS4xOCwxMDkuNGMtMC43NS0wLjM2LTEtMS4yMi0wLjYyLTEuODNsNDEuOTctOTEuODJjMC4zOC0wLjYxLDEuMjUtMC44NiwyLTAuMzdjMC42MiwwLjM3LDAuODcsMS4xLDAuNSwxLjgzCglsLTQxLjk4LDkxLjdDODAuNjcsMTA5LjY1LDc5LjkyLDEwOS43Nyw3OS4xOCwxMDkuNHoiLz4KPGc+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNODQuMyw3NC4wMWwxLjY3LTMuNjVMNjIuNywxOS4xNmMtMC41NS0xLjIzLTIuNDYtMS4zNC0zLjAxLDBsLTQwLjEyLDg3LjkzYy0wLjQxLDAuNjEsMCwxLjU5LDAuODIsMS44MwoJCWMwLjgzLDAuMjQsMS43OCwwLDIuMDYtMC44Nkw2MS4yLDIzLjMyTDg0LjMsNzQuMDF6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNTIuMyw4Mi4wMmMwLDAuNzQsMC44MiwxLjM0LDEuNjQsMS4zNGgxNC4zOGMwLjk2LDAsMS42NC0wLjYxLDEuNjQtMS4zNGMwLTAuMjQsMC0wLjM2LTAuMTMtMC42MUw2Mi43LDY1Ljc1CgkJYy0wLjQxLTAuODYtMS4yMy0xLjIyLTIuMTktMC44NmMtMC42OSwwLjI1LTEuMDksMS4xMS0wLjgyLDEuODRsNi40MywxMy45NEg1My45NEM1My4xMiw4MC42Nyw1Mi4zLDgxLjI4LDUyLjMsODIuMDJ6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzMuMiw5OC4yOEg0NmMtMC42OSwwLTEuMzcsMC4yNC0xLjUsMC44NmwtMy43LDcuOTVjLTAuNDEsMC42MSwwLDEuNTksMC45NiwxLjgzYzAuODMsMC4yNCwxLjY0LDAsMS45Mi0wLjg2CgkJbDMuMjktNy4wOWgyNS4wMUw3My4yLDk4LjI4eiIvPgo8L2c+CjxnPgoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTEyOS4zOCwyMi43N2MxLjY2LTAuMTcsMy4yOC0wLjM5LDUuMDUtMC40M2M3LjQ3LTAuMjQsMTQuNjgsMC44NiwyMS43NiwyLjkzYzAuOCwwLjI0LDEuNi0wLjEyLDItMC44NgoJCWMwLjI3LTAuNzMtMC4yNy0xLjU5LTEuMDctMS44M2MtNy40OC0yLjItMTQuOTUtMy40My0yMi44My0zLjE4Yy0xLjIzLDAuMDQtMi4zMywwLjIyLTMuNTIsMC4zMkwxMjkuMzgsMjIuNzd6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTU1LjY1LDQyLjE1YzAuNC0wLjYxLDAtMS40Ny0wLjgtMS44NGMtNi40MS0yLjY5LTEzLjYxLTMuNDItMjAuNTYtMy4xOGMtNC44MywwLjE2LTkuMDMsMS4xNC0xMi42OSwyLjYzCgkJbC0xLjkzLDQuMjJjMy45NC0yLjI4LDguODItMy43NiwxNC43Ni0zLjkyYzYuNTQtMC4zNiwxMy4wOCwwLjM3LDE5LjIyLDIuODFDMTU0LjQ1LDQzLjI1LDE1NS4zOCw0Mi44OCwxNTUuNjUsNDIuMTV6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTM0LjI5LDkzLjI3YzEuODcsMCwzLjc0LTAuMTIsNS40Ny0wLjQ5YzAuOCwwLDEuMzQtMC42MSwxLjM0LTEuMzRWNjUuMTRjMC0wLjc0LTAuNjctMS4zNS0xLjQ3LTEuMzUKCQljLTAuOTMsMC0xLjYsMC42MS0xLjYsMS4zNXYyNC45NGMtMS4zNCwwLjI1LTIuNTQsMC4yNS0zLjc0LDAuMjVjLTEyLjQxLDAtMjAuNDItNi4xMi0yNC4yOS0xNC4xOWMtMS4wMy0yLjA4LTEuNjMtNC4zMy0yLjAyLTYuNjIKCQlMMTA2LDczLjg0YzAuMzcsMS4xNSwwLjY3LDIuMzIsMS4xOSwzLjQxQzExMS4zNCw4Ni4zLDEyMC41NCw5My4yNywxMzQuMjksOTMuMjd6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTU4Ljk4LDYzLjc5Yy0wLjgsMC0xLjYsMC42MS0xLjYsMS4zNXYzNy41NGMtNy4wOCwzLjY3LTE1LjA5LDUuMzgtMjMuMSw1LjM4CgkJYy0xNi42OSwwLTI5LjAzLTYuMjktMzYuODktMTUuNDRsLTEuMzcsMi45OWM4LjM5LDkuMTYsMjEuMiwxNS4zOSwzOC4yNSwxNS4zOWM4Ljk0LDAsMTcuNjItMi4yMSwyNS41LTYuMjQKCQljMC40LTAuMzcsMC44LTAuODYsMC42Ny0xLjM0VjY1LjE0QzE2MC40Niw2NC40MSwxNTkuNzksNjMuNzksMTU4Ljk4LDYzLjc5eiIvPgo8L2c+Cjwvc3ZnPgo=';


	public static $single_instance = null;
	public static $args = array();

	/**
	 * run instance
	 */
	public static function run_instance( $args = array() ) {
		if ( self::$single_instance === null ) {
			self::$args            = $args;
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}


	/**
	 * Construct
	 */
	private function __construct() {
		if ( ! AG_direct_licence::valid_licence() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'ag_welcome_screen' ), 20 );

	}

	/**
	 * Add Welcome page
	 */
	public function ag_welcome_screen() {

		
		add_menu_page(
            'Welcome to AG',
            'AG Plugins',
            'manage_options',
            htmlspecialchars(self::$args['parent_slug']),
            array( $this, 'setup_welcome_page' ),
            'dashicons-admin-network'
		);


	}


	/**
	 * Setting up the welcome page
	 */
	public function setup_welcome_page() {

		$page_title = sprintf( '<div style="padding-bottom: 15px;">%s from <a href="https://weareag.co.uk/?utm_source=ePDQ-Direct&utm_medium=insideplugin" target="_blank">We are AG</a> <em style="opacity: 0.6; font-size: 80%%;">(v%s)</em></div>', self::$args['plugin_title'], self::$args['plugin_version'] );

		self::getting_started(); ?>

		<div class="wrap ag-welcome-wrap">
        <h2><?php echo $page_title; ?></h2>

			<div class="ag-welcome-body">

				<h2>Account, Licence settings & Affiliation</h2>
				<div class="section">

				        <div class="main-card">
							<div class="card-contents">
								<div class="card-body">
									<h3>License &amp; Billing</h3>
									<p>Activate or sync your license, cancel your subscription, print invoices, and manage your account information.</p>
									<a href="<?php echo ePDQ_admin; ?>admin.php?page=<?php echo self::$args['main_slug']; ?>-account" class="ag-button">Manage Licence &amp; Billing</a>
								</div>
							</div>
						</div>

						<div class="main-card">
							<div class="card-contents">
								<div class="card-body">
									<h3>Your Account</h3>
									<p>Manage all of your AG plugins, subscriptions, renewals, and more.</p>
									<a target="_blank" href="https://weareag.co.uk/account?utm_source=<?php echo self::$args['main_slug']; ?>&amp;utm_medium=insideplugin" class="ag-button" target="_blank">Manage Your Account</a>
								</div>
							</div>
						</div>

						<div class="main-card">
							<div class="card-contents">
								<div class="card-body">
									<h3>Affiliate</h3>
									<p>Become an ambassador for AG and earn 20% commission for each sale!</p>
									<a href="<?php echo ePDQ_admin; ?>admin.php?page=<?php echo self::$args['main_slug']; ?>-affiliation" class="ag-button">Find Out More</a>
								</div>
							</div>
						</div>
						<div style="clear: both"></div>
				</div>
				<h2>Getting the help you need</h2>
				<div class="section">
						<div class="main-card">
							<div class="card-contents">
								<div class="card-body">
									<h3>Getting Support</h3>
									<p>Get premium support with a valid licence</p>
									<a target="_blank" href="https://weareag.co.uk/support?utm_source=<?php echo self::$args['main_slug']; ?>&amp;utm_medium=insideplugin" class="ag-button">Submit a ticket</a>
								</div>
							</div>
						</div>
						<div class="main-card">
							<div class="card-contents">
								<div class="card-body">
									<h3>Documentation</h3>
									<p>Have a read of the plugin documentation.</p>
									<a target="_blank" href="<?php echo self::$AG_doc_url; ?>collection/<?php echo self::$args['collection']; ?>" class="ag-button" target="_blank">Documentation</a>
								</div>
							</div>
						</div>
						<div style="clear: both"></div>
				</div>
				<h2>Other AG payment gateways</h2>
				<div class="section">
					<div class="row">
						<?php AG_direct_up_sell::output_up_sells() ?>
					</div>
				 <div style="clear: both"></div>
				</div>
				<h2>Payment gateway tips and information</h2>
				<div class="section">
					<div class="row">
						<?php AG_direct_gateway_tips::output_tips() ?>
					</div>
				 <div style="clear: both"></div>
				</div>


			</div>




		</div>
		<style>

			.st1 {
				fill: #23282d !important;
			}

        	.ag-notice {
				padding: 35px 30px;
				background-color: #fff;
				margin: 20px 20px 20px 0;
				box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
				font-size: 15px;
				position: relative;
				border-left: 4px solid #422450;
			}

			.ag-notice h2 {
				margin: 0 0 1.2em;
				font-size: 28px;
				position: relative;
				line-height: 1.2em;
			}

			.ag-notice h3 {
				margin: 0 0 1.5em;
			}

			.ag-notice p,
			.ag-notice li {
				font-size: 15px;
			}

			.ag-notice li {
				margin: 0 0 10px;
			}

			.ag-notice p,
			.ag-notice ol,
			.ag-notice ul {
				margin-bottom: 2em;
			}

			.ag-notice :last-child {
				margin-bottom: 0;
			}

			.ag-notice__dismiss {
				position: absolute;
				top: 20px;
				right: 20px;
			}

			.ag-notice__dismiss button {
				background: none;
				border: none;
				padding: 0;
				margin: 0;
				cursor: pointer;
				color: #422450;
				outline: none;
			}

			.ag-notice__dismiss button:hover,
			.ag-notice__dismiss button:active {
				color: #422450;
			}

			.ag-welcome .ag-badge {
				position: absolute;
				right: 0;
				top: 0;
			}
			.ag-welcome {
				font-size: 15px;
				margin: 25px 40px 0 20px;
				max-width: 1050px;
				position: relative;
			}
			.thickbox strong {
				display: block;
				width: 100%
			}

            .ag-welcome h2 { text-align: left; }

			.ag-welcome-body { margin-top: 20px;
			    border: 0.0625rem solid rgba(56, 56, 56, 0.1);
    border-radius: 0.25rem;
    -webkit-box-shadow: 3px 3px 8px 0px rgba(0, 0, 0, 0.05);
    box-shadow: 3px 3px 8px 0px rgba(0, 0, 0, 0.05);
	}

			.ag-welcome-body h2 {
				padding: 15px 30px;
				border: none;
				margin: 0;
				background: #23282d;
				color: #fff;
				font-size: 1.25em;
			}

			.ag-welcome-body .section {
				padding: 30px;
				background: #eaeaea;
			}

			.main-card {
                -webkit-box-flex: 0 0 25%;
                -ms-flex: 0 0 25%;
                flex: 0 0 25%;
                max-width: 350px;
            }

            .main-card {
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
            .main-card .card-contents {
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

            .main-card .card-body {
                -webkit-box-flex: 1;
                -ms-flex: 1;
                flex: 1;
                background-color: #ffffff;
                padding: 0.9375rem 0.9375rem 4rem 0.9375rem;
                border-radius: 0.25rem;
                position: relative;
				min-width: 318px;
            }
            .main-card .card-body p { height: 50px; }
            .main-card .card-body .ag-button {
                position: absolute;
                bottom: 0.9375rem;
                left: 0.9375rem;
                right: 0.9375rem;
            }
            .main-card .card-contents .card-body .ag-button {
                display: block;
                text-align: center;
                color: #ffffff;
                background-color: #383838;
                text-decoration: none;
                line-height: 1;
                padding: 1.0625rem 1.5rem 1.125rem 1.5rem;
                border: none;
                border-radius: 0.25rem;
                font-size: 1.1875rem;
                font-weight: 700;
                -webkit-transition: background-color .2s ease;
                transition: background-color .2s ease;
            }

            .main-card .card-contents .card-body .ag-button:hover { color: #fff; background-color: #543a60;}
		</style>
		<?php
	}


	/**
	 * Getting started
	 */
	public static function getting_started() {

				$option_name = 'ag_dismiss_welcome';
				$dismissed   = get_option( $option_name, false );

				if ( $dismissed ) {
					return;
				}

				$dismiss = filter_input( INPUT_POST, $option_name );

				if ( $dismiss ) {
					update_option( $option_name, true );

					return;
				}
		?>
			<div class="ag-notice ag-notice--getting-started">
				<form action="" method="post" class="ag-notice__dismiss">
					<input type="hidden" name="ag_dismiss_welcome" value="1">
					<button title="Dismiss" class="is-dismissible">
						Hide <span class="dashicons dashicons-dismiss"></span></button>
				</form>
				<h2><img height="35" style="display: inline-block; vertical-align: text-bottom; margin: 0 8px 0 0" src="<?php echo esc_attr(self::$ag_svg); ?>">Welcome to <?php echo self::$args['plugin_title']; ?>!</h2>
				<p>Thank you for choosing We are AG as your payment gateway partner.<br />Below are some useful links to help you get started:</p>

				<h3>Start here</h3>


				<?php AG_direct_start_here_docs::output_doc_links() ?>

			</div>
		<?php

	}

}
