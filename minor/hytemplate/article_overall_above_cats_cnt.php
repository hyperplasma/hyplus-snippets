<?php
/**
 * Articles Overall - Display cats, cnt and sth else above - PHP
 * 分类弹窗功能：点击分类按钮显示分类描述（如系列博文列表）
 */
add_action('generate_before_entry_title', 'lh_single_cats_above_title');
add_action('wp_ajax_nopriv_hyplus_get_category_description', 'hyplus_get_category_description_ajax');
add_action('wp_ajax_hyplus_get_category_description', 'hyplus_get_category_description_ajax');

// AJAX 处理器：获取分类描述
function hyplus_get_category_description_ajax() {
    check_ajax_referer('hyplus_category_popup', 'nonce');
    
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    if (!$category_id) {
        wp_send_json_error('Invalid category ID');
        wp_die();
    }
    
    $category = get_category($category_id);
    if (!$category || is_wp_error($category)) {
        wp_send_json_error('Category not found');
        wp_die();
    }
    
    $description = $category->description;
    if (empty($description)) {
        wp_send_json_error('No description');
        wp_die();
    }
    
    // 处理 HTML 内容（保留基本格式）
    $description = wp_kses_post($description);
    
    wp_send_json_success([
        'category_name' => $category->name,
        'category_id' => $category_id,
        'category_link' => get_category_link($category_id),
        'description' => $description
    ]);
    wp_die();
}

// 统计预估阅读时间
function count_words_read_time() {
    global $post;
    $text = html_entity_decode($post->post_content);
    
    // 按照 Typora 的方式计数：汉字1个算1个词，连续的非空白字符（字母、数字、符号等）也算1个词
    $chinese_chars = preg_match_all('/[\x{4E00}-\x{9FFF}]/u', $text);
    
    // 移除汉字后，统计连续的非空白字符序列
    $text_without_chinese = preg_replace('/[\x{4E00}-\x{9FFF}]/u', '', $text);
    $other_words = preg_match_all('/[^\s]+/u', $text_without_chinese);
    
    $text_num = $chinese_chars + $other_words;
    
    $read_time = $text_num > 0 ? ceil($text_num / 200) : 0;
    $output = '&nbsp;<span title="每个汉字或其他连续非空白字符算1个字">' . $text_num . '字</span>&nbsp;&nbsp;<span title="预估阅读时间（200字/分钟）">' . $read_time . '分钟</span>';
    return $output;
}

function lh_single_cats_above_title() {
    if (is_single()) {
        $categories = get_the_category();
        if (empty($categories)) {
            return;
        }
        
        $counter_str = count_words_read_time();
        $emoji = '';
        global $post;
        
        // 检查是否为密码保护的文章
        if (!empty($post->post_password)) {
            $emoji .= '🔐';
        }
        
        // 构建分类链接（按钮样式）
        $cat_links_html = '';
        foreach ($categories as $index => $category) {
            if ($index > 0) {
                $cat_links_html .= '<span style="margin: 0 2px;"> | </span>';
            }
            $cat_links_html .= sprintf(
                '<a href="#" class="hyplus-cat-popup-trigger hyplus-scale" data-category-id="%d" data-category-name="%s" style="text-decoration: none; cursor: pointer;">%s</a>',
                esc_attr($category->term_id),
                esc_attr($category->name),
                esc_html($category->name)
            );
        }

        ob_start();
        ?>
        <div class="post-buttons">
            <span class="entry-meta cat-links">
                <?php echo $cat_links_html; ?>
                <span style="color: green;">
                    <?php echo $counter_str; ?><span class="hyplus-unselectable"><?php echo $emoji ? '&nbsp;&nbsp;<a class="hyplus-scale" href="/user/akira37/"  style="text-decoration: none; display: inline-block;" title="受限内容">' . $emoji . '</a>' : ''; ?></span>
                </span>
            </span>
        </div>

        <!-- 分类描述弹窗容器 -->
        <div id="hyplus-category-popup" class="hyplus-category-popup-container" style="display: none;">
            <div id="hyplus-popup-content"></div>
        </div>

        <style>
        .hyplus-category-popup-container {
            position: fixed;
            z-index: 88888;
            max-width: 600px;
            max-height: 70vh;
            min-height: 100px;
            background: var(--hyplus-bg-container-solid);
            color: var(--hyplus-text-primary);
            border: 1px solid var(--hyplus-border-color-light);
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 64, 128, 0.5), 0 2px 8px rgba(0, 0, 0, 0.2);
            padding: 0;
            font-size: 0.9em;
            line-height: 1.6;
            display: none;
            word-break: break-word;
            pointer-events: auto;
            overflow: hidden;
            overflow-y: auto;
            overscroll-behavior: none;
            will-change: transform;
        }

        .hyplus-category-popup-container #hyplus-popup-content {
            padding: 10px 15px;
            font-size: 0.9em;
        }

        .hyplus-category-popup-container h3 {
            font-size: 1.1em;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .hyplus-category-popup-container .hyplus-category-title {
            text-align: center;
            font-size: 1.1em;
            font-weight: bold;
            margin: 0;
        }

        .hyplus-category-popup-container .hyplus-category-title a {
            text-decoration: none;
            color: var(--hyplus-primary-link-color);
            transition: color 0.2s ease;
        }

        .hyplus-category-popup-container .hyplus-category-title a:hover {
            color: var(--hyplus-link-hover-color);
        }

        /* 移动端适配 */
        @media screen and (max-width: 768px) {
            .hyplus-category-popup-container {
                max-width: 85vw;
                max-height: 60vh;
            }
        }
        </style>

        <script>
        (function() {
            var popup = document.getElementById('hyplus-category-popup');
            var popupContent = document.getElementById('hyplus-popup-content');
            var categoryCache = {}; // 缓存分类描述
            var categoryLinkCache = {}; // 缓存分类链接
            var currentActiveLink = null; // 当前激活的分类链接
            var minLeftPosition = null; // 最左侧分类的left位置

            // 计算最左侧分类的位置
            function calculateMinLeftPosition() {
                var triggers = document.querySelectorAll('.hyplus-cat-popup-trigger');
                if (triggers.length === 0) return 0;
                
                var minLeft = Infinity;
                triggers.forEach(function(trigger) {
                    var rect = trigger.getBoundingClientRect();
                    var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
                    var left = rect.left + scrollLeft;
                    if (left < minLeft) {
                        minLeft = left;
                    }
                });
                
                return minLeft === Infinity ? 0 : minLeft;
            }

            // 绑定所有分类链接点击事件
            document.querySelectorAll('.hyplus-cat-popup-trigger').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    var categoryId = this.getAttribute('data-category-id');
                    var categoryName = this.getAttribute('data-category-name');
                    
                    // Toggle逻辑：如果点击的是当前激活的分类，则关闭弹窗
                    if (currentActiveLink === this && popup.style.display !== 'none') {
                        closePopup();
                        return;
                    }
                    
                    showCategoryPopup(categoryId, categoryName, this);
                });
            });

            function extractContentFromFirstH3(htmlContent) {
                // 创建临时容器来解析HTML
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = htmlContent;
                
                // 查找第一个有id属性的h3标签
                var h3WithId = tempDiv.querySelector('h3[id]');
                
                if (!h3WithId) {
                    // 没有找到有id的h3标签，返回空
                    return null;
                }
                
                // 获取该h3标签及其后续的所有内容
                var resultHtml = '';
                var element = h3WithId;
                while (element) {
                    resultHtml += element.outerHTML;
                    element = element.nextElementSibling;
                }
                
                return resultHtml;
            }

            function showCategoryPopup(categoryId, categoryName, triggerElement) {
                // 更新活跃分类
                updateActiveLink(triggerElement);
                
                // 检查缓存
                if (categoryCache[categoryId] !== undefined) {
                    // 使用缓存内容
                    popup.style.display = 'block';
                    displayCategoryContent(categoryCache[categoryId], categoryLinkCache[categoryId]);
                    positionPopup();
                    return;
                }
                
                // 显示加载状态
                popupContent.innerHTML = '<div style="text-align: center; padding: 20px;">加载中...</div>';
                
                popup.style.display = 'block';
                positionPopup();

                // 发送 AJAX 请求获取分类描述
                var data = new FormData();
                data.append('action', 'hyplus_get_category_description');
                data.append('category_id', categoryId);
                data.append('nonce', '<?php echo wp_create_nonce('hyplus_category_popup'); ?>');

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: data
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(result) {
                    if (result.success) {
                        var fullDescription = result.data.description;
                        var categoryLink = result.data.category_link;
                        var extractedContent = extractContentFromFirstH3(fullDescription);
                        
                        // 缓存分类链接
                        categoryLinkCache[categoryId] = categoryLink;
                        
                        if (extractedContent) {
                            // 缓存提取后的内容
                            categoryCache[categoryId] = extractedContent;
                            displayCategoryContent(extractedContent, categoryLink);
                        } else {
                            // 没有找到有id的h3标签
                            categoryCache[categoryId] = null;
                            displayCategoryContent(null, categoryLink);
                        }
                    } else {
                        categoryCache[categoryId] = null;
                        displayCategoryContent(null, null);
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    categoryCache[categoryId] = null;
                    displayCategoryContent(null, null);
                });
            }

            function updateActiveLink(triggerElement) {
                // 移除前一个激活链接的颜色
                if (currentActiveLink) {
                    currentActiveLink.style.color = '';
                }
                
                // 设置新的激活链接颜色
                currentActiveLink = triggerElement;
                currentActiveLink.style.color = 'var(--hyplus-link-hover-color)';
            }

            function positionPopup() {
                if (minLeftPosition === null) {
                    minLeftPosition = calculateMinLeftPosition();
                }
                
                var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // 使用最左侧分类的位置作为弹窗的左侧位置
                popup.style.left = minLeftPosition + 'px';
                // 垂直位置在当前激活元素下方
                var rect = currentActiveLink.getBoundingClientRect();
                popup.style.top = (rect.bottom + scrollTop + 10) + 'px';
            }

            function displayCategoryContent(content, categoryLink) {
                if (content === null) {
                    popupContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;">无系列内容</div>';
                } else {
                    // 获取当前激活分类的名称
                    var categoryName = currentActiveLink ? currentActiveLink.getAttribute('data-category-name') : '分类';
                    var titleHtml = categoryLink 
                        ? '<div class="hyplus-category-title"><a href="' + categoryLink + '">' + categoryName + '</a></div>'
                        : '';
                    popupContent.innerHTML = titleHtml + content;
                }
            }

            function closePopup() {
                popup.style.display = 'none';
                // 移除颜色
                if (currentActiveLink) {
                    currentActiveLink.style.color = '';
                    currentActiveLink = null;
                }
            }

            // 点击弹窗外关闭
            document.addEventListener('click', function(e) {
                if (popup && popup.style.display !== 'none') {
                    // 如果点击目标不在弹窗内，也不是触发链接，则关闭弹窗
                    if (!popup.contains(e.target) && !e.target.classList.contains('hyplus-cat-popup-trigger')) {
                        closePopup();
                    }
                }
            });

            // 防止弹窗内的点击导致关闭
            popup.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // ESC键关闭弹窗
            document.addEventListener('keydown', function(e) {
                if ((e.key === 'Escape' || e.keyCode === 27) && popup.style.display !== 'none') {
                    closePopup();
                }
            });
        })();
        </script>
        <?php
        echo ob_get_clean();
    }
}
?>