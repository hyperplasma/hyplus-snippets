<?php
/**
 * HyImg - 异步获取并展示图片的短代码插件
 * Description: 通过[hyimg]短代码实现异步加载图片的功能
 * Usage: [hyimg src="example.com/image.jpg" title="图片" width="80%" height="auto"]
 * 
 * Parameters:
 * - src: 图片URL（必需）。相对路径会自动加上本站域名
 * - title: 按钮文字（可选，默认为"图片"）
 * - width: 图片宽度（可选，默认为100%）支持百分比和px
 * - height: 图片高度（可选，默认为auto）支持百分比和px
 */

// 注册短代码
add_shortcode('hyimg', 'hyimg_shortcode_handler');

/**
 * HyImg 短代码处理函数
 */
function hyimg_shortcode_handler($atts) {
    // 解析短代码参数，设置默认值
    $atts = shortcode_atts(array(
        'src'    => '',
        'title'  => '图片',
        'width'  => '100%',
        'height' => 'auto'
    ), $atts, 'hyimg');

    // 验证URL参数
    if (empty($atts['src'])) {
        return '<p style="color: #d9534f; font-weight: bold;">⚠ HyImg: src参数为必需</p>';
    }

    // 处理图片URL
    $img_url = $atts['src'];
    
    // 如果是相对路径（不以http开头），则加上本站域名
    if (strpos($img_url, 'http') !== 0) {
        $img_url = home_url() . '/' . ltrim($img_url, '/');
    }

    // 生成唯一ID（用于区分多个[hyimg]短代码）
    $unique_id = 'hyimg-' . uniqid();

    // 获取安全的属性值
    $btn_text     = esc_html($atts['title']);
    $safe_url     = esc_attr($img_url);
    $safe_width   = esc_attr($atts['width']);
    $safe_height  = esc_attr($atts['height']);
    $safe_id      = esc_attr($unique_id);

    // 构建HTML
    ob_start();
    ?>
    <div class="hyimg-wrapper" style="margin: 15px 0;">
        <button 
            class="hyplus-nav-link" 
            data-hyimg-toggle="<?php echo $safe_id; ?>"
            data-hyimg-url="<?php echo $safe_url; ?>"
            data-hyimg-width="<?php echo $safe_width; ?>"
            data-hyimg-height="<?php echo $safe_height; ?>"
            type="button"
            style="margin: 0; cursor: pointer;">
            <?php echo $btn_text; ?>
        </button>
        <div 
            id="<?php echo $safe_id; ?>" 
            class="hyimg-display" 
            style="margin-top: 15px; display: none; text-align: center; align-items: center; justify-content: center;">
        </div>
    </div>
    <?php
    $html = ob_get_clean();

    // 注入JavaScript代码（只注入一次）
    static $script_injected = false;
    if (!$script_injected) {
        $html .= hyimg_get_script();
        $script_injected = true;
    }

    return $html;
}

/**
 * 返回HyImg的JavaScript代码
 */
function hyimg_get_script() {
    ob_start();
    ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 存储已加载的图片，避免重复加载
    const hyimgCache = {};

    // 监听所有具有data-hyimg-toggle属性的按钮点击事件
    document.addEventListener('click', function(event) {
        const button = event.target.closest('[data-hyimg-toggle]');
        if (!button) return;

        const toggleId = button.getAttribute('data-hyimg-toggle');
        const imgUrl   = button.getAttribute('data-hyimg-url');
        const imgWidth = button.getAttribute('data-hyimg-width');
        const imgHeight = button.getAttribute('data-hyimg-height');
        const display  = document.getElementById(toggleId);

        if (!display) return;

        // 切换显示/隐藏
        if (display.style.display === 'none' || display.style.display === '') {
            // 显示
            display.style.display = 'flex';
            display.style.minHeight = '50px';

            // 如果还没有加载过，则异步加载
            if (!hyimgCache[toggleId]) {
                // 设置加载中状态
                if (!display.dataset.hyimgLoading) {
                    display.dataset.hyimgLoading = 'true';
                    display.innerHTML = '<p style="color: #666; font-size: 14px;">⏳ 加载中...</p>';

                    // 异步加载图片
                    const img = new Image();
                    img.style.maxWidth = '100%';
                    img.style.width = imgWidth;
                    img.style.height = imgHeight;
                    img.style.borderRadius = '6px';

                    img.onload = function() {
                        display.innerHTML = '';
                        display.appendChild(img);
                        display.style.minHeight = 'auto';
                        hyimgCache[toggleId] = true;
                        delete display.dataset.hyimgLoading;
                        display.style.display = 'flex';
                    };

                    img.onerror = function() {
                        display.innerHTML = '<p style="color: #d9534f; font-weight: bold;">❌ 图片加载失败</p>';
                        delete display.dataset.hyimgLoading;
                    };

                    // 启动加载
                    img.src = imgUrl;
                }
            }
        } else {
            // 隐藏
            display.style.display = 'none';
        }
    });
});
</script>
    <?php
    return ob_get_clean();
}
?>
