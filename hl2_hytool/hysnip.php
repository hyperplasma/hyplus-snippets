<?php
/**
 * HySnip - 本站内容快速引用短代码插件
 * Description: 通过[hysnip]短代码实现加载并展示本站博文或页面的弹出框
 * Usage: [hysnip href="/kokuyou" name="黑曜酱与白玉君" mode="link" async="1"]
 *        [hysnip id="123" name="自定义名称" title="弹出框标题" mode="button" async="1"]
 * 
 * Parameters:
 * - href: 博文或页面的相对链接（可选，如果设置了id则无效）。会自动加上本站域名
 * - id: 博文或页面的post ID（可选，优先级高于href）
 * - name: 按钮文字（可选）。如果未设置，则使用title；如果title也未设置，则使用博文的真实标题
 * - title: 弹出框标题（可选）。如果未设置则使用页面的真实标题
 * - limit: 内容字数限制（可选，默认为0，表示不限制）。如果设置为大于0的值，将只显示指定字数的内容，并在末尾添加省略号
 * - mode: 显示模式（可选，默认为"button"）。可选值为"button"（按钮式链接）或"link"（普通文本链接）
 * - async: 是否异步预加载内容（可选，默认为0）。设为1时会在页面加载时预加载内容
 * 
 * Features:
 * - 生成与HyImg一致的按钮式链接
 * - 点击后在弹出框中展示内容
 * - 自动缓存加载的内容
 * - 支持异步预加载，不阻塞页面渲染
 * - 支持点击框外关闭和ESC快速关闭
 */

// 注册短代码
add_shortcode('hysnip', 'hysnip_shortcode_handler');

/**
 * HySnip 短代码处理函数
 */
function hysnip_shortcode_handler($atts) {
    // 标记页面上存在 hysnip 短代码，用于在 footer 中注入 JavaScript
    global $hysnip_page_has_shortcode;
    $hysnip_page_has_shortcode = true;

    // 解析短代码参数，初始化为空值
    $atts = shortcode_atts(array(
        'href'  => '',
        'id'    => '',
        'name'  => '',
        'title' => '',
        'limit' => 0,
        'mode'  => 'button',
        'async' => 0
    ), $atts, 'hysnip');

    // 使用静态缓存避免重复数据库查询
    static $post_cache = array();

    // 确定 post_id：优先使用 id 参数
    $post_id = null;
    if (!empty($atts['id'])) {
        $post_id = (int)$atts['id'];
    } elseif (!empty($atts['href'])) {
        // 如果是相对路径（不以http开头），则加上本站域名
        $permalink = $atts['href'];
        if (strpos($permalink, 'http') !== 0) {
            $permalink = home_url() . '/' . ltrim($permalink, '/');
        }
        $post_id = url_to_postid($permalink);
    }

    // 验证 post_id
    if (!$post_id) {
        return '<p style="color: #d9534f; font-weight: bold;">⚠ HySnip: 无效的 id 或 href 参数</p>';
    }

    // 检查缓存，如果没有则查询数据库
    if (!isset($post_cache[$post_id])) {
        $post_cache[$post_id] = get_post($post_id);
    }
    $post = $post_cache[$post_id];

    if (!$post || $post->post_status !== 'publish') {
        return '<p style="color: #d9534f; font-weight: bold;">⚠ HySnip: 页面不存在或不可访问</p>';
    }

    // 获取真实标题
    $real_title = $post->post_title;

    // 智能处理 name 参数：
    // 1. 如果 name 为空但 title 不为空，则 name = title
    // 2. 如果 name 和 title 都为空，则 name = 真实标题
    if (empty($atts['name'])) {
        if (!empty($atts['title'])) {
            $atts['name'] = $atts['title'];
        } else {
            $atts['name'] = $real_title;
        }
    }

    // 处理链接URL
    if (!empty($atts['id'])) {
        $permalink = get_permalink($post_id);
    } else {
        $permalink = $atts['href'];
        // 如果是相对路径（不以http开头），则加上本站域名
        if (strpos($permalink, 'http') !== 0) {
            $permalink = home_url() . '/' . ltrim($permalink, '/');
        }
    }

    // 获取安全的属性值
    $btn_text  = esc_html($atts['name']);
    $popup_title = esc_html($atts['title']);
    $safe_href = esc_attr($permalink);
    $mode      = esc_attr($atts['mode']);
    $async     = (int)$atts['async'];

    // 根据mode参数设置class
    $classes = array('hysnip-trigger'); // 总是添加触发器class
    if ($mode === 'button') {
        $classes[] = 'hyplus-nav-link';
    }
    $class_attr = ' class="' . implode(' ', $classes) . '"';
    
    // 如果启用异步加载，添加data属性
    $async_attr = $async ? ' data-async="1"' : '';
    
    // 将弹出框标题作为 data 属性传递给 JavaScript
    $title_attr = $popup_title ? ' data-popup-title="' . $popup_title . '"' : '';

    // 构建HTML（单行输出，避免换行符被转换为<br>标签）
    $html = '<a href="' . $safe_href . '"' . $class_attr . $async_attr . $title_attr . ' target="_blank">' . $btn_text . '</a>';

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
    let loadingPromises = {}; // 记录正在加载的 Promise，防止并发请求相同URL
    let currentPermalink = null; // 记录当前打开的 permalink
    let mutationObserver = null;
    let activePreloads = 0; // 当前活跃的预加载数
    const MAX_CONCURRENT_PRELOADS = 5; // 最多同时5个预加载
    let preloadQueue = []; // 待加载的URL队列

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
        const link = event.target.closest('.hysnip-trigger');
        if (!link) return;

        let permalink = link.href;
        if (!permalink) return;

        // 规范化URL以确保缓存key一致
        permalink = normalizeUrl(permalink);

        // 阻止默认的链接跳转行为
        event.preventDefault();

        // 从 data 属性获取自定义弹出框标题，如果没有则为空（后续会使用页面真实标题）
        const customTitle = link.getAttribute('data-popup-title');
        
        // 打开弹出框
        openSnippetPopup(permalink, customTitle || '');
    });

    function openSnippetPopup(permalink, customTitle) {
        const popup = getSnippetPopup();
        
        // 记录当前打开的 permalink
        currentPermalink = permalink;

        // 检查缓存
        if (snippetCache[permalink] !== undefined) {
            const cachedData = snippetCache[permalink];
            const titleToDisplay = customTitle || cachedData.title;
            displaySnippetContent(cachedData.content, titleToDisplay, permalink);
            popup.classList.add('active');
            return;
        }

        // 检查是否已经在加载中，如果是则等待已有的请求完成
        if (loadingPromises[permalink]) {
            loadingPromises[permalink].then(() => {
                // 加载完成后，如果缓存中有内容就使用缓存
                if (snippetCache[permalink] !== undefined && currentPermalink === permalink) {
                    const cachedData = snippetCache[permalink];
                    const titleToDisplay = customTitle || cachedData.title;
                    displaySnippetContent(cachedData.content, titleToDisplay, permalink);
                    popup.classList.add('active');
                }
            });
            // 显示加载状态
            const contentDiv = popup.querySelector('.hysnip-popup-content');
            const headerTitle = customTitle || '加载中...';
            contentDiv.innerHTML = '<div class="hysnip-popup-header"><a href="' + esc(permalink) + '" target="_blank">' + esc(headerTitle) + '</a></div><div style="text-align: center; padding: 20px; color: #999; font-style: italic;">加载中...</div>';
            popup.classList.add('active');
            return;
        }

        // 显示加载状态
        const contentDiv = popup.querySelector('.hysnip-popup-content');
        const headerTitle = customTitle || '加载中...';
        contentDiv.innerHTML = '<div class="hysnip-popup-header"><a href="' + esc(permalink) + '" target="_blank">' + esc(headerTitle) + '</a></div><div style="text-align: center; padding: 20px; color: #999; font-style: italic;">加载中...</div>';
        
        popup.classList.add('active');

        // 发送 AJAX 请求获取内容（添加 AbortController 超时控制）
        var data = new FormData();
        data.append('action', 'hysnip_get_content');
        data.append('permalink', permalink);
        data.append('nonce', '<?php echo wp_create_nonce('hysnip_popup'); ?>');

        // 创建 AbortController 用于超时控制
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10秒超时

        // 创建 Promise 并保存到 loadingPromises，供其他相同URL的请求等待
        loadingPromises[permalink] = fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
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
                // 缓存加载的内容和标题
                snippetCache[permalink] = {
                    content: result.data.content,
                    title: result.data.post_title
                };
                // 如果没有自定义标题，使用页面的真实标题
                const titleToDisplay = customTitle || result.data.post_title;
                displaySnippetContent(result.data.content, titleToDisplay, permalink);
            } else {
                displaySnippetContent(null, customTitle, permalink);
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
                displaySnippetContent(null, customTitle, permalink);
            }
        })
        .finally(function() {
            // 请求完成后，从 loadingPromises 中删除，允许后续相同URL的请求重新发起
            delete loadingPromises[permalink];
        });
    }

    function displaySnippetContent(content, title, permalink) {
        const popup = getSnippetPopup();
        const contentDiv = popup.querySelector('.hysnip-popup-content');
        
        let html = '<div class="hysnip-popup-header"><a href="' + esc(permalink) + '" target="_blank">' + esc(title) + '</a><button class="hysnip-close-btn hyplus-scale hyplus-unselectable" aria-label="关闭" title="关闭（ESC）"></button></div>';
        
        if (content === null) {
            html += '<div style="text-align: center; padding: 20px; color: #999; font-style: italic;">加载失败</div>';
        } else {
            html += '<div class="hysnip-popup-body">' + content + '</div>';
        }
        
        contentDiv.innerHTML = html;
        
        // 为关闭按钮添加事件处理（使用事件委托避免重复绑定）
        const closeBtn = contentDiv.querySelector('.hysnip-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeSnippetPopup();
            });
        }
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

    // 规范化URL函数：确保末尾有斜杠，移除查询参数和hash
    function normalizeUrl(url) {
        // 移除查询参数和hash
        url = url.split('?')[0].split('#')[0];
        // 确保末尾有斜杠
        if (!url.endsWith('/')) {
            url += '/';
        }
        return url;
    }

    // 预加载异步内容
    function preloadAsyncContent() {
        const asyncLinks = document.querySelectorAll('.hysnip-trigger[data-async="1"]');
        asyncLinks.forEach(link => {
            let permalink = link.href;
            if (permalink) {
                // 规范化URL以确保缓存key一致
                permalink = normalizeUrl(permalink);
                if (snippetCache[permalink] === undefined && !loadingPromises[permalink]) {
                    preloadQueue.push(permalink);
                }
            }
        });
        // 开始处理队列
        processPreloadQueue();
    }

    // 处理预加载队列，限制并发数
    function processPreloadQueue() {
        // 如果队列为空或达到并发上限，则返回
        if (preloadQueue.length === 0 || activePreloads >= MAX_CONCURRENT_PRELOADS) {
            return;
        }
        
        // 从队列中取一个URL
        const permalink = preloadQueue.shift();
        activePreloads++;
        
        // 发送 AJAX 请求预加载内容
        var data = new FormData();
        data.append('action', 'hysnip_get_content');
        data.append('permalink', permalink);
        data.append('nonce', '<?php echo wp_create_nonce('hysnip_popup'); ?>');

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10秒超时

        // 记录加载状态，防止并发
        loadingPromises[permalink] = fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: data,
            signal: controller.signal
        })
        .then(response => response.json())
        .then(result => {
            clearTimeout(timeoutId);
            if (result.success) {
                snippetCache[permalink] = {
                    content: result.data.content,
                    title: result.data.post_title
                };
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            // 静默处理错误，不影响页面
        })
        .finally(() => {
            delete loadingPromises[permalink];
            activePreloads--;
            // 继续处理队列中的下一个
            processPreloadQueue();
        });
    }

    // 页面加载完成后预加载异步内容（不阻塞页面渲染）
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', preloadAsyncContent);
    } else {
        setTimeout(preloadAsyncContent, 0);
    }

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
        'content' => $content,
        'post_title' => $post->post_title
    ));
}

add_action('wp_ajax_hysnip_get_content', 'hysnip_get_content_ajax');
add_action('wp_ajax_nopriv_hysnip_get_content', 'hysnip_get_content_ajax');

/**
 * 在页脚注入 HySnip JavaScript（仅在有 hysnip 短代码的页面注入）
 */
function hysnip_inject_script_to_footer() {
    global $hysnip_page_has_shortcode;
    
    // 仅在页面上存在 hysnip 短代码时注入
    if (!empty($hysnip_page_has_shortcode)) {
        echo hysnip_get_script();
    }
}
add_action('wp_footer', 'hysnip_inject_script_to_footer');
?>
