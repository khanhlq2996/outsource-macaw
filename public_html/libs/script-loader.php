<?php
/**
 * WordPress scripts and styles default loader.
 *
 * Several constants are used to manage the loading, concatenating and compression of scripts and CSS:
 * define('SCRIPT_DEBUG', true); loads the development (non-minified) versions of all scripts and CSS, and disables compression and concatenation,
 * define('CONCATENATE_SCRIPTS', false); disables compression and concatenation of scripts and CSS,
 * define('COMPRESS_SCRIPTS', false); disables compression of scripts,
 * define('COMPRESS_CSS', false); disables compression of CSS,
 * define('ENFORCE_GZIP', true); forces gzip for compression (default is deflate).
 *
 * The globals $concatenate_scripts, $compress_scripts and $compress_css can be set by plugins
 * to temporarily override the above settings. Also a compression test is run once and the result is saved
 * as option 'can_compress_scripts' (0/1). The test will run again if that option is deleted.
 *
 * @package WordPress
 */

/** WordPress Dependency Class */
require( ABSPATH . WPINC . '/class-wp-dependency.php' );

/** WordPress Dependencies Class */
require( ABSPATH . WPINC . '/class.wp-dependencies.php' );

/** WordPress Scripts Class */
require( ABSPATH . WPINC . '/class.wp-scripts.php' );

/** WordPress Scripts Functions */
require( ABSPATH . WPINC . '/functions.wp-scripts.php' );

/** WordPress Styles Class */
require( ABSPATH . WPINC . '/class.wp-styles.php' );

/** WordPress Styles Functions */
require( ABSPATH . WPINC . '/functions.wp-styles.php' );

/**
 * Registers TinyMCE scripts.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_register_tinymce_scripts( &$scripts, $force_uncompressed = false ) {
	global $tinymce_version, $concatenate_scripts, $compress_scripts;
	$suffix     = wp_scripts_get_suffix();
	$dev_suffix = wp_scripts_get_suffix( 'dev' );

	script_concat_settings();

	$compressed = $compress_scripts && $concatenate_scripts && isset( $_SERVER['HTTP_ACCEPT_ENCODING'] )
				  && false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) && ! $force_uncompressed;

	// Load tinymce.js when running from /src, otherwise load wp-tinymce.js.gz (in production) or
	// tinymce.min.js (when SCRIPT_DEBUG is true).
	if ( $compressed ) {
		$scripts->add( 'wp-tinymce', includes_url( 'js/tinymce/' ) . 'wp-tinymce.js', array(), $tinymce_version );
	} else {
		$scripts->add( 'wp-tinymce-root', includes_url( 'js/tinymce/' ) . "tinymce$dev_suffix.js", array(), $tinymce_version );
		$scripts->add( 'wp-tinymce', includes_url( 'js/tinymce/' ) . "plugins/compat3x/plugin$dev_suffix.js", array( 'wp-tinymce-root' ), $tinymce_version );
	}

	$scripts->add( 'wp-tinymce-lists', includes_url( "js/tinymce/plugins/lists/plugin$suffix.js" ), array( 'wp-tinymce' ), $tinymce_version );
}

/**
 * Registers all the WordPress vendor scripts that are in the standardized
 * `js/dist/vendor/` location.
 *
 * For the order of `$scripts->add` see `wp_default_scripts`.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_packages_vendor( &$scripts ) {
	global $wp_locale;

	$suffix = wp_scripts_get_suffix();

	$vendor_scripts = array(
		'react'     => array( 'wp-polyfill' ),
		'react-dom' => array( 'react' ),
		'moment',
		'lodash',
		'wp-polyfill-fetch',
		'wp-polyfill-formdata',
		'wp-polyfill-node-contains',
		'wp-polyfill-element-closest',
		'wp-polyfill',
	);

	$vendor_scripts_versions = array(
		'react'                       => '16.6.3',
		'react-dom'                   => '16.6.3',
		'moment'                      => '2.22.2',
		'lodash'                      => '4.17.11',
		'wp-polyfill-fetch'           => '3.0.0',
		'wp-polyfill-formdata'        => '3.0.12',
		'wp-polyfill-node-contains'   => '3.26.0-0',
		'wp-polyfill-element-closest' => '2.0.2',
		'wp-polyfill'                 => '7.0.0',
	);

	foreach ( $vendor_scripts as $handle => $dependencies ) {
		if ( is_string( $dependencies ) ) {
			$handle       = $dependencies;
			$dependencies = array();
		}

		$path    = "/libs/js/dist/vendor/$handle$suffix.js";
		$version = $vendor_scripts_versions[ $handle ];

		$scripts->add( $handle, $path, $dependencies, $version, 1 );
	}

	$scripts->add( 'wp-polyfill', null, array( 'wp-polyfill' ) );
	did_action( 'init' ) && $scripts->add_inline_script(
		'wp-polyfill',
		wp_get_script_polyfill(
			$scripts,
			array(
				'\'fetch\' in window' => 'wp-polyfill-fetch',
				'document.contains'   => 'wp-polyfill-node-contains',
				'window.FormData && window.FormData.prototype.keys' => 'wp-polyfill-formdata',
				'Element.prototype.matches && Element.prototype.closest' => 'wp-polyfill-element-closest',
			)
		)
	);

	did_action( 'init' ) && $scripts->add_inline_script( 'lodash', 'window.lodash = _.noConflict();' );

	did_action( 'init' ) && $scripts->add_inline_script(
		'moment',
		sprintf(
			"moment.locale( '%s', %s );",
			get_user_locale(),
			wp_json_encode(
				array(
					'months'         => array_values( $wp_locale->month ),
					'monthsShort'    => array_values( $wp_locale->month_abbrev ),
					'weekdays'       => array_values( $wp_locale->weekday ),
					'weekdaysShort'  => array_values( $wp_locale->weekday_abbrev ),
					'week'           => array(
						'dow' => (int) get_option( 'start_of_week', 0 ),
					),
					'longDateFormat' => array(
						'LT'   => get_option( 'time_format', __( 'g:i a', 'default' ) ),
						'LTS'  => null,
						'L'    => null,
						'LL'   => get_option( 'date_format', __( 'F j, Y', 'default' ) ),
						'LLL'  => __( 'F j, Y g:i a', 'default' ),
						'LLLL' => null,
					),
				)
			)
		),
		'after'
	);
}

/**
 * Returns contents of an inline script used in appending polyfill scripts for
 * browsers which fail the provided tests. The provided array is a mapping from
 * a condition to verify feature support to its polyfill script handle.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 * @param array      $tests   Features to detect.
 * @return string Conditional polyfill inline script.
 */
function wp_get_script_polyfill( &$scripts, $tests ) {
	$polyfill = '';
	foreach ( $tests as $test => $handle ) {
		if ( ! array_key_exists( $handle, $scripts->registered ) ) {
			continue;
		}

		$src = $scripts->registered[ $handle ]->src;
		$ver = $scripts->registered[ $handle ]->ver;

		if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $scripts->content_url && 0 === strpos( $src, $scripts->content_url ) ) ) {
			$src = $scripts->base_url . $src;
		}

		if ( ! empty( $ver ) ) {
			$src = add_query_arg( 'ver', $ver, $src );
		}

		/** This filter is documented in libs/class.wp-scripts.php */
		$src = esc_url( apply_filters( 'script_loader_src', $src, $handle ) );

		if ( ! $src ) {
			continue;
		}

		$polyfill .= (
			// Test presence of feature...
			'( ' . $test . ' ) || ' .
			// ...appending polyfill on any failures. Cautious viewers may balk
			// at the `document.write`. Its caveat of synchronous mid-stream
			// blocking write is exactly the behavior we need though.
			'document.write( \'<script src="' .
			$src .
			'"></scr\' + \'ipt>\' );'
		);
	}

	return $polyfill;
}

/**
 * Registers all the WordPress packages scripts that are in the standardized
 * `js/dist/` location.
 *
 * For the order of `$scripts->add` see `wp_default_scripts`.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_packages_scripts( &$scripts ) {
	$suffix = wp_scripts_get_suffix();

	$packages_versions = array(
		'api-fetch'                          => '2.2.8',
		'a11y'                               => '2.0.2',
		'annotations'                        => '1.0.8',
		'autop'                              => '2.0.2',
		'blob'                               => '2.1.0',
		'block-library'                      => '2.2.16',
		'block-serialization-default-parser' => '2.0.5',
		'blocks'                             => '6.0.6',
		'components'                         => '7.0.8',
		'compose'                            => '3.0.1',
		'core-data'                          => '2.0.17',
		'data'                               => '4.2.1',
		'date'                               => '3.0.1',
		'deprecated'                         => '2.0.5',
		'dom'                                => '2.0.8',
		'dom-ready'                          => '2.0.2',
		'edit-post'                          => '3.1.11',
		'editor'                             => '9.0.11',
		'element'                            => '2.1.9',
		'escape-html'                        => '1.0.1',
		'format-library'                     => '1.2.14',
		'hooks'                              => '2.0.5',
		'html-entities'                      => '2.0.4',
		'i18n'                               => '3.1.1',
		'is-shallow-equal'                   => '1.1.5',
		'keycodes'                           => '2.0.6',
		'list-reusable-blocks'               => '1.1.21',
		'notices'                            => '1.1.3',
		'nux'                                => '3.0.9',
		'plugins'                            => '2.0.10',
		'redux-routine'                      => '3.0.4',
		'rich-text'                          => '3.0.7',
		'shortcode'                          => '2.0.2',
		'token-list'                         => '1.1.0',
		'url'                                => '2.3.3',
		'viewport'                           => '2.1.1',
		'wordcount'                          => '2.0.3',
	);

	$packages_dependencies = array(
		'api-fetch'                          => array( 'wp-polyfill', 'wp-hooks', 'wp-i18n', 'wp-url' ),
		'a11y'                               => array( 'wp-dom-ready', 'wp-polyfill' ),
		'annotations'                        => array(
			'wp-data',
			'wp-hooks',
			'wp-i18n',
			'wp-polyfill',
			'wp-rich-text',
		),
		'autop'                              => array( 'wp-polyfill' ),
		'blob'                               => array( 'wp-polyfill' ),
		'blocks'                             => array(
			'wp-autop',
			'wp-blob',
			'wp-block-serialization-default-parser',
			'wp-data',
			'wp-dom',
			'wp-element',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-polyfill',
			'wp-shortcode',
			'lodash',
		),
		'block-library'                      => array(
			'editor',
			'lodash',
			'wp-api-fetch',
			'wp-autop',
			'wp-blob',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-date',
			'wp-editor',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
			'wp-keycodes',
			'wp-polyfill',
			'wp-url',
			'wp-viewport',
			'wp-rich-text',
		),
		'block-serialization-default-parser' => array(),
		'components'                         => array(
			'lodash',
			'moment',
			'wp-a11y',
			'wp-api-fetch',
			'wp-compose',
			'wp-dom',
			'wp-element',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-keycodes',
			'wp-polyfill',
			'wp-rich-text',
			'wp-url',
		),
		'compose'                            => array(
			'lodash',
			'wp-element',
			'wp-is-shallow-equal',
			'wp-polyfill',
		),
		'core-data'                          => array( 'wp-data', 'wp-api-fetch', 'wp-polyfill', 'wp-url', 'lodash' ),
		'data'                               => array(
			'lodash',
			'wp-compose',
			'wp-element',
			'wp-is-shallow-equal',
			'wp-polyfill',
			'wp-redux-routine',
		),
		'date'                               => array( 'moment', 'wp-polyfill' ),
		'deprecated'                         => array( 'wp-polyfill', 'wp-hooks' ),
		'dom'                                => array( 'lodash', 'wp-polyfill', 'wp-tinymce' ),
		'dom-ready'                          => array( 'wp-polyfill' ),
		'edit-post'                          => array(
			'jquery',
			'lodash',
			'postbox',
			'media-models',
			'media-views',
			'wp-a11y',
			'wp-api-fetch',
			'wp-block-library',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-dom-ready',
			'wp-editor',
			'wp-element',
			'wp-embed',
			'wp-i18n',
			'wp-keycodes',
			'wp-notices',
			'wp-nux',
			'wp-plugins',
			'wp-polyfill',
			'wp-url',
			'wp-viewport',
		),
		'editor'                             => array(
			'jquery',
			'lodash',
			'wp-a11y',
			'wp-api-fetch',
			'wp-blob',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-date',
			'wp-deprecated',
			'wp-dom',
			'wp-element',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-keycodes',
			'wp-notices',
			'wp-nux',
			'wp-polyfill',
			'wp-tinymce',
			'wp-token-list',
			'wp-url',
			'wp-viewport',
			'wp-wordcount',
			'wp-rich-text',
		),
		'element'                            => array( 'wp-polyfill', 'react', 'react-dom', 'lodash', 'wp-escape-html' ),
		'escape-html'                        => array( 'wp-polyfill' ),
		'format-library'                     => array(
			'wp-components',
			'wp-dom',
			'wp-editor',
			'wp-element',
			'wp-i18n',
			'wp-keycodes',
			'wp-polyfill',
			'wp-rich-text',
			'wp-url',
		),
		'hooks'                              => array( 'wp-polyfill' ),
		'html-entities'                      => array( 'wp-polyfill' ),
		'i18n'                               => array( 'wp-polyfill' ),
		'is-shallow-equal'                   => array( 'wp-polyfill' ),
		'keycodes'                           => array( 'lodash', 'wp-polyfill', 'wp-i18n' ),
		'list-reusable-blocks'               => array(
			'lodash',
			'wp-api-fetch',
			'wp-components',
			'wp-compose',
			'wp-element',
			'wp-i18n',
			'wp-polyfill',
		),
		'notices'                            => array(
			'lodash',
			'wp-a11y',
			'wp-data',
			'wp-polyfill',
		),
		'nux'                                => array(
			'wp-element',
			'lodash',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-i18n',
			'wp-polyfill',
			'lodash',
		),
		'plugins'                            => array( 'lodash', 'wp-compose', 'wp-element', 'wp-hooks', 'wp-polyfill' ),
		'redux-routine'                      => array( 'wp-polyfill' ),
		'rich-text'                          => array(
			'lodash',
			'wp-data',
			'wp-escape-html',
			'wp-polyfill',
		),
		'shortcode'                          => array( 'wp-polyfill', 'lodash' ),
		'token-list'                         => array( 'lodash', 'wp-polyfill' ),
		'url'                                => array( 'wp-polyfill' ),
		'viewport'                           => array( 'wp-polyfill', 'wp-element', 'wp-data', 'wp-compose', 'lodash' ),
		'wordcount'                          => array( 'wp-polyfill' ),
	);

	$package_translations = array(
		'api-fetch',
		'blocks',
		'block-library',
		'components',
		'edit-post',
		'editor',
		'format-library',
		'keycodes',
		'list-reusable-blocks',
		'nux',
	);

	foreach ( $packages_dependencies as $package => $dependencies ) {
		$handle  = 'wp-' . $package;
		$path    = "/libs/js/dist/$package$suffix.js";
		$version = $packages_versions[ $package ];

		$scripts->add( $handle, $path, $dependencies, $version, 1 );

		if ( in_array( $package, $package_translations, true ) ) {
			$scripts->set_translations( $handle );
		}
	}
}

/**
 * Adds inline scripts required for the WordPress JavaScript packages.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_packages_inline_scripts( &$scripts ) {
	global $wp_locale;

	$scripts->add_inline_script(
		'wp-api-fetch',
		sprintf(
			'wp.apiFetch.use( wp.apiFetch.createNonceMiddleware( "%s" ) );',
			( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' )
		),
		'after'
	);
	$scripts->add_inline_script(
		'wp-api-fetch',
		sprintf(
			'wp.apiFetch.use( wp.apiFetch.createRootURLMiddleware( "%s" ) );',
			esc_url_raw( get_rest_url() )
		),
		'after'
	);

	$scripts->add_inline_script(
		'wp-data',
		implode(
			"\n",
			array(
				'( function() {',
				'	var userId = ' . get_current_user_ID() . ';',
				'	var storageKey = "WP_DATA_USER_" + userId;',
				'	wp.data',
				'		.use( wp.data.plugins.persistence, { storageKey: storageKey } )',
				'		.use( wp.data.plugins.controls );',
				'} )();',
			)
		)
	);

	$scripts->add_inline_script(
		'wp-date',
		sprintf(
			'wp.date.setSettings( %s );',
			wp_json_encode(
				array(
					'l10n'     => array(
						'locale'        => get_user_locale(),
						'months'        => array_values( $wp_locale->month ),
						'monthsShort'   => array_values( $wp_locale->month_abbrev ),
						'weekdays'      => array_values( $wp_locale->weekday ),
						'weekdaysShort' => array_values( $wp_locale->weekday_abbrev ),
						'meridiem'      => (object) $wp_locale->meridiem,
						'relative'      => array(
							/* translators: %s: duration */
							'future' => __( '%s from now' ),
							/* translators: %s: duration */
							'past'   => __( '%s ago' ),
						),
					),
					'formats'  => array(
						/* translators: Time format, see https://secure.php.net/date */
						'time'                => get_option( 'time_format', __( 'g:i a' ) ),
						/* translators: Date format, see https://secure.php.net/date */
						'date'                => get_option( 'date_format', __( 'F j, Y' ) ),
						/* translators: Date/Time format, see https://secure.php.net/date */
						'datetime'            => __( 'F j, Y g:i a' ),
						/* translators: Abbreviated date/time format, see https://secure.php.net/date */
						'datetimeAbbreviated' => __( 'M j, Y g:i a' ),
					),
					'timezone' => array(
						'offset' => get_option( 'gmt_offset', 0 ),
						'string' => get_option( 'timezone_string', 'UTC' ),
					),
				)
			)
		),
		'after'
	);

	// Loading the old editor and its config to ensure the classic block works as expected.
	$scripts->add_inline_script(
		'editor',
		'window.wp.oldEditor = window.wp.editor;',
		'after'
	);
}

/**
 * Adds inline scripts required for the TinyMCE in the block editor.
 *
 * These TinyMCE init settings are used to extend and override the default settings
 * from `_WP_Editors::default_settings()` for the Classic block.
 *
 * @since 5.0.0
 *
 * @global WP_Scripts $wp_scripts
 */
function wp_tinymce_inline_scripts() {
	global $wp_scripts;

	/** This filter is documented in libs/class-wp-editor.php */
	$editor_settings = apply_filters( 'wp_editor_settings', array( 'tinymce' => true ), 'classic-block' );

	$tinymce_plugins = array(
		'charmap',
		'colorpicker',
		'hr',
		'lists',
		'media',
		'paste',
		'tabfocus',
		'textcolor',
		'fullscreen',
		'wordpress',
		'wpautoresize',
		'wpeditimage',
		'wpemoji',
		'wpgallery',
		'wplink',
		'wpdialogs',
		'wptextpattern',
		'wpview',
	);

	/* This filter is documented in libs/class-wp-editor.php */
	$tinymce_plugins = apply_filters( 'tiny_mce_plugins', $tinymce_plugins, 'classic-block' );
	$tinymce_plugins = array_unique( $tinymce_plugins );

	$disable_captions = false;
	// Runs after `tiny_mce_plugins` but before `mce_buttons`.
	/** This filter is documented in acp/includes/media.php */
	if ( apply_filters( 'disable_captions', '' ) ) {
		$disable_captions = true;
	}

	$toolbar1 = array(
		'formatselect',
		'bold',
		'italic',
		'bullist',
		'numlist',
		'blockquote',
		'alignleft',
		'aligncenter',
		'alignright',
		'link',
		'unlink',
		'wp_more',
		'spellchecker',
		'wp_add_media',
		'wp_adv',
	);

	/* This filter is documented in libs/class-wp-editor.php */
	$toolbar1 = apply_filters( 'mce_buttons', $toolbar1, 'classic-block' );

	$toolbar2 = array(
		'strikethrough',
		'hr',
		'forecolor',
		'pastetext',
		'removeformat',
		'charmap',
		'outdent',
		'indent',
		'undo',
		'redo',
		'wp_help',
	);

	/* This filter is documented in libs/class-wp-editor.php */
	$toolbar2 = apply_filters( 'mce_buttons_2', $toolbar2, 'classic-block' );
	/* This filter is documented in libs/class-wp-editor.php */
	$toolbar3 = apply_filters( 'mce_buttons_3', array(), 'classic-block' );
	/* This filter is documented in libs/class-wp-editor.php */
	$toolbar4 = apply_filters( 'mce_buttons_4', array(), 'classic-block' );
	/* This filter is documented in libs/class-wp-editor.php */
	$external_plugins = apply_filters( 'mce_external_plugins', array(), 'classic-block' );

	$tinymce_settings = array(
		'plugins'              => implode( ',', $tinymce_plugins ),
		'toolbar1'             => implode( ',', $toolbar1 ),
		'toolbar2'             => implode( ',', $toolbar2 ),
		'toolbar3'             => implode( ',', $toolbar3 ),
		'toolbar4'             => implode( ',', $toolbar4 ),
		'external_plugins'     => wp_json_encode( $external_plugins ),
		'classic_block_editor' => true,
	);

	if ( $disable_captions ) {
		$tinymce_settings['wpeditimage_disable_captions'] = true;
	}

	if ( ! empty( $editor_settings['tinymce'] ) && is_array( $editor_settings['tinymce'] ) ) {
		array_merge( $tinymce_settings, $editor_settings['tinymce'] );
	}

	/* This filter is documented in libs/class-wp-editor.php */
	$tinymce_settings = apply_filters( 'tiny_mce_before_init', $tinymce_settings, 'classic-block' );

	// Do "by hand" translation from PHP array to js object.
	// Prevents breakage in some custom settings.
	$init_obj = '';
	foreach ( $tinymce_settings as $key => $value ) {
		if ( is_bool( $value ) ) {
			$val       = $value ? 'true' : 'false';
			$init_obj .= $key . ':' . $val . ',';
			continue;
		} elseif ( ! empty( $value ) && is_string( $value ) && (
			( '{' == $value{0} && '}' == $value{strlen( $value ) - 1} ) ||
			( '[' == $value{0} && ']' == $value{strlen( $value ) - 1} ) ||
			preg_match( '/^\(?function ?\(/', $value ) ) ) {
			$init_obj .= $key . ':' . $value . ',';
			continue;
		}
		$init_obj .= $key . ':"' . $value . '",';
	}

	$init_obj = '{' . trim( $init_obj, ' ,' ) . '}';

	$script = 'window.wpEditorL10n = {
		tinymce: {
			baseURL: ' . wp_json_encode( includes_url( 'js/tinymce' ) ) . ',
			suffix: ' . ( SCRIPT_DEBUG ? '""' : '".min"' ) . ',
			settings: ' . $init_obj . ',
		}
	}';

	$wp_scripts->add_inline_script( 'wp-block-library', $script, 'before' );
}

/**
 * Registers all the WordPress packages scripts.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_packages( &$scripts ) {
	wp_default_packages_vendor( $scripts );
	wp_register_tinymce_scripts( $scripts );
	wp_default_packages_scripts( $scripts );

	if ( did_action( 'init' ) ) {
		wp_default_packages_inline_scripts( $scripts );
	}
}

/**
 * Returns the suffix that can be used for the scripts.
 *
 * There are two suffix types, the normal one and the dev suffix.
 *
 * @since 5.0.0
 *
 * @param string $type The type of suffix to retrieve.
 * @return string The script suffix.
 */
function wp_scripts_get_suffix( $type = '' ) {
	static $suffixes;

	if ( $suffixes === null ) {
		include( ABSPATH . WPINC . '/version.php' ); // include an unmodified $wp_version

		$develop_src = false !== strpos( $wp_version, '-src' );

		if ( ! defined( 'SCRIPT_DEBUG' ) ) {
			define( 'SCRIPT_DEBUG', $develop_src );
		}
		$suffix     = SCRIPT_DEBUG ? '' : '.min';
		$dev_suffix = $develop_src ? '' : '.min';

		$suffixes = array(
			'suffix'     => $suffix,
			'dev_suffix' => $dev_suffix,
		);
	}

	if ( $type === 'dev' ) {
		return $suffixes['dev_suffix'];
	}

	return $suffixes['suffix'];
}

/**
 * Register all WordPress scripts.
 *
 * Localizes some of them.
 * args order: `$scripts->add( 'handle', 'url', 'dependencies', 'query-string', 1 );`
 * when last arg === 1 queues the script for the footer
 *
 * @since 2.6.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_scripts( &$scripts ) {
	$suffix     = wp_scripts_get_suffix();
	$dev_suffix = wp_scripts_get_suffix( 'dev' );

	if ( ! $guessurl = site_url() ) {
		$guessed_url = true;
		$guessurl    = wp_guess_url();
	}

	$scripts->base_url        = $guessurl;
	$scripts->content_url     = defined( 'WP_CONTENT_URL' ) ? WP_CONTENT_URL : '';
	$scripts->default_version = get_bloginfo( 'version' );
	$scripts->default_dirs    = array( '/acp/js/', '/libs/js/' );

	$scripts->add( 'utils', "/libs/js/utils$suffix.js" );
	did_action( 'init' ) && $scripts->localize(
		'utils',
		'userSettings',
		array(
			'url'    => (string) SITECOOKIEPATH,
			'uid'    => (string) get_current_user_id(),
			'time'   => (string) time(),
			'secure' => (string) ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) ),
		)
	);

	$scripts->add( 'common', "/acp/js/common$suffix.js", array( 'jquery', 'hoverIntent', 'utils' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'common',
		'commonL10n',
		array(
			'warnDelete'   => __( "You are about to permanently delete these items from your site.\nThis action cannot be undone.\n 'Cancel' to stop, 'OK' to delete." ),
			'dismiss'      => __( 'Dismiss this notice.' ),
			'collapseMenu' => __( 'Collapse Main menu' ),
			'expandMenu'   => __( 'Expand Main menu' ),
		)
	);

	$scripts->add( 'wp-a11y', "/libs/js/wp-a11y$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'sack', "/libs/js/tw-sack$suffix.js", array(), '1.6.1', 1 );

	$scripts->add( 'quicktags', "/libs/js/quicktags$suffix.js", array(), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'quicktags',
		'quicktagsL10n',
		array(
			'closeAllOpenTags'      => __( 'Close all open tags' ),
			'closeTags'             => __( 'close tags' ),
			'enterURL'              => __( 'Enter the URL' ),
			'enterImageURL'         => __( 'Enter the URL of the image' ),
			'enterImageDescription' => __( 'Enter a description of the image' ),
			'textdirection'         => __( 'text direction' ),
			'toggleTextdirection'   => __( 'Toggle Editor Text Direction' ),
			'dfw'                   => __( 'Distraction-free writing mode' ),
			'strong'                => __( 'Bold' ),
			'strongClose'           => __( 'Close bold tag' ),
			'em'                    => __( 'Italic' ),
			'emClose'               => __( 'Close italic tag' ),
			'link'                  => __( 'Insert link' ),
			'blockquote'            => __( 'Blockquote' ),
			'blockquoteClose'       => __( 'Close blockquote tag' ),
			'del'                   => __( 'Deleted text (strikethrough)' ),
			'delClose'              => __( 'Close deleted text tag' ),
			'ins'                   => __( 'Inserted text' ),
			'insClose'              => __( 'Close inserted text tag' ),
			'image'                 => __( 'Insert image' ),
			'ul'                    => __( 'Bulleted list' ),
			'ulClose'               => __( 'Close bulleted list tag' ),
			'ol'                    => __( 'Numbered list' ),
			'olClose'               => __( 'Close numbered list tag' ),
			'li'                    => __( 'List item' ),
			'liClose'               => __( 'Close list item tag' ),
			'code'                  => __( 'Code' ),
			'codeClose'             => __( 'Close code tag' ),
			'more'                  => __( 'Insert Read More tag' ),
		)
	);

	$scripts->add( 'colorpicker', "/libs/js/colorpicker$suffix.js", array( 'prototype' ), '3517m' );

	$scripts->add( 'editor', "/acp/js/editor$suffix.js", array( 'utils', 'jquery' ), false, 1 );

	// Back-compat for old DFW. To-do: remove at the end of 2016.
	$scripts->add( 'wp-fullscreen-stub', "/acp/js/wp-fullscreen-stub$suffix.js", array(), false, 1 );

	$scripts->add( 'wp-ajax-response', "/libs/js/wp-ajax-response$suffix.js", array( 'jquery' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'wp-ajax-response',
		'wpAjax',
		array(
			'noPerm' => __( 'Sorry, you are not allowed to do that.' ),
			'broken' => __( 'Something went wrong.' ),
		)
	);

	$scripts->add( 'wp-api-request', "/libs/js/api-request$suffix.js", array( 'jquery' ), false, 1 );
	// `wpApiSettings` is also used by `wp-api`, which depends on this script.
	did_action( 'init' ) && $scripts->localize(
		'wp-api-request',
		'wpApiSettings',
		array(
			'root'          => esc_url_raw( get_rest_url() ),
			'nonce'         => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ),
			'versionString' => 'wp/v2/',
		)
	);

	$scripts->add( 'wp-pointer', "/libs/js/wp-pointer$suffix.js", array( 'jquery-ui-widget', 'jquery-ui-position' ), '20111129a', 1 );
	did_action( 'init' ) && $scripts->localize(
		'wp-pointer',
		'wpPointerL10n',
		array(
			'dismiss' => __( 'Dismiss' ),
		)
	);

	$scripts->add( 'autosave', "/libs/js/autosave$suffix.js", array( 'heartbeat' ), false, 1 );

	$scripts->add( 'heartbeat', "/libs/js/heartbeat$suffix.js", array( 'jquery', 'wp-hooks' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'heartbeat',
		'heartbeatSettings',
		/**
		 * Filters the Heartbeat settings.
		 *
		 * @since 3.6.0
		 *
		 * @param array $settings Heartbeat settings array.
		 */
		apply_filters( 'heartbeat_settings', array() )
	);

	$scripts->add( 'wp-auth-check', "/libs/js/wp-auth-check$suffix.js", array( 'heartbeat' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'wp-auth-check',
		'authcheckL10n',
		array(
			'beforeunload' => __( 'Your session has expired. You can log in again from this page or go to the login page.' ),

			/**
			 * Filters the authentication check interval.
			 *
			 * @since 3.6.0
			 *
			 * @param int $interval The interval in which to check a user's authentication.
			 *                      Default 3 minutes in seconds, or 180.
			 */
			'interval'     => apply_filters( 'wp_auth_check_interval', 3 * MINUTE_IN_SECONDS ),
		)
	);

	$scripts->add( 'wp-lists', "/libs/js/wp-lists$suffix.js", array( 'wp-ajax-response', 'jquery-color' ), false, 1 );

	// WordPress no longer uses or bundles Prototype or script.aculo.us. These are now pulled from an external source.
	$scripts->add( 'prototype', 'https://ajax.googleapis.com/ajax/libs/prototype/1.7.1.0/prototype.js', array(), '1.7.1' );
	$scripts->add( 'scriptaculous-root', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js', array( 'prototype' ), '1.9.0' );
	$scripts->add( 'scriptaculous-builder', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/builder.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-dragdrop', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/dragdrop.js', array( 'scriptaculous-builder', 'scriptaculous-effects' ), '1.9.0' );
	$scripts->add( 'scriptaculous-effects', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/effects.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-slider', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/slider.js', array( 'scriptaculous-effects' ), '1.9.0' );
	$scripts->add( 'scriptaculous-sound', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/sound.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-controls', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/controls.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous', false, array( 'scriptaculous-dragdrop', 'scriptaculous-slider', 'scriptaculous-controls' ) );

	// not used in core, replaced by Jcrop.js
	$scripts->add( 'cropper', '/libs/js/crop/cropper.js', array( 'scriptaculous-dragdrop' ) );

	// jQuery
	$scripts->add( 'jquery', false, array( 'jquery-core', 'jquery-migrate' ), '1.12.4' );
	$scripts->add( 'jquery-core', '/libs/js/jquery/jquery.js', array(), '1.12.4' );
	$scripts->add( 'jquery-migrate', "/libs/js/jquery/jquery-migrate$suffix.js", array(), '1.4.1' );

	// full jQuery UI
	$scripts->add( 'jquery-ui-core', "/libs/js/jquery/ui/core$dev_suffix.js", array( 'jquery' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-core', "/libs/js/jquery/ui/effect$dev_suffix.js", array( 'jquery' ), '1.11.4', 1 );

	$scripts->add( 'jquery-effects-blind', "/libs/js/jquery/ui/effect-blind$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-bounce', "/libs/js/jquery/ui/effect-bounce$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-clip', "/libs/js/jquery/ui/effect-clip$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-drop', "/libs/js/jquery/ui/effect-drop$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-explode', "/libs/js/jquery/ui/effect-explode$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-fade', "/libs/js/jquery/ui/effect-fade$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-fold', "/libs/js/jquery/ui/effect-fold$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-highlight', "/libs/js/jquery/ui/effect-highlight$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-puff', "/libs/js/jquery/ui/effect-puff$dev_suffix.js", array( 'jquery-effects-core', 'jquery-effects-scale' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-pulsate', "/libs/js/jquery/ui/effect-pulsate$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-scale', "/libs/js/jquery/ui/effect-scale$dev_suffix.js", array( 'jquery-effects-core', 'jquery-effects-size' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-shake', "/libs/js/jquery/ui/effect-shake$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-size', "/libs/js/jquery/ui/effect-size$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-slide', "/libs/js/jquery/ui/effect-slide$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-transfer', "/libs/js/jquery/ui/effect-transfer$dev_suffix.js", array( 'jquery-effects-core' ), '1.11.4', 1 );

	$scripts->add( 'jquery-ui-accordion', "/libs/js/jquery/ui/accordion$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-autocomplete', "/libs/js/jquery/ui/autocomplete$dev_suffix.js", array( 'jquery-ui-menu', 'wp-a11y' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-button', "/libs/js/jquery/ui/button$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-datepicker', "/libs/js/jquery/ui/datepicker$dev_suffix.js", array( 'jquery-ui-core' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-dialog', "/libs/js/jquery/ui/dialog$dev_suffix.js", array( 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-draggable', "/libs/js/jquery/ui/draggable$dev_suffix.js", array( 'jquery-ui-mouse' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-droppable', "/libs/js/jquery/ui/droppable$dev_suffix.js", array( 'jquery-ui-draggable' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-menu', "/libs/js/jquery/ui/menu$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-mouse', "/libs/js/jquery/ui/mouse$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-position', "/libs/js/jquery/ui/position$dev_suffix.js", array( 'jquery' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-progressbar', "/libs/js/jquery/ui/progressbar$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-resizable', "/libs/js/jquery/ui/resizable$dev_suffix.js", array( 'jquery-ui-mouse' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-selectable', "/libs/js/jquery/ui/selectable$dev_suffix.js", array( 'jquery-ui-mouse' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-selectmenu', "/libs/js/jquery/ui/selectmenu$dev_suffix.js", array( 'jquery-ui-menu' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-slider', "/libs/js/jquery/ui/slider$dev_suffix.js", array( 'jquery-ui-mouse' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-sortable', "/libs/js/jquery/ui/sortable$dev_suffix.js", array( 'jquery-ui-mouse' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-spinner', "/libs/js/jquery/ui/spinner$dev_suffix.js", array( 'jquery-ui-button' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-tabs', "/libs/js/jquery/ui/tabs$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-tooltip', "/libs/js/jquery/ui/tooltip$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-widget', "/libs/js/jquery/ui/widget$dev_suffix.js", array( 'jquery' ), '1.11.4', 1 );

	// Strings for 'jquery-ui-autocomplete' live region messages
	did_action( 'init' ) && $scripts->localize(
		'jquery-ui-autocomplete',
		'uiAutocompleteL10n',
		array(
			'noResults'    => __( 'No results found.' ),
			/* translators: Number of results found when using jQuery UI Autocomplete */
			'oneResult'    => __( '1 result found. Use up and down arrow keys to navigate.' ),
			/* translators: %d: Number of results found when using jQuery UI Autocomplete */
			'manyResults'  => __( '%d results found. Use up and down arrow keys to navigate.' ),
			'itemSelected' => __( 'Item selected.' ),
		)
	);

	// deprecated, not used in core, most functionality is included in jQuery 1.3
	$scripts->add( 'jquery-form', "/libs/js/jquery/jquery.form$suffix.js", array( 'jquery' ), '4.2.1', 1 );

	// jQuery plugins
	$scripts->add( 'jquery-color', '/libs/js/jquery/jquery.color.min.js', array( 'jquery' ), '2.1.1', 1 );
	$scripts->add( 'schedule', '/libs/js/jquery/jquery.schedule.js', array( 'jquery' ), '20m', 1 );
	$scripts->add( 'jquery-query', '/libs/js/jquery/jquery.query.js', array( 'jquery' ), '2.1.7', 1 );
	$scripts->add( 'jquery-serialize-object', '/libs/js/jquery/jquery.serialize-object.js', array( 'jquery' ), '0.2', 1 );
	$scripts->add( 'jquery-hotkeys', "/libs/js/jquery/jquery.hotkeys$suffix.js", array( 'jquery' ), '0.0.2m', 1 );
	$scripts->add( 'jquery-table-hotkeys', "/libs/js/jquery/jquery.table-hotkeys$suffix.js", array( 'jquery', 'jquery-hotkeys' ), false, 1 );
	$scripts->add( 'jquery-touch-punch', '/libs/js/jquery/jquery.ui.touch-punch.js', array( 'jquery-ui-widget', 'jquery-ui-mouse' ), '0.2.2', 1 );

	// Not used any more, registered for backwards compatibility.
	$scripts->add( 'suggest', "/libs/js/jquery/suggest$suffix.js", array( 'jquery' ), '1.1-20110113', 1 );

	// Masonry v2 depended on jQuery. v3 does not. The older jquery-masonry handle is a shiv.
	// It sets jQuery as a dependency, as the theme may have been implicitly loading it this way.
	$scripts->add( 'imagesloaded', '/libs/js/imagesloaded.min.js', array(), '3.2.0', 1 );
	$scripts->add( 'masonry', '/libs/js/masonry.min.js', array( 'imagesloaded' ), '3.3.2', 1 );
	$scripts->add( 'jquery-masonry', "/libs/js/jquery/jquery.masonry$dev_suffix.js", array( 'jquery', 'masonry' ), '3.1.2b', 1 );

	$scripts->add( 'thickbox', '/libs/js/thickbox/thickbox.js', array( 'jquery' ), '3.1-20121105', 1 );
	did_action( 'init' ) && $scripts->localize(
		'thickbox',
		'thickboxL10n',
		array(
			'next'             => __( 'Next &gt;' ),
			'prev'             => __( '&lt; Prev' ),
			'image'            => __( 'Image' ),
			'of'               => __( 'of' ),
			'close'            => __( 'Close' ),
			'noiframes'        => __( 'This feature requires inline frames. You have iframes disabled or your browser does not support them.' ),
			'loadingAnimation' => includes_url( 'js/thickbox/loadingAnimation.gif' ),
		)
	);

	$scripts->add( 'jcrop', '/libs/js/jcrop/jquery.Jcrop.min.js', array( 'jquery' ), '0.9.12' );

	$scripts->add( 'swfobject', '/libs/js/swfobject.js', array(), '2.2-20120417' );

	// Error messages for Plupload.
	$uploader_l10n = array(
		'queue_limit_exceeded'      => __( 'You have attempted to queue too many files.' ),
		'file_exceeds_size_limit'   => __( '%s exceeds the maximum upload size for this site.' ),
		'zero_byte_file'            => __( 'This file is empty. Please try another.' ),
		'invalid_filetype'          => __( 'Sorry, this file type is not permitted for security reasons.' ),
		'not_an_image'              => __( 'This file is not an image. Please try another.' ),
		'image_memory_exceeded'     => __( 'Memory exceeded. Please try another smaller file.' ),
		'image_dimensions_exceeded' => __( 'This is larger than the maximum size. Please try another.' ),
		'default_error'             => __( 'An error occurred in the upload. Please try again later.' ),
		'missing_upload_url'        => __( 'There was a configuration error. Please contact the server administrator.' ),
		'upload_limit_exceeded'     => __( 'You may only upload 1 file.' ),
		'http_error'                => __( 'HTTP error.' ),
		'upload_failed'             => __( 'Upload failed.' ),
		/* translators: 1: Opening link tag, 2: Closing link tag */
		'big_upload_failed'         => __( 'Please try uploading this file with the %1$sbrowser uploader%2$s.' ),
		'big_upload_queued'         => __( '%s exceeds the maximum upload size for the multi-file uploader when used in your browser.' ),
		'io_error'                  => __( 'IO error.' ),
		'security_error'            => __( 'Security error.' ),
		'file_cancelled'            => __( 'File canceled.' ),
		'upload_stopped'            => __( 'Upload stopped.' ),
		'dismiss'                   => __( 'Dismiss' ),
		'crunching'                 => __( 'Crunching&hellip;' ),
		'deleted'                   => __( 'moved to the trash.' ),
		'error_uploading'           => __( '&#8220;%s&#8221; has failed to upload.' ),
	);

	$scripts->add( 'moxiejs', "/libs/js/plupload/moxie$suffix.js", array(), '1.3.5' );
	$scripts->add( 'plupload', "/libs/js/plupload/plupload$suffix.js", array( 'moxiejs' ), '2.1.9' );
	// Back compat handles:
	foreach ( array( 'all', 'html5', 'flash', 'silverlight', 'html4' ) as $handle ) {
		$scripts->add( "plupload-$handle", false, array( 'plupload' ), '2.1.1' );
	}

	$scripts->add( 'plupload-handlers', "/libs/js/plupload/handlers$suffix.js", array( 'plupload', 'jquery' ) );
	did_action( 'init' ) && $scripts->localize( 'plupload-handlers', 'pluploadL10n', $uploader_l10n );

	$scripts->add( 'wp-plupload', "/libs/js/plupload/wp-plupload$suffix.js", array( 'plupload', 'jquery', 'json2', 'media-models' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-plupload', 'pluploadL10n', $uploader_l10n );

	// keep 'swfupload' for back-compat.
	$scripts->add( 'swfupload', '/libs/js/swfupload/swfupload.js', array(), '2201-20110113' );
	$scripts->add( 'swfupload-all', false, array( 'swfupload' ), '2201' );
	$scripts->add( 'swfupload-handlers', "/libs/js/swfupload/handlers$suffix.js", array( 'swfupload-all', 'jquery' ), '2201-20110524' );
	did_action( 'init' ) && $scripts->localize( 'swfupload-handlers', 'swfuploadL10n', $uploader_l10n );

	$scripts->add( 'comment-reply', "/libs/js/comment-reply$suffix.js", array(), false, 1 );

	$scripts->add( 'json2', "/libs/js/json2$suffix.js", array(), '2015-05-03' );
	did_action( 'init' ) && $scripts->add_data( 'json2', 'conditional', 'lt IE 8' );

	$scripts->add( 'underscore', "/libs/js/underscore$dev_suffix.js", array(), '1.8.3', 1 );
	$scripts->add( 'backbone', "/libs/js/backbone$dev_suffix.js", array( 'underscore', 'jquery' ), '1.2.3', 1 );

	$scripts->add( 'wp-util', "/libs/js/wp-util$suffix.js", array( 'underscore', 'jquery' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'wp-util',
		'_wpUtilSettings',
		array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php', 'relative' ),
			),
		)
	);

	$scripts->add( 'wp-sanitize', "/libs/js/wp-sanitize$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'wp-backbone', "/libs/js/wp-backbone$suffix.js", array( 'backbone', 'wp-util' ), false, 1 );

	$scripts->add( 'revisions', "/acp/js/revisions$suffix.js", array( 'wp-backbone', 'jquery-ui-slider', 'hoverIntent' ), false, 1 );

	$scripts->add( 'imgareaselect', "/libs/js/imgareaselect/jquery.imgareaselect$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'mediaelement', false, array( 'jquery', 'mediaelement-core', 'mediaelement-migrate' ), '4.2.6-78496d1' );
	$scripts->add( 'mediaelement-core', "/libs/js/mediaelement/mediaelement-and-player$suffix.js", array(), '4.2.6-78496d1', 1 );
	$scripts->add( 'mediaelement-migrate', "/libs/js/mediaelement/mediaelement-migrate$suffix.js", array(), false, 1 );

	did_action( 'init' ) && $scripts->add_inline_script(
		'mediaelement-core',
		sprintf(
			'var mejsL10n = %s;',
			wp_json_encode(
				array(
					'language' => strtolower( strtok( determine_locale(), '_-' ) ),
					'strings'  => array(
						'mejs.install-flash'       => __( 'You are using a browser that does not have Flash player enabled or installed. Please turn on your Flash player plugin or download the latest version from https://get.adobe.com/flashplayer/' ),
						'mejs.fullscreen-off'      => __( 'Turn off Fullscreen' ),
						'mejs.fullscreen-on'       => __( 'Go Fullscreen' ),
						'mejs.download-video'      => __( 'Download Video' ),
						'mejs.fullscreen'          => __( 'Fullscreen' ),
						'mejs.time-jump-forward'   => array( __( 'Jump forward 1 second' ), __( 'Jump forward %1 seconds' ) ),
						'mejs.loop'                => __( 'Toggle Loop' ),
						'mejs.play'                => __( 'Play' ),
						'mejs.pause'               => __( 'Pause' ),
						'mejs.close'               => __( 'Close' ),
						'mejs.time-slider'         => __( 'Time Slider' ),
						'mejs.time-help-text'      => __( 'Use Left/Right Arrow keys to advance one second, Up/Down arrows to advance ten seconds.' ),
						'mejs.time-skip-back'      => array( __( 'Skip back 1 second' ), __( 'Skip back %1 seconds' ) ),
						'mejs.captions-subtitles'  => __( 'Captions/Subtitles' ),
						'mejs.captions-chapters'   => __( 'Chapters' ),
						'mejs.none'                => __( 'None' ),
						'mejs.mute-toggle'         => __( 'Mute Toggle' ),
						'mejs.volume-help-text'    => __( 'Use Up/Down Arrow keys to increase or decrease volume.' ),
						'mejs.unmute'              => __( 'Unmute' ),
						'mejs.mute'                => __( 'Mute' ),
						'mejs.volume-slider'       => __( 'Volume Slider' ),
						'mejs.video-player'        => __( 'Video Player' ),
						'mejs.audio-player'        => __( 'Audio Player' ),
						'mejs.ad-skip'             => __( 'Skip ad' ),
						'mejs.ad-skip-info'        => array( __( 'Skip in 1 second' ), __( 'Skip in %1 seconds' ) ),
						'mejs.source-chooser'      => __( 'Source Chooser' ),
						'mejs.stop'                => __( 'Stop' ),
						'mejs.speed-rate'          => __( 'Speed Rate' ),
						'mejs.live-broadcast'      => __( 'Live Broadcast' ),
						'mejs.afrikaans'           => __( 'Afrikaans' ),
						'mejs.albanian'            => __( 'Albanian' ),
						'mejs.arabic'              => __( 'Arabic' ),
						'mejs.belarusian'          => __( 'Belarusian' ),
						'mejs.bulgarian'           => __( 'Bulgarian' ),
						'mejs.catalan'             => __( 'Catalan' ),
						'mejs.chinese'             => __( 'Chinese' ),
						'mejs.chinese-simplified'  => __( 'Chinese (Simplified)' ),
						'mejs.chinese-traditional' => __( 'Chinese (Traditional)' ),
						'mejs.croatian'            => __( 'Croatian' ),
						'mejs.czech'               => __( 'Czech' ),
						'mejs.danish'              => __( 'Danish' ),
						'mejs.dutch'               => __( 'Dutch' ),
						'mejs.english'             => __( 'English' ),
						'mejs.estonian'            => __( 'Estonian' ),
						'mejs.filipino'            => __( 'Filipino' ),
						'mejs.finnish'             => __( 'Finnish' ),
						'mejs.french'              => __( 'French' ),
						'mejs.galician'            => __( 'Galician' ),
						'mejs.german'              => __( 'German' ),
						'mejs.greek'               => __( 'Greek' ),
						'mejs.haitian-creole'      => __( 'Haitian Creole' ),
						'mejs.hebrew'              => __( 'Hebrew' ),
						'mejs.hindi'               => __( 'Hindi' ),
						'mejs.hungarian'           => __( 'Hungarian' ),
						'mejs.icelandic'           => __( 'Icelandic' ),
						'mejs.indonesian'          => __( 'Indonesian' ),
						'mejs.irish'               => __( 'Irish' ),
						'mejs.italian'             => __( 'Italian' ),
						'mejs.japanese'            => __( 'Japanese' ),
						'mejs.korean'              => __( 'Korean' ),
						'mejs.latvian'             => __( 'Latvian' ),
						'mejs.lithuanian'          => __( 'Lithuanian' ),
						'mejs.macedonian'          => __( 'Macedonian' ),
						'mejs.malay'               => __( 'Malay' ),
						'mejs.maltese'             => __( 'Maltese' ),
						'mejs.norwegian'           => __( 'Norwegian' ),
						'mejs.persian'             => __( 'Persian' ),
						'mejs.polish'              => __( 'Polish' ),
						'mejs.portuguese'          => __( 'Portuguese' ),
						'mejs.romanian'            => __( 'Romanian' ),
						'mejs.russian'             => __( 'Russian' ),
						'mejs.serbian'             => __( 'Serbian' ),
						'mejs.slovak'              => __( 'Slovak' ),
						'mejs.slovenian'           => __( 'Slovenian' ),
						'mejs.spanish'             => __( 'Spanish' ),
						'mejs.swahili'             => __( 'Swahili' ),
						'mejs.swedish'             => __( 'Swedish' ),
						'mejs.tagalog'             => __( 'Tagalog' ),
						'mejs.thai'                => __( 'Thai' ),
						'mejs.turkish'             => __( 'Turkish' ),
						'mejs.ukrainian'           => __( 'Ukrainian' ),
						'mejs.vietnamese'          => __( 'Vietnamese' ),
						'mejs.welsh'               => __( 'Welsh' ),
						'mejs.yiddish'             => __( 'Yiddish' ),
					),
				)
			)
		),
		'before'
	);

	$scripts->add( 'mediaelement-vimeo', '/libs/js/mediaelement/renderers/vimeo.min.js', array( 'mediaelement' ), '4.2.6-78496d1', 1 );
	$scripts->add( 'wp-mediaelement', "/libs/js/mediaelement/wp-mediaelement$suffix.js", array( 'mediaelement' ), false, 1 );
	$mejs_settings = array(
		'pluginPath'  => includes_url( 'js/mediaelement/', 'relative' ),
		'classPrefix' => 'mejs-',
		'stretching'  => 'responsive',
	);
	did_action( 'init' ) && $scripts->localize(
		'mediaelement',
		'_wpmejsSettings',
		/**
		 * Filters the MediaElement configuration settings.
		 *
		 * @since 4.4.0
		 *
		 * @param array $mejs_settings MediaElement settings array.
		 */
		apply_filters( 'mejs_settings', $mejs_settings )
	);

	$scripts->add( 'wp-codemirror', '/libs/js/codemirror/codemirror.min.js', array(), '5.29.1-alpha-ee20357' );
	$scripts->add( 'csslint', '/libs/js/codemirror/csslint.js', array(), '1.0.5' );
	$scripts->add( 'esprima', '/libs/js/codemirror/esprima.js', array(), '4.0.0' );
	$scripts->add( 'jshint', '/libs/js/codemirror/fakejshint.js', array( 'esprima' ), '2.9.5' );
	$scripts->add( 'jsonlint', '/libs/js/codemirror/jsonlint.js', array(), '1.6.2' );
	$scripts->add( 'htmlhint', '/libs/js/codemirror/htmlhint.js', array(), '0.9.14-xwp' );
	$scripts->add( 'htmlhint-kses', '/libs/js/codemirror/htmlhint-kses.js', array( 'htmlhint' ) );
	$scripts->add( 'code-editor', "/acp/js/code-editor$suffix.js", array( 'jquery', 'wp-codemirror', 'underscore' ) );
	$scripts->add( 'wp-theme-plugin-editor', "/acp/js/theme-plugin-editor$suffix.js", array( 'wp-util', 'wp-sanitize', 'jquery', 'jquery-ui-core', 'wp-a11y', 'underscore' ) );
	did_action( 'init' ) && $scripts->add_inline_script(
		'wp-theme-plugin-editor',
		sprintf(
			'wp.themePluginEditor.l10n = %s;',
			wp_json_encode(
				array(
					'saveAlert' => __( 'The changes you made will be lost if you navigate away from this page.' ),
					'saveError' => __( 'Something went wrong. Your change may not have been saved. Please try again. There is also a chance that you may need to manually fix and upload the file over FTP.' ),
					'lintError' => array(
						/* translators: %d: error count */
						'singular' => _n( 'There is %d error which must be fixed before you can update this file.', 'There are %d errors which must be fixed before you can update this file.', 1 ),
						/* translators: %d: error count */
						'plural'   => _n( 'There is %d error which must be fixed before you can update this file.', 'There are %d errors which must be fixed before you can update this file.', 2 ), // @todo This is lacking, as some languages have a dedicated dual form. For proper handling of plurals in JS, see #20491.
					),
				)
			)
		)
	);

	$scripts->add( 'wp-playlist', "/libs/js/mediaelement/wp-playlist$suffix.js", array( 'wp-util', 'backbone', 'mediaelement' ), false, 1 );

	$scripts->add( 'zxcvbn-async', "/libs/js/zxcvbn-async$suffix.js", array(), '1.0' );
	did_action( 'init' ) && $scripts->localize(
		'zxcvbn-async',
		'_zxcvbnSettings',
		array(
			'src' => empty( $guessed_url ) ? includes_url( '/js/zxcvbn.min.js' ) : $scripts->base_url . '/libs/js/zxcvbn.min.js',
		)
	);

	$scripts->add( 'password-strength-meter', "/acp/js/password-strength-meter$suffix.js", array( 'jquery', 'zxcvbn-async' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'password-strength-meter',
		'pwsL10n',
		array(
			'unknown'  => _x( 'Password strength unknown', 'password strength' ),
			'short'    => _x( 'Very weak', 'password strength' ),
			'bad'      => _x( 'Weak', 'password strength' ),
			'good'     => _x( 'Medium', 'password strength' ),
			'strong'   => _x( 'Strong', 'password strength' ),
			'mismatch' => _x( 'Mismatch', 'password mismatch' ),
		)
	);

	$scripts->add( 'user-profile', "/acp/js/user-profile$suffix.js", array( 'jquery', 'password-strength-meter', 'wp-util' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'user-profile',
		'userProfileL10n',
		array(
			'warn'     => __( 'Your new password has not been saved.' ),
			'warnWeak' => __( 'Confirm use of weak password' ),
			'show'     => __( 'Show' ),
			'hide'     => __( 'Hide' ),
			'cancel'   => __( 'Cancel' ),
			'ariaShow' => esc_attr__( 'Show password' ),
			'ariaHide' => esc_attr__( 'Hide password' ),
		)
	);

	$scripts->add( 'language-chooser', "/acp/js/language-chooser$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'user-suggest', "/acp/js/user-suggest$suffix.js", array( 'jquery-ui-autocomplete' ), false, 1 );

	$scripts->add( 'admin-bar', "/libs/js/admin-bar$suffix.js", array(), false, 1 );

	$scripts->add( 'wplink', "/libs/js/wplink$suffix.js", array( 'jquery', 'wp-a11y' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'wplink',
		'wpLinkL10n',
		array(
			'title'          => __( 'Insert/edit link' ),
			'update'         => __( 'Update' ),
			'save'           => __( 'Add Link' ),
			'noTitle'        => __( '(no title)' ),
			'noMatchesFound' => __( 'No results found.' ),
			'linkSelected'   => __( 'Link selected.' ),
			'linkInserted'   => __( 'Link inserted.' ),
		)
	);

	$scripts->add( 'wpdialogs', "/libs/js/wpdialog$suffix.js", array( 'jquery-ui-dialog' ), false, 1 );

	$scripts->add( 'word-count', "/acp/js/word-count$suffix.js", array(), false, 1 );

	$scripts->add( 'media-upload', "/acp/js/media-upload$suffix.js", array( 'thickbox', 'shortcode' ), false, 1 );

	$scripts->add( 'hoverIntent', "/libs/js/hoverIntent$suffix.js", array( 'jquery' ), '1.8.1', 1 );

	$scripts->add( 'customize-base', "/libs/js/customize-base$suffix.js", array( 'jquery', 'json2', 'underscore' ), false, 1 );
	$scripts->add( 'customize-loader', "/libs/js/customize-loader$suffix.js", array( 'customize-base' ), false, 1 );
	$scripts->add( 'customize-preview', "/libs/js/customize-preview$suffix.js", array( 'wp-a11y', 'customize-base' ), false, 1 );
	$scripts->add( 'customize-models', '/libs/js/customize-models.js', array( 'underscore', 'backbone' ), false, 1 );
	$scripts->add( 'customize-views', '/libs/js/customize-views.js', array( 'jquery', 'underscore', 'imgareaselect', 'customize-models', 'media-editor', 'media-views' ), false, 1 );
	$scripts->add( 'customize-controls', "/acp/js/customize-controls$suffix.js", array( 'customize-base', 'wp-a11y', 'wp-util', 'jquery-ui-core' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'customize-controls',
		'_wpCustomizeControlsL10n',
		array(
			'activate'                => __( 'Activate &amp; Publish' ),
			'save'                    => __( 'Save &amp; Publish' ), // @todo Remove as not required.
			'publish'                 => __( 'Publish' ),
			'published'               => __( 'Published' ),
			'saveDraft'               => __( 'Save Draft' ),
			'draftSaved'              => __( 'Draft Saved' ),
			'updating'                => __( 'Updating' ),
			'schedule'                => _x( 'Schedule', 'customizer changeset action/button label' ),
			'scheduled'               => _x( 'Scheduled', 'customizer changeset status' ),
			'invalid'                 => __( 'Invalid' ),
			'saveBeforeShare'         => __( 'Please save your changes in order to share the preview.' ),
			'futureDateError'         => __( 'You must supply a future date to schedule.' ),
			'saveAlert'               => __( 'The changes you made will be lost if you navigate away from this page.' ),
			'saved'                   => __( 'Saved' ),
			'cancel'                  => __( 'Cancel' ),
			'close'                   => __( 'Close' ),
			'action'                  => __( 'Action' ),
			'discardChanges'          => __( 'Discard changes' ),
			'cheatin'                 => __( 'Something went wrong.' ),
			'notAllowedHeading'       => __( 'You need a higher level of permission.' ),
			'notAllowed'              => __( 'Sorry, you are not allowed to customize this site.' ),
			'previewIframeTitle'      => __( 'Site Preview' ),
			'loginIframeTitle'        => __( 'Session expired' ),
			'collapseSidebar'         => _x( 'Hide Controls', 'label for hide controls button without length constraints' ),
			'expandSidebar'           => _x( 'Show Controls', 'label for hide controls button without length constraints' ),
			'untitledBlogName'        => __( '(Untitled)' ),
			'unknownRequestFail'      => __( 'Looks like something&#8217;s gone wrong. Wait a couple seconds, and then try again.' ),
			'themeDownloading'        => __( 'Downloading your new theme&hellip;' ),
			'themePreviewWait'        => __( 'Setting up your live preview. This may take a bit.' ),
			'revertingChanges'        => __( 'Reverting unpublished changes&hellip;' ),
			'trashConfirm'            => __( 'Are you sure you&#8217;d like to discard your unpublished changes?' ),
			/* translators: %s: Display name of the user who has taken over the changeset in customizer. */
			'takenOverMessage'        => __( '%s has taken over and is currently customizing.' ),
			/* translators: %s: URL to the Customizer to load the autosaved version */
			'autosaveNotice'          => __( 'There is a more recent autosave of your changes than the one you are previewing. <a href="%s">Restore the autosave</a>' ),
			'videoHeaderNotice'       => __( 'This theme doesn&#8217;t support video headers on this page. Navigate to the front page or another page that supports video headers.' ),
			// Used for overriding the file types allowed in plupload.
			'allowedFiles'            => __( 'Allowed Files' ),
			'customCssError'          => array(
				/* translators: %d: error count */
				'singular' => _n( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 1 ),
				/* translators: %d: error count */
				'plural'   => _n( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 2 ), // @todo This is lacking, as some languages have a dedicated dual form. For proper handling of plurals in JS, see #20491.
			),
			'pageOnFrontError'        => __( 'Homepage and posts page must be different.' ),
			'saveBlockedError'        => array(
				/* translators: %s: number of invalid settings */
				'singular' => _n( 'Unable to save due to %s invalid setting.', 'Unable to save due to %s invalid settings.', 1 ),
				/* translators: %s: number of invalid settings */
				'plural'   => _n( 'Unable to save due to %s invalid setting.', 'Unable to save due to %s invalid settings.', 2 ), // @todo This is lacking, as some languages have a dedicated dual form. For proper handling of plurals in JS, see #20491.
			),
			'scheduleDescription'     => __( 'Schedule your customization changes to publish ("go live") at a future date.' ),
			'themePreviewUnavailable' => __( 'Sorry, you can&#8217;t preview new themes when you have changes scheduled or saved as a draft. Please publish your changes, or wait until they publish to preview new themes.' ),
			'themeInstallUnavailable' => sprintf(
				/* translators: %s: URL to Add Themes admin screen */
				   __( 'You won&#8217;t be able to install new themes from here yet since your install requires SFTP credentials. For now, please <a href="%s">add themes in the admin</a>.' ),
				esc_url( admin_url( 'theme-install.php' ) )
			),
			'publishSettings'         => __( 'Publish Settings' ),
			'invalidDate'             => __( 'Invalid date.' ),
			'invalidValue'            => __( 'Invalid value.' ),
		)
	);
	$scripts->add( 'customize-selective-refresh', "/libs/js/customize-selective-refresh$suffix.js", array( 'jquery', 'wp-util', 'customize-preview' ), false, 1 );

	$scripts->add( 'customize-widgets', "/acp/js/customize-widgets$suffix.js", array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-droppable', 'wp-backbone', 'customize-controls' ), false, 1 );
	$scripts->add( 'customize-preview-widgets', "/libs/js/customize-preview-widgets$suffix.js", array( 'jquery', 'wp-util', 'customize-preview', 'customize-selective-refresh' ), false, 1 );

	$scripts->add( 'customize-nav-menus', "/acp/js/customize-nav-menus$suffix.js", array( 'jquery', 'wp-backbone', 'customize-controls', 'accordion', 'nav-menu' ), false, 1 );
	$scripts->add( 'customize-preview-nav-menus', "/libs/js/customize-preview-nav-menus$suffix.js", array( 'jquery', 'wp-util', 'customize-preview', 'customize-selective-refresh' ), false, 1 );

	$scripts->add( 'wp-custom-header', "/libs/js/wp-custom-header$suffix.js", array( 'wp-a11y' ), false, 1 );

	$scripts->add( 'accordion', "/acp/js/accordion$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'shortcode', "/libs/js/shortcode$suffix.js", array( 'underscore' ), false, 1 );
	$scripts->add( 'media-models', "/libs/js/media-models$suffix.js", array( 'wp-backbone' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'media-models',
		'_wpMediaModelsL10n',
		array(
			'settings' => array(
				'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
				'post'    => array( 'id' => 0 ),
			),
		)
	);

	$scripts->add( 'wp-embed', "/libs/js/wp-embed$suffix.js" );

	// To enqueue media-views or media-editor, call wp_enqueue_media().
	// Both rely on numerous settings, styles, and templates to operate correctly.
	$scripts->add( 'media-views', "/libs/js/media-views$suffix.js", array( 'utils', 'media-models', 'wp-plupload', 'jquery-ui-sortable', 'wp-mediaelement', 'wp-api-request' ), false, 1 );
	$scripts->add( 'media-editor', "/libs/js/media-editor$suffix.js", array( 'shortcode', 'media-views' ), false, 1 );
	$scripts->add( 'media-audiovideo', "/libs/js/media-audiovideo$suffix.js", array( 'media-editor' ), false, 1 );
	$scripts->add( 'mce-view', "/libs/js/mce-view$suffix.js", array( 'shortcode', 'jquery', 'media-views', 'media-audiovideo' ), false, 1 );

	$scripts->add( 'wp-api', "/libs/js/wp-api$suffix.js", array( 'jquery', 'backbone', 'underscore', 'wp-api-request' ), false, 1 );

	if ( is_admin() ) {
		$scripts->add( 'admin-tags', "/acp/js/tags$suffix.js", array( 'jquery', 'wp-ajax-response' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'admin-tags',
			'tagsl10n',
			array(
				'noPerm' => __( 'Sorry, you are not allowed to do that.' ),
				'broken' => __( 'Something went wrong.' ),
			)
		);

		$scripts->add( 'admin-comments', "/acp/js/edit-comments$suffix.js", array( 'wp-lists', 'quicktags', 'jquery-query' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'admin-comments',
			'adminCommentsL10n',
			array(
				'hotkeys_highlight_first' => isset( $_GET['hotkeys_highlight_first'] ),
				'hotkeys_highlight_last'  => isset( $_GET['hotkeys_highlight_last'] ),
				'replyApprove'            => __( 'Approve and Reply' ),
				'reply'                   => __( 'Reply' ),
				'warnQuickEdit'           => __( "Are you sure you want to edit this comment?\nThe changes you made will be lost." ),
				'warnCommentChanges'      => __( "Are you sure you want to do this?\nThe comment changes you made will be lost." ),
				'docTitleComments'        => __( 'Comments' ),
				/* translators: %s: comments count */
				'docTitleCommentsCount'   => __( 'Comments (%s)' ),
			)
		);

		$scripts->add( 'xfn', "/acp/js/xfn$suffix.js", array( 'jquery' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'xfn',
			'privacyToolsL10n',
			array(
				'noDataFound'     => __( 'No personal data was found for this user.' ),
				'foundAndRemoved' => __( 'All of the personal data found for this user was erased.' ),
				'noneRemoved'     => __( 'Personal data was found for this user but was not erased.' ),
				'someNotRemoved'  => __( 'Personal data was found for this user but some of the personal data found was not erased.' ),
				'removalError'    => __( 'An error occurred while attempting to find and erase personal data.' ),
				'noExportFile'    => __( 'No personal data export file was generated.' ),
				'exportError'     => __( 'An error occurred while attempting to export personal data.' ),
			)
		);

		$scripts->add( 'postbox', "/acp/js/postbox$suffix.js", array( 'jquery-ui-sortable' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'postbox',
			'postBoxL10n',
			array(
				'postBoxEmptyString' => __( 'Drag boxes here' ),
			)
		);

		$scripts->add( 'tags-box', "/acp/js/tags-box$suffix.js", array( 'jquery', 'tags-suggest' ), false, 1 );

		$scripts->add( 'tags-suggest', "/acp/js/tags-suggest$suffix.js", array( 'jquery-ui-autocomplete', 'wp-a11y' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'tags-suggest',
			'tagsSuggestL10n',
			array(
				'tagDelimiter' => _x( ',', 'tag delimiter' ),
				'removeTerm'   => __( 'Remove term:' ),
				'termSelected' => __( 'Term selected.' ),
				'termAdded'    => __( 'Term added.' ),
				'termRemoved'  => __( 'Term removed.' ),
			)
		);

		$scripts->add( 'post', "/acp/js/post$suffix.js", array( 'suggest', 'wp-lists', 'postbox', 'tags-box', 'underscore', 'word-count', 'wp-a11y' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'post',
			'postL10n',
			array(
				'ok'                 => __( 'OK' ),
				'cancel'             => __( 'Cancel' ),
				'publishOn'          => __( 'Publish on:' ),
				'publishOnFuture'    => __( 'Schedule for:' ),
				'publishOnPast'      => __( 'Published on:' ),
				/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
				'dateFormat'         => __( '%1$s %2$s, %3$s @ %4$s:%5$s' ),
				'showcomm'           => __( 'Show more comments' ),
				'endcomm'            => __( 'No more comments found.' ),
				'publish'            => __( 'Publish' ),
				'schedule'           => _x( 'Schedule', 'post action/button label' ),
				'update'             => __( 'Update' ),
				'savePending'        => __( 'Save as Pending' ),
				'saveDraft'          => __( 'Save Draft' ),
				'private'            => __( 'Private' ),
				'public'             => __( 'Public' ),
				'publicSticky'       => __( 'Public, Sticky' ),
				'password'           => __( 'Password Protected' ),
				'privatelyPublished' => __( 'Privately Published' ),
				'published'          => __( 'Published' ),
				'saveAlert'          => __( 'The changes you made will be lost if you navigate away from this page.' ),
				'savingText'         => __( 'Saving Draft&#8230;' ),
				'permalinkSaved'     => __( 'Permalink saved' ),
			)
		);

		$scripts->add( 'editor-expand', "/acp/js/editor-expand$suffix.js", array( 'jquery', 'underscore' ), false, 1 );

		$scripts->add( 'link', "/acp/js/link$suffix.js", array( 'wp-lists', 'postbox' ), false, 1 );

		$scripts->add( 'comment', "/acp/js/comment$suffix.js", array( 'jquery', 'postbox' ) );
		$scripts->add_data( 'comment', 'group', 1 );
		did_action( 'init' ) && $scripts->localize(
			'comment',
			'commentL10n',
			array(
				'submittedOn' => __( 'Submitted on:' ),
				/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
				'dateFormat'  => __( '%1$s %2$s, %3$s @ %4$s:%5$s' ),
			)
		);

		$scripts->add( 'admin-gallery', "/acp/js/gallery$suffix.js", array( 'jquery-ui-sortable' ) );

		$scripts->add( 'admin-widgets', "/acp/js/widgets$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ), false, 1 );
		did_action( 'init' ) && $scripts->add_inline_script(
			'admin-widgets',
			sprintf(
				'wpWidgets.l10n = %s;',
				wp_json_encode(
					array(
						'save'      => __( 'Save' ),
						'saved'     => __( 'Saved' ),
						'saveAlert' => __( 'The changes you made will be lost if you navigate away from this page.' ),
					)
				)
			)
		);

		$scripts->add( 'media-widgets', "/acp/js/widgets/media-widgets$suffix.js", array( 'jquery', 'media-models', 'media-views', 'wp-api-request' ) );
		$scripts->add_inline_script( 'media-widgets', 'wp.mediaWidgets.init();', 'after' );

		$scripts->add( 'media-audio-widget', "/acp/js/widgets/media-audio-widget$suffix.js", array( 'media-widgets', 'media-audiovideo' ) );
		$scripts->add( 'media-image-widget', "/acp/js/widgets/media-image-widget$suffix.js", array( 'media-widgets' ) );
		$scripts->add( 'media-gallery-widget', "/acp/js/widgets/media-gallery-widget$suffix.js", array( 'media-widgets' ) );
		$scripts->add( 'media-video-widget', "/acp/js/widgets/media-video-widget$suffix.js", array( 'media-widgets', 'media-audiovideo', 'wp-api-request' ) );
		$scripts->add( 'text-widgets', "/acp/js/widgets/text-widgets$suffix.js", array( 'jquery', 'backbone', 'editor', 'wp-util', 'wp-a11y' ) );
		$scripts->add( 'custom-html-widgets', "/acp/js/widgets/custom-html-widgets$suffix.js", array( 'jquery', 'backbone', 'wp-util', 'jquery-ui-core', 'wp-a11y' ) );

		$scripts->add( 'theme', "/acp/js/theme$suffix.js", array( 'wp-backbone', 'wp-a11y', 'customize-base' ), false, 1 );

		$scripts->add( 'inline-edit-post', "/acp/js/inline-edit-post$suffix.js", array( 'jquery', 'tags-suggest', 'wp-a11y' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'inline-edit-post',
			'inlineEditL10n',
			array(
				'error'      => __( 'Error while saving the changes.' ),
				'ntdeltitle' => __( 'Remove From Bulk Edit' ),
				'notitle'    => __( '(no title)' ),
				'comma'      => trim( _x( ',', 'tag delimiter' ) ),
				'saved'      => __( 'Changes saved.' ),
			)
		);

		$scripts->add( 'inline-edit-tax', "/acp/js/inline-edit-tax$suffix.js", array( 'jquery', 'wp-a11y' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'inline-edit-tax',
			'inlineEditL10n',
			array(
				'error' => __( 'Error while saving the changes.' ),
				'saved' => __( 'Changes saved.' ),
			)
		);

		$scripts->add( 'plugin-install', "/acp/js/plugin-install$suffix.js", array( 'jquery', 'jquery-ui-core', 'thickbox' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'plugin-install',
			'plugininstallL10n',
			array(
				'plugin_information' => __( 'Plugin:' ),
				'plugin_modal_label' => __( 'Plugin details' ),
				'ays'                => __( 'Are you sure you want to install this plugin?' ),
			)
		);

		$scripts->add( 'updates', "/acp/js/updates$suffix.js", array( 'jquery', 'wp-util', 'wp-a11y' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'updates',
			'_wpUpdatesSettings',
			array(
				'ajax_nonce' => wp_create_nonce( 'updates' ),
				'l10n'       => array(
					/* translators: %s: Search string */
					'searchResults'            => __( 'Search results for &#8220;%s&#8221;' ),
					'searchResultsLabel'       => __( 'Search Results' ),
					'noPlugins'                => __( 'You do not appear to have any plugins available at this time.' ),
					'noItemsSelected'          => __( 'Please select at least one item to perform this action on.' ),
					'updating'                 => __( 'Updating...' ), // No ellipsis.
					'pluginUpdated'            => _x( 'Updated!', 'plugin' ),
					'themeUpdated'             => _x( 'Updated!', 'theme' ),
					'update'                   => __( 'Update' ),
					'updateNow'                => __( 'Update Now' ),
					/* translators: %s: Plugin name and version */
					'pluginUpdateNowLabel'     => _x( 'Update %s now', 'plugin' ),
					'updateFailedShort'        => __( 'Update Failed!' ),
					/* translators: %s: Error string for a failed update */
					'updateFailed'             => __( 'Update Failed: %s' ),
					/* translators: %s: Plugin name and version */
					'pluginUpdatingLabel'      => _x( 'Updating %s...', 'plugin' ), // No ellipsis.
					/* translators: %s: Plugin name and version */
					'pluginUpdatedLabel'       => _x( '%s updated!', 'plugin' ),
					/* translators: %s: Plugin name and version */
					'pluginUpdateFailedLabel'  => _x( '%s update failed', 'plugin' ),
					/* translators: Accessibility text */
					'updatingMsg'              => __( 'Updating... please wait.' ), // No ellipsis.
					/* translators: Accessibility text */
					'updatedMsg'               => __( 'Update completed successfully.' ),
					/* translators: Accessibility text */
					'updateCancel'             => __( 'Update canceled.' ),
					'beforeunload'             => __( 'Updates may not complete if you navigate away from this page.' ),
					'installNow'               => __( 'Install Now' ),
					/* translators: %s: Plugin name */
					'pluginInstallNowLabel'    => _x( 'Install %s now', 'plugin' ),
					'installing'               => __( 'Installing...' ),
					'pluginInstalled'          => _x( 'Installed!', 'plugin' ),
					'themeInstalled'           => _x( 'Installed!', 'theme' ),
					'installFailedShort'       => __( 'Installation Failed!' ),
					/* translators: %s: Error string for a failed installation */
					'installFailed'            => __( 'Installation failed: %s' ),
					/* translators: %s: Plugin name and version */
					'pluginInstallingLabel'    => _x( 'Installing %s...', 'plugin' ), // no ellipsis
					/* translators: %s: Theme name and version */
					'themeInstallingLabel'     => _x( 'Installing %s...', 'theme' ), // no ellipsis
					/* translators: %s: Plugin name and version */
					'pluginInstalledLabel'     => _x( '%s installed!', 'plugin' ),
					/* translators: %s: Theme name and version */
					'themeInstalledLabel'      => _x( '%s installed!', 'theme' ),
					/* translators: %s: Plugin name and version */
					'pluginInstallFailedLabel' => _x( '%s installation failed', 'plugin' ),
					/* translators: %s: Theme name and version */
					'themeInstallFailedLabel'  => _x( '%s installation failed', 'theme' ),
					'installingMsg'            => __( 'Installing... please wait.' ),
					'installedMsg'             => __( 'Installation completed successfully.' ),
					/* translators: %s: Activation URL */
					'importerInstalledMsg'     => __( 'Importer installed successfully. <a href="%s">Run importer</a>' ),
					/* translators: %s: Theme name */
					'aysDelete'                => __( 'Are you sure you want to delete %s?' ),
					/* translators: %s: Plugin name */
					'aysDeleteUninstall'       => __( 'Are you sure you want to delete %s and its data?' ),
					'aysBulkDelete'            => __( 'Are you sure you want to delete the selected plugins and their data?' ),
					'aysBulkDeleteThemes'      => __( 'Caution: These themes may be active on other sites in the network. Are you sure you want to proceed?' ),
					'deleting'                 => __( 'Deleting...' ),
					/* translators: %s: Error string for a failed deletion */
					'deleteFailed'             => __( 'Deletion failed: %s' ),
					'pluginDeleted'            => _x( 'Deleted!', 'plugin' ),
					'themeDeleted'             => _x( 'Deleted!', 'theme' ),
					'livePreview'              => __( 'Live Preview' ),
					'activatePlugin'           => is_network_admin() ? __( 'Network Activate' ) : __( 'Activate' ),
					'activateTheme'            => is_network_admin() ? __( 'Network Enable' ) : __( 'Activate' ),
					/* translators: %s: Plugin name */
					'activatePluginLabel'      => is_network_admin() ? _x( 'Network Activate %s', 'plugin' ) : _x( 'Activate %s', 'plugin' ),
					/* translators: %s: Theme name */
					'activateThemeLabel'       => is_network_admin() ? _x( 'Network Activate %s', 'theme' ) : _x( 'Activate %s', 'theme' ),
					'activateImporter'         => __( 'Run Importer' ),
					/* translators: %s: Importer name */
					'activateImporterLabel'    => __( 'Run %s' ),
					'unknownError'             => __( 'Something went wrong.' ),
					'connectionError'          => __( 'Connection lost or the server is busy. Please try again later.' ),
					'nonceError'               => __( 'An error has occurred. Please reload the page and try again.' ),
					'pluginsFound'             => __( 'Number of plugins found: %d' ),
					'noPluginsFound'           => __( 'No plugins found. Try a different search.' ),
				),
			)
		);

		$scripts->add( 'farbtastic', '/acp/js/farbtastic.js', array( 'jquery' ), '1.2' );

		$scripts->add( 'iris', '/acp/js/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), '1.0.7', 1 );
		$scripts->add( 'wp-color-picker', "/acp/js/color-picker$suffix.js", array( 'iris' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'wp-color-picker',
			'wpColorPickerL10n',
			array(
				'clear'            => __( 'Clear' ),
				'clearAriaLabel'   => __( 'Clear color' ),
				'defaultString'    => __( 'Default' ),
				'defaultAriaLabel' => __( 'Select default color' ),
				'pick'             => __( 'Select Color' ),
				'defaultLabel'     => __( 'Color value' ),
			)
		);

		$scripts->add( 'dashboard', "/acp/js/dashboard$suffix.js", array( 'jquery', 'admin-comments', 'postbox', 'wp-util', 'wp-a11y' ), false, 1 );

		$scripts->add( 'list-revisions', "/libs/js/wp-list-revisions$suffix.js" );

		$scripts->add( 'media-grid', "/libs/js/media-grid$suffix.js", array( 'media-editor' ), false, 1 );
		$scripts->add( 'media', "/acp/js/media$suffix.js", array( 'jquery' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'media',
			'attachMediaBoxL10n',
			array(
				'error' => __( 'An error has occurred. Please reload the page and try again.' ),
			)
		);

		$scripts->add( 'image-edit', "/acp/js/image-edit$suffix.js", array( 'jquery', 'json2', 'imgareaselect' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'image-edit',
			'imageEditL10n',
			array(
				'error' => __( 'Could not load the preview image. Please reload the page and try again.' ),
			)
		);

		$scripts->add( 'set-post-thumbnail', "/acp/js/set-post-thumbnail$suffix.js", array( 'jquery' ), false, 1 );
		did_action( 'init' ) && $scripts->localize(
			'set-post-thumbnail',
			'setPostThumbnailL10n',
			array(
				'setThumbnail' => __( 'Use as featured image' ),
				'saving'       => __( 'Saving...' ), // no ellipsis
				'error'        => __( 'Could not set that as the thumbnail image. Try a different attachment.' ),
				'done'         => __( 'Done' ),
			)
		);

		/*
		 * Navigation Menus: Adding underscore as a dependency to utilize _.debounce
		 * see https://core.trac.wordpress.org/ticket/42321
		 */
		$scripts->add( 'nav-menu', "/acp/js/nav-menu$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'wp-lists', 'postbox', 'json2', 'underscore' ) );
		did_action( 'init' ) && $scripts->localize(
			'nav-menu',
			'navMenuL10n',
			array(
				'noResultsFound' => __( 'No results found.' ),
				'warnDeleteMenu' => __( "You are about to permanently delete this menu. \n 'Cancel' to stop, 'OK' to delete." ),
				'saveAlert'      => __( 'The changes you made will be lost if you navigate away from this page.' ),
				'untitled'       => _x( '(no label)', 'missing menu item navigation label' ),
			)
		);

		$scripts->add( 'custom-header', '/acp/js/custom-header.js', array( 'jquery-masonry' ), false, 1 );
		$scripts->add( 'custom-background', "/acp/js/custom-background$suffix.js", array( 'wp-color-picker', 'media-views' ), false, 1 );
		$scripts->add( 'media-gallery', "/acp/js/media-gallery$suffix.js", array( 'jquery' ), false, 1 );

		$scripts->add( 'svg-painter', '/acp/js/svg-painter.js', array( 'jquery' ), false, 1 );
	}
}

/**
 * Assign default styles to $styles object.
 *
 * Nothing is returned, because the $styles parameter is passed by reference.
 * Meaning that whatever object is passed will be updated without having to
 * reassign the variable that was passed back to the same value. This saves
 * memory.
 *
 * Adding default styles is not the only task, it also assigns the base_url
 * property, the default version, and text direction for the object.
 *
 * @since 2.6.0
 *
 * @param WP_Styles $styles
 */
function wp_default_styles( &$styles ) {
	include( ABSPATH . WPINC . '/version.php' ); // include an unmodified $wp_version

	if ( ! defined( 'SCRIPT_DEBUG' ) ) {
		define( 'SCRIPT_DEBUG', false !== strpos( $wp_version, '-src' ) );
	}

	if ( ! $guessurl = site_url() ) {
		$guessurl = wp_guess_url();
	}

	$styles->base_url        = $guessurl;
	$styles->content_url     = defined( 'WP_CONTENT_URL' ) ? WP_CONTENT_URL : '';
	$styles->default_version = get_bloginfo( 'version' );
	$styles->text_direction  = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';
	$styles->default_dirs    = array( '/acp/', '/libs/css/' );

	// Open Sans is no longer used by core, but may be relied upon by themes and plugins.
	$open_sans_font_url = '';

	/* translators: If there are characters in your language that are not supported
	 * by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off' ) ) {
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Open Sans character subset specific to your language,
		 * translate this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language.
		 */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)' );

		if ( 'cyrillic' == $subset ) {
			$subsets .= ',cyrillic,cyrillic-ext';
		} elseif ( 'greek' == $subset ) {
			$subsets .= ',greek,greek-ext';
		} elseif ( 'vietnamese' == $subset ) {
			$subsets .= ',vietnamese';
		}

		// Hotlink Open Sans, for now
		$open_sans_font_url = "https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,300,400,600&subset=$subsets";
	}

	// Register a stylesheet for the selected admin color scheme.
	$styles->add( 'colors', true, array( 'acp', 'buttons' ) );

	$suffix = SCRIPT_DEBUG ? '' : '.min';

	// Admin CSS
	$styles->add( 'common', "/acp/css/common$suffix.css" );
	$styles->add( 'forms', "/acp/css/forms$suffix.css" );
	$styles->add( 'admin-menu', "/acp/css/admin-menu$suffix.css" );
	$styles->add( 'dashboard', "/acp/css/dashboard$suffix.css" );
	$styles->add( 'list-tables', "/acp/css/list-tables$suffix.css" );
	$styles->add( 'edit', "/acp/css/edit$suffix.css" );
	$styles->add( 'revisions', "/acp/css/revisions$suffix.css" );
	$styles->add( 'media', "/acp/css/media$suffix.css" );
	$styles->add( 'themes', "/acp/css/themes$suffix.css" );
	$styles->add( 'about', "/acp/css/about$suffix.css" );
	$styles->add( 'nav-menus', "/acp/css/nav-menus$suffix.css" );
	$styles->add( 'widgets', "/acp/css/widgets$suffix.css", array( 'wp-pointer' ) );
	$styles->add( 'site-icon', "/acp/css/site-icon$suffix.css" );
	$styles->add( 'l10n', "/acp/css/l10n$suffix.css" );
	$styles->add( 'code-editor', "/acp/css/code-editor$suffix.css", array( 'wp-codemirror' ) );

	$styles->add( 'acp', false, array( 'dashicons', 'common', 'forms', 'admin-menu', 'dashboard', 'list-tables', 'edit', 'revisions', 'media', 'themes', 'about', 'nav-menus', 'widgets', 'site-icon', 'l10n' ) );

	$styles->add( 'login', "/acp/css/login$suffix.css", array( 'dashicons', 'buttons', 'forms', 'l10n' ) );
	$styles->add( 'install', "/acp/css/install$suffix.css", array( 'buttons' ) );
	$styles->add( 'wp-color-picker', "/acp/css/color-picker$suffix.css" );
	$styles->add( 'customize-controls', "/acp/css/customize-controls$suffix.css", array( 'acp', 'colors', 'ie', 'imgareaselect' ) );
	$styles->add( 'customize-widgets', "/acp/css/customize-widgets$suffix.css", array( 'acp', 'colors' ) );
	$styles->add( 'customize-nav-menus', "/acp/css/customize-nav-menus$suffix.css", array( 'acp', 'colors' ) );

	$styles->add( 'ie', "/acp/css/ie$suffix.css" );
	$styles->add_data( 'ie', 'conditional', 'lte IE 7' );

	// Common dependencies
	$styles->add( 'buttons', "/libs/css/buttons$suffix.css" );
	$styles->add( 'dashicons', "/libs/css/dashicons$suffix.css" );

	// Includes CSS
	$styles->add( 'admin-bar', "/libs/css/admin-bar$suffix.css", array( 'dashicons' ) );
	$styles->add( 'wp-auth-check', "/libs/css/wp-auth-check$suffix.css", array( 'dashicons' ) );
	$styles->add( 'editor-buttons', "/libs/css/editor$suffix.css", array( 'dashicons' ) );
	$styles->add( 'media-views', "/libs/css/media-views$suffix.css", array( 'buttons', 'dashicons', 'wp-mediaelement' ) );
	$styles->add( 'wp-pointer', "/libs/css/wp-pointer$suffix.css", array( 'dashicons' ) );
	$styles->add( 'customize-preview', "/libs/css/customize-preview$suffix.css", array( 'dashicons' ) );
	$styles->add( 'wp-embed-template-ie', "/libs/css/wp-embed-template-ie$suffix.css" );
	$styles->add_data( 'wp-embed-template-ie', 'conditional', 'lte IE 8' );

	// External libraries and friends
	$styles->add( 'imgareaselect', '/libs/js/imgareaselect/imgareaselect.css', array(), '0.9.8' );
	$styles->add( 'wp-jquery-ui-dialog', "/libs/css/jquery-ui-dialog$suffix.css", array( 'dashicons' ) );
	$styles->add( 'mediaelement', '/libs/js/mediaelement/mediaelementplayer-legacy.min.css', array(), '4.2.6-78496d1' );
	$styles->add( 'wp-mediaelement', "/libs/js/mediaelement/wp-mediaelement$suffix.css", array( 'mediaelement' ) );
	$styles->add( 'thickbox', '/libs/js/thickbox/thickbox.css', array( 'dashicons' ) );
	$styles->add( 'wp-codemirror', '/libs/js/codemirror/codemirror.min.css', array(), '5.29.1-alpha-ee20357' );

	// Deprecated CSS
	$styles->add( 'deprecated-media', "/acp/css/deprecated-media$suffix.css" );
	$styles->add( 'farbtastic', "/acp/css/farbtastic$suffix.css", array(), '1.3u1' );
	$styles->add( 'jcrop', '/libs/js/jcrop/jquery.Jcrop.min.css', array(), '0.9.12' );
	$styles->add( 'colors-fresh', false, array( 'acp', 'buttons' ) ); // Old handle.
	$styles->add( 'open-sans', $open_sans_font_url ); // No longer used in core as of 4.6

	// Packages styles
	$fonts_url = '';

	/*
	 * Translators: Use this to specify the proper Google Font name and variants
	 * to load that is supported by your language. Do not translate.
	 * Set to 'off' to disable loading.
	 */
	$font_family = _x( 'Noto Serif:400,400i,700,700i', 'Google Font Name and Variants' );
	if ( 'off' !== $font_family ) {
		$fonts_url = 'https://fonts.googleapis.com/css?family=' . urlencode( $font_family );
	}
	$styles->add( 'wp-editor-font', $fonts_url );

	$styles->add( 'wp-block-library-theme', "/libs/css/dist/block-library/theme$suffix.css" );

	$styles->add(
		'wp-edit-blocks',
		"/libs/css/dist/block-library/editor$suffix.css",
		array(
			'wp-components',
			'wp-editor',
			'wp-block-library',
			// Always include visual styles so the editor never appears broken.
			'wp-block-library-theme',
		)
	);

	$package_styles = array(
		'block-library'        => array(),
		'components'           => array(),
		'edit-post'            => array( 'wp-components', 'wp-editor', 'wp-edit-blocks', 'wp-block-library', 'wp-nux' ),
		'editor'               => array( 'wp-components', 'wp-editor-font', 'wp-nux' ),
		'format-library'       => array(),
		'list-reusable-blocks' => array( 'wp-components' ),
		'nux'                  => array( 'wp-components' ),
	);

	foreach ( $package_styles as $package => $dependencies ) {
		$handle = 'wp-' . $package;
		$path   = "/libs/css/dist/$package/style$suffix.css";

		$styles->add( $handle, $path, $dependencies );
	}

	// RTL CSS
	$rtl_styles = array(
		// Admin CSS
		'common',
		'forms',
		'admin-menu',
		'dashboard',
		'list-tables',
		'edit',
		'revisions',
		'media',
		'themes',
		'about',
		'nav-menus',
		'widgets',
		'site-icon',
		'l10n',
		'install',
		'wp-color-picker',
		'customize-controls',
		'customize-widgets',
		'customize-nav-menus',
		'customize-preview',
		'ie',
		'login',
		// Includes CSS
		'buttons',
		'admin-bar',
		'wp-auth-check',
		'editor-buttons',
		'media-views',
		'wp-pointer',
		'wp-jquery-ui-dialog',
		// Package styles
		'wp-block-library-theme',
		'wp-edit-blocks',
		'wp-block-library',
		'wp-components',
		'wp-edit-post',
		'wp-editor',
		'wp-format-library',
		'wp-list-reusable-blocks',
		'wp-nux',
		// Deprecated CSS
		'deprecated-media',
		'farbtastic',
	);

	foreach ( $rtl_styles as $rtl_style ) {
		$styles->add_data( $rtl_style, 'rtl', 'replace' );
		if ( $suffix ) {
			$styles->add_data( $rtl_style, 'suffix', $suffix );
		}
	}
}

/**
 * Reorder JavaScript scripts array to place prototype before jQuery.
 *
 * @since 2.3.1
 *
 * @param array $js_array JavaScript scripts array
 * @return array Reordered array, if needed.
 */
function wp_prototype_before_jquery( $js_array ) {
	if ( false === $prototype = array_search( 'prototype', $js_array, true ) ) {
		return $js_array;
	}

	if ( false === $jquery = array_search( 'jquery', $js_array, true ) ) {
		return $js_array;
	}

	if ( $prototype < $jquery ) {
		return $js_array;
	}

	unset( $js_array[ $prototype ] );

	array_splice( $js_array, $jquery, 0, 'prototype' );

	return $js_array;
}

/**
 * Load localized data on print rather than initialization.
 *
 * These localizations require information that may not be loaded even by init.
 *
 * @since 2.5.0
 */
function wp_just_in_time_script_localization() {

	wp_localize_script(
		'autosave',
		'autosaveL10n',
		array(
			'autosaveInterval' => AUTOSAVE_INTERVAL,
			'blog_id'          => get_current_blog_id(),
		)
	);

	wp_localize_script(
		'mce-view',
		'mceViewL10n',
		array(
			'shortcodes' => ! empty( $GLOBALS['shortcode_tags'] ) ? array_keys( $GLOBALS['shortcode_tags'] ) : array(),
		)
	);

	wp_localize_script(
		'word-count',
		'wordCountL10n',
		array(
			/*
			 * translators: If your word count is based on single characters (e.g. East Asian characters),
			 * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
			 * Do not translate into your own language.
			 */
			'type'       => _x( 'words', 'Word count type. Do not translate!' ),
			'shortcodes' => ! empty( $GLOBALS['shortcode_tags'] ) ? array_keys( $GLOBALS['shortcode_tags'] ) : array(),
		)
	);
}

/**
 * Localizes the jQuery UI datepicker.
 *
 * @since 4.6.0
 *
 * @link https://api.jqueryui.com/datepicker/#options
 *
 * @global WP_Locale $wp_locale The WordPress date and time locale object.
 */
function wp_localize_jquery_ui_datepicker() {
	global $wp_locale;

	if ( ! wp_script_is( 'jquery-ui-datepicker', 'enqueued' ) ) {
		return;
	}

	// Convert the PHP date format into jQuery UI's format.
	$datepicker_date_format = str_replace(
		array(
			'd',
			'j',
			'l',
			'z', // Day.
			'F',
			'M',
			'n',
			'm', // Month.
			'Y',
			'y',            // Year.
		),
		array(
			'dd',
			'd',
			'DD',
			'o',
			'MM',
			'M',
			'm',
			'mm',
			'yy',
			'y',
		),
		get_option( 'date_format' )
	);

	$datepicker_defaults = wp_json_encode(
		array(
			'closeText'       => __( 'Close' ),
			'currentText'     => __( 'Today' ),
			'monthNames'      => array_values( $wp_locale->month ),
			'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
			'nextText'        => __( 'Next' ),
			'prevText'        => __( 'Previous' ),
			'dayNames'        => array_values( $wp_locale->weekday ),
			'dayNamesShort'   => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'     => array_values( $wp_locale->weekday_initial ),
			'dateFormat'      => $datepicker_date_format,
			'firstDay'        => absint( get_option( 'start_of_week' ) ),
			'isRTL'           => $wp_locale->is_rtl(),
		)
	);

	wp_add_inline_script( 'jquery-ui-datepicker', "jQuery(document).ready(function(jQuery){jQuery.datepicker.setDefaults({$datepicker_defaults});});" );
}

/**
 * Localizes community events data that needs to be passed to dashboard.js.
 *
 * @since 4.8.0
 */
function wp_localize_community_events() {
	if ( ! wp_script_is( 'dashboard' ) ) {
		return;
	}

	require_once(ABSPATH . 'acp/includes/class-wp-community-events.php');

	$user_id            = get_current_user_id();
	$saved_location     = get_user_option( 'community-events-location', $user_id );
	$saved_ip_address   = isset( $saved_location['ip'] ) ? $saved_location['ip'] : false;
	$current_ip_address = WP_Community_Events::get_unsafe_client_ip();

	/*
	 * If the user's location is based on their IP address, then update their
	 * location when their IP address changes. This allows them to see events
	 * in their current city when travelling. Otherwise, they would always be
	 * shown events in the city where they were when they first loaded the
	 * Dashboard, which could have been months or years ago.
	 */
	if ( $saved_ip_address && $current_ip_address && $current_ip_address !== $saved_ip_address ) {
		$saved_location['ip'] = $current_ip_address;
		update_user_option( $user_id, 'community-events-location', $saved_location, true );
	}

	$events_client = new WP_Community_Events( $user_id, $saved_location );

	wp_localize_script(
		'dashboard',
		'communityEventsData',
		array(
			'nonce' => wp_create_nonce( 'community_events' ),
			'cache' => $events_client->get_cached_events(),

			'l10n'  => array(
				'enter_closest_city'              => __( 'Enter your closest city to find nearby events.' ),
				'error_occurred_please_try_again' => __( 'An error occurred. Please try again.' ),
				'attend_event_near_generic'       => __( 'Attend an upcoming event near you.' ),

				/*
				 * These specific examples were chosen to highlight the fact that a
				 * state is not needed, even for cities whose name is not unique.
				 * It would be too cumbersome to include that in the instructions
				 * to the user, so it's left as an implication.
				 */
				/* translators: %s is the name of the city we couldn't locate.
				 * Replace the examples with cities related to your locale. Test that
				 * they match the expected location and have upcoming events before
				 * including them. If no cities related to your locale have events,
				 * then use cities related to your locale that would be recognizable
				 * to most users. Use only the city name itself, without any region
				 * or country. Use the endonym (native locale name) instead of the
				 * English name if possible.
				 */
				'could_not_locate_city'           => __( 'We couldn&#8217;t locate %s. Please try another nearby city. For example: Kansas City; Springfield; Portland.' ),

				// This one is only used with wp.a11y.speak(), so it can/should be more brief.
				/* translators: %s: the name of a city */
				'city_updated'                    => __( 'City updated. Listing events near %s.' ),
			),
		)
	);
}

/**
 * Administration Screen CSS for changing the styles.
 *
 * If installing the 'acp/' directory will be replaced with './'.
 *
 * The $_wp_admin_css_colors global manages the Administration Screens CSS
 * stylesheet that is loaded. The option that is set is 'admin_color' and is the
 * color and key for the array. The value for the color key is an object with
 * a 'url' parameter that has the URL path to the CSS file.
 *
 * The query from $src parameter will be appended to the URL that is given from
 * the $_wp_admin_css_colors array value URL.
 *
 * @since 2.6.0
 * @global array $_wp_admin_css_colors
 *
 * @param string $src    Source URL.
 * @param string $handle Either 'colors' or 'colors-rtl'.
 * @return string|false URL path to CSS stylesheet for Administration Screens.
 */
function wp_style_loader_src( $src, $handle ) {
	global $_wp_admin_css_colors;

	if ( wp_installing() ) {
		return preg_replace( '#^acp/#', './', $src );
	}

	if ( 'colors' == $handle ) {
		$color = get_user_option( 'admin_color' );

		if ( empty( $color ) || ! isset( $_wp_admin_css_colors[ $color ] ) ) {
			$color = 'fresh';
		}

		$color = $_wp_admin_css_colors[ $color ];
		$url   = $color->url;

		if ( ! $url ) {
			return false;
		}

		$parsed = parse_url( $src );
		if ( isset( $parsed['query'] ) && $parsed['query'] ) {
			wp_parse_str( $parsed['query'], $qv );
			$url = add_query_arg( $qv, $url );
		}

		return $url;
	}

	return $src;
}

/**
 * Prints the script queue in the HTML head on admin pages.
 *
 * Postpones the scripts that were queued for the footer.
 * print_footer_scripts() is called in the footer to print these scripts.
 *
 * @since 2.8.0
 *
 * @see wp_print_scripts()
 *
 * @global bool $concatenate_scripts
 *
 * @return array
 */
function print_head_scripts() {
	global $concatenate_scripts;

	if ( ! did_action( 'wp_print_scripts' ) ) {
		/** This action is documented in libs/functions.wp-scripts.php */
		do_action( 'wp_print_scripts' );
	}

	$wp_scripts = wp_scripts();

	script_concat_settings();
	$wp_scripts->do_concat = $concatenate_scripts;
	$wp_scripts->do_head_items();

	/**
	 * Filters whether to print the head scripts.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the head scripts. Default true.
	 */
	if ( apply_filters( 'print_head_scripts', true ) ) {
		_print_scripts();
	}

	$wp_scripts->reset();
	return $wp_scripts->done;
}

/**
 * Prints the scripts that were queued for the footer or too late for the HTML head.
 *
 * @since 2.8.0
 *
 * @global WP_Scripts $wp_scripts
 * @global bool       $concatenate_scripts
 *
 * @return array
 */
function print_footer_scripts() {
	global $wp_scripts, $concatenate_scripts;

	if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
		return array(); // No need to run if not instantiated.
	}
	script_concat_settings();
	$wp_scripts->do_concat = $concatenate_scripts;
	$wp_scripts->do_footer_items();

	/**
	 * Filters whether to print the footer scripts.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the footer scripts. Default true.
	 */
	if ( apply_filters( 'print_footer_scripts', true ) ) {
		_print_scripts();
	}

	$wp_scripts->reset();
	return $wp_scripts->done;
}

/**
 * Print scripts (internal use only)
 *
 * @ignore
 *
 * @global WP_Scripts $wp_scripts
 * @global bool       $compress_scripts
 */
function _print_scripts() {
	global $wp_scripts, $compress_scripts;

	$zip = $compress_scripts ? 1 : 0;
	if ( $zip && defined( 'ENFORCE_GZIP' ) && ENFORCE_GZIP ) {
		$zip = 'gzip';
	}

	if ( $concat = trim( $wp_scripts->concat, ', ' ) ) {

		if ( ! empty( $wp_scripts->print_code ) ) {
			echo "\n<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n"; // not needed in HTML 5
			echo $wp_scripts->print_code;
			echo "/* ]]> */\n";
			echo "</script>\n";
		}

		$concat = str_split( $concat, 128 );
		$concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

		$src = $wp_scripts->base_url . "/acp/load-scripts.php?c={$zip}&" . $concat . '&ver=' . $wp_scripts->default_version;
		echo "<script type='text/javascript' src='" . esc_attr( $src ) . "'></script>\n";
	}

	if ( ! empty( $wp_scripts->print_html ) ) {
		echo $wp_scripts->print_html;
	}
}

/**
 * Prints the script queue in the HTML head on the front end.
 *
 * Postpones the scripts that were queued for the footer.
 * wp_print_footer_scripts() is called in the footer to print these scripts.
 *
 * @since 2.8.0
 *
 * @global WP_Scripts $wp_scripts
 *
 * @return array
 */
function wp_print_head_scripts() {
	if ( ! did_action( 'wp_print_scripts' ) ) {
		/** This action is documented in libs/functions.wp-scripts.php */
		do_action( 'wp_print_scripts' );
	}

	global $wp_scripts;

	if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
		return array(); // no need to run if nothing is queued
	}
	return print_head_scripts();
}

/**
 * Private, for use in *_footer_scripts hooks
 *
 * @since 3.3.0
 */
function _wp_footer_scripts() {
	print_late_styles();
	print_footer_scripts();
}

/**
 * Hooks to print the scripts and styles in the footer.
 *
 * @since 2.8.0
 */
function wp_print_footer_scripts() {
	/**
	 * Fires when footer scripts are printed.
	 *
	 * @since 2.8.0
	 */
	do_action( 'wp_print_footer_scripts' );
}

/**
 * Wrapper for do_action('wp_enqueue_scripts')
 *
 * Allows plugins to queue scripts for the front end using wp_enqueue_script().
 * Runs first in wp_head() where all is_home(), is_page(), etc. functions are available.
 *
 * @since 2.8.0
 */
function wp_enqueue_scripts() {
	/**
	 * Fires when scripts and styles are enqueued.
	 *
	 * @since 2.8.0
	 */
	do_action( 'wp_enqueue_scripts' );
}

/**
 * Prints the styles queue in the HTML head on admin pages.
 *
 * @since 2.8.0
 *
 * @global bool $concatenate_scripts
 *
 * @return array
 */
function print_admin_styles() {
	global $concatenate_scripts;

	$wp_styles = wp_styles();

	script_concat_settings();
	$wp_styles->do_concat = $concatenate_scripts;
	$wp_styles->do_items( false );

	/**
	 * Filters whether to print the admin styles.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the admin styles. Default true.
	 */
	if ( apply_filters( 'print_admin_styles', true ) ) {
		_print_styles();
	}

	$wp_styles->reset();
	return $wp_styles->done;
}

/**
 * Prints the styles that were queued too late for the HTML head.
 *
 * @since 3.3.0
 *
 * @global WP_Styles $wp_styles
 * @global bool      $concatenate_scripts
 *
 * @return array|void
 */
function print_late_styles() {
	global $wp_styles, $concatenate_scripts;

	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	script_concat_settings();
	$wp_styles->do_concat = $concatenate_scripts;
	$wp_styles->do_footer_items();

	/**
	 * Filters whether to print the styles queued too late for the HTML head.
	 *
	 * @since 3.3.0
	 *
	 * @param bool $print Whether to print the 'late' styles. Default true.
	 */
	if ( apply_filters( 'print_late_styles', true ) ) {
		_print_styles();
	}

	$wp_styles->reset();
	return $wp_styles->done;
}

/**
 * Print styles (internal use only)
 *
 * @ignore
 * @since 3.3.0
 *
 * @global bool $compress_css
 */
function _print_styles() {
	global $compress_css;

	$wp_styles = wp_styles();

	$zip = $compress_css ? 1 : 0;
	if ( $zip && defined( 'ENFORCE_GZIP' ) && ENFORCE_GZIP ) {
		$zip = 'gzip';
	}

	if ( $concat = trim( $wp_styles->concat, ', ' ) ) {
		$dir = $wp_styles->text_direction;
		$ver = $wp_styles->default_version;

		$concat = str_split( $concat, 128 );
		$concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

		$href = $wp_styles->base_url . "/acp/load-styles.php?c={$zip}&dir={$dir}&" . $concat . '&ver=' . $ver;
		echo "<link rel='stylesheet' href='" . esc_attr( $href ) . "' type='text/css' media='all' />\n";

		if ( ! empty( $wp_styles->print_code ) ) {
			echo "<style type='text/css'>\n";
			echo $wp_styles->print_code;
			echo "\n</style>\n";
		}
	}

	if ( ! empty( $wp_styles->print_html ) ) {
		echo $wp_styles->print_html;
	}
}

/**
 * Determine the concatenation and compression settings for scripts and styles.
 *
 * @since 2.8.0
 *
 * @global bool $concatenate_scripts
 * @global bool $compress_scripts
 * @global bool $compress_css
 */
function script_concat_settings() {
	global $concatenate_scripts, $compress_scripts, $compress_css;

	$compressed_output = ( ini_get( 'zlib.output_compression' ) || 'ob_gzhandler' == ini_get( 'output_handler' ) );

	if ( ! isset( $concatenate_scripts ) ) {
		$concatenate_scripts = defined( 'CONCATENATE_SCRIPTS' ) ? CONCATENATE_SCRIPTS : true;
		if ( ( ! is_admin() && ! did_action( 'login_init' ) ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
			$concatenate_scripts = false;
		}
	}

	if ( ! isset( $compress_scripts ) ) {
		$compress_scripts = defined( 'COMPRESS_SCRIPTS' ) ? COMPRESS_SCRIPTS : true;
		if ( $compress_scripts && ( ! get_site_option( 'can_compress_scripts' ) || $compressed_output ) ) {
			$compress_scripts = false;
		}
	}

	if ( ! isset( $compress_css ) ) {
		$compress_css = defined( 'COMPRESS_CSS' ) ? COMPRESS_CSS : true;
		if ( $compress_css && ( ! get_site_option( 'can_compress_scripts' ) || $compressed_output ) ) {
			$compress_css = false;
		}
	}
}

/**
 * Handles the enqueueing of block scripts and styles that are common to both
 * the editor and the front-end.
 *
 * @since 5.0.0
 *
 * @global WP_Screen $current_screen
 */
function wp_common_block_scripts_and_styles() {
	global $current_screen;

	if ( is_admin() && ( $current_screen instanceof WP_Screen ) && ! $current_screen->is_block_editor() ) {
		return;
	}

	wp_enqueue_style( 'wp-block-library' );

	if ( current_theme_supports( 'wp-block-styles' ) ) {
		wp_enqueue_style( 'wp-block-library-theme' );
	}

	/**
	 * Fires after enqueuing block assets for both editor and front-end.
	 *
	 * Call `add_action` on any hook before 'wp_enqueue_scripts'.
	 *
	 * In the function call you supply, simply use `wp_enqueue_script` and
	 * `wp_enqueue_style` to add your functionality to the Gutenberg editor.
	 *
	 * @since 5.0.0
	 */
	  do_action( 'enqueue_block_assets' );
}

/**
 * Enqueues registered block scripts and styles, depending on current rendered
 * context (only enqueuing editor scripts while in context of the editor).
 *
 * @since 5.0.0
 *
 * @global WP_Screen $current_screen
 */
function wp_enqueue_registered_block_scripts_and_styles() {
	global $current_screen;

	$is_editor = ( ( $current_screen instanceof WP_Screen ) && $current_screen->is_block_editor() );

	$block_registry = WP_Block_Type_Registry::get_instance();
	foreach ( $block_registry->get_all_registered() as $block_name => $block_type ) {
		// Front-end styles.
		if ( ! empty( $block_type->style ) ) {
			wp_enqueue_style( $block_type->style );
		}

		// Front-end script.
		if ( ! empty( $block_type->script ) ) {
			wp_enqueue_script( $block_type->script );
		}

		// Editor styles.
		if ( $is_editor && ! empty( $block_type->editor_style ) ) {
			wp_enqueue_style( $block_type->editor_style );
		}

		// Editor script.
		if ( $is_editor && ! empty( $block_type->editor_script ) ) {
			wp_enqueue_script( $block_type->editor_script );
		}
	}
}
