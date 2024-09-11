<?php

/**
 * Bootstrap
 *
 * @package nlmg
 */
class NeverLetMeGo extends \NeverLetMeGo\Pattern\Application {

	/**
	 * constructor
	 *
	 * @param array $settings
	 */
	public function __construct( array $settings = [] ) {
		// Initialize instances.
		\NeverLetMeGo\Admin::getInstance();
		\NeverLetMeGo\Page::getInstance();
		\NeverLetMeGo\WooCommerce::getInstance();
		// Register command if available.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::add_command( 'nlmg', \NeverLetMeGo\Command::class );
		}
		// Register script
		add_action( 'init', [ $this, 'register_assets' ] );
		// Register block
		\NeverLetMeGo\ResignButton::getInstance();
	}

	/**
	 * Register assets.
	 *
	 * @return void
	 */
	public function register_assets() {
		$root = plugin_dir_path( NLMG_BASE_FILE );
		$json = $root . '/wp-dependencies.json';
		if ( ! file_exists( $json ) ) {
			return;
		}
		$deps = json_decode( file_get_contents( $json ), true );
		if ( ! $deps ) {
			return;
		}
		$root_url = plugin_dir_url( NLMG_BASE_FILE );
		foreach ( $deps as $dep ) {
			if ( empty( $dep['path'] ) ) {
				continue;
			}
			$url     = $root_url . $dep['path'];
			$version = $dep['hash'];
			switch ( $dep['ext'] ) {
				case 'css':
					wp_register_style( $dep['handle'], $url, $dep['deps'], $version, 'screen' );
					break;
				case 'js':
					$arg = apply_filters( 'nlmg_js_args', [
						'in_footer' => $dep['footer'],
					], $dep['handle'] );
					wp_register_script( $dep['handle'], $url, $dep['deps'], $version, $arg );
					break;
			}
		}
	}
}
