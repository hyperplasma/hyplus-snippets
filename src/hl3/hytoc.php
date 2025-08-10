<?php
/*
 * Name: Hyplus TOC
 * Description: 生成Ultimate Buttons风格的目录，支持短代码[toc mode=ub|widget|post hideparent=true|false]，可自动插入正文第一个标题前。
 * Code type: PHP
 * Shortcode: [toc mode=post]
 */

// 自动在正文第一个被TOC捕获的标题前插入[toc]（仅文章页且未手动插入时）
add_filter('the_content', 'hyplus_auto_insert_toc_before_first_toc_heading');
function hyplus_auto_insert_toc_before_first_toc_heading($content) {
    if (!is_singular('post')) return $content;
    if (strpos($content, '[toc') !== false) return $content; // 已有短代码则不自动插入

    // 匹配第一个被TOC捕获的标题（带数字序号的h1-h6）
    if (preg_match('/(<h[1-6][^>]*>\s*\d+(\.\d+)*(\)|\.)?\s.*?<\/h[1-6]>)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
        $pos = $matches[0][1];
        $toc = '[toc]';
        // 在第一个被TOC捕获的标题前插入
        return substr($content, 0, $pos) . $toc . substr($content, $pos);
    }
    return $content;
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
    ob_start();
    ?>
    <div class="hyplus-toc-container" data-toc-mode="<?php echo esc_attr($mode); ?>" data-hideparent="<?php echo esc_attr($hideparent); ?>" data-emptymsg="<?php echo esc_attr($emptymsg); ?>">
        <?php if ($mode !== 'widget'): ?>
            <div class="hyplus-toc-header">Hyplus目录</div>
        <?php endif; ?>
        <div class="hyplus-toc-content"></div>
    </div>
    <script>
    (function(){
        function generateToc(container, mode, hideParent, emptyMsg) {
            var article = document.querySelector('article') || document.getElementById('main') || document.body;
            var headers = article.querySelectorAll('h1, h2, h3, h4, h5, h6');
            var tocContent = container.querySelector('.hyplus-toc-content');
            var tocHeader = container.querySelector('.hyplus-toc-header');
            var pattern = /^[0-9]+(\.[0-9]+)*(\)|\.)?[\s]/;
            var anchorMap = {};

            var validHeaders = [];
            headers.forEach(function(header){
                if (header.textContent.trim().match(pattern)) validHeaders.push(header);
            });

            var tocSection = (mode === 'ub') ? container.closest('.toc-section') : container;
            if (validHeaders.length === 0) {
                if (mode === 'widget' && emptyMsg === 'true') {
                    if (tocHeader) tocHeader.style.display = 'block';
                    tocContent.innerHTML = '<div class="hyplus-toc-empty hyplus-unselectable" style="margin-top:10px;text-align:center;color:#999;font-style:italic;">当前无可用目录</div>';
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
            validHeaders.forEach(function(header){
                var titleText = header.textContent.trim();
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
                    e.preventDefault();
                    var targetId = e.target.getAttribute('href').substring(1);
                    var targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        // 获取目标元素距离页面顶部的绝对位置，减去 sticky header 高度
                        var rect = targetElement.getBoundingClientRect();
                        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        var targetY = rect.top + scrollTop - 65;    // 减去 sticky header 高度
                        window.scrollTo({top: targetY, behavior: "smooth"});
                    }
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
            var container = document.querySelector('.hyplus-toc-container[data-toc-mode="post"]');
            if (!container) return;
            var hideParent = container.getAttribute('data-hideparent');
            var emptyMsg = container.getAttribute('data-emptymsg') || 'true';
            generateToc(container, 'post', hideParent, emptyMsg);
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

        document.addEventListener('DOMContentLoaded', function(){
            insertPostToc();
            initToc();
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('toc', 'hyplus_render_toc_shortcode');