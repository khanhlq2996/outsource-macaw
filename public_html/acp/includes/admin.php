<?php
/**
 * Core Administration API
 *
 * @package WordPress
 * @subpackage Administration
 * @since 2.3.0
 */

if ( ! defined( 'WP_ADMIN' ) ) {
	/*
	 * This file is being included from a file other than acp/admin.php, so
	 * some setup was skipped. Make sure the admin message catalog is loaded since
	 * load_default_textdomain() will not have done so in this context.
	 */
	load_textdomain( 'default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo' );
}

/** WordPress Administration Hooks */
require_once(ABSPATH . 'acp/includes/admin-filters.php');

/** WordPress Bookmark Administration API */
require_once(ABSPATH . 'acp/includes/bookmark.php');

/** WordPress Comment Administration API */
require_once(ABSPATH . 'acp/includes/comment.php');

/** WordPress Administration File API */
require_once(ABSPATH . 'acp/includes/file.php');

/** WordPress Image Administration API */
require_once(ABSPATH . 'acp/includes/image.php');

/** WordPress Media Administration API */
require_once(ABSPATH . 'acp/includes/media.php');

/** WordPress Import Administration API */
require_once(ABSPATH . 'acp/includes/import.php');

/** WordPress Misc Administration API */
require_once(ABSPATH . 'acp/includes/misc.php');

/** WordPress Options Administration API */
require_once(ABSPATH . 'acp/includes/options.php');

/** WordPress Plugin Administration API */
require_once(ABSPATH . 'acp/includes/plugin.php');

/** WordPress Post Administration API */
require_once(ABSPATH . 'acp/includes/post.php');

/** WordPress Administration Screen API */
require_once(ABSPATH . 'acp/includes/class-wp-screen.php');
require_once(ABSPATH . 'acp/includes/screen.php');

/** WordPress Taxonomy Administration API */
require_once(ABSPATH . 'acp/includes/taxonomy.php');

/** WordPress Template Administration API */
require_once(ABSPATH . 'acp/includes/template.php');

/** WordPress List Table Administration API and base class */
require_once(ABSPATH . 'acp/includes/class-wp-list-table.php');
require_once(ABSPATH . 'acp/includes/class-wp-list-table-compat.php');
require_once(ABSPATH . 'acp/includes/list-table.php');

/** WordPress Theme Administration API */
require_once(ABSPATH . 'acp/includes/theme.php');

/** WordPress User Administration API */
require_once(ABSPATH . 'acp/includes/user.php');

/** WordPress Site Icon API */
require_once(ABSPATH . 'acp/includes/class-wp-site-icon.php');

/** WordPress Update Administration API */
require_once(ABSPATH . 'acp/includes/update.php');

/** WordPress Deprecated Administration API */
require_once(ABSPATH . 'acp/includes/deprecated.php');

/** WordPress Multisite support API */
if ( is_multisite() ) {
	require_once(ABSPATH . 'acp/includes/ms-admin-filters.php');
	require_once(ABSPATH . 'acp/includes/ms.php');
	require_once(ABSPATH . 'acp/includes/ms-deprecated.php');
}
