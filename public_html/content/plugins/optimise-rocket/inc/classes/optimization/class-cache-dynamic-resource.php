<?php
namespace WP_Rocket\Optimization;

use WP_Rocket\Admin\Options_Data as Options;

/**
 * Create a static file for CSS/JS generated by a PHP file
 *
 * @since 3.1
 * @author Remy Perona
 */
class Cache_Dynamic_Resource extends Abstract_Optimization {
	use \WP_Rocket\Optimization\CSS\Path_Rewriter;

	/**
	 * Plugin options instance.
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * Cache busting base path
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @var string
	 */
	protected $busting_path;

	/**
	 * Cache busting base URL
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @var string
	 */
	protected $busting_url;

	/**
	 * Excluded files from optimization
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @var string
	 */
	protected $excluded_files;

	/**
	 * Constructor
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @param Options $options      Plugin options instance.
	 * @param string  $busting_path Base cache busting files path.
	 * @param string  $busting_url  Base cache busting files URL.
	 */
	public function __construct( Options $options, $busting_path, $busting_url ) {
		$this->options      = $options;
		$this->busting_path = $busting_path . get_current_blog_id() . '/';
		$this->busting_url  = $busting_url . get_current_blog_id() . '/';

		/**
		 * Filters files to exclude from static dynamic resources
		 *
		 * @since 2.9.3
		 * @author Remy Perona
		 *
		 * @param array $excluded_files An array of filepath to exclude.
		 */
		$this->excluded_files   = apply_filters( 'rocket_exclude_static_dynamic_resources', array() );
		$this->excluded_files[] = '/acp/admin-ajax.php';

		foreach ( $this->excluded_files as $i => $excluded_file ) {
			// Escape character for future use in regex pattern.
			$this->excluded_files[ $i ] = str_replace( '#', '\#', $excluded_file );
		}

		$this->excluded_files = implode( '|', $this->excluded_files );
	}

	/**
	 * Replaces the dynamic URL by the static file URL
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @param string $src Source URL.
	 * @return string
	 */
	public function replace_url( $src ) {
		$path = ltrim( rocket_extract_url_component( $src, PHP_URL_PATH ), '/' );

		/*
		* Filters the dynamic resource cache filename
		*
		* @since 2.9
		* @author Remy Perona
		*
		* @param string $filename filename for the cache file
		*/
		$filename = apply_filters( 'rocket_dynamic_resource_cache_filename', preg_replace( '/\.php$/', '-' . $this->minify_key . '.' . $this->extension, $path ) );
		$filename = rocket_realpath( rtrim( str_replace( array( ' ', '%20' ), '-', $filename ) ) );
		$filepath = $this->busting_path . $filename;

		if ( ! rocket_direct_filesystem()->is_readable( $filepath ) ) {
			$content = $this->get_url_content( $src );

			if ( ! $content ) {
				return $src;
			}

			if ( 'css' === $this->extension ) {
				$content = $this->rewrite_paths( $this->get_file_path( $src ), $filepath, $content );
			}

			if ( ! $this->write_file( $content, $filepath ) ) {
				return $src;
			}
		}

		return $this->get_cache_url( $filename );
	}

	/**
	 * Determines if we can optimize
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @return boolean
	 */
	public function is_allowed() {
		global $pagenow;

		if ( defined( 'DONOTROCKETOPTIMIZE' ) && DONOTROCKETOPTIMIZE ) {
			return false;
		}

		if ( is_user_logged_in() && ! $this->options->get( 'cache_logged_user' ) ) {
			return false;
		}

		if ( 'wp-login.php' === $pagenow ) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the file is excluded from optimization
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @param string $src source URL.
	 * @return boolean
	 */
	public function is_excluded_file( $src ) {
		$file = get_rocket_parse_url( $src );

		if ( ! preg_match( '#\.php$#', $file['path'] ) ) {
			return true;
		}

		if ( $this->is_external_file( $src ) ) {
			return true;
		}

		if ( preg_match( '#^' . $this->excluded_files . '$#', rocket_clean_exclude_file( $src ) ) ) {
			return true;
		}

		$file['query'] = remove_query_arg( 'ver', $file['query'] );

		if ( $file['query'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Sets the current file extension and minify key
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @param string $extension Current file extension.
	 * @return void
	 */
	public function set_extension( $extension ) {
		$this->extension  = $extension;
		$this->minify_key = $this->options->get( 'minify_' . $this->extension . '_key' );
	}

	/**
	 * Gets the CDN zones.
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @return array
	 */
	public function get_zones() {
		return [ 'all', 'css_and_js', $this->extension ];

	}

	/**
	 * Gets the cache URL for the static file
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @param string $filename Filename for the static file.
	 * @return string
	 */
	protected function get_cache_url( $filename ) {
		$cache_url = get_rocket_cdn_url( $this->busting_url . $filename, $this->get_zones() );

		switch ( $this->extension ) {
			case 'css':
				// This filter is documented in inc/classes/optimization/css/class-abstract-css-optimization.php.
				$cache_url = apply_filters( 'rocket_css_url', $cache_url );
				break;
			case 'js':
				// This filter is documented in inc/classes/optimization/css/class-abstract-js-optimization.php.
				$cache_url = apply_filters( 'rocket_js_url', $cache_url );
				break;
		}

		return $cache_url;
	}

	/**
	 * Gets content from an URL
	 *
	 * @since 3.1
	 * @author Remy Perona
	 *
	 * @param string $url URL to get the content from.
	 * @return string|bool
	 */
	protected function get_url_content( $url ) {
		$content  = wp_remote_retrieve_body( wp_remote_get( $url ) );

		if ( ! $content ) {
			return false;
		}

		return $content;
	}
}
