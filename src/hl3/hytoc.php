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
    // if (!is_singular('post')) return $content;
    if (strpos($content, '[toc') !== false) return $content; // 已有短代码则不自动插入

    // 匹配第一个被TOC捕获的标题（以数字或大写字母序号的h1-h6，支持用点分段，如 B.1 或 C.D.3）
    if (preg_match('/(<h[1-6][^>]*>\s*[0-9A-Z]+(\.[0-9A-Z]+)*(\)|\.)?\s.*?<\/h[1-6]>)/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        $pos = $matches[0][1];
        $toc = '[toc]';
        // 在第一个被TOC捕获的标题前插入
        return substr($content, 0, $pos) . $toc . substr($content, $pos);
    }
    return $content;
}

// 输出 TOC 脚本和样式（仅一次）
function hyplus_output_toc_scripts() {
    static $scripts_output = false;
    if ($scripts_output) return;
    $scripts_output = true;
    ?>
    <style>
    .hyplus-toc-header { position: relative; display: block; text-align: center; padding-right: 44px; }
    .hyplus-toc-header::after { content: ""; display: inline-block; width: 44px; height: 1px; vertical-align: middle; }
    .hyplus-toc-toggle { z-index: 1; }
    /* 容器宽度随内容而定，避免 100% 铺满 */
    .hyplus-toc-container {
        display: inline-block;
        width: auto;
        max-width: 100%;
        box-sizing: border-box;
        vertical-align: top;
    }
    /* 在post模式下的特定样式
     .hyplus-toc-container[data-toc-mode=post] in ultimate_buttons.php */

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
        transition: transform 0.15s ease;
    }
    .hyplus-toc-toggle[aria-label="显示"]::after { content: '➕'; }
    /* .hyplus-toc-toggle:hover::after { transform: translateY(1px) scale(1.1); } */
    /* .hyplus-toc-toggle:active::after { transform: translateY(1px) scale(0.95); } */
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
    .hyplus-toc-content ul, .hyplus-toc-content ol { margin: 0; }
    /* 当按钮处于“显示”（即当前折叠）状态时，折叠内容 */
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
    /* 移除之前在折叠时强制控制容器宽度的规则，让两种状态都由内容决定 */
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

        function generateToc(container, mode, hideParent, emptyMsg) {
            var article = document.querySelector('article') || document.getElementById('main') || document.body;
            var headers = article.querySelectorAll('h1, h2, h3, h4, h5, h6');
            var tocContent = container.querySelector('.hyplus-toc-content');
            var tocHeader = container.querySelector('.hyplus-toc-header');
            // 匹配以数字或大写字母开头的序号，支持点分段，例如: 1.2, B, B.1, C.D.3
            var pattern = /^[0-9A-Z]+(\.[0-9A-Z]+)*(\)|\.)?[\s]/;
            var anchorMap = {};

            var validHeaders = [];
            headers.forEach(function(header){
                var pureText = getPureText(header);
                if (pureText.match(pattern)) validHeaders.push({header: header, pureText: pureText});
            });

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
                var baseAnchor = titleText.replace(/[^a-zA-Z0-9\s]/g, '').replace(/\s+/g, '_');
                var anchor = baseAnchor;
                var suffix = 2;
                while (anchorMap[anchor]) {
                    anchor = baseAnchor + '_' + suffix;
                    suffix++;
                }
                anchorMap[anchor] = true;
                header.id = anchor;

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

        function initToc() {
            var containers = document.querySelectorAll('.hyplus-toc-container');
            containers.forEach(function(container){
                var mode = container.getAttribute('data-toc-mode');
                var hideParent = container.getAttribute('data-hideparent');
                var emptyMsg = container.getAttribute('data-emptymsg') || 'true';
                if (mode === 'post') return;
                generateToc(container, mode, hideParent, emptyMsg);
            });
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
    <div class="hyplus-toc-container" data-toc-mode="<?php echo esc_attr($mode); ?>" data-hideparent="<?php echo esc_attr($hideparent); ?>" data-emptymsg="<?php echo esc_attr($emptymsg); ?>"<?php if ($mode === 'post') { echo ' data-post-id="' . esc_attr(get_the_ID()) . '"'; } ?>>
        <?php if ($mode === 'post'): ?>
            <div class="hyplus-toc-header">Hyplus目录<button type="button" class="hyplus-toc-toggle" aria-label="折叠" title="折叠">-</button></div>
        <?php endif; ?>
        <div class="hyplus-toc-content"></div>
    </div>
    <?php if ($mode === 'post'): ?>
        <br>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}

add_shortcode('toc', 'hyplus_render_toc_shortcode');
?>