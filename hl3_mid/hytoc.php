<?php
/*
 * Name: Hyplus TOC
 * Description: 生成Ultimate Buttons风格的目录，支持短代码[toc mode=ub|widget|post hideparent=true|false]，可自动插入正文第一个标题前。
 * Code type: PHP
 * Shortcode: [toc mode=post | ub | widget]
 */

// 自动在正文第一个被TOC捕获的标题前插入[toc]（仅文章页且未手动插入时）
add_filter('the_content', 'hyplus_auto_insert_toc_before_first_toc_heading');
function hyplus_auto_insert_toc_before_first_toc_heading($content) {
    if (!is_singular('post') || is_home()) return $content;
    if (strpos($content, '[toc') !== false) return $content; // 已有短代码则不自动插入

    // 只捕获两种格式：1. 数字（可多位）或单大写字母+点分段，且后面必须有空格（如1 标题、10 标题、2.1 标题、3.A.4 标题、B.1.3 标题、11.2.3 标题）；2. “第”+阿拉伯数字（如第1、第2、第3）
    if (preg_match('/(<h[1-6][^>]*>\s*((第\d+)|((?:[0-9]+|[A-Z])(?:\.(?:[0-9]+|[A-Z]))* )).*?<\/h[1-6]>)/u', $content, $matches, PREG_OFFSET_CAPTURE)) {
        $pos = $matches[0][1];
        $toc = '[toc]';
        // 在第一个被TOC捕获的标题前插入
        return substr($content, 0, $pos) . $toc . substr($content, $pos);
    }
    return $content;
}

// 输出 TOC 脚本和样式（仅一次）
function hyplus_output_toc_scripts() {
    if (is_home()) return;
    static $scripts_output = false;
    if ($scripts_output) return;
    $scripts_output = true;
    ?>
    <style>
    .hyplus-toc-header {
        position: relative;
        display: block;
        text-align: center;
        padding-right: 44px;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 8px;
    }
    .hyplus-toc-header::after {
        content: "";
        display: inline-block;
        width: 44px;
        height: 1px;
        vertical-align: middle;
    }
    .hyplus-toc-toggle {
        z-index: 1;
    }
    /* 容器宽度随内容而定，避免 100% 铺满 */
    .hyplus-toc-container {
        display: inline-block;
        width: auto;
        max-width: 100%;
        box-sizing: border-box;
        vertical-align: top;
    }

    /* 仅用 emoji 显示，不要背景与边框 */
    .hyplus-toc-toggle {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        float: none !important;
        background: none !important;
        border: none !important;
        border-radius: 0;
        cursor: pointer;
        padding: 0 6px;
        margin-left: 0;
        line-height: 1;
        font-size: 0; /* 隐藏按钮文字，由伪元素显示 emoji */
        color: transparent !important; /* 隐藏实际文本 + / - */
        -webkit-text-fill-color: transparent; /* Safari */
        user-select: none;
    }
    .hyplus-toc-toggle:hover,
    .hyplus-toc-toggle:focus,
    .hyplus-toc-toggle:active {
        background: none !important;
        border: none !important;
        color: transparent !important;
        -webkit-text-fill-color: transparent;
    }
    .hyplus-toc-toggle::after {
        content: '➖';
        font-size: 18px;
        color: #333; /* 指定可见颜色，避免继承透明导致不可见 */
        -webkit-text-fill-color: #333; /* Safari */
        display: inline-block;
        transform: translateY(1px);
        transition: transform 0.15s cubic-bezier(0.4,0,0.2,1);
    }
    .hyplus-toc-toggle[aria-label="显示"]::after { content: '➕'; }
    .hyplus-toc-toggle:hover::after,
    .hyplus-toc-toggle:focus::after {
        transform: translateY(1px) scale(1.13);
    }
    .hyplus-toc-toggle:active::after {
        transform: translateY(2px) scale(0.93);
        transition: none;
    }
    @media (prefers-color-scheme: dark) {
        .hyplus-toc-toggle::after {
            color: #eee;
            -webkit-text-fill-color: #eee;
        }
    }

    /* 平滑过渡（仅 CSS 控制）；强制显示由 max-height/opacity 管理隐藏 */
    .hyplus-toc-content {
        display: block !important; /* 块级，避免 baseline 造成额外行高 */
        overflow: hidden !important;
        max-height: 9999px !important; /* 展开上限，避免高度跳变 */
        opacity: 1 !important;
        will-change: max-height, opacity;
        transition: max-height 0.5s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.35s ease !important;
    }
    /* .hyplus-toc-content ul, .hyplus-toc-content ol {
        margin: 0;
    } */
    /* 当前位置的目录项高亮为粗体 */
    .hyplus-toc-content a.hyplus-toc-active {
        /* font-weight: bold; */
        color: #3000aa;
    }
    /* 当按钮处于"显示"（即当前折叠）状态时，折叠内容 */
    .hyplus-toc-container:has(.hyplus-toc-toggle[aria-label="显示"]) .hyplus-toc-content {
        max-height: 0 !important;
        opacity: 0 !important;
        width: 0 !important;            /* 横向也收缩 */
        height: 0 !important;
        line-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        display: block !important;
        transition: none !important;     /* 折叠时不需要缓慢动画，立即隐藏 */
    }
    .hyplus-toc-container:has(.hyplus-toc-toggle[aria-label="显示"]) {
        max-width: max-content !important; /* 仅容纳标题与按钮 */
    }
    
    /* Hyplus TOC 通用样式 */
    /* .hyplus-toc-container {
        margin: 0 0 18px 0;
    } */
    .hyplus-toc-content ul {
        list-style-type: none;
        padding-left: 0;
        margin: 10px 0;
    }
    .hyplus-toc-content ul li {
        margin-bottom: 10px;
    }
    .hyplus-toc-content ul li a {
        text-decoration: none;
        color: #0073aa;
        transition: color 0.2s ease;
    }
    .hyplus-toc-content ul li a:hover {
        color: red;
    }
    .hyplus-toc-content ul li.level-1 { margin-left: 0px; }
    .hyplus-toc-content ul li.level-2 { margin-left: 15px; }
    .hyplus-toc-content ul li.level-3 { margin-left: 30px; }
    .hyplus-toc-content ul li.level-4 { margin-left: 45px; }
    .hyplus-toc-content ul li.level-5 { margin-left: 60px; }
    .hyplus-toc-content ul li.level-6 { margin-left: 75px; }

    /* post模式专用样式 */
	.hyplus-toc-container[data-toc-mode="post"] {
		background: #fbfdfe;
		border: 1px solid #b6dded;
		border-radius: 14px;
		box-shadow: 0 2px 6px rgba(0, 64, 128, 0.05);
		padding: 16px 22px 12px 22px;
		display: inline-block;
		max-width: 100%;
		margin: 0 0 18px 0;
		box-sizing: border-box;
		vertical-align: top;
	}
	.hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-header {
		text-align: center;
		margin-bottom: 10px;
		font-weight: bold;
		color: #333;
		padding: 0;
	}
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-2 { margin-left: 12px; }
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-3 { margin-left: 24px; }
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-4 { margin-left: 36px; }
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-5 { margin-left: 48px; }
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-6 { margin-left: 60px; }
    </style>
    <script>
    (function(){
        // ============ TOC 核心工具函数 ============
        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days*24*60*60*1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }

        // 提取节点下所有TextNode内容，忽略标签
        function getPureText(node) {
            var text = '';
            node.childNodes.forEach(function(child) {
                if (child.nodeType === Node.TEXT_NODE) {
                    text += child.textContent;
                }
            });
            return text.trim();
        }

        // 全局缓存：一次性预处理所有headers的anchor和pureText（页面级别，只执行一次）
        var cachedValidHeaders = null;

        // 一次性预处理所有headers，确定anchor和pureText，供所有mode共享，避免重复计算
        function preprocessAllHeaders() {
            if (cachedValidHeaders !== null) return; // 已缓存，直接返回
            
            var article = document.querySelector('article') || document.getElementById('main') || document.body;
            var headers = article.querySelectorAll('h1, h2, h3, h4, h5, h6');
            var pattern = /^(第\d+|(?:[0-9]+|[A-Z])(?:\.(?:[0-9]+|[A-Z]))* )/;
            var anchorSet = new Set();

            function getHeaderTextWithoutSup(header) {
                var clone = header.cloneNode(true);
                var sups = clone.querySelectorAll('sup');
                sups.forEach(function(sup){ sup.remove(); });
                return clone.textContent.trim();
            }

            var validHeaders = [];
            Array.prototype.forEach.call(headers, function(header) {
                if (header.classList && header.classList.contains('entry-title')) return;
                var pureText = getHeaderTextWithoutSup(header);
                if (pattern.test(pureText)) {
                    // 为每个header一次性预处理和存储anchor
                    var originalId = header.getAttribute('id');
                    var baseAnchor = originalId ? originalId.replace(/_.+$/, '') : pureText.replace(/[^a-zA-Z0-9\s]/g, '').replace(/\s+/g, '_');
                    var anchor = baseAnchor;
                    var suffix = 2;
                    while (anchorSet.has(anchor)) {
                        anchor = baseAnchor + '_' + suffix;
                        suffix++;
                    }
                    anchorSet.add(anchor);
                    // 一次性存储anchor和pureText到dataset，后续所有mode共享
                    header.dataset.hypluscurrentAnchor = anchor;
                    header.dataset.hypluspureText = pureText;
                    header.id = anchor;
                    validHeaders.push({header: header, pureText: pureText});
                }
            });
            cachedValidHeaders = validHeaders;
        }

        function generateToc(container, mode, hideParent, emptyMsg) {
            // 确保已完成全局预处理（只会在第一个mode触发，后续mode直接使用缓存）
            preprocessAllHeaders();
            
            var validHeaders = cachedValidHeaders;
            var tocContent = container.querySelector('.hyplus-toc-content');
            var tocHeader = container.querySelector('.hyplus-toc-header');
            // 只捕获两种格式：1. 数字/字母+点分段（如1、2.1、3.A.4、B.1.3）；2. “第”+阿拉伯数字（如第1、第2、第3）
            var tocSection = (mode === 'ub') ? container.closest('.toc-section') : container;
            if (validHeaders.length === 0) {
                if (mode === 'ub' && emptyMsg === 'true') {
                    if (tocHeader) tocHeader.style.display = 'block';
                    var style = 'margin-top:20px;text-align:center;color:gray;font-style:italic;font-size:1.25em;';
                    tocContent.innerHTML = '<div class="hyplus-toc-empty hyplus-unselectable" style="' + style + '">当前无可用目录</div>';
                    if (tocSection) tocSection.style.display = 'block';
                } else {
                    if (tocHeader) tocHeader.style.display = 'none';
                    tocContent.innerHTML = '';
                    if (tocSection) tocSection.style.display = 'none';
                    if (mode === 'widget' && hideParent === 'true') {
                        var parent = container.parentElement;
                        while (parent && parent !== document.body) {
                            if (parent.classList.contains('widget') || parent.classList.contains('widget-area') || parent.classList.contains('sidebar-widget')) {
                                parent.style.display = 'none';
                                break;
                            }
                            parent = parent.parentElement;
                        }
                    }
                }
                return;
            } else {
                if (tocHeader) tocHeader.style.display = 'block';
                if (tocSection) tocSection.style.display = 'inline-block';
            }

            var ul = document.createElement('ul');
            validHeaders.forEach(function(item){
                var header = item.header;
                var titleText = item.pureText;
                // 所有anchor已在预处理阶段确定并存储到dataset（所有mode共享同一份anchor）
                var anchor = header.dataset.hypluscurrentAnchor;

                var li = document.createElement('li');
                var a = document.createElement('a');
                a.textContent = titleText;
                a.href = '#' + anchor;
                var level = parseInt(header.tagName.substring(1), 10);
                li.className = 'level-' + level;
                li.appendChild(a);
                ul.appendChild(li);
            });
            tocContent.innerHTML = '';
            tocContent.appendChild(ul);

            tocContent.addEventListener('click', function(e){
                if (e.target.tagName.toLowerCase() === 'a') {
                    // 全局锚点处理函数会统一处理滚动，这里只处理 UB 模式的导航隐藏
                    if (mode === 'ub') {
                        var navContainer = document.getElementById('navContainer');
                        if (navContainer) {
                            setTimeout(function(){
                                navContainer.style.display = "none";
                                document.body.classList.remove("nav-open");
                            }, 50);
                        }
                    }
                }
            });
        }

        function insertPostToc() {
            var container = document.querySelector('.hyplus-toc-container[data-toc-mode=post]');
            if (!container) return;
            var hideParent = container.getAttribute('data-hideparent');
            var emptyMsg = container.getAttribute('data-emptymsg') || 'true';
            generateToc(container, 'post', hideParent, emptyMsg);

            // 初始化折叠按钮与状态（默认展示）
            var header = container.querySelector('.hyplus-toc-header');
            var content = container.querySelector('.hyplus-toc-content');
            if (!header || !content) return;
            var toggleBtn = header.querySelector('.hyplus-toc-toggle');
            if (!toggleBtn) {
                toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'hyplus-toc-toggle';
                header.appendChild(toggleBtn);
            }

            var cookieKey = 'hyplus_toc_post_collapsed';
            var collapsed = getCookie(cookieKey) === '1';

            // 动画辅助函数
            function animateOpen(element) {
                element.style.display = 'block';
                element.style.overflow = 'hidden';
                element.style.maxHeight = '0px';
                element.style.opacity = '0';
                element.style.transition = 'max-height 0.25s ease, opacity 0.25s ease';
                var target = element.scrollHeight + 'px';
                requestAnimationFrame(function(){
                    element.style.maxHeight = target;
                    element.style.opacity = '1';
                });
                setTimeout(function(){
                    element.style.maxHeight = '';
                    element.style.overflow = '';
                    element.style.transition = '';
                }, 300);
            }

            function animateClose(element) {
                element.style.overflow = 'hidden';
                element.style.maxHeight = element.scrollHeight + 'px';
                element.style.opacity = '1';
                element.style.transition = 'max-height 0.25s ease, opacity 0.25s ease';
                requestAnimationFrame(function(){
                    element.style.maxHeight = '0px';
                    element.style.opacity = '0';
                });
                setTimeout(function(){
                    element.style.display = 'none';
                    element.style.maxHeight = '';
                    element.style.overflow = '';
                    element.style.transition = '';
                }, 300);
            }

            function applyState() {
                if (collapsed) {
                    content.style.display = 'none';
                    toggleBtn.textContent = '+'; // 显示
                    toggleBtn.setAttribute('aria-label', '显示');
                    toggleBtn.setAttribute('title', '显示');
                } else {
                    content.style.display = '';
                    toggleBtn.textContent = '-'; // 折叠
                    toggleBtn.setAttribute('aria-label', '折叠');
                    toggleBtn.setAttribute('title', '折叠');
                }
            }

            applyState();

            toggleBtn.addEventListener('click', function(){
                collapsed = !collapsed;
                setCookie(cookieKey, collapsed ? '1' : '0', 365);
                if (collapsed) {
                    animateClose(content);
                    toggleBtn.textContent = '+';
                    toggleBtn.setAttribute('aria-label', '显示');
                    toggleBtn.setAttribute('title', '显示');
                } else {
                    animateOpen(content);
                    toggleBtn.textContent = '-';
                    toggleBtn.setAttribute('aria-label', '折叠');
                    toggleBtn.setAttribute('title', '折叠');
                }
            });
        }

        // 更新当前活跃的导航项（高亮为粗体）
        function updateActiveNavItem(container) {
            var HEADER_HEIGHT = 70;
            var links = container.querySelectorAll('.hyplus-toc-content a');
            
            // 缓存：初始化时建立链接和目标元素的映射，避免重复 DOM 查询
            var linkElementMap = [];
            links.forEach(function(link){
                var href = link.getAttribute('href');
                var anchorId = href.substring(1);
                var targetElement = document.getElementById(anchorId);
                if (targetElement) {
                    linkElementMap.push({
                        link: link,
                        element: targetElement
                    });
                }
            });
            
            if (linkElementMap.length === 0) return; // 没有有效的链接，无需监听
            
            var currentActiveLink = null;
            
            function updateActive() {
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                var scrollOffset = scrollTop + HEADER_HEIGHT + 45;  // 多加若干px作为缓冲
                var nextActiveLink = null;
                var minDistance = Infinity;
                
                // 单次遍历：找到最接近顶部的链接
                linkElementMap.forEach(function(item){
                    var rect = item.element.getBoundingClientRect();
                    var headerTop = rect.top + scrollTop;
                    var distance = Math.abs(headerTop - scrollOffset);
                    
                    if (headerTop <= scrollOffset && distance < minDistance) {
                        nextActiveLink = item.link;
                        minDistance = distance;
                    }
                });
                
                // 只在活跃链接真的改变时才更新 DOM，避免不必要的重排
                if (nextActiveLink !== currentActiveLink) {
                    if (currentActiveLink) {
                        currentActiveLink.classList.remove('hyplus-toc-active');
                    }
                    if (nextActiveLink) {
                        nextActiveLink.classList.add('hyplus-toc-active');
                    }
                    currentActiveLink = nextActiveLink;
                }
            }
            
            // 使用节流防止频繁更新
            var scrollTimeout;
            function throttledUpdate() {
                if (scrollTimeout) return;
                scrollTimeout = setTimeout(function(){
                    updateActive();
                    scrollTimeout = null;
                }, 300);
            }
            
            // 初始更新
            updateActive();
            
            // 监听滚动事件
            window.addEventListener('scroll', throttledUpdate, false);
        }

        // 全局处理所有锚点链接点击事件，减去 sticky header 高度
        function handleAllAnchorLinks() {
            var HEADER_HEIGHT = 70; // Sticky header height with a little extra offset
            document.addEventListener('click', function(e){
                var target = e.target.closest('a[href*="#"]');
                if (!target) return;
                
                var href = target.getAttribute('href');
                if (href === '#' || !href.includes('#')) return;
                
                var anchorId = href.substring(1);
                var targetElement = document.getElementById(anchorId);
                if (!targetElement) return;
                
                e.preventDefault();
                var rect = targetElement.getBoundingClientRect();
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                var targetY = rect.top + scrollTop - HEADER_HEIGHT;
                window.scrollTo({top: targetY, behavior: "smooth"});
            }, true);
            
            // 处理页面加载时的哈希导航
            if (window.location.hash) {
                var targetId = window.location.hash.substring(1);
                var targetElement = document.getElementById(targetId);
                if (targetElement) {
                    setTimeout(function(){
                        var rect = targetElement.getBoundingClientRect();
                        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        var targetY = rect.top + scrollTop - HEADER_HEIGHT;
                        window.scrollTo({top: targetY});
                    }, 100);
                }
            }
        }

        function initToc() {
            var containers = document.querySelectorAll('.hyplus-toc-container');
            containers.forEach(function(container){
                var mode = container.getAttribute('data-toc-mode');
                var hideParent = container.getAttribute('data-hideparent');
                var emptyMsg = container.getAttribute('data-emptymsg') || 'true';
                if (mode === 'post') return;
                generateToc(container, mode, hideParent, emptyMsg);
                
                // 仅在 ub 和 widget 模式下启用活跃导航项高亮
                if (mode === 'ub' || mode === 'widget') {
                    updateActiveNavItem(container);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function(){
            insertPostToc();
            initToc();
            handleAllAnchorLinks();
        });
    })();
    </script>
    <?php
}

// 短代码处理函数
function hyplus_render_toc_shortcode($atts) {
    $atts = shortcode_atts([
        'mode' => 'post',
        'hideparent' => 'true',
        'emptymsg' => 'true'
    ], $atts);

    $mode = $atts['mode'];
    $hideparent = ($atts['hideparent'] === 'false') ? 'false' : 'true'; // 默认为true
    $emptymsg = ($atts['emptymsg'] === 'false') ? 'false' : 'true'; // 默认为true
    
    // 在第一个短代码时输出脚本和样式
    hyplus_output_toc_scripts();
    
    ob_start();
    ?>
    <div class="hyplus-toc-container" <?php if ($mode === 'post'): ?> style="margin-top: 1em;" <?php endif; ?> data-toc-mode="<?php echo esc_attr($mode); ?>" data-hideparent="<?php echo esc_attr($hideparent); ?>" data-emptymsg="<?php echo esc_attr($emptymsg); ?>"<?php if ($mode === 'post') { echo ' data-post-id="' . esc_attr(get_the_ID()) . '"'; } ?>>
        <?php if ($mode === 'post'): ?>
            <div class="hyplus-toc-header">Hyplus目录<button type="button" class="hyplus-toc-toggle" aria-label="折叠" title="折叠">-</button></div>
        <?php endif; ?>
        <div class="hyplus-toc-content"></div>
    </div>
    <?php if ($mode === 'post'): ?>
        <br>
    <?php endif;
    return ob_get_clean();
}

add_shortcode('toc', 'hyplus_render_toc_shortcode');
?>