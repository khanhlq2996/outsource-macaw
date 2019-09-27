<?php
/**
 * Dashboard Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

/** Load WordPress dashboard API */
require_once(ABSPATH . 'acp/includes/dashboard.php');

wp_dashboard_setup();

wp_enqueue_script( 'dashboard' );

if ( current_user_can( 'install_plugins' ) ) {
    wp_enqueue_script( 'plugin-install' );
    wp_enqueue_script( 'updates' );
}
if ( current_user_can( 'upload_files' ) ) {
    wp_enqueue_script( 'media-upload' );
}
add_thickbox();

if ( wp_is_mobile() ) {
    wp_enqueue_script( 'jquery-touch-punch' );
}

$title       = __( 'Dashboard' );
$parent_file = 'index.php';
unset( $help );

include(ABSPATH . 'acp/admin-header.php');
?>
    <div class="wrap">
        <h1><?php echo esc_html( $title ); ?></h1>

        <div id="dashboard-widgets-wrap" style="margin-top: 30px;">
            <div class="row">
                <div class="col col-sm-4 col-md-3">
                    <div class="dashboard-item">
                        <div class="dashboard-item-head">
                            <i class="ion ion-ios-list-box"></i>
                            <span>Bài viết</span>
                        </div>
                        <div class="dashboard-item-menu">
                            <ul>
                                <li><a href="<?php echo get_home_url(); ?>/acp/post-new.php">Thêm Bài viết</a></li>
                                <li><a href="<?php echo get_home_url(); ?>/acp/edit.php">Danh sách Bài viết</a></li>
                                <li><a href="<?php echo get_home_url(); ?>/acp/edit-tags.php?taxonomy=category">Danh mục Bài viết</a></li>
                                <li><a href="<?php echo get_home_url(); ?>/acp/edit-tags.php?taxonomy=post_tag">Tags Bài viết</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col col-sm-4 col-md-3 hide-if-no-customize">
                    <div class="dashboard-item">
                        <div class="dashboard-item-head">
                            <i class="ion ion-ios-paper"></i>
                            <span>Trang</span>
                        </div>
                        <div class="dashboard-item-menu">
                            <ul>
                                <li><a href="<?php echo get_home_url(); ?>/acp/post-new.php?post_type=page">Thêm Trang</a></li>
                                <li><a href="<?php echo get_home_url(); ?>/acp/edit.php?post_type=page">Danh sách Trang</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col col-sm-4 col-md-3">
                    <div class="dashboard-item">
                        <div class="dashboard-item-head">
                            <i class="ion ion-ios-images"></i>
                            <span>Thư viện</span>
                        </div>
                        <div class="dashboard-item-menu">
                            <ul>
                                <li><a href="<?php echo get_home_url(); ?>/acp/media-new.php">Tải lên</a></li>
                                <li><a href="<?php echo get_home_url(); ?>/acp/upload.php">Danh sách</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col col-sm-4 col-md-3 hide-if-no-customize">
                    <div class="dashboard-item">
                        <div class="dashboard-item-head">
                            <i class="ion ion-ios-list"></i>
                            <span>Menu</span>
                        </div>
                        <div class="dashboard-item-menu">
                            <ul>
                                <li><a href="<?php echo get_home_url(); ?>/acp/nav-menus.php?action=edit&menu=0">Thêm Menu</a></li>
                                <li><a href="<?php echo get_home_url(); ?>/acp/nav-menus.php">Danh sách Menu</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col col-sm-4 col-md-3 hide-if-no-customize">
                    <div class="dashboard-item">
                        <div class="dashboard-item-head">
                            <i class="ion ion-ios-people"></i>
                            <span>Thành viên</span>
                        </div>
                        <div class="dashboard-item-menu">
                            <ul>
                                <li><a href="<?php echo get_home_url(); ?>/acp/user-new.php">Thành viên</a></li>
                                <li><a href="<?php echo get_home_url(); ?>/acp/users.php">Danh sách Thành viên</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 30px;">
                <div class="col-xs-12 col-sm-6 col-md-63">
                    <div class="guide-box dashboard-item">
                        <div class="dashboard-item-head">
                            <i class="ion ion-md-help-circle-outline"></i>
                            <span style="font-size: 150%; line-height: 24px;">Hướng dẫn sử dụng</span>
                        </div>

                        <div class="guide-menu">
                            <ul>
                                <li><a href="#">Hướng dẫn sử dung UX Builder</a></li>
                                <li><a href="#">Hướng dẫn thêm/sửa/xóa Bài viết</a></li>
                                <li><a href="#">Hướng dẫn thêm/sửa/xóa Danh mục</a></li>
                                <li><a href="#">Hướng dẫn thêm/sửa/xóa Trang</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-63">
                    <div class="support-box dashboard-item">
                        <div class="dashboard-item-head">
                            <i style="content: &quot;&quot;;background: url(/acp/images/khanhlq_logo_dark.png);height: 30px;width: 215px;display: block;float: left;top: -5px;background-size: cover;background-position: center;position: relative;"></i>
                            <span style="font-size: 150%;line-height: 24px;">Hỗ trợ</span>
                        </div>

                        <div class="support-menu">
                            <ul>
                                <li>Website: <a href="//khanhlq.com">khanhlq.com</a></li>
                                <li>Hotline: <a href="tel:0829985588">08.29.98.55.88</a></li>
                                <li>Phone: <a href="tel:0388295534">03.88.29.55.34</a></li>
                                <li>Fanpage: <a href="https://www.facebook.com/khanhlq.com.fanpage">khanhlq.com</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div><!-- wrap -->
    <script lang="javascript">var _vc_data = {id : 2897550, secret : '3831712a3ba338412c38b1cefd43945d'};(function() {var ga = document.createElement('script');ga.type = 'text/javascript';ga.async=true; ga.defer=true;ga.src = '//live.vnpgroup.net/client/tracking.js?id=2897550';var s = document.getElementsByTagName('script');s[0].parentNode.insertBefore(ga, s[0]);})();</script>

<?php
wp_print_community_events_templates();

require(ABSPATH . 'acp/admin-footer.php');

