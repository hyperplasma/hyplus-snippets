<?php
/**
 * HySnip - 本站内容快速引用短代码插件
 * Description: 通过[hysnip]短代码实现加载并展示本站博文或页面的弹出框
 * Usage: [hysnip href="/blog/post-name/" title="查看文章"]
 * 
 * Parameters:
 * - href: 博文或页面的相对链接（必需）。会自动加上本站域名
 * - title: 按钮文字（可选，默认为"查看内容"）
 * 
 * Features:
 * - 生成与HyImg一致的按钮式链接
 * - 点击后在弹出框中展示内容
 * - 自动缓存加载的内容
 * - 支持点击框外关闭和ESC快速关闭
 */

// 注册短代码
add_shortcode('hysnip', 'hysnip_shortcode_handler');

/**
 * HySnip 短代码处理函数
 */
function hysnip_shortcode_handler($atts) {
    // 解析短代码参数，设置默认值
    $atts = shortcode_atts(array(
        'href'  => '',
        'title' => '查看内容',
        'limit' => 0,
        'mode'  => 'button'
    ), $atts, 'hysnip');

    // 验证href参数
    if (empty($atts['href'])) {
        return '<p style="color: #d9534f; font-weight: bold;">⚠ HySnip: href参数为必需</p>';
    }

    // 处理链接URL
    $permalink = $atts['href'];
    
    // 如果是相对路径（不以http开头），则加上本站域名
    if (strpos($permalink, 'http') !== 0) {
        $permalink = home_url() . '/' . ltrim($permalink, '/');
    }

    // 获取安全的属性值
    $btn_text  = esc_html($atts['title']);
    $safe_href = esc_attr($permalink);
    $mode      = esc_attr($atts['mode']);

    // 根据mode参数设置class
    $classes = array();
    if ($mode === 'button') {
        $classes = array('hyplus-nav-link', 'hysnip-button');
    }
    $class_attr = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';

    // 构建HTML
    ob_start();
    ?>
    <a 
        href="<?php echo $safe_href; ?>"<?php echo $class_attr; ?>
        target="_blank"
    >
        <?php echo $btn_text; ?>
    </a>
    <?php
    $html = ob_get_clean();

    // 注入JavaScript代码（只注入一次）
    static $script_injected = false;
    if (!$script_injected) {
        $html .= hysnip_get_script();
        $script_injected = true;
    }

    return $html;
}

/**
 * 返回HySnip的JavaScript代码
 */
function hysnip_get_script() {
    ob_start();
    ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 缓存容器和弹出框
    let cachedContainer = null;
    let snippetCache = {}; // 缓存加载的内容
    let currentPermalink = null; // 记录当前打开的 permalink
    let mutationObserver = null;

    function getContainer() {
        if (!cachedContainer) {
            cachedContainer = document.querySelector('article') || document.body;
        }
        return cachedContainer;
    }

    // 获取或创建弹出框
    function getSnippetPopup() {
        let popup = document.getElementById('hysnip-popup-wrapper');
        if (!popup) {
            popup = document.createElement('div');
            popup.id = 'hysnip-popup-wrapper';
            popup.className = 'hysnip-popup-wrapper';
            popup.innerHTML = '<div class="hysnip-popup-content"></div>';
            document.body.appendChild(popup);
        }
        return popup;
    }

    // 监听所有HySnip链接的点击事件
    document.addEventListener('click', function(event) {
        const link = event.target.closest('.hysnip-button');
        if (!link) return;

        const permalink = link.href;
        if (!permalink) return;

        // 阻止默认的链接跳转行为
        event.preventDefault();

        // 打开弹出框
        openSnippetPopup(permalink, link.textContent.trim());
    });

    function openSnippetPopup(permalink, title) {
        const popup = getSnippetPopup();
        
        // 记录当前打开的 permalink
        currentPermalink = permalink;

        // 检查缓存
        if (snippetCache[permalink] !== undefined) {
            displaySnippetContent(snippetCache[permalink], title, permalink);
            popup.classList.add('active');
            return;
        }

        // 显示加载状态
        const contentDiv = popup.querySelector('.hysnip-popup-content');
        contentDiv.innerHTML = '<div class="hysnip-popup-header"><a href="' + esc(permalink) + '" target="_blank">' + esc(title) + '</a></div><div style="text-align: center; padding: 20px; color: #999; font-style: italic;">加载中...</div>';
        
        popup.classList.add('active');

        // 发送 AJAX 请求获取内容（添加 AbortController 超时控制）
        var data = new FormData();
        data.append('action', 'hysnip_get_content');
        data.append('permalink', permalink);
        data.append('nonce', '<?php echo wp_create_nonce('hysnip_popup'); ?>');

        // 创建 AbortController 用于超时控制
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10秒超时

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: data,
            signal: controller.signal
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            clearTimeout(timeoutId);
            // 只处理与当前打开的 permalink 匹配的响应
            if (currentPermalink !== permalink) return;
            
            if (result.success) {
                // 缓存加载的内容
                snippetCache[permalink] = result.data.content;
                displaySnippetContent(result.data.content, title, permalink);
            } else {
                displaySnippetContent(null, title, permalink);
            }
        })
        .catch(function(error) {
            clearTimeout(timeoutId);
            // 只在非中止情况下打印错误
            if (error.name !== 'AbortError') {
                console.error('Error:', error);
            }
            // 只处理与当前打开的 permalink 匹配的错误
            if (currentPermalink === permalink) {
                displaySnippetContent(null, title, permalink);
            }
        });
    }

    function displaySnippetContent(content, title, permalink) {
        const popup = getSnippetPopup();
        const contentDiv = popup.querySelector('.hysnip-popup-content');
        
        let html = '<div class="hysnip-popup-header"><a href="' + esc(permalink) + '" target="_blank">' + esc(title) + '</a></div>';
        
        if (content === null) {
            html += '<div style="text-align: center; padding: 20px; color: #999; font-style: italic;">加载失败</div>';
        } else {
            html += '<div class="hysnip-popup-body">' + content + '</div>';
        }
        
        contentDiv.innerHTML = html;
    }

    function closeSnippetPopup() {
        const popup = getSnippetPopup();
        popup.classList.remove('active');
    }

    // 获取弹出框实例以便绑定事件
    function setupPopupEvents() {
        const popup = getSnippetPopup();
        
        // 点击弹窗外关闭
        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                closeSnippetPopup();
            }
        });

        // ESC键关闭弹窗
        document.addEventListener('keydown', function(e) {
            if ((e.key === 'Escape' || e.keyCode === 27) && popup.classList.contains('active')) {
                closeSnippetPopup();
            }
        });
    }

    // 立即初始化弹出框事件（移除不必要的延迟）
    setupPopupEvents();

    // 辅助函数：HTML转义
    function esc(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>
    <?php
    return ob_get_clean();
}

/**
 * AJAX 处理器：获取博文或页面内容
 */
function hysnip_get_content_ajax() {
    // 验证nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'hysnip_popup')) {
        wp_send_json_error('Security check failed');
    }

    // 获取并验证permalink
    $permalink = isset($_POST['permalink']) ? esc_url_raw($_POST['permalink']) : '';
    if (empty($permalink)) {
        wp_send_json_error('Invalid permalink');
    }

    // 通过permalink获取post ID
    $post_id = url_to_postid($permalink);
    if (!$post_id) {
        wp_send_json_error('Post not found');
    }

    // 获取post对象
    $post = get_post($post_id);
    if (!$post || $post->post_status !== 'publish') {
        wp_send_json_error('Post not accessible');
    }

    // 对数据库内容进行HTML渲染
    $content = apply_filters('the_content', $post->post_content);

    wp_send_json_success(array(
        'content' => $content
    ));
}

add_action('wp_ajax_hysnip_get_content', 'hysnip_get_content_ajax');
add_action('wp_ajax_nopriv_hysnip_get_content', 'hysnip_get_content_ajax');
?>
