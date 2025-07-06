<?php
/**
 * 在WordPress经典编辑器界面隐藏Elementor编辑按钮
 */
function hide_elementor_edit_button() {
    // 确保只在管理后台加载
    if (!is_admin()) {
        return;
    }
    
    // 添加内联CSS
    echo '<style>#elementor-switch-mode{display:none!important}</style>';
}

// 使用admin_head钩子在管理界面头部添加样式
add_action('admin_head', 'hide_elementor_edit_button');