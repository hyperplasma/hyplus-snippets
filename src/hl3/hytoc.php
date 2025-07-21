<?php
/*
Plugin Name: HyTOC
Description: Hyplus 文章目录生成器，支持多种布局模式。
Version: 1.0
Author: Hyperplasma
*/

add_shortcode('toc', 'hyplus_render_toc_shortcode');

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

function hyplus_render_toc_shortcode($atts) {
    $atts = shortcode_atts([
        'mode' => 'post'
    ], $atts);

    $mode = $atts['mode'];
    ob_start();
    ?>
    <div class="hyplus-toc-container" data-toc-mode="<?php echo esc_attr($mode); ?>">
        <div class="hyplus-toc-header">Hyplus目录</div>
        <div class="hyplus-toc-content"></div>
    </div>
    <script>
    (function(){
        function generateToc(container, mode) {
            // 选择正文区域
            var article = document.querySelector('article') || document.getElementById('main') || document.body;
            var headers = article.querySelectorAll('h1, h2, h3, h4, h5, h6');
            var tocContent = container.querySelector('.hyplus-toc-content');
            var tocHeader = container.querySelector('.hyplus-toc-header');
            var pattern = /^[0-9]+(\.[0-9]+)*(\)|\.)?[\s]/;
            var anchorMap = {};

            // 只收录带数字序号的标题
            var validHeaders = [];
            headers.forEach(function(header){
                if (header.textContent.trim().match(pattern)) validHeaders.push(header);
            });

            // 目录显示/隐藏控制
            var tocSection = (mode === 'ub') ? container.closest('.toc-section') : container;
            if (validHeaders.length === 0) {
                if (tocHeader) tocHeader.style.display = 'none';
                tocContent.innerHTML = '';
                if (tocSection) tocSection.style.display = (mode === 'ub' ? 'none' : 'none');
                // widget模式下，隐藏父widget
                if (mode === 'widget') {
                    // 常见WordPress小工具父容器有 widget 或 widget-area 类
                    var parent = container.parentElement;
                    // 向上查找带 widget 或 widget-area 类的父元素
                    while (parent && parent !== document.body) {
                        if (parent.classList.contains('widget') || parent.classList.contains('widget-area') || parent.classList.contains('sidebar-widget')) {
                            parent.style.display = 'none';
                            break;
                        }
                        parent = parent.parentElement;
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

            // 点击目录跳转
            tocContent.addEventListener('click', function(e){
                if (e.target.tagName.toLowerCase() === 'a') {
                    e.preventDefault();
                    var targetId = e.target.getAttribute('href').substring(1);
                    var targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({behavior: "smooth"});
                    }
                    // UB模式下自动关闭弹窗
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

        // 自动插入post模式TOC到正文第一个被TOC捕获的标题前（已由PHP实现，这里只生成目录）
        function insertPostToc() {
            var container = document.querySelector('.hyplus-toc-container[data-toc-mode="post"]');
            if (!container) return;
            generateToc(container, 'post');
        }

        // 其他模式：直接生成
        function initToc() {
            var containers = document.querySelectorAll('.hyplus-toc-container');
            containers.forEach(function(container){
                var mode = container.getAttribute('data-toc-mode');
                if (mode === 'post') return; // post模式单独处理
                generateToc(container, mode);
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