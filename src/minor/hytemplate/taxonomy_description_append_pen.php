<?php
/** Show the Pen Date in Taxonomy Description & Random Post Button
 * Description: 在分类描述末尾添加随机博文按钮和编辑按钮（仅管理员可见）
 * Code type: universal (html + js + php)
 * 优化版本：使用 Fetch API，减少代码冗余
 */
// 分类页面插入随机博文按钮和编辑按钮
add_action('wp_footer', function() {
    if (is_category() || is_tax()) {
        $term = get_queried_object();
        if (!$term || !isset($term->term_id, $term->taxonomy)) {
            return;
        }
        
        $term_id = $term->term_id;
        $edit_link = get_edit_term_link($term_id, $term->taxonomy);
        
        if (!$edit_link) {
            return;
        }
        
        $term_url = get_term_link($term_id, $term->taxonomy);
        $term_name = $term->name;
        
        // 获取该分类下最新修改的文章时间
        $latest_posts = get_posts(array(
            'cat' => $term_id,
            'orderby' => 'modified',
            'order' => 'DESC',
            'posts_per_page' => 1,
            'post_status' => 'publish',
        ));
        
        $lastModifiedDate = '';
        $lastModifiedTime = '';
        if (!empty($latest_posts)) {
            $lastModifiedDate = get_the_modified_date('Y年n月j日', $latest_posts[0]->ID);
            $lastModifiedTime = get_the_modified_time('H:i', $latest_posts[0]->ID);
        }
        
        $term_id_js = esc_js($term_id);
        $nonce_js = esc_js(wp_create_nonce('randpost_nonce'));
        $ajax_url = esc_js(esc_url(admin_url('admin-ajax.php')));
        $term_url_js = esc_js(esc_url($term_url));
        $term_name_js = esc_js($term_name);
        
        // 检查用户是否为管理员（避免 shortcode 开销）
        $show_edit_btn = current_user_can('manage_categories') ? 1 : 0;
        
        // 更新信息
        $update_info = '';
        if (!empty($lastModifiedDate)) {
            $update_info = sprintf(
                '&nbsp;<span class="updated-on" style="display: inline; color: #575760;">更新于 %s %s</span><span class="hyplus-unselectable">&nbsp;</span>',
                esc_html($lastModifiedDate),
                esc_html($lastModifiedTime)
            );
        }
        
        // 生成按钮 HTML（条件性包含编辑按钮）
        $edit_btn_html = $show_edit_btn 
            ? sprintf('&nbsp;&nbsp;<a href="%s" target="_blank" title="编辑分类" style="text-decoration: none;"><span style="cursor: pointer;">🖊️</span></a>', esc_url($edit_link))
            : '';
        
        // 分享和打印按钮
        $share_print_html = sprintf(
            '&nbsp;&nbsp;<span style="display: inline-block;"><a href="#" onclick="window.shareArticle(\'%s\', \'%s\'); return false;" title="分享页面" style="text-decoration: none;">📤</a></span>' .
            '&nbsp;&nbsp;<span style="display: inline-block;"><a href="javascript:window.print();" title="打印页面（建议先在Hyplus设置隐藏必要元素）" onclick="window.print(); return false;" style="text-decoration: none;">🖨</a></span>',
            $term_url_js,
            $term_name_js
        );
        
        $buttons_html = sprintf(
            '%s<span class="hyplus-unselectable">&nbsp;<button id="taxonomy-random-post-btn" title="随机博文" type="button" style="cursor: pointer; border: none; background: none; padding: 0; font-size: 1em;">🎲</button>%s%s</span>',
            $update_info,
            $share_print_html,
            $edit_btn_html
        );
        
        $buttons_json = wp_json_encode($buttons_html);
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const description = document.querySelector('.taxonomy-description p');
        if (!description) return;
        
        // 插入按钮组合（只执行一次）
        description.insertAdjacentHTML('beforeend', <?php echo $buttons_json; ?>);
        
        // 绑定随机博文按钮事件
        const randomPostBtn = document.getElementById('taxonomy-random-post-btn');
        if (randomPostBtn) {
            randomPostBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const formData = new URLSearchParams();
                formData.append('action', 'get_random_post');
                formData.append('category', '<?php echo $term_id_js; ?>');
                formData.append('nonce', '<?php echo $nonce_js; ?>');
                
                // 使用 Fetch API（更现代）
                fetch('<?php echo $ajax_url; ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data?.post_url) {
                        window.location.href = data.data.post_url;
                    } else {
                        alert(data.data?.message || '没有找到文章');
                    }
                })
                .catch(() => alert('网络请求失败'));
            });
        }
    }, { once: true }); // { once: true } 确保 DOMContentLoaded 只处理一次

    // 分享功能
    window.shareArticle = function(url, title) {
        if (navigator.share) {
            navigator.share({ title: title, url: url })
                .then(() => console.log('分享成功'))
                .catch(err => console.error('分享失败', err));
        } else {
            alert('您的浏览器不支持此分享功能');
        }
    };
</script>
<?php
    }
});
?>