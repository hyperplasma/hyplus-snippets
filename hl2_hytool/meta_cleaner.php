<?php
/**
 * 清理 reSmush.it Plugin 元数据
 * 在后台生成一个清理工具，可以删除所有的 reSmush.it 元数据
 */

// 添加清理工具菜单项
function hyplus_cleanup_add_admin_menu() {
    add_options_page(
        __( 'Clean reSmush.it Data', 'hyplus' ),
        __( 'Clean reSmush.it', 'hyplus' ),
        'manage_options',
        'hyplus-cleanup-resmushed',
        'hyplus_cleanup_page'
    );
}

add_action( 'admin_menu', 'hyplus_cleanup_add_admin_menu' );

// 清理页面UI
function hyplus_cleanup_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Clean reSmush.it Meta Data', 'hyplus' ); ?></h1>
        
        <div style="margin: 20px 0; padding: 20px; background: #fff8dc; border-left: 4px solid #ff9800; border-radius: 4px;">
            <p><strong><?php _e( 'Warning:', 'hyplus' ); ?></strong> <?php _e( 'This will permanently delete all reSmush.it meta data from your database.', 'hyplus' ); ?></p>
            <p><?php _e( 'Meta fields to be deleted:', 'hyplus' ); ?></p>
            <ul style="margin-left: 20px; list-style: disc;">
                <li><code>resmushed_cumulated_optimized_sizes</code></li>
                <li><code>resmushed_cumulated_original_sizes</code></li>
                <li><code>resmushed_quality</code></li>
            </ul>
        </div>

        <?php
        if ( isset( $_POST['hyplus_cleanup_nonce'] ) && wp_verify_nonce( $_POST['hyplus_cleanup_nonce'], 'hyplus_cleanup_resmushed_action' ) ) {
            if ( isset( $_POST['hyplus_confirm_cleanup'] ) && $_POST['hyplus_confirm_cleanup'] === 'yes' ) {
                $count = hyplus_cleanup_resmushed_metadata();
                ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php printf( __( 'Successfully deleted %d meta entries!', 'hyplus' ), $count ); ?></p>
                </div>
                <?php
            }
        }
        ?>

        <form method="post" action="" style="margin-top: 30px;">
            <?php wp_nonce_field( 'hyplus_cleanup_resmushed_action', 'hyplus_cleanup_nonce' ); ?>
            
            <label>
                <input type="checkbox" name="hyplus_confirm_cleanup" value="yes" required>
                <strong><?php _e( 'I understand this action cannot be undone.', 'hyplus' ); ?></strong>
            </label>
            <br><br>

            <?php submit_button( __( 'Delete reSmush.it Meta Data', 'hyplus' ), 'delete', 'submit', false ); ?>
        </form>
    </div>
    <?php
}

// 执行清理操作
function hyplus_cleanup_resmushed_metadata() {
    global $wpdb;
    
    $count = 0;

    // 删除 resmushed_cumulated_optimized_sizes meta
    $deleted1 = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
            'resmushed_cumulated_optimized_sizes'
        )
    );
    $count += $deleted1 ?: 0;

    // 删除 resmushed_cumulated_original_sizes meta
    $deleted2 = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
            'resmushed_cumulated_original_sizes'
        )
    );
    $count += $deleted2 ?: 0;

    // 删除 resmushed_quality meta
    $deleted3 = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
            'resmushed_quality'
        )
    );
    $count += $deleted3 ?: 0;

    return $count;
}
?>