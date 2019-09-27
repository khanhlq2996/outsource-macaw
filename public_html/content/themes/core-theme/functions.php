<?php
/**
 * Flatsome functions and definitions
 *
 * @package flatsome
 */

require get_template_directory() . '/inc/init.php';

/**
 * Note: It's not recommended to add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * Learn more here: http://codex.wordpress.org/Child_Themes
 */

add_filter( 'wp_prepare_themes_for_js', 'hide_themes', 11, 1 );
function hide_themes($prepared_themes){

    // hide parent-theme theme
    unset($prepared_themes['core-theme']);

    return $prepared_themes;
}

function remove_core_updates(){
    global $wp_version;
    return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
add_filter('pre_site_transient_update_plugins','remove_core_updates');
add_filter('pre_site_transient_update_themes','remove_core_updates');

remove_action( 'wp_version_check', 'wp_version_check' );
remove_action( 'admin_init', '_maybe_update_core' );