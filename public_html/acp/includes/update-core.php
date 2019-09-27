<?php
/**
 * WordPress core upgrade functionality.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 2.7.0
 */

/**
 * Stores files to be deleted.
 *
 * @since 2.7.0
 * @global array $_old_files
 * @var array
 * @name $_old_files
 */
global $_old_files;

$_old_files = array(
	// 2.0
	'acp/import-b2.php',
	'acp/import-blogger.php',
	'acp/import-greymatter.php',
	'acp/import-livejournal.php',
	'acp/import-mt.php',
	'acp/import-rss.php',
	'acp/import-textpattern.php',
	'acp/quicktags.js',
	'wp-images/fade-butt.png',
	'wp-images/get-firefox.png',
	'wp-images/header-shadow.png',
	'wp-images/smilies',
	'wp-images/wp-small.png',
	'wp-images/wpminilogo.png',
	'wp.php',
	// 2.0.8
	'libs/js/tinymce/plugins/inlinepopups/readme.txt',
	// 2.1
	'acp/edit-form-ajax-cat.php',
	'acp/execute-pings.php',
	'acp/inline-uploading.php',
	'acp/link-categories.php',
	'acp/list-manipulation.js',
	'acp/list-manipulation.php',
	'libs/comment-functions.php',
	'libs/feed-functions.php',
	'libs/functions-compat.php',
	'libs/functions-formatting.php',
	'libs/functions-post.php',
	'libs/js/dbx-key.js',
	'libs/js/tinymce/plugins/autosave/langs/cs.js',
	'libs/js/tinymce/plugins/autosave/langs/sv.js',
	'libs/links.php',
	'libs/pluggable-functions.php',
	'libs/template-functions-author.php',
	'libs/template-functions-category.php',
	'libs/template-functions-general.php',
	'libs/template-functions-links.php',
	'libs/template-functions-post.php',
	'libs/wp-l10n.php',
	// 2.2
	'acp/cat-js.php',
	'acp/import/b2.php',
	'libs/js/autosave-js.php',
	'libs/js/list-manipulation-js.php',
	'libs/js/wp-ajax-js.php',
	// 2.3
	'acp/admin-db.php',
	'acp/cat.js',
	'acp/categories.js',
	'acp/custom-fields.js',
	'acp/dbx-admin-key.js',
	'acp/edit-comments.js',
	'acp/install-rtl.css',
	'acp/install.css',
	'acp/upgrade-schema.php',
	'acp/upload-functions.php',
	'acp/upload-rtl.css',
	'acp/upload.css',
	'acp/upload.js',
	'acp/users.js',
	'acp/widgets-rtl.css',
	'acp/widgets.css',
	'acp/xfn.js',
	'libs/js/tinymce/license.html',
	// 2.5
	'acp/css/upload.css',
	'acp/images/box-bg-left.gif',
	'acp/images/box-bg-right.gif',
	'acp/images/box-bg.gif',
	'acp/images/box-butt-left.gif',
	'acp/images/box-butt-right.gif',
	'acp/images/box-butt.gif',
	'acp/images/box-head-left.gif',
	'acp/images/box-head-right.gif',
	'acp/images/box-head.gif',
	'acp/images/heading-bg.gif',
	'acp/images/login-bkg-bottom.gif',
	'acp/images/login-bkg-tile.gif',
	'acp/images/notice.gif',
	'acp/images/toggle.gif',
	'acp/includes/upload.php',
	'acp/js/dbx-admin-key.js',
	'acp/js/link-cat.js',
	'acp/profile-update.php',
	'acp/templates.php',
	'libs/images/wlw/WpComments.png',
	'libs/images/wlw/WpIcon.png',
	'libs/images/wlw/WpWatermark.png',
	'libs/js/dbx.js',
	'libs/js/fat.js',
	'libs/js/list-manipulation.js',
	'libs/js/tinymce/langs/en.js',
	'libs/js/tinymce/plugins/autosave/editor_plugin_src.js',
	'libs/js/tinymce/plugins/autosave/langs',
	'libs/js/tinymce/plugins/directionality/images',
	'libs/js/tinymce/plugins/directionality/langs',
	'libs/js/tinymce/plugins/inlinepopups/css',
	'libs/js/tinymce/plugins/inlinepopups/images',
	'libs/js/tinymce/plugins/inlinepopups/jscripts',
	'libs/js/tinymce/plugins/paste/images',
	'libs/js/tinymce/plugins/paste/jscripts',
	'libs/js/tinymce/plugins/paste/langs',
	'libs/js/tinymce/plugins/spellchecker/classes/HttpClient.class.php',
	'libs/js/tinymce/plugins/spellchecker/classes/TinyGoogleSpell.class.php',
	'libs/js/tinymce/plugins/spellchecker/classes/TinyPspell.class.php',
	'libs/js/tinymce/plugins/spellchecker/classes/TinyPspellShell.class.php',
	'libs/js/tinymce/plugins/spellchecker/css/spellchecker.css',
	'libs/js/tinymce/plugins/spellchecker/images',
	'libs/js/tinymce/plugins/spellchecker/langs',
	'libs/js/tinymce/plugins/spellchecker/tinyspell.php',
	'libs/js/tinymce/plugins/wordpress/images',
	'libs/js/tinymce/plugins/wordpress/langs',
	'libs/js/tinymce/plugins/wordpress/wordpress.css',
	'libs/js/tinymce/plugins/wphelp',
	'libs/js/tinymce/themes/advanced/css',
	'libs/js/tinymce/themes/advanced/images',
	'libs/js/tinymce/themes/advanced/jscripts',
	'libs/js/tinymce/themes/advanced/langs',
	// 2.5.1
	'libs/js/tinymce/tiny_mce_gzip.php',
	// 2.6
	'acp/bookmarklet.php',
	'libs/js/jquery/jquery.dimensions.min.js',
	'libs/js/tinymce/plugins/wordpress/popups.css',
	'libs/js/wp-ajax.js',
	// 2.7
	'acp/css/press-this-ie-rtl.css',
	'acp/css/press-this-ie.css',
	'acp/css/upload-rtl.css',
	'acp/edit-form.php',
	'acp/images/comment-pill.gif',
	'acp/images/comment-stalk-classic.gif',
	'acp/images/comment-stalk-fresh.gif',
	'acp/images/comment-stalk-rtl.gif',
	'acp/images/del.png',
	'acp/images/gear.png',
	'acp/images/media-button-gallery.gif',
	'acp/images/media-buttons.gif',
	'acp/images/postbox-bg.gif',
	'acp/images/tab.png',
	'acp/images/tail.gif',
	'acp/js/forms.js',
	'acp/js/upload.js',
	'acp/link-import.php',
	'libs/images/audio.png',
	'libs/images/css.png',
	'libs/images/default.png',
	'libs/images/doc.png',
	'libs/images/exe.png',
	'libs/images/html.png',
	'libs/images/js.png',
	'libs/images/pdf.png',
	'libs/images/swf.png',
	'libs/images/tar.png',
	'libs/images/text.png',
	'libs/images/video.png',
	'libs/images/zip.png',
	'libs/js/tinymce/tiny_mce_config.php',
	'libs/js/tinymce/tiny_mce_ext.js',
	// 2.8
	'acp/js/users.js',
	'libs/js/swfupload/plugins/swfupload.documentready.js',
	'libs/js/swfupload/plugins/swfupload.graceful_degradation.js',
	'libs/js/swfupload/swfupload_f9.swf',
	'libs/js/tinymce/plugins/autosave',
	'libs/js/tinymce/plugins/paste/css',
	'libs/js/tinymce/utils/mclayer.js',
	'libs/js/tinymce/wordpress.css',
	// 2.8.5
	'acp/import/btt.php',
	'acp/import/jkw.php',
	// 2.9
	'acp/js/page.dev.js',
	'acp/js/page.js',
	'acp/js/set-post-thumbnail-handler.dev.js',
	'acp/js/set-post-thumbnail-handler.js',
	'acp/js/slug.dev.js',
	'acp/js/slug.js',
	'libs/gettext.php',
	'libs/js/tinymce/plugins/wordpress/js',
	'libs/streams.php',
	// MU
	'README.txt',
	'htaccess.dist',
	'index-install.php',
	'acp/css/mu-rtl.css',
	'acp/css/mu.css',
	'acp/images/site-admin.png',
	'acp/includes/mu.php',
	'acp/wpmu-admin.php',
	'acp/wpmu-blogs.php',
	'acp/wpmu-edit.php',
	'acp/wpmu-options.php',
	'acp/wpmu-themes.php',
	'acp/wpmu-upgrade-site.php',
	'acp/wpmu-users.php',
	'libs/images/wordpress-mu.png',
	'libs/wpmu-default-filters.php',
	'libs/wpmu-functions.php',
	'wpmu-settings.php',
	// 3.0
	'acp/categories.php',
	'acp/edit-category-form.php',
	'acp/edit-page-form.php',
	'acp/edit-pages.php',
	'acp/images/admin-header-footer.png',
	'acp/images/browse-happy.gif',
	'acp/images/ico-add.png',
	'acp/images/ico-close.png',
	'acp/images/ico-edit.png',
	'acp/images/ico-viewpage.png',
	'acp/images/fav-top.png',
	'acp/images/screen-options-left.gif',
	'acp/images/wp-logo-vs.gif',
	'acp/images/wp-logo.gif',
	'acp/import',
	'acp/js/wp-gears.dev.js',
	'acp/js/wp-gears.js',
	'acp/options-misc.php',
	'acp/page-new.php',
	'acp/page.php',
	'acp/rtl.css',
	'acp/rtl.dev.css',
	'acp/update-links.php',
	'acp/acp.css',
	'acp/acp.dev.css',
	'libs/js/codepress',
	'libs/js/codepress/engines/khtml.js',
	'libs/js/codepress/engines/older.js',
	'libs/js/jquery/autocomplete.dev.js',
	'libs/js/jquery/autocomplete.js',
	'libs/js/jquery/interface.js',
	'libs/js/scriptaculous/prototype.js',
	// Following file added back in 5.1 see #45645
	//'libs/js/tinymce/wp-tinymce.js',
	// 3.1
	'acp/edit-attachment-rows.php',
	'acp/edit-link-categories.php',
	'acp/edit-link-category-form.php',
	'acp/edit-post-rows.php',
	'acp/images/button-grad-active-vs.png',
	'acp/images/button-grad-vs.png',
	'acp/images/fav-arrow-vs-rtl.gif',
	'acp/images/fav-arrow-vs.gif',
	'acp/images/fav-top-vs.gif',
	'acp/images/list-vs.png',
	'acp/images/screen-options-right-up.gif',
	'acp/images/screen-options-right.gif',
	'acp/images/visit-site-button-grad-vs.gif',
	'acp/images/visit-site-button-grad.gif',
	'acp/link-category.php',
	'acp/sidebar.php',
	'libs/classes.php',
	'libs/js/tinymce/blank.htm',
	'libs/js/tinymce/plugins/media/css/content.css',
	'libs/js/tinymce/plugins/media/img',
	'libs/js/tinymce/plugins/safari',
	// 3.2
	'acp/images/logo-login.gif',
	'acp/images/star.gif',
	'acp/js/list-table.dev.js',
	'acp/js/list-table.js',
	'libs/default-embeds.php',
	'libs/js/tinymce/plugins/wordpress/img/help.gif',
	'libs/js/tinymce/plugins/wordpress/img/more.gif',
	'libs/js/tinymce/plugins/wordpress/img/toolbars.gif',
	'libs/js/tinymce/themes/advanced/img/fm.gif',
	'libs/js/tinymce/themes/advanced/img/sflogo.png',
	// 3.3
	'acp/css/colors-classic-rtl.css',
	'acp/css/colors-classic-rtl.dev.css',
	'acp/css/colors-fresh-rtl.css',
	'acp/css/colors-fresh-rtl.dev.css',
	'acp/css/dashboard-rtl.dev.css',
	'acp/css/dashboard.dev.css',
	'acp/css/global-rtl.css',
	'acp/css/global-rtl.dev.css',
	'acp/css/global.css',
	'acp/css/global.dev.css',
	'acp/css/install-rtl.dev.css',
	'acp/css/login-rtl.dev.css',
	'acp/css/login.dev.css',
	'acp/css/ms.css',
	'acp/css/ms.dev.css',
	'acp/css/nav-menu-rtl.css',
	'acp/css/nav-menu-rtl.dev.css',
	'acp/css/nav-menu.css',
	'acp/css/nav-menu.dev.css',
	'acp/css/plugin-install-rtl.css',
	'acp/css/plugin-install-rtl.dev.css',
	'acp/css/plugin-install.css',
	'acp/css/plugin-install.dev.css',
	'acp/css/press-this-rtl.dev.css',
	'acp/css/press-this.dev.css',
	'acp/css/theme-editor-rtl.css',
	'acp/css/theme-editor-rtl.dev.css',
	'acp/css/theme-editor.css',
	'acp/css/theme-editor.dev.css',
	'acp/css/theme-install-rtl.css',
	'acp/css/theme-install-rtl.dev.css',
	'acp/css/theme-install.css',
	'acp/css/theme-install.dev.css',
	'acp/css/widgets-rtl.dev.css',
	'acp/css/widgets.dev.css',
	'acp/includes/internal-linking.php',
	'libs/images/admin-bar-sprite-rtl.png',
	'libs/js/jquery/ui.button.js',
	'libs/js/jquery/ui.core.js',
	'libs/js/jquery/ui.dialog.js',
	'libs/js/jquery/ui.draggable.js',
	'libs/js/jquery/ui.droppable.js',
	'libs/js/jquery/ui.mouse.js',
	'libs/js/jquery/ui.position.js',
	'libs/js/jquery/ui.resizable.js',
	'libs/js/jquery/ui.selectable.js',
	'libs/js/jquery/ui.sortable.js',
	'libs/js/jquery/ui.tabs.js',
	'libs/js/jquery/ui.widget.js',
	'libs/js/l10n.dev.js',
	'libs/js/l10n.js',
	'libs/js/tinymce/plugins/wplink/css',
	'libs/js/tinymce/plugins/wplink/img',
	'libs/js/tinymce/plugins/wplink/js',
	'libs/js/tinymce/themes/advanced/img/wpicons.png',
	'libs/js/tinymce/themes/advanced/skins/wp_theme/img/butt2.png',
	'libs/js/tinymce/themes/advanced/skins/wp_theme/img/button_bg.png',
	'libs/js/tinymce/themes/advanced/skins/wp_theme/img/down_arrow.gif',
	'libs/js/tinymce/themes/advanced/skins/wp_theme/img/fade-butt.png',
	'libs/js/tinymce/themes/advanced/skins/wp_theme/img/separator.gif',
	// Don't delete, yet: 'wp-rss.php',
	// Don't delete, yet: 'wp-rdf.php',
	// Don't delete, yet: 'wp-rss2.php',
	// Don't delete, yet: 'wp-commentsrss2.php',
	// Don't delete, yet: 'wp-atom.php',
	// Don't delete, yet: 'wp-feed.php',
	// 3.4
	'acp/images/gray-star.png',
	'acp/images/logo-login.png',
	'acp/images/star.png',
	'acp/index-extra.php',
	'acp/network/index-extra.php',
	'acp/user/index-extra.php',
	'acp/images/screenshots/admin-flyouts.png',
	'acp/images/screenshots/coediting.png',
	'acp/images/screenshots/drag-and-drop.png',
	'acp/images/screenshots/help-screen.png',
	'acp/images/screenshots/media-icon.png',
	'acp/images/screenshots/new-feature-pointer.png',
	'acp/images/screenshots/welcome-screen.png',
	'libs/css/editor-buttons.css',
	'libs/css/editor-buttons.dev.css',
	'libs/js/tinymce/plugins/paste/blank.htm',
	'libs/js/tinymce/plugins/wordpress/css',
	'libs/js/tinymce/plugins/wordpress/editor_plugin.dev.js',
	'libs/js/tinymce/plugins/wordpress/img/embedded.png',
	'libs/js/tinymce/plugins/wordpress/img/more_bug.gif',
	'libs/js/tinymce/plugins/wordpress/img/page_bug.gif',
	'libs/js/tinymce/plugins/wpdialogs/editor_plugin.dev.js',
	'libs/js/tinymce/plugins/wpeditimage/css/editimage-rtl.css',
	'libs/js/tinymce/plugins/wpeditimage/editor_plugin.dev.js',
	'libs/js/tinymce/plugins/wpfullscreen/editor_plugin.dev.js',
	'libs/js/tinymce/plugins/wpgallery/editor_plugin.dev.js',
	'libs/js/tinymce/plugins/wpgallery/img/gallery.png',
	'libs/js/tinymce/plugins/wplink/editor_plugin.dev.js',
	// Don't delete, yet: 'wp-pass.php',
	// Don't delete, yet: 'wp-register.php',
	// 3.5
	'acp/gears-manifest.php',
	'acp/includes/manifest.php',
	'acp/images/archive-link.png',
	'acp/images/blue-grad.png',
	'acp/images/button-grad-active.png',
	'acp/images/button-grad.png',
	'acp/images/ed-bg-vs.gif',
	'acp/images/ed-bg.gif',
	'acp/images/fade-butt.png',
	'acp/images/fav-arrow-rtl.gif',
	'acp/images/fav-arrow.gif',
	'acp/images/fav-vs.png',
	'acp/images/fav.png',
	'acp/images/gray-grad.png',
	'acp/images/loading-publish.gif',
	'acp/images/logo-ghost.png',
	'acp/images/logo.gif',
	'acp/images/menu-arrow-frame-rtl.png',
	'acp/images/menu-arrow-frame.png',
	'acp/images/menu-arrows.gif',
	'acp/images/menu-bits-rtl-vs.gif',
	'acp/images/menu-bits-rtl.gif',
	'acp/images/menu-bits-vs.gif',
	'acp/images/menu-bits.gif',
	'acp/images/menu-dark-rtl-vs.gif',
	'acp/images/menu-dark-rtl.gif',
	'acp/images/menu-dark-vs.gif',
	'acp/images/menu-dark.gif',
	'acp/images/required.gif',
	'acp/images/screen-options-toggle-vs.gif',
	'acp/images/screen-options-toggle.gif',
	'acp/images/toggle-arrow-rtl.gif',
	'acp/images/toggle-arrow.gif',
	'acp/images/upload-classic.png',
	'acp/images/upload-fresh.png',
	'acp/images/white-grad-active.png',
	'acp/images/white-grad.png',
	'acp/images/widgets-arrow-vs.gif',
	'acp/images/widgets-arrow.gif',
	'acp/images/wpspin_dark.gif',
	'libs/images/upload.png',
	'libs/js/prototype.js',
	'libs/js/scriptaculous',
	'acp/css/wp-admin-rtl.dev.css',
	'acp/css/acp.dev.css',
	'acp/css/media-rtl.dev.css',
	'acp/css/media.dev.css',
	'acp/css/colors-classic.dev.css',
	'acp/css/customize-controls-rtl.dev.css',
	'acp/css/customize-controls.dev.css',
	'acp/css/ie-rtl.dev.css',
	'acp/css/ie.dev.css',
	'acp/css/install.dev.css',
	'acp/css/colors-fresh.dev.css',
	'libs/js/customize-base.dev.js',
	'libs/js/json2.dev.js',
	'libs/js/comment-reply.dev.js',
	'libs/js/customize-preview.dev.js',
	'libs/js/wplink.dev.js',
	'libs/js/tw-sack.dev.js',
	'libs/js/wp-list-revisions.dev.js',
	'libs/js/autosave.dev.js',
	'libs/js/admin-bar.dev.js',
	'libs/js/quicktags.dev.js',
	'libs/js/wp-ajax-response.dev.js',
	'libs/js/wp-pointer.dev.js',
	'libs/js/hoverIntent.dev.js',
	'libs/js/colorpicker.dev.js',
	'libs/js/wp-lists.dev.js',
	'libs/js/customize-loader.dev.js',
	'libs/js/jquery/jquery.table-hotkeys.dev.js',
	'libs/js/jquery/jquery.color.dev.js',
	'libs/js/jquery/jquery.color.js',
	'libs/js/jquery/jquery.hotkeys.dev.js',
	'libs/js/jquery/jquery.form.dev.js',
	'libs/js/jquery/suggest.dev.js',
	'acp/js/xfn.dev.js',
	'acp/js/set-post-thumbnail.dev.js',
	'acp/js/comment.dev.js',
	'acp/js/theme.dev.js',
	'acp/js/cat.dev.js',
	'acp/js/password-strength-meter.dev.js',
	'acp/js/user-profile.dev.js',
	'acp/js/theme-preview.dev.js',
	'acp/js/post.dev.js',
	'acp/js/media-upload.dev.js',
	'acp/js/word-count.dev.js',
	'acp/js/plugin-install.dev.js',
	'acp/js/edit-comments.dev.js',
	'acp/js/media-gallery.dev.js',
	'acp/js/custom-fields.dev.js',
	'acp/js/custom-background.dev.js',
	'acp/js/common.dev.js',
	'acp/js/inline-edit-tax.dev.js',
	'acp/js/gallery.dev.js',
	'acp/js/utils.dev.js',
	'acp/js/widgets.dev.js',
	'acp/js/wp-fullscreen.dev.js',
	'acp/js/nav-menu.dev.js',
	'acp/js/dashboard.dev.js',
	'acp/js/link.dev.js',
	'acp/js/user-suggest.dev.js',
	'acp/js/postbox.dev.js',
	'acp/js/tags.dev.js',
	'acp/js/image-edit.dev.js',
	'acp/js/media.dev.js',
	'acp/js/customize-controls.dev.js',
	'acp/js/inline-edit-post.dev.js',
	'acp/js/categories.dev.js',
	'acp/js/editor.dev.js',
	'libs/js/tinymce/plugins/wpeditimage/js/editimage.dev.js',
	'libs/js/tinymce/plugins/wpdialogs/js/popup.dev.js',
	'libs/js/tinymce/plugins/wpdialogs/js/wpdialog.dev.js',
	'libs/js/plupload/handlers.dev.js',
	'libs/js/plupload/wp-plupload.dev.js',
	'libs/js/swfupload/handlers.dev.js',
	'libs/js/jcrop/jquery.Jcrop.dev.js',
	'libs/js/jcrop/jquery.Jcrop.js',
	'libs/js/jcrop/jquery.Jcrop.css',
	'libs/js/imgareaselect/jquery.imgareaselect.dev.js',
	'libs/css/wp-pointer.dev.css',
	'libs/css/editor.dev.css',
	'libs/css/jquery-ui-dialog.dev.css',
	'libs/css/admin-bar-rtl.dev.css',
	'libs/css/admin-bar.dev.css',
	'libs/js/jquery/ui/jquery.effects.clip.min.js',
	'libs/js/jquery/ui/jquery.effects.scale.min.js',
	'libs/js/jquery/ui/jquery.effects.blind.min.js',
	'libs/js/jquery/ui/jquery.effects.core.min.js',
	'libs/js/jquery/ui/jquery.effects.shake.min.js',
	'libs/js/jquery/ui/jquery.effects.fade.min.js',
	'libs/js/jquery/ui/jquery.effects.explode.min.js',
	'libs/js/jquery/ui/jquery.effects.slide.min.js',
	'libs/js/jquery/ui/jquery.effects.drop.min.js',
	'libs/js/jquery/ui/jquery.effects.highlight.min.js',
	'libs/js/jquery/ui/jquery.effects.bounce.min.js',
	'libs/js/jquery/ui/jquery.effects.pulsate.min.js',
	'libs/js/jquery/ui/jquery.effects.transfer.min.js',
	'libs/js/jquery/ui/jquery.effects.fold.min.js',
	'acp/images/screenshots/captions-1.png',
	'acp/images/screenshots/captions-2.png',
	'acp/images/screenshots/flex-header-1.png',
	'acp/images/screenshots/flex-header-2.png',
	'acp/images/screenshots/flex-header-3.png',
	'acp/images/screenshots/flex-header-media-library.png',
	'acp/images/screenshots/theme-customizer.png',
	'acp/images/screenshots/twitter-embed-1.png',
	'acp/images/screenshots/twitter-embed-2.png',
	'acp/js/utils.js',
	'acp/options-privacy.php',
	'wp-app.php',
	'libs/class-wp-atom-server.php',
	'libs/js/tinymce/themes/advanced/skins/wp_theme/ui.css',
	// 3.5.2
	'libs/js/swfupload/swfupload-all.js',
	// 3.6
	'acp/js/revisions-js.php',
	'acp/images/screenshots',
	'acp/js/categories.js',
	'acp/js/categories.min.js',
	'acp/js/custom-fields.js',
	'acp/js/custom-fields.min.js',
	// 3.7
	'acp/js/cat.js',
	'acp/js/cat.min.js',
	'libs/js/tinymce/plugins/wpeditimage/js/editimage.min.js',
	// 3.8
	'libs/js/tinymce/themes/advanced/skins/wp_theme/img/page_bug.gif',
	'libs/js/tinymce/themes/advanced/skins/wp_theme/img/more_bug.gif',
	'libs/js/thickbox/tb-close-2x.png',
	'libs/js/thickbox/tb-close.png',
	'libs/images/wpmini-blue-2x.png',
	'libs/images/wpmini-blue.png',
	'acp/css/colors-fresh.css',
	'acp/css/colors-classic.css',
	'acp/css/colors-fresh.min.css',
	'acp/css/colors-classic.min.css',
	'acp/js/about.min.js',
	'acp/js/about.js',
	'acp/images/arrows-dark-vs-2x.png',
	'acp/images/wp-logo-vs.png',
	'acp/images/arrows-dark-vs.png',
	'acp/images/wp-logo.png',
	'acp/images/arrows-pr.png',
	'acp/images/arrows-dark.png',
	'acp/images/press-this.png',
	'acp/images/press-this-2x.png',
	'acp/images/arrows-vs-2x.png',
	'acp/images/welcome-icons.png',
	'acp/images/wp-logo-2x.png',
	'acp/images/stars-rtl-2x.png',
	'acp/images/arrows-dark-2x.png',
	'acp/images/arrows-pr-2x.png',
	'acp/images/menu-shadow-rtl.png',
	'acp/images/arrows-vs.png',
	'acp/images/about-search-2x.png',
	'acp/images/bubble_bg-rtl-2x.gif',
	'acp/images/wp-badge-2x.png',
	'acp/images/wordpress-logo-2x.png',
	'acp/images/bubble_bg-rtl.gif',
	'acp/images/wp-badge.png',
	'acp/images/menu-shadow.png',
	'acp/images/about-globe-2x.png',
	'acp/images/welcome-icons-2x.png',
	'acp/images/stars-rtl.png',
	'acp/images/wp-logo-vs-2x.png',
	'acp/images/about-updates-2x.png',
	// 3.9
	'acp/css/colors.css',
	'acp/css/colors.min.css',
	'acp/css/colors-rtl.css',
	'acp/css/colors-rtl.min.css',
	// Following files added back in 4.5 see #36083
	// 'acp/css/media-rtl.min.css',
	// 'acp/css/media.min.css',
	// 'acp/css/farbtastic-rtl.min.css',
	'acp/images/lock-2x.png',
	'acp/images/lock.png',
	'acp/js/theme-preview.js',
	'acp/js/theme-install.min.js',
	'acp/js/theme-install.js',
	'acp/js/theme-preview.min.js',
	'libs/js/plupload/plupload.html4.js',
	'libs/js/plupload/plupload.html5.js',
	'libs/js/plupload/changelog.txt',
	'libs/js/plupload/plupload.silverlight.js',
	'libs/js/plupload/plupload.flash.js',
	// Added back in 4.9 [41328], see #41755
	// 'libs/js/plupload/plupload.js',
	'libs/js/tinymce/plugins/spellchecker',
	'libs/js/tinymce/plugins/inlinepopups',
	'libs/js/tinymce/plugins/media/js',
	'libs/js/tinymce/plugins/media/css',
	'libs/js/tinymce/plugins/wordpress/img',
	'libs/js/tinymce/plugins/wpdialogs/js',
	'libs/js/tinymce/plugins/wpeditimage/img',
	'libs/js/tinymce/plugins/wpeditimage/js',
	'libs/js/tinymce/plugins/wpeditimage/css',
	'libs/js/tinymce/plugins/wpgallery/img',
	'libs/js/tinymce/plugins/wpfullscreen/css',
	'libs/js/tinymce/plugins/paste/js',
	'libs/js/tinymce/themes/advanced',
	'libs/js/tinymce/tiny_mce.js',
	'libs/js/tinymce/mark_loaded_src.js',
	'libs/js/tinymce/wp-tinymce-schema.js',
	'libs/js/tinymce/plugins/media/editor_plugin.js',
	'libs/js/tinymce/plugins/media/editor_plugin_src.js',
	'libs/js/tinymce/plugins/media/media.htm',
	'libs/js/tinymce/plugins/wpview/editor_plugin_src.js',
	'libs/js/tinymce/plugins/wpview/editor_plugin.js',
	'libs/js/tinymce/plugins/directionality/editor_plugin.js',
	'libs/js/tinymce/plugins/directionality/editor_plugin_src.js',
	'libs/js/tinymce/plugins/wordpress/editor_plugin.js',
	'libs/js/tinymce/plugins/wordpress/editor_plugin_src.js',
	'libs/js/tinymce/plugins/wpdialogs/editor_plugin_src.js',
	'libs/js/tinymce/plugins/wpdialogs/editor_plugin.js',
	'libs/js/tinymce/plugins/wpeditimage/editimage.html',
	'libs/js/tinymce/plugins/wpeditimage/editor_plugin.js',
	'libs/js/tinymce/plugins/wpeditimage/editor_plugin_src.js',
	'libs/js/tinymce/plugins/fullscreen/editor_plugin_src.js',
	'libs/js/tinymce/plugins/fullscreen/fullscreen.htm',
	'libs/js/tinymce/plugins/fullscreen/editor_plugin.js',
	'libs/js/tinymce/plugins/wplink/editor_plugin_src.js',
	'libs/js/tinymce/plugins/wplink/editor_plugin.js',
	'libs/js/tinymce/plugins/wpgallery/editor_plugin_src.js',
	'libs/js/tinymce/plugins/wpgallery/editor_plugin.js',
	'libs/js/tinymce/plugins/tabfocus/editor_plugin.js',
	'libs/js/tinymce/plugins/tabfocus/editor_plugin_src.js',
	'libs/js/tinymce/plugins/wpfullscreen/editor_plugin.js',
	'libs/js/tinymce/plugins/wpfullscreen/editor_plugin_src.js',
	'libs/js/tinymce/plugins/paste/editor_plugin.js',
	'libs/js/tinymce/plugins/paste/pasteword.htm',
	'libs/js/tinymce/plugins/paste/editor_plugin_src.js',
	'libs/js/tinymce/plugins/paste/pastetext.htm',
	'libs/js/tinymce/langs/wp-langs.php',
	// 4.1
	'libs/js/jquery/ui/jquery.ui.accordion.min.js',
	'libs/js/jquery/ui/jquery.ui.autocomplete.min.js',
	'libs/js/jquery/ui/jquery.ui.button.min.js',
	'libs/js/jquery/ui/jquery.ui.core.min.js',
	'libs/js/jquery/ui/jquery.ui.datepicker.min.js',
	'libs/js/jquery/ui/jquery.ui.dialog.min.js',
	'libs/js/jquery/ui/jquery.ui.draggable.min.js',
	'libs/js/jquery/ui/jquery.ui.droppable.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-blind.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-bounce.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-clip.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-drop.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-explode.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-fade.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-fold.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-highlight.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-pulsate.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-scale.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-shake.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-slide.min.js',
	'libs/js/jquery/ui/jquery.ui.effect-transfer.min.js',
	'libs/js/jquery/ui/jquery.ui.effect.min.js',
	'libs/js/jquery/ui/jquery.ui.menu.min.js',
	'libs/js/jquery/ui/jquery.ui.mouse.min.js',
	'libs/js/jquery/ui/jquery.ui.position.min.js',
	'libs/js/jquery/ui/jquery.ui.progressbar.min.js',
	'libs/js/jquery/ui/jquery.ui.resizable.min.js',
	'libs/js/jquery/ui/jquery.ui.selectable.min.js',
	'libs/js/jquery/ui/jquery.ui.slider.min.js',
	'libs/js/jquery/ui/jquery.ui.sortable.min.js',
	'libs/js/jquery/ui/jquery.ui.spinner.min.js',
	'libs/js/jquery/ui/jquery.ui.tabs.min.js',
	'libs/js/jquery/ui/jquery.ui.tooltip.min.js',
	'libs/js/jquery/ui/jquery.ui.widget.min.js',
	'libs/js/tinymce/skins/wordpress/images/dashicon-no-alt.png',
	// 4.3
	'acp/js/wp-fullscreen.js',
	'acp/js/wp-fullscreen.min.js',
	'libs/js/tinymce/wp-mce-help.php',
	'libs/js/tinymce/plugins/wpfullscreen',
	// 4.5
	'libs/theme-compat/comments-popup.php',
	// 4.6
	'acp/includes/class-wp-automatic-upgrader.php', // Wrong file name, see #37628.
	// 4.8
	'libs/js/tinymce/plugins/wpembed',
	'libs/js/tinymce/plugins/media/moxieplayer.swf',
	'libs/js/tinymce/skins/lightgray/fonts/readme.md',
	'libs/js/tinymce/skins/lightgray/fonts/tinymce-small.json',
	'libs/js/tinymce/skins/lightgray/fonts/tinymce.json',
	'libs/js/tinymce/skins/lightgray/skin.ie7.min.css',
	// 4.9
	'acp/css/press-this-editor-rtl.css',
	'acp/css/press-this-editor-rtl.min.css',
	'acp/css/press-this-editor.css',
	'acp/css/press-this-editor.min.css',
	'acp/css/press-this-rtl.css',
	'acp/css/press-this-rtl.min.css',
	'acp/css/press-this.css',
	'acp/css/press-this.min.css',
	'acp/includes/class-wp-press-this.php',
	'acp/js/bookmarklet.js',
	'acp/js/bookmarklet.min.js',
	'acp/js/press-this.js',
	'acp/js/press-this.min.js',
	'libs/js/mediaelement/background.png',
	'libs/js/mediaelement/bigplay.png',
	'libs/js/mediaelement/bigplay.svg',
	'libs/js/mediaelement/controls.png',
	'libs/js/mediaelement/controls.svg',
	'libs/js/mediaelement/flashmediaelement.swf',
	'libs/js/mediaelement/froogaloop.min.js',
	'libs/js/mediaelement/jumpforward.png',
	'libs/js/mediaelement/loading.gif',
	'libs/js/mediaelement/silverlightmediaelement.xap',
	'libs/js/mediaelement/skipback.png',
	'libs/js/plupload/plupload.flash.swf',
	'libs/js/plupload/plupload.full.min.js',
	'libs/js/plupload/plupload.silverlight.xap',
	'libs/js/swfupload/plugins',
	'libs/js/swfupload/swfupload.swf',
	// 4.9.2
	'libs/js/mediaelement/lang',
	'libs/js/mediaelement/lang/ca.js',
	'libs/js/mediaelement/lang/cs.js',
	'libs/js/mediaelement/lang/de.js',
	'libs/js/mediaelement/lang/es.js',
	'libs/js/mediaelement/lang/fa.js',
	'libs/js/mediaelement/lang/fr.js',
	'libs/js/mediaelement/lang/hr.js',
	'libs/js/mediaelement/lang/hu.js',
	'libs/js/mediaelement/lang/it.js',
	'libs/js/mediaelement/lang/ja.js',
	'libs/js/mediaelement/lang/ko.js',
	'libs/js/mediaelement/lang/nl.js',
	'libs/js/mediaelement/lang/pl.js',
	'libs/js/mediaelement/lang/pt.js',
	'libs/js/mediaelement/lang/ro.js',
	'libs/js/mediaelement/lang/ru.js',
	'libs/js/mediaelement/lang/sk.js',
	'libs/js/mediaelement/lang/sv.js',
	'libs/js/mediaelement/lang/uk.js',
	'libs/js/mediaelement/lang/zh-cn.js',
	'libs/js/mediaelement/lang/zh.js',
	'libs/js/mediaelement/mediaelement-flash-audio-ogg.swf',
	'libs/js/mediaelement/mediaelement-flash-audio.swf',
	'libs/js/mediaelement/mediaelement-flash-video-hls.swf',
	'libs/js/mediaelement/mediaelement-flash-video-mdash.swf',
	'libs/js/mediaelement/mediaelement-flash-video.swf',
	'libs/js/mediaelement/renderers/dailymotion.js',
	'libs/js/mediaelement/renderers/dailymotion.min.js',
	'libs/js/mediaelement/renderers/facebook.js',
	'libs/js/mediaelement/renderers/facebook.min.js',
	'libs/js/mediaelement/renderers/soundcloud.js',
	'libs/js/mediaelement/renderers/soundcloud.min.js',
	'libs/js/mediaelement/renderers/twitch.js',
	'libs/js/mediaelement/renderers/twitch.min.js',
	// 5.0
	'libs/js/codemirror/jshint.js',
	// 5.1
	'libs/random_compat/random_bytes_openssl.php',
	'libs/js/tinymce/wp-tinymce.js.gz',
);

/**
 * Stores new files in content to copy
 *
 * The contents of this array indicate any new bundled plugins/themes which
 * should be installed with the WordPress Upgrade. These items will not be
 * re-installed in future upgrades, this behaviour is controlled by the
 * introduced version present here being older than the current installed version.
 *
 * The content of this array should follow the following format:
 * Filename (relative to content) => Introduced version
 * Directories should be noted by suffixing it with a trailing slash (/)
 *
 * @since 3.2.0
 * @since 4.7.0 New themes were not automatically installed for 4.4-4.6 on
 *              upgrade. New themes are now installed again. To disable new
 *              themes from being installed on upgrade, explicitly define
 *              CORE_UPGRADE_SKIP_NEW_BUNDLED as false.
 * @global array $_new_bundled_files
 * @var array
 * @name $_new_bundled_files
 */
global $_new_bundled_files;

$_new_bundled_files = array(
	'plugins/akismet/'        => '2.0',
	'themes/twentyten/'       => '3.0',
	'themes/twentyeleven/'    => '3.2',
	'themes/twentytwelve/'    => '3.5',
	'themes/twentythirteen/'  => '3.6',
	'themes/twentyfourteen/'  => '3.8',
	'themes/twentyfifteen/'   => '4.1',
	'themes/twentysixteen/'   => '4.4',
	'themes/twentyseventeen/' => '4.7',
	'themes/twentynineteen/'  => '5.0',
);

/**
 * Upgrades the core of WordPress.
 *
 * This will create a .maintenance file at the base of the WordPress directory
 * to ensure that people can not access the web site, when the files are being
 * copied to their locations.
 *
 * The files in the `$_old_files` list will be removed and the new files
 * copied from the zip file after the database is upgraded.
 *
 * The files in the `$_new_bundled_files` list will be added to the installation
 * if the version is greater than or equal to the old version being upgraded.
 *
 * The steps for the upgrader for after the new release is downloaded and
 * unzipped is:
 *   1. Test unzipped location for select files to ensure that unzipped worked.
 *   2. Create the .maintenance file in current WordPress base.
 *   3. Copy new WordPress directory over old WordPress files.
 *   4. Upgrade WordPress to new version.
 *     4.1. Copy all files/folders other than content
 *     4.2. Copy any language files to WP_LANG_DIR (which may differ from WP_CONTENT_DIR
 *     4.3. Copy any new bundled themes/plugins to their respective locations
 *   5. Delete new WordPress directory path.
 *   6. Delete .maintenance file.
 *   7. Remove old files.
 *   8. Delete 'update_core' option.
 *
 * There are several areas of failure. For instance if PHP times out before step
 * 6, then you will not be able to access any portion of your site. Also, since
 * the upgrade will not continue where it left off, you will not be able to
 * automatically remove old files and remove the 'update_core' option. This
 * isn't that bad.
 *
 * If the copy of the new WordPress over the old fails, then the worse is that
 * the new WordPress directory will remain.
 *
 * If it is assumed that every file will be copied over, including plugins and
 * themes, then if you edit the default theme, you should rename it, so that
 * your changes remain.
 *
 * @since 2.7.0
 *
 * @global WP_Filesystem_Base $wp_filesystem          WordPress filesystem subclass.
 * @global array              $_old_files
 * @global array              $_new_bundled_files
 * @global wpdb               $wpdb
 * @global string             $wp_version
 * @global string             $required_php_version
 * @global string             $required_mysql_version
 *
 * @param string $from New release unzipped path.
 * @param string $to   Path to old WordPress installation.
 * @return WP_Error|null WP_Error on failure, null on success.
 */
function update_core( $from, $to ) {
	global $wp_filesystem, $_old_files, $_new_bundled_files, $wpdb;

	@set_time_limit( 300 );

	/**
	 * Filters feedback messages displayed during the core update process.
	 *
	 * The filter is first evaluated after the zip file for the latest version
	 * has been downloaded and unzipped. It is evaluated five more times during
	 * the process:
	 *
	 * 1. Before WordPress begins the core upgrade process.
	 * 2. Before Maintenance Mode is enabled.
	 * 3. Before WordPress begins copying over the necessary files.
	 * 4. Before Maintenance Mode is disabled.
	 * 5. Before the database is upgraded.
	 *
	 * @since 2.5.0
	 *
	 * @param string $feedback The core update feedback messages.
	 */
	apply_filters( 'update_feedback', __( 'Verifying the unpacked files&#8230;' ) );

	// Sanity check the unzipped distribution.
	$distro = '';
	$roots  = array( '/wordpress/', '/wordpress-mu/' );
	foreach ( $roots as $root ) {
		if ( $wp_filesystem->exists( $from . $root . 'readme.html' ) && $wp_filesystem->exists( $from . $root . 'libs/version.php' ) ) {
			$distro = $root;
			break;
		}
	}
	if ( ! $distro ) {
		$wp_filesystem->delete( $from, true );
		return new WP_Error( 'insane_distro', __( 'The update could not be unpacked' ) );
	}

	/*
	 * Import $wp_version, $required_php_version, and $required_mysql_version from the new version.
	 * DO NOT globalise any variables imported from `version-current.php` in this function.
	 *
	 * BC Note: $wp_filesystem->wp_content_dir() returned unslashed pre-2.8
	 */
	$versions_file = trailingslashit( $wp_filesystem->wp_content_dir() ) . 'upgrade/version-current.php';
	if ( ! $wp_filesystem->copy( $from . $distro . 'libs/version.php', $versions_file ) ) {
		$wp_filesystem->delete( $from, true );
		return new WP_Error( 'copy_failed_for_version_file', __( 'The update cannot be installed because we will be unable to copy some files. This is usually due to inconsistent file permissions.' ), 'libs/version.php' );
	}

	$wp_filesystem->chmod( $versions_file, FS_CHMOD_FILE );
	require( WP_CONTENT_DIR . '/upgrade/version-current.php' );
	$wp_filesystem->delete( $versions_file );

	$php_version       = phpversion();
	$mysql_version     = $wpdb->db_version();
	$old_wp_version    = $GLOBALS['wp_version']; // The version of WordPress we're updating from
	$development_build = ( false !== strpos( $old_wp_version . $wp_version, '-' ) ); // a dash in the version indicates a Development release
	$php_compat        = version_compare( $php_version, $required_php_version, '>=' );
	if ( file_exists( WP_CONTENT_DIR . '/db.php' ) && empty( $wpdb->is_mysql ) ) {
		$mysql_compat = true;
	} else {
		$mysql_compat = version_compare( $mysql_version, $required_mysql_version, '>=' );
	}

	if ( ! $mysql_compat || ! $php_compat ) {
		$wp_filesystem->delete( $from, true );
	}

	if ( ! $mysql_compat && ! $php_compat ) {
		return new WP_Error( 'php_mysql_not_compatible', sprintf( __( 'The update cannot be installed because WordPress %1$s requires PHP version %2$s or higher and MySQL version %3$s or higher. You are running PHP version %4$s and MySQL version %5$s.' ), $wp_version, $required_php_version, $required_mysql_version, $php_version, $mysql_version ) );
	} elseif ( ! $php_compat ) {
		return new WP_Error( 'php_not_compatible', sprintf( __( 'The update cannot be installed because WordPress %1$s requires PHP version %2$s or higher. You are running version %3$s.' ), $wp_version, $required_php_version, $php_version ) );
	} elseif ( ! $mysql_compat ) {
		return new WP_Error( 'mysql_not_compatible', sprintf( __( 'The update cannot be installed because WordPress %1$s requires MySQL version %2$s or higher. You are running version %3$s.' ), $wp_version, $required_mysql_version, $mysql_version ) );
	}

	/** This filter is documented in acp/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Preparing to install the latest version&#8230;' ) );

	// Don't copy content, we'll deal with that below
	// We also copy version.php last so failed updates report their old version
	$skip              = array( 'content', 'libs/version.php' );
	$check_is_writable = array();

	// Check to see which files don't really need updating - only available for 3.7 and higher
	if ( function_exists( 'get_core_checksums' ) ) {
		// Find the local version of the working directory
		$working_dir_local = WP_CONTENT_DIR . '/upgrade/' . basename( $from ) . $distro;

		$checksums = get_core_checksums( $wp_version, isset( $wp_local_package ) ? $wp_local_package : 'en_US' );
		if ( is_array( $checksums ) && isset( $checksums[ $wp_version ] ) ) {
			$checksums = $checksums[ $wp_version ]; // Compat code for 3.7-beta2
		}
		if ( is_array( $checksums ) ) {
			foreach ( $checksums as $file => $checksum ) {
				if ( 'content' == substr( $file, 0, 10 ) ) {
					continue;
				}
				if ( ! file_exists( ABSPATH . $file ) ) {
					continue;
				}
				if ( ! file_exists( $working_dir_local . $file ) ) {
					continue;
				}
				if ( '.' === dirname( $file ) && in_array( pathinfo( $file, PATHINFO_EXTENSION ), array( 'html', 'txt' ) ) ) {
					continue;
				}
				if ( md5_file( ABSPATH . $file ) === $checksum ) {
					$skip[] = $file;
				} else {
					$check_is_writable[ $file ] = ABSPATH . $file;
				}
			}
		}
	}

	// If we're using the direct method, we can predict write failures that are due to permissions.
	if ( $check_is_writable && 'direct' === $wp_filesystem->method ) {
		$files_writable = array_filter( $check_is_writable, array( $wp_filesystem, 'is_writable' ) );
		if ( $files_writable !== $check_is_writable ) {
			$files_not_writable = array_diff_key( $check_is_writable, $files_writable );
			foreach ( $files_not_writable as $relative_file_not_writable => $file_not_writable ) {
				// If the writable check failed, chmod file to 0644 and try again, same as copy_dir().
				$wp_filesystem->chmod( $file_not_writable, FS_CHMOD_FILE );
				if ( $wp_filesystem->is_writable( $file_not_writable ) ) {
					unset( $files_not_writable[ $relative_file_not_writable ] );
				}
			}

			// Store package-relative paths (the key) of non-writable files in the WP_Error object.
			$error_data = version_compare( $old_wp_version, '3.7-beta2', '>' ) ? array_keys( $files_not_writable ) : '';

			if ( $files_not_writable ) {
				return new WP_Error( 'files_not_writable', __( 'The update cannot be installed because we will be unable to copy some files. This is usually due to inconsistent file permissions.' ), implode( ', ', $error_data ) );
			}
		}
	}

	/** This filter is documented in acp/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Enabling Maintenance mode&#8230;' ) );
	// Create maintenance file to signal that we are upgrading
	$maintenance_string = '<?php $upgrading = ' . time() . '; ?>';
	$maintenance_file   = $to . '.maintenance';
	$wp_filesystem->delete( $maintenance_file );
	$wp_filesystem->put_contents( $maintenance_file, $maintenance_string, FS_CHMOD_FILE );

	/** This filter is documented in acp/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Copying the required files&#8230;' ) );
	// Copy new versions of WP files into place.
	$result = _copy_dir( $from . $distro, $to, $skip );
	if ( is_wp_error( $result ) ) {
		$result = new WP_Error( $result->get_error_code(), $result->get_error_message(), substr( $result->get_error_data(), strlen( $to ) ) );
	}

	// Since we know the core files have copied over, we can now copy the version file
	if ( ! is_wp_error( $result ) ) {
		if ( ! $wp_filesystem->copy( $from . $distro . 'libs/version.php', $to . 'libs/version.php', true /* overwrite */ ) ) {
			$wp_filesystem->delete( $from, true );
			$result = new WP_Error( 'copy_failed_for_version_file', __( 'The update cannot be installed because we will be unable to copy some files. This is usually due to inconsistent file permissions.' ), 'libs/version.php' );
		}
		$wp_filesystem->chmod( $to . 'libs/version.php', FS_CHMOD_FILE );
	}

	// Check to make sure everything copied correctly, ignoring the contents of content
	$skip   = array( 'content' );
	$failed = array();
	if ( isset( $checksums ) && is_array( $checksums ) ) {
		foreach ( $checksums as $file => $checksum ) {
			if ( 'content' == substr( $file, 0, 10 ) ) {
				continue;
			}
			if ( ! file_exists( $working_dir_local . $file ) ) {
				continue;
			}
			if ( '.' === dirname( $file ) && in_array( pathinfo( $file, PATHINFO_EXTENSION ), array( 'html', 'txt' ) ) ) {
				$skip[] = $file;
				continue;
			}
			if ( file_exists( ABSPATH . $file ) && md5_file( ABSPATH . $file ) == $checksum ) {
				$skip[] = $file;
			} else {
				$failed[] = $file;
			}
		}
	}

	// Some files didn't copy properly
	if ( ! empty( $failed ) ) {
		$total_size = 0;
		foreach ( $failed as $file ) {
			if ( file_exists( $working_dir_local . $file ) ) {
				$total_size += filesize( $working_dir_local . $file );
			}
		}

		// If we don't have enough free space, it isn't worth trying again.
		// Unlikely to be hit due to the check in unzip_file().
		$available_space = @disk_free_space( ABSPATH );
		if ( $available_space && $total_size >= $available_space ) {
			$result = new WP_Error( 'disk_full', __( 'There is not enough free disk space to complete the update.' ) );
		} else {
			$result = _copy_dir( $from . $distro, $to, $skip );
			if ( is_wp_error( $result ) ) {
				$result = new WP_Error( $result->get_error_code() . '_retry', $result->get_error_message(), substr( $result->get_error_data(), strlen( $to ) ) );
			}
		}
	}

	// Custom Content Directory needs updating now.
	// Copy Languages
	if ( ! is_wp_error( $result ) && $wp_filesystem->is_dir( $from . $distro . 'content/languages' ) ) {
		if ( WP_LANG_DIR != ABSPATH . WPINC . '/languages' || @is_dir( WP_LANG_DIR ) ) {
			$lang_dir = WP_LANG_DIR;
		} else {
			$lang_dir = WP_CONTENT_DIR . '/languages';
		}

		if ( ! @is_dir( $lang_dir ) && 0 === strpos( $lang_dir, ABSPATH ) ) { // Check the language directory exists first
			$wp_filesystem->mkdir( $to . str_replace( ABSPATH, '', $lang_dir ), FS_CHMOD_DIR ); // If it's within the ABSPATH we can handle it here, otherwise they're out of luck.
			clearstatcache(); // for FTP, Need to clear the stat cache
		}

		if ( @is_dir( $lang_dir ) ) {
			$wp_lang_dir = $wp_filesystem->find_folder( $lang_dir );
			if ( $wp_lang_dir ) {
				$result = copy_dir( $from . $distro . 'content/languages/', $wp_lang_dir );
				if ( is_wp_error( $result ) ) {
					$result = new WP_Error( $result->get_error_code() . '_languages', $result->get_error_message(), substr( $result->get_error_data(), strlen( $wp_lang_dir ) ) );
				}
			}
		}
	}

	/** This filter is documented in acp/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Disabling Maintenance mode&#8230;' ) );
	// Remove maintenance file, we're done with potential site-breaking changes
	$wp_filesystem->delete( $maintenance_file );

	// 3.5 -> 3.5+ - an empty twentytwelve directory was created upon upgrade to 3.5 for some users, preventing installation of Twenty Twelve.
	if ( '3.5' == $old_wp_version ) {
		if ( is_dir( WP_CONTENT_DIR . '/themes/twentytwelve' ) && ! file_exists( WP_CONTENT_DIR . '/themes/twentytwelve/style.css' ) ) {
			$wp_filesystem->delete( $wp_filesystem->wp_themes_dir() . 'twentytwelve/' );
		}
	}

	// Copy New bundled plugins & themes
	// This gives us the ability to install new plugins & themes bundled with future versions of WordPress whilst avoiding the re-install upon upgrade issue.
	// $development_build controls us overwriting bundled themes and plugins when a non-stable release is being updated
	if ( ! is_wp_error( $result ) && ( ! defined( 'CORE_UPGRADE_SKIP_NEW_BUNDLED' ) || ! CORE_UPGRADE_SKIP_NEW_BUNDLED ) ) {
		foreach ( (array) $_new_bundled_files as $file => $introduced_version ) {
			// If a $development_build or if $introduced version is greater than what the site was previously running
			if ( $development_build || version_compare( $introduced_version, $old_wp_version, '>' ) ) {
				$directory             = ( '/' == $file[ strlen( $file ) - 1 ] );
				list($type, $filename) = explode( '/', $file, 2 );

				// Check to see if the bundled items exist before attempting to copy them
				if ( ! $wp_filesystem->exists( $from . $distro . 'content/' . $file ) ) {
					continue;
				}

				if ( 'plugins' == $type ) {
					$dest = $wp_filesystem->wp_plugins_dir();
				} elseif ( 'themes' == $type ) {
					$dest = trailingslashit( $wp_filesystem->wp_themes_dir() ); // Back-compat, ::wp_themes_dir() did not return trailingslash'd pre-3.2
				} else {
					continue;
				}

				if ( ! $directory ) {
					if ( ! $development_build && $wp_filesystem->exists( $dest . $filename ) ) {
						continue;
					}

					if ( ! $wp_filesystem->copy( $from . $distro . 'content/' . $file, $dest . $filename, FS_CHMOD_FILE ) ) {
						$result = new WP_Error( "copy_failed_for_new_bundled_$type", __( 'Could not copy file.' ), $dest . $filename );
					}
				} else {
					if ( ! $development_build && $wp_filesystem->is_dir( $dest . $filename ) ) {
						continue;
					}

					$wp_filesystem->mkdir( $dest . $filename, FS_CHMOD_DIR );
					$_result = copy_dir( $from . $distro . 'content/' . $file, $dest . $filename );

					// If a error occurs partway through this final step, keep the error flowing through, but keep process going.
					if ( is_wp_error( $_result ) ) {
						if ( ! is_wp_error( $result ) ) {
							$result = new WP_Error;
						}
						$result->add( $_result->get_error_code() . "_$type", $_result->get_error_message(), substr( $_result->get_error_data(), strlen( $dest ) ) );
					}
				}
			}
		} //end foreach
	}

	// Handle $result error from the above blocks
	if ( is_wp_error( $result ) ) {
		$wp_filesystem->delete( $from, true );
		return $result;
	}

	// Remove old files
	foreach ( $_old_files as $old_file ) {
		$old_file = $to . $old_file;
		if ( ! $wp_filesystem->exists( $old_file ) ) {
			continue;
		}

		// If the file isn't deleted, try writing an empty string to the file instead.
		if ( ! $wp_filesystem->delete( $old_file, true ) && $wp_filesystem->is_file( $old_file ) ) {
			$wp_filesystem->put_contents( $old_file, '' );
		}
	}

	// Remove any Genericons example.html's from the filesystem
	_upgrade_422_remove_genericons();

	// Remove the REST API plugin if its version is Beta 4 or lower
	_upgrade_440_force_deactivate_incompatible_plugins();

	// Upgrade DB with separate request
	/** This filter is documented in acp/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Upgrading database&#8230;' ) );
	$db_upgrade_url = admin_url( 'upgrade.php?step=upgrade_db' );
	wp_remote_post( $db_upgrade_url, array( 'timeout' => 60 ) );

	// Clear the cache to prevent an update_option() from saving a stale db_version to the cache
	wp_cache_flush();
	// (Not all cache back ends listen to 'flush')
	wp_cache_delete( 'alloptions', 'options' );

	// Remove working directory
	$wp_filesystem->delete( $from, true );

	// Force refresh of update information
	if ( function_exists( 'delete_site_transient' ) ) {
		delete_site_transient( 'update_core' );
	} else {
		delete_option( 'update_core' );
	}

	/**
	 * Fires after WordPress core has been successfully updated.
	 *
	 * @since 3.3.0
	 *
	 * @param string $wp_version The current WordPress version.
	 */
	do_action( '_core_updated_successfully', $wp_version );

	// Clear the option that blocks auto updates after failures, now that we've been successful.
	if ( function_exists( 'delete_site_option' ) ) {
		delete_site_option( 'auto_core_update_failed' );
	}

	return $wp_version;
}

/**
 * Copies a directory from one location to another via the WordPress Filesystem Abstraction.
 * Assumes that WP_Filesystem() has already been called and setup.
 *
 * This is a temporary function for the 3.1 -> 3.2 upgrade, as well as for those upgrading to
 * 3.7+
 *
 * @ignore
 * @since 3.2.0
 * @since 3.7.0 Updated not to use a regular expression for the skip list
 * @see copy_dir()
 *
 * @global WP_Filesystem_Base $wp_filesystem
 *
 * @param string $from     source directory
 * @param string $to       destination directory
 * @param array $skip_list a list of files/folders to skip copying
 * @return mixed WP_Error on failure, True on success.
 */
function _copy_dir( $from, $to, $skip_list = array() ) {
	global $wp_filesystem;

	$dirlist = $wp_filesystem->dirlist( $from );

	$from = trailingslashit( $from );
	$to   = trailingslashit( $to );

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( in_array( $filename, $skip_list ) ) {
			continue;
		}

		if ( 'f' == $fileinfo['type'] ) {
			if ( ! $wp_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
				// If copy failed, chmod file to 0644 and try again.
				$wp_filesystem->chmod( $to . $filename, FS_CHMOD_FILE );
				if ( ! $wp_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
					return new WP_Error( 'copy_failed__copy_dir', __( 'Could not copy file.' ), $to . $filename );
				}
			}
		} elseif ( 'd' == $fileinfo['type'] ) {
			if ( ! $wp_filesystem->is_dir( $to . $filename ) ) {
				if ( ! $wp_filesystem->mkdir( $to . $filename, FS_CHMOD_DIR ) ) {
					return new WP_Error( 'mkdir_failed__copy_dir', __( 'Could not create directory.' ), $to . $filename );
				}
			}

			/*
			 * Generate the $sub_skip_list for the subdirectory as a sub-set
			 * of the existing $skip_list.
			 */
			$sub_skip_list = array();
			foreach ( $skip_list as $skip_item ) {
				if ( 0 === strpos( $skip_item, $filename . '/' ) ) {
					$sub_skip_list[] = preg_replace( '!^' . preg_quote( $filename, '!' ) . '/!i', '', $skip_item );
				}
			}

			$result = _copy_dir( $from . $filename, $to . $filename, $sub_skip_list );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}
	}
	return true;
}

/**
 * Redirect to the About WordPress page after a successful upgrade.
 *
 * This function is only needed when the existing installation is older than 3.4.0.
 *
 * @since 3.3.0
 *
 * @global string $wp_version
 * @global string $pagenow
 * @global string $action
 *
 * @param string $new_version
 */
function _redirect_to_about_wordpress( $new_version ) {
	global $wp_version, $pagenow, $action;

	if ( version_compare( $wp_version, '3.4-RC1', '>=' ) ) {
		return;
	}

	// Ensure we only run this on the update-core.php page. The Core_Upgrader may be used in other contexts.
	if ( 'update-core.php' != $pagenow ) {
		return;
	}

	if ( 'do-core-upgrade' != $action && 'do-core-reinstall' != $action ) {
		return;
	}

	// Load the updated default text localization domain for new strings.
	load_default_textdomain();

	// See do_core_upgrade()
	show_message( __( 'WordPress updated successfully' ) );

	// self_admin_url() won't exist when upgrading from <= 3.0, so relative URLs are intentional.
	show_message( '<span class="hide-if-no-js">' . sprintf( __( 'Welcome to WordPress %1$s. You will be redirected to the About WordPress screen. If not, click <a href="%2$s">here</a>.' ), $new_version, 'about.php?updated' ) . '</span>' );
	show_message( '<span class="hide-if-js">' . sprintf( __( 'Welcome to WordPress %1$s. <a href="%2$s">Learn more</a>.' ), $new_version, 'about.php?updated' ) . '</span>' );
	echo '</div>';
	?>
<script type="text/javascript">
window.location = 'about.php?updated';
</script>
	<?php

	// Include admin-footer.php and exit.
	include(ABSPATH . 'acp/admin-footer.php');
	exit();
}

/**
 * Cleans up Genericons example files.
 *
 * @since 4.2.2
 *
 * @global array              $wp_theme_directories
 * @global WP_Filesystem_Base $wp_filesystem
 */
function _upgrade_422_remove_genericons() {
	global $wp_theme_directories, $wp_filesystem;

	// A list of the affected files using the filesystem absolute paths.
	$affected_files = array();

	// Themes
	foreach ( $wp_theme_directories as $directory ) {
		$affected_theme_files = _upgrade_422_find_genericons_files_in_folder( $directory );
		$affected_files       = array_merge( $affected_files, $affected_theme_files );
	}

	// Plugins
	$affected_plugin_files = _upgrade_422_find_genericons_files_in_folder( WP_PLUGIN_DIR );
	$affected_files        = array_merge( $affected_files, $affected_plugin_files );

	foreach ( $affected_files as $file ) {
		$gen_dir = $wp_filesystem->find_folder( trailingslashit( dirname( $file ) ) );
		if ( empty( $gen_dir ) ) {
			continue;
		}

		// The path when the file is accessed via WP_Filesystem may differ in the case of FTP
		$remote_file = $gen_dir . basename( $file );

		if ( ! $wp_filesystem->exists( $remote_file ) ) {
			continue;
		}

		if ( ! $wp_filesystem->delete( $remote_file, false, 'f' ) ) {
			$wp_filesystem->put_contents( $remote_file, '' );
		}
	}
}

/**
 * Recursively find Genericons example files in a given folder.
 *
 * @ignore
 * @since 4.2.2
 *
 * @param string $directory Directory path. Expects trailingslashed.
 * @return array
 */
function _upgrade_422_find_genericons_files_in_folder( $directory ) {
	$directory = trailingslashit( $directory );
	$files     = array();

	if ( file_exists( "{$directory}example.html" ) && false !== strpos( file_get_contents( "{$directory}example.html" ), '<title>Genericons</title>' ) ) {
		$files[] = "{$directory}example.html";
	}

	$dirs = glob( $directory . '*', GLOB_ONLYDIR );
	if ( $dirs ) {
		foreach ( $dirs as $dir ) {
			$files = array_merge( $files, _upgrade_422_find_genericons_files_in_folder( $dir ) );
		}
	}

	return $files;
}

/**
 * @ignore
 * @since 4.4.0
 */
function _upgrade_440_force_deactivate_incompatible_plugins() {
	if ( defined( 'REST_API_VERSION' ) && version_compare( REST_API_VERSION, '2.0-beta4', '<=' ) ) {
		deactivate_plugins( array( 'rest-api/plugin.php' ), true );
	}
}
