<?php
/**
 * HyImg - 异步获取并展示图片的短代码插件（灯箱模式）
 * Description: 通过[hyimg]短代码实现异步加载图片并通过灯箱展示
 * Usage: [hyimg src="example.com/image.jpg" title="查看图片"]
 * 
 * Parameters:
 * - src: 图片URL（必需）。相对路径会自动加上本站域名
 * - title: 链接文字（可选，默认为"图片"）
 * 
 * Features:
 * - 无需 data 属性，直接从 href 读取图片 URL
 * - 使用 MutationObserver 监听灯箱关闭事件，性能高效
 * - 自动清理临时元素，防止内存泄漏
 * - 容器缓存，减少 DOM 查询
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
    $btn_text = esc_html($atts['title']);
    $safe_url = esc_attr($img_url);

    // 构建HTML
    ob_start();
    ?>
    <a 
        href="<?php echo $safe_url; ?>"
        class="hyplus-nav-link hyimg-button"
        target="_blank"
    >
        <?php echo $btn_text; ?>
    </a>
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
    // 缓存容器和灯箱包装器
    let cachedContainer = null;
    let cachedWrapper = null;
    let mutationObserver = null;

    function getContainer() {
        if (!cachedContainer) {
            cachedContainer = document.querySelector('article');
        }
        return cachedContainer;
    }

    function getWrapper() {
        if (!cachedWrapper) {
            cachedWrapper = document.querySelector('.hylightbox-wrapper');
        }
        return cachedWrapper;
    }

    // 监听所有HyImg链接的点击事件
    document.addEventListener('click', function(event) {
        const link = event.target.closest('.hyimg-button');
        if (!link) return;

        const imgUrl = link.href;
        if (!imgUrl) return;

        // 阻止默认的链接跳转行为
        event.preventDefault();

        // 直接打开灯箱，让图片在灯箱中加载
        openImageInLightbox(imgUrl);
    });

    function openImageInLightbox(imgUrl) {
        const container = getContainer();
        if (!container) {
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
        container.appendChild(tempImg);

        // 设置src后，浏览器会开始加载图片
        tempImg.src = imgUrl;

        // 触发click事件让hylightbox打开灯箱
        setTimeout(() => {
            tempImg.click();
        }, 0);

        // 使用 MutationObserver 监听灯箱关闭
        setupLightboxCloseListener(tempImg);
    }

    function setupLightboxCloseListener(tempImg) {
        const wrapper = getWrapper();
        const container = getContainer();
        if (!wrapper || !container) return;

        // 标记是否已清理，防止重复清理
        let isCleanedUp = false;

        // 创建新的观察者监听 class 变化
        mutationObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    // 检查灯箱是否还有 active 类
                    if (!wrapper.classList.contains('active') && tempImg.parentNode && !isCleanedUp) {
                        // 灯箱已关闭，清理临时元素
                        cleanupTempImg();
                    }
                }
            });
        });

        // 监听 wrapper 的 class 属性变化
        mutationObserver.observe(wrapper, {
            attributes: true,
            attributeFilter: ['class']
        });

        // 防止内存泄漏：如果灯箱在10秒后仍未打开，清理临时元素
        const cleanupTimeout = setTimeout(() => {
            if (tempImg.parentNode && wrapper && !wrapper.classList.contains('active') && !isCleanedUp) {
                cleanupTempImg();
            }
        }, 10000);

        // 统一的清理函数，避免重复清理和内存泄漏
        function cleanupTempImg() {
            if (isCleanedUp) return;
            isCleanedUp = true;
            
            if (tempImg.parentNode) {
                tempImg.remove();
            }
            
            clearTimeout(cleanupTimeout);
            
            if (mutationObserver) {
                mutationObserver.disconnect();
                mutationObserver = null;
            }
        }
    }
});
</script>
    <?php
    return ob_get_clean();
}
?>
