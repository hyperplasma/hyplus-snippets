<?php
/**
 * Meta Enhancer - 文章元信息增强功能
 * 1. Articles Overall - Display cnt and sth else above：在文章标题上方显示字数、阅读估时间等信息
 * 2. 系列链接功能：如果文章设置了`hy_series_id`，显示 hysnip 短代码链接；可通过`hy_series_button_label`设置系列按钮标签（逗号分隔），默认使用数字索引。
 */
add_action('generate_before_entry_title', 'lh_single_cats_above_title');
add_action('wp_footer', 'hyplus_render_series_buttons_container');

/**
 * 获取系列博文
 * @param int $post_id 当前文章ID
 * @return array 返回系列博文对象数组
 */
function hyplus_get_series_posts($post_id) {
    $series_ids_raw = get_post_meta($post_id, 'hy_series_id', true);
    if (empty($series_ids_raw)) {
        return array();
    }
    
    $series_posts = array();
    if (ctype_digit((string)$series_ids_raw)) {
        // 单个ID
        $series_id = (int)$series_ids_raw;
        if ($series_id > 0) {
            $series_post = get_post($series_id);
            if ($series_post && $series_post->post_status === 'publish') {
                $series_posts[] = $series_post;
            }
        }
    } else {
        // 多个ID
        foreach (array_map('trim', explode(',', $series_ids_raw)) as $series_id) {
            $series_id = (int)$series_id;
            if ($series_id <= 0) continue;
            
            $series_post = get_post($series_id);
            if ($series_post && $series_post->post_status === 'publish') {
                $series_posts[] = $series_post;
            }
        }
    }
    
    return $series_posts;
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
    $output = '<span title="每个汉字或其他连续非空白字符算1个字">' . $text_num . '字</span>&nbsp;<span title="预估阅读时间（200字/分钟）">' . $read_time . '分钟</span>';
    return $output;
}

function lh_single_cats_above_title() {
    if (is_single()) {
        global $post;
        
        $counter_str = count_words_read_time();
        $emoji = '';
        
        // 检查是否为密码保护的文章
        if (!empty($post->post_password)) {
            $emoji .= '🔐';
        }
        
        // 获取系列博文
        $series_posts = hyplus_get_series_posts($post->ID);
        $series_html = '';
        
        // 获取按钮标签配置（逗号分隔的字符串）
        $labels_raw = get_post_meta($post->ID, 'hy_series_button_label', true);
        $labels = array();
        if (!empty($labels_raw)) {
            $labels = array_map('trim', explode(',', $labels_raw));
        }
        
        // 生成短代码（仅包含标签为数字的系列博文）
        if (!empty($series_posts)) {
            foreach ($series_posts as $index => $series_post) {
                // 获取对应位置的标签
                $button_label = isset($labels[$index]) && !empty($labels[$index]) 
                    ? $labels[$index] 
                    : ($index + 1);
                
                // 仅当标签为数字时才生成hysnip短代码
                if (!is_numeric($button_label)) {
                    continue;
                }
                
                // 正确转义短代码参数
                $series_url = esc_url(get_permalink($series_post->ID));
                $series_title = esc_attr($series_post->post_title);
                
                $series_html .= sprintf(
                    " [hysnip href='%s' name='%s' title='%s' mode='link' async='1']",
                    $series_url,
                    $series_title,
                    $series_title
                );
            }
        }

        ?>
        <div class="post-buttons">
            <span class="entry-meta post-meta">
                <?php if (!empty($series_html)): ?>
                    <span>
                        <?php echo do_shortcode($series_html); ?>
                    </span>
                <?php endif; ?>
                <span style="color: green;">
                    <?php echo $counter_str; ?><span class="hyplus-unselectable"><?php echo $emoji ? '&nbsp;<a class="hyplus-scale" href="/user/akira37/"  style="display: inline-block;" title="受限内容">' . $emoji . '</a>' : ''; ?></span>
                </span>
            </span>
        </div>
        <?php
    }
}

/**
 * 在页脚渲染系列按钮群容器
 * 仅在单篇博文页面显示，为 #seriesButtonContainer 添加内容
 */
function hyplus_render_series_buttons_container() {
    // 仅在单篇文章页面执行
    if (!is_single()) {
        return;
    }
    
    global $post;
    
    // 获取系列博文
    $series_posts = hyplus_get_series_posts($post->ID);
    if (empty($series_posts)) {
        return;
    }
    
    // 获取当前文章的按钮标签配置（逗号分隔的字符串）
    $labels_raw = get_post_meta($post->ID, 'hy_series_button_label', true);
    $labels = array();
    if (!empty($labels_raw)) {
        $labels = array_map('trim', explode(',', $labels_raw));
    }
    
    // 构建按钮数据数组，使用 wp_json_encode() 统一转义
    $buttons_data = array();
    foreach ($series_posts as $index => $series_post) {
        // 获取对应位置的标签
        $button_label = isset($labels[$index]) && !empty($labels[$index]) 
            ? $labels[$index] 
            : ($index + 1); // fallback 到数字
        
        // 根据标签是否为数字来设置async参数
        $async = is_numeric($button_label) ? '1' : '0';
        
        $buttons_data[] = array(
            'href' => get_permalink($series_post->ID),
            'label' => $button_label,
            'title' => $series_post->post_title,
            'async' => $async
        );
    }
    
    // 直接输出 HTML，使用 wp_json_encode() 安全转义数据
    ?>
    <script>
        (function() {
            const container = document.getElementById('seriesButtonContainer');
            if (!container) return;
            
            const buttons = <?php echo wp_json_encode($buttons_data); ?>;
            
            buttons.forEach(btn => {
                const link = document.createElement('a');
                link.href = btn.href;
                link.target = '_blank';
                link.className = 'series-button hysnip-trigger';
                link.textContent = btn.label;
                link.title = btn.title;
                link.setAttribute('data-popup-title', btn.title);
                link.setAttribute('data-async', btn.async);
                container.appendChild(link);
            });
        })();
    </script>
    <?php
}
?>