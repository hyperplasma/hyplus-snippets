<?php
/**
 * HyImg - 异步获取并展示图片的短代码插件（灯箱模式）
 * Description: 通过[hyimg]短代码实现异步加载图片并通过灯箱展示
 * Usage: [hyimg src="example.com/image.jpg" title="查看图片"]
 * 
 * Parameters:
 * - src: 图片URL（必需）。相对路径会自动加上本站域名
 * - title: 按钮文字（可选，默认为"图片"）
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
        'title'  => '图片'
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
    $safe_id      = esc_attr($unique_id);

    // 构建HTML
    ob_start();
    ?>
    <button 
        class="hyplus-nav-link hyimg-button" 
        data-hyimg-url="<?php echo $safe_url; ?>"
        type="button"
        style="margin: 4px; cursor: pointer; display: inline-block;">
        <?php echo $btn_text; ?>
    </button>
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
    // 存储已打开过的图片（用于避免重复点击操作）
    const hyimgOpened = new Set();

    // 监听所有HyImg按钮的点击事件
    document.addEventListener('click', function(event) {
        const button = event.target.closest('.hyimg-button');
        if (!button) return;

        const imgUrl = button.getAttribute('data-hyimg-url');
        if (!imgUrl) return;

        // 直接打开灯箱，让图片在灯箱中加载
        openImageInLightbox(imgUrl);
        hyimgOpened.add(imgUrl);
    });

    function openImageInLightbox(imgUrl) {
        const main = document.getElementById('main');
        if (!main) {
            console.warn('HyImg: 灯箱模式需要页面内存在 #main 元素');
            return;
        }

        // 创建临时img元素
        const tempImg = document.createElement('img');
        tempImg.setAttribute('data-src', imgUrl);
        // 使用visibility和position隐藏，而不是display:none，这样offsetParent不会为null
        tempImg.style.visibility = 'hidden';
        tempImg.style.position = 'absolute';
        tempImg.style.left = '-9999px';
        tempImg.style.width = '1px';
        tempImg.style.height = '1px';
        tempImg.setAttribute('data-hyimg-temp', 'true');
        main.appendChild(tempImg);

        // 设置src后，浏览器会开始加载图片
        // 灯箱打开后，用户会在灯箱中看到图片逐渐加载的过程
        tempImg.src = imgUrl;

        // 触发click事件让hylightbox打开灯箱
        setTimeout(() => {
            tempImg.click();
        }, 0);

        // 监听灯箱关闭，清理临时元素
        const wrapper = document.querySelector('.hylightbox-wrapper');
        if (wrapper) {
            const checkClose = setInterval(() => {
                if (!wrapper.classList.contains('active') && tempImg.parentNode) {
                    tempImg.remove();
                    clearInterval(checkClose);
                }
            }, 100);
        }
    }
});
</script>
    <?php
    return ob_get_clean();
}
?>
