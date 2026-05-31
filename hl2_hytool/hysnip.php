<?php
/**
 * HySnip - 本站内容快速引用短代码插件
 * Description: 通过[hysnip]短代码实现加载并展示本站博文或页面的弹出框
 * Usage: [hysnip id="123" name="自定义名称" title="弹出框标题" mode="button" async="1"]
 * 
 * Parameters:
 * - id: 博文或页面的 post ID（必填）
 * - name: 按钮文字（可选）。如果未设置，则使用 title；如果 title 也未设置，则使用博文的真实标题
 * - title: 弹出框标题（可选）。如果未设置则使用页面的真实标题
 * - limit: 内容字数限制（可选，默认为0，表示不限制）。如果设置为大于0的值，将只显示指定字数的内容，并在末尾添加省略号
 * - mode: 显示模式（可选，默认为"button"）。可选值为"button"（【默认】按钮式链接，带hyplus-nav-link类）、"link"（普通文本链接，无hyplus-nav-link类）或"none"（纯普通a链接，无任何特殊class）
 * - newtab: 是否在新标签页打开（可选，仅与mode搭配使用）。可选值为"1"（强制新标签页打开）或"0"（强制当前标签页打开）。未设置时，mode="button"或"link"默认新标签页打开，mode="none"默认当前标签页打开
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
        'id'      => '',
        'name'    => '',
        'title'   => '',
        'limit'   => 0,
        'mode'    => 'button',
        'newtab'  => '',
        'async'   => 0
    ), $atts, 'hysnip');

    // 使用静态缓存避免重复数据库查询
    static $post_cache = array();

    // 确定 post_id：仅使用 id 参数
    $post_id = !empty($atts['id']) ? (int)$atts['id'] : null;

    // 验证 post_id
    if (!$post_id) {
        return '<p style="color: #d9534f; font-weight: bold;">⚠ HySnip: 无效的 id 参数</p>';
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

    // 处理链接URL，始终使用 post_id 的永久链接
    $permalink = get_permalink($post_id);

    // 获取安全的属性值
    $btn_text  = esc_html($atts['name']);
    $popup_title = esc_html($atts['title']);
    $safe_href = esc_attr($permalink);
    $mode      = esc_attr($atts['mode']);
    $async     = (int)$atts['async'];
    $edit_link_attr = '';

    if ($post_id && current_user_can('manage_options')) {
        $edit_link = html_entity_decode(get_edit_post_link($post_id), ENT_QUOTES, 'UTF-8');
        if (!empty($edit_link)) {
            $edit_link_attr = ' data-edit-link="' . esc_attr($edit_link) . '"';
        }
    }

    // 根据mode参数设置class
    $classes = array();
    if ($mode === 'button') {
        $classes[] = 'hysnip-trigger';
        $classes[] = 'hyplus-nav-link';
    } elseif ($mode === 'link') {
        $classes[] = 'hysnip-trigger';
    }
    // 'none' 模式不添加任何class
    $class_attr = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';
    
    // 确定是否在新标签页打开
    $open_in_new_tab = false;
    if (!empty($atts['newtab'])) {
        // 如果显式设置了newtab参数，则遵循该设置
        $open_in_new_tab = (int)$atts['newtab'] === 1;
    } else {
        // 默认情况：mode="button" 或 "link" 时在新标签页打开，mode="none" 时不打开
        $open_in_new_tab = ($mode !== 'none');
    }
    $target_attr = $open_in_new_tab ? ' target="_blank"' : '';
    
    // 如果启用异步加载，添加data属性
    $async_attr = $async ? ' data-async="1"' : '';
    
    // 将弹出框标题作为 data 属性传递给 JavaScript
    $title_attr = $popup_title ? ' data-popup-title="' . $popup_title . '"' : '';
    $post_id_attr = ' data-post-id="' . esc_attr($post_id) . '"';

    // 构建HTML（单行输出，避免换行符被转换为<br>标签）
    $html = '<a href="' . $safe_href . '"' . $class_attr . $post_id_attr . $async_attr . $title_attr . $edit_link_attr . $target_attr . '>' . $btn_text . '</a>';

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
    let loadingPromises = {}; // 记录正在加载的 Promise，防止并发请求相同postId
    let currentPostId = null; // 记录当前打开的 postId
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
        const triggerElement = event.target.closest('.hysnip-trigger');
        if (!triggerElement) return;

        let permalink = '';
        let customTitle = '';
        let editLink = '';
        let postId = '';

        // 区分是a标签还是li标签
        if (triggerElement.tagName === 'A') {
            postId = triggerElement.getAttribute('data-post-id');
            permalink = triggerElement.href;
            customTitle = triggerElement.getAttribute('data-popup-title');
            editLink = triggerElement.getAttribute('data-edit-link') || '';
        } else if (triggerElement.tagName === 'LI') {
            const innerLink = triggerElement.querySelector('a[data-post-id]');
            if (!innerLink) return;

            postId = innerLink.getAttribute('data-post-id');
            permalink = innerLink.href;
            customTitle = triggerElement.getAttribute('data-popup-title') || innerLink.getAttribute('data-popup-title');
            editLink = triggerElement.getAttribute('data-edit-link') || innerLink.getAttribute('data-edit-link') || '';
        } else {
            return;
        }

        if (!postId) return;

        // 阻止默认的链接跳转行为
        event.preventDefault();

        // 打开弹出框
        openSnippetPopup(postId, permalink, customTitle || '', editLink);
    });

    function openSnippetPopup(postId, permalink, customTitle, editLink) {
        const popup = getSnippetPopup();
        const headerHref = editLink || permalink;

        // 记录当前打开的 postId
        currentPostId = postId;

        // 检查缓存
        if (snippetCache[postId] !== undefined) {
            const cachedData = snippetCache[postId];
            const titleToDisplay = customTitle || cachedData.title;
            displaySnippetContent(cachedData.content, titleToDisplay, headerHref);
            popup.classList.add('active');
            return;
        }

        // 检查是否已经在加载中，如果是则等待已有的请求完成
        if (loadingPromises[postId]) {
            loadingPromises[postId].then(() => {
                if (snippetCache[postId] !== undefined && currentPostId === postId) {
                    const cachedData = snippetCache[postId];
                    const titleToDisplay = customTitle || cachedData.title;
                    displaySnippetContent(cachedData.content, titleToDisplay, headerHref);
                    popup.classList.add('active');
                }
            });
            const contentDiv = popup.querySelector('.hysnip-popup-content');
            const headerTitle = customTitle || '加载中...';
            contentDiv.innerHTML = '<div class="hysnip-popup-header"><a href="' + esc(headerHref) + '" target="_blank">' + esc(headerTitle) + '</a></div><div style="text-align: center; padding: 20px; color: #999; font-style: italic;">加载中...</div>';
            popup.classList.add('active');
            return;
        }

        const contentDiv = popup.querySelector('.hysnip-popup-content');
        const headerTitle = customTitle || '加载中...';
        contentDiv.innerHTML = '<div class="hysnip-popup-header"><a href="' + esc(headerHref) + '" target="_blank">' + esc(headerTitle) + '</a></div><div style="text-align: center; padding: 20px; color: #999; font-style: italic;">加载中...</div>';
        
        popup.classList.add('active');

        var data = new FormData();
        data.append('action', 'hysnip_get_content');
        data.append('post_id', postId);
        data.append('nonce', '<?php echo wp_create_nonce('hysnip_popup'); ?>');

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10秒超时

        loadingPromises[postId] = fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: data,
            signal: controller.signal
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            clearTimeout(timeoutId);
            if (currentPostId !== postId) return;
            
            if (result.success) {
                snippetCache[postId] = {
                    content: result.data.content,
                    title: result.data.post_title
                };
                const titleToDisplay = customTitle || result.data.post_title;
                displaySnippetContent(result.data.content, titleToDisplay, headerHref);
            } else {
                displaySnippetContent(null, customTitle, headerHref);
            }
        })
        .catch(function(error) {
            clearTimeout(timeoutId);
            if (error.name !== 'AbortError') {
                console.error('Error:', error);
            }
            if (currentPostId === postId) {
                displaySnippetContent(null, customTitle, headerHref);
            }
        })
        .finally(function() {
            delete loadingPromises[postId];
        });
    }

    function displaySnippetContent(content, title, headerHref) {
        const popup = getSnippetPopup();
        const contentDiv = popup.querySelector('.hysnip-popup-content');
        
        let html = '<div class="hysnip-popup-header"><a href="' + esc(headerHref) + '" target="_blank">' + esc(title) + '</a><button class="hysnip-close-btn hyplus-scale hyplus-unselectable" aria-label="关闭" title="关闭（ESC）"></button></div>';
        
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

    // 预加载异步内容
    function preloadAsyncContent() {
        const asyncLinks = document.querySelectorAll('.hysnip-trigger[data-async="1"]');
        asyncLinks.forEach(link => {
            const postId = link.getAttribute('data-post-id');
            if (postId && snippetCache[postId] === undefined && !loadingPromises[postId]) {
                preloadQueue.push(postId);
            }
        });
        processPreloadQueue();
    }

    // 处理预加载队列，限制并发数
    function processPreloadQueue() {
        if (preloadQueue.length === 0 || activePreloads >= MAX_CONCURRENT_PRELOADS) {
            return;
        }
        
        const postId = preloadQueue.shift();
        activePreloads++;
        
        var data = new FormData();
        data.append('action', 'hysnip_get_content');
        data.append('post_id', postId);
        data.append('nonce', '<?php echo wp_create_nonce('hysnip_popup'); ?>');

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10秒超时

        loadingPromises[postId] = fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: data,
            signal: controller.signal
        })
        .then(response => response.json())
        .then(result => {
            clearTimeout(timeoutId);
            if (result.success) {
                snippetCache[postId] = {
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
            delete loadingPromises[postId];
            activePreloads--;
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

    // 获取并验证 post_id
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    if (!$post_id) {
        wp_send_json_error('Invalid post_id');
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
 * 在页脚注入 HySnip JavaScript（在全部页面中注入，以支持导航菜单中的hysnip-trigger类）
 */
function hysnip_inject_script_to_footer() {
    echo hysnip_get_script();
}
add_action('wp_footer', 'hysnip_inject_script_to_footer');
?>
