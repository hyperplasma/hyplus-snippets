<?php
/**
 * Meta Enhancer - 文章元信息增强功能
 * 1. Articles Overall - Display cnt and sth else above：在文章标题上方显示字数、阅读估时间等信息
 * 2. 系列链接功能：如果文章设置了`hy_series_id`，显示 hysnip 短代码链接
 */
add_action('generate_before_entry_title', 'lh_single_cats_above_title');
add_action('wp_footer', 'hyplus_render_series_buttons_container');

/**
 * 获取系列博文
 * @param int $post_id 当前文章ID
 * @return array 返回系列博文对象数组
 */
function hyplus_get_series_posts($post_id) {
    $cache_key = 'hyplus_series_posts_' . absint($post_id);
    $cached = wp_cache_get($cache_key, 'hyplus_series');

    if ($cached !== false) {
        return $cached;
    }

    $series_ids_raw = get_post_meta($post_id, 'hy_series_id', true);
    if (empty($series_ids_raw)) {
        $cached = array();
        wp_cache_set($cache_key, $cached, 'hyplus_series', 300);
        return $cached;
    }

    $series_ids = array();
    if (ctype_digit((string) $series_ids_raw)) {
        $series_id = (int) $series_ids_raw;
        if ($series_id > 0) {
            $series_ids[] = $series_id;
        }
    } else {
        foreach (array_map('trim', explode(',', $series_ids_raw)) as $series_id) {
            $series_id = (int) $series_id;
            if ($series_id > 0) {
                $series_ids[] = $series_id;
            }
        }
    }

    $cached = array();
    if (!empty($series_ids)) {
        $series_posts = get_posts(array(
            'post__in' => $series_ids,
            'post_type' => 'any',
            'post_status' => 'publish',
            'orderby' => 'post__in',
            'posts_per_page' => count($series_ids),
            'no_found_rows' => true,
            'suppress_filters' => true,
            'fields' => 'all',
        ));

        if (!empty($series_posts)) {
            $cached = $series_posts;
        }
    }

    wp_cache_set($cache_key, $cached, 'hyplus_series', 300);
    return $cached;
}

/**
 * 构建系列按钮配置
 * 第一个按钮固定使用 async=1，并作为快捷键目标
 *
 * @param int $post_id 当前文章ID
 * @return array 返回按钮配置数组
 */
function hyplus_get_series_button_specs($post_id) {
    $cache_key = 'hyplus_series_buttons_' . absint($post_id);
    $cached = wp_cache_get($cache_key, 'hyplus_series');

    if ($cached !== false) {
        return $cached;
    }

    $series_posts = hyplus_get_series_posts($post_id);
    $buttons = array();

    foreach ($series_posts as $index => $series_post) {
        $buttons[] = array(
            'post_id' => $series_post->ID,
            'label' => (string) ($index + 1),
            'title' => $series_post->post_title,
            'async' => $index === 0 ? '1' : '0',
            'shortcut_target' => $index === 0,
        );
    }

    wp_cache_set($cache_key, $buttons, 'hyplus_series', 300);
    return $buttons;
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

        $series_buttons = hyplus_get_series_button_specs($post->ID);
        $series_html = '';

        if (!empty($series_buttons)) {
            foreach ($series_buttons as $button) {
                $series_title = esc_attr($button['title']);

                $series_html .= sprintf(
                    " [hysnip id='%d' name='%s' title='%s' mode='link' async='%s']",
                    $button['post_id'],
                    $series_title,
                    $series_title,
                    $button['async']
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
                <span class="post-meta-counter">
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

    $series_buttons = hyplus_get_series_button_specs($post->ID);
    if (empty($series_buttons)) {
        return;
    }

    $buttons_data = array();
    foreach ($series_buttons as $button) {
        $edit_link = current_user_can('manage_options')
            ? html_entity_decode(get_edit_post_link($button['post_id']), ENT_QUOTES, 'UTF-8')
            : '';

        $buttons_data[] = array(
            'href' => get_permalink($button['post_id']),
            'post_id' => $button['post_id'],
            'label' => $button['label'],
            'title' => $button['title'],
            'async' => $button['async'],
            'edit_link' => $edit_link,
            'nonce' => wp_create_nonce('hysnip_popup_' . $button['post_id']),
            'shortcut_target' => $button['shortcut_target'] ? 1 : 0,
        );
    }

    // 直接输出 HTML，使用 wp_json_encode() 安全转义数据
    ?>
    <script>
        (function() {
            const container = document.getElementById('seriesButtonContainer');
            if (!container) return;

            const buttons = <?php echo wp_json_encode($buttons_data); ?>;
            if (!Array.isArray(buttons) || !buttons.length) return;

            let shortcutTargetButton = null;
            let initialized = false;

            const buildButtons = () => {
                if (initialized) {
                    return;
                }

                initialized = true;
                container.setAttribute('data-rendered', '1');

                const fragment = document.createDocumentFragment();
                buttons.forEach(btn => {
                    const link = document.createElement('a');
                    link.href = btn.href;
                    link.target = '_blank';
                    link.className = 'series-button hysnip-trigger';
                    link.textContent = btn.label;
                    link.title = btn.shortcut_target ? (btn.title + '（⌥A）') : btn.title;
                    link.setAttribute('data-post-id', btn.post_id);
                    link.setAttribute('data-popup-title', btn.title);
                    link.setAttribute('data-async', btn.async);
                    link.setAttribute('data-nonce', btn.nonce);
                    link.setAttribute('data-shortcut-target', btn.shortcut_target ? '1' : '0');
                    if (btn.edit_link) {
                        link.setAttribute('data-edit-link', btn.edit_link);
                    }
                    if (btn.shortcut_target) {
                        shortcutTargetButton = link;
                    }
                    fragment.appendChild(link);
                });

                container.appendChild(fragment);

                if (shortcutTargetButton) {
                    document.addEventListener('keydown', function(event) {
                        const isShortcutKey = event.altKey && !event.ctrlKey && !event.metaKey && !event.shiftKey &&
                            (event.key === 'a' || event.key === 'A' || event.key == 'å');

                        if (!isShortcutKey) {
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();

                        const popup = document.getElementById('hysnip-popup-wrapper');
                        const targetPostId = shortcutTargetButton.getAttribute('data-post-id');
                        const activePostId = popup ? popup.getAttribute('data-active-post-id') : null;
                        const isTargetPopupOpen = popup && popup.classList.contains('active') && activePostId === targetPostId;

                        if (isTargetPopupOpen) {
                            const closeBtn = popup.querySelector('.hysnip-close-btn');
                            if (closeBtn) {
                                closeBtn.click();
                            }
                            return;
                        }

                        shortcutTargetButton.click();
                    });
                }
            };

            const tryRender = () => {
                if (initialized) {
                    return;
                }

                const rect = container.getBoundingClientRect();
                const isVisible = rect.height > 0 || rect.width > 0 || rect.top < window.innerHeight;

                if (isVisible) {
                    buildButtons();
                    return;
                }

                if ('IntersectionObserver' in window) {
                    const observer = new IntersectionObserver((entries, obs) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                buildButtons();
                                obs.disconnect();
                            }
                        });
                    }, { rootMargin: '150px 0px' });

                    observer.observe(container);
                    return;
                }

                window.setTimeout(buildButtons, 100);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', tryRender, { once: true });
            } else {
                tryRender();
            }
        })();
    </script>
    <?php
}
?>