<?php
/*-----------------------------------------------------------------------------------*/
/*	AG Get doc urls
/*-----------------------------------------------------------------------------------*/
defined( 'ABSPATH' ) or die( "No script kiddies please!" );


if ( class_exists( 'AG_start_here_docs' ) ) {
	return;
}


class AG_direct_start_here_docs {

    
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
    
    /**
	 * Doc url
	 * @var string
	 */
    public static $AG_doc_url = 'https://we-are-ag.helpscoutdocs.com/';
    

    public static function get_doc_links() {

		$return = array();
        $url    = self::$AG_doc_url . self::$args['start_here'];
        $transient_name = 'get_doc_links';
        
		$html   = file_get_contents( $url );

		if ( ! $html ) {
			set_transient( $transient_name, $return, 12 * HOUR_IN_SECONDS );

			return $return;
		}

		$dom = new DOMDocument;

		@$dom->loadHTML( $html );

		$lists = $dom->getElementsByTagName( 'ul' );

		if ( empty( $lists ) ) {
			set_transient( $transient_name, $return, 12 * HOUR_IN_SECONDS );

			return $return;
		}

		foreach ( $lists as $list ) {
			$classes = $list->getAttribute( 'class' );

			if ( strpos( $classes, 'articleList' ) === false ) {
				continue;
			}

			$links = $list->getElementsByTagName( 'a' );

			foreach ( $links as $link ) {
				$return[] = array(
					'href'  => $link->getAttribute( 'href' ),
					'title' => $link->nodeValue,
				);
			}
		}

		set_transient( $transient_name, $return, 30 * DAY_IN_SECONDS );

		return $return;
	}


	public static function output_doc_links() {
        $links = self::get_doc_links();

		if ( empty( $links ) ) {
			return;
		} ?>

		<ol>
			<?php foreach ( $links as $link ) { ?>
				<li>
					<a href="<?php echo esc_attr( 'https://we-are-ag.helpscoutdocs.com' . $link['href'] ); ?>?utm_source=<?php echo self::$args['plugin_slug']; ?>&utm_medium=insideplugin" target="_blank"><?php echo $link['title']; ?></a>
				</li>
			<?php } ?>
		</ol>

        <p><strong>Still having problems?</strong> Have a look at our <a href="<?php echo self::$AG_doc_url . self::$args['troubleshooting']; ?>" target="_blank">troubleshooting</a> documentation.<br />There is a permanent link to the plugin documentation below.</p>

		<p>Want to know more about other Payment options or PCI compliance? have a look at our tips and information section below.</p>

		<p><strong>Need multiple payment gateways for your clients? <a href="https://weareag.co.uk/ag-bundles/?utm_source=<?php echo self::$args['plugin_slug']; ?>&utm_medium=insideplugin" target="_blank">Save over 50% with an AG bundle.</a></strong></p>
	<?php }





}