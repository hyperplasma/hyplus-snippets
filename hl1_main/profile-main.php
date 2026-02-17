<?php
/** Hyplus 导航 - Profile - Main (Bio + Stylesheet + Script)
 * Description: 自定义注册版本 - 前后端分离；使用了hynav按钮样式
 * Code type: Universal Snippet (PHP Only)
 * Shortcode: [hyplus-profile]
 */

/**
 * Hyplus Profile 短代码处理
 * 前端显示逻辑
 */
function hyplus_profile_shortcode_callback() {
    // 只在主循环外或单页面调用此函数
    ob_start();
    ?>
    <style>
        body {
            background: var(--hyplus-bg-settings);
        }
        .profile-main-row {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            max-width: 900px;
            margin: 40px auto;
            padding: 0;
            align-items: stretch;
            box-sizing: border-box;
            justify-content: center;
        }
        .profile-main-col {
            flex: 1 1 0;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .profile-main-col.left {
            flex: 1 1 320px;
            min-width: 200px;
            max-width: 380px;
            box-sizing: border-box;
        }
        .profile-main-col.right {
            flex: 2 1 0;
            min-width: 0;
            box-sizing: border-box;
        }
        .profile-card,
        .profile-panel {
            background: var(--hyplus-bg-container);
            border-radius: 22px;
            box-shadow: 0 4px 24px var(--hyplus-shadow-light), 0 1.5px 6px var(--hyplus-shadow-light);
            padding: 36px 28px 32px 28px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            box-sizing: border-box;
        }
        .profile-card {
            justify-content: center;
        }
        .profile-panel {
            min-height: 340px;
            justify-content: flex-start;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--hyplus-primary-link-color);
            background: #fff;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px var(--hyplus-shadow-light);
        }
        /* 使用艺术字样式显示个人简介行 */
        .profile-desc {
            color: var(--hyplus-text-primary);
            font-size: 1.13em;
            margin-bottom: 18px;
            font-family: "Dancing Script", "Segoe Script", "Comic Sans MS", cursive, sans-serif;
            font-weight: 600;
        }
        .profile-desc a {
            color: inherit;
            text-decoration: none;
        }
        .profile-desc a:hover {
            text-decoration: none;
        }
        
        .profile-links {
            line-height: 1.7;
            margin-bottom: 0;
        }
        .profile-links a {
            margin: 0 4px;
        }
        .profile-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
            justify-content: center;
            flex-wrap: wrap;
            width: 100%;
            box-sizing: border-box;
        }
        .profile-tab-btn {
            flex: 1 1 120px;
            min-width: 80px;
            max-width: 220px;
            background: #fff;
            border: 1.5px solid var(--hyplus-border-color-neutral);
            color: var(--hyplus-text-nav-link);
            font-size: 1.08em;
            font-weight: 500;
            border-radius: 16px;
            padding: 7px 22px 7px 18px;
            cursor: pointer;
            outline: none;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: background 0.18s, color 0.18s, border 0.18s;
            justify-content: center;
            box-sizing: border-box;
        }
        .profile-tab-btn.active {
            background: var(--hyplus-bg-button-light);
            color: var(--hyplus-text-title);
            border: 2px solid var(--hyplus-border-color-light);
            font-weight: bold;
        }
        .profile-tab-btn svg {
            opacity: 0.8;
        }
        .profile-tab-content {
            display: none;
            width: 100%;
        }
        .profile-tab-content.active {
            display: block;
        }
        .profile-repo-list {
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            margin-top: 8px;
            justify-content: center;
        }
        .profile-repo-item {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 8px var(--hyplus-shadow-light);
            padding: 18px 18px 12px 18px;
            width: 300px;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .profile-repo-title {
            font-size: 1.18em;
            font-weight: bold;
            color: var(--hyplus-primary-link-color);
            margin-bottom: 4px;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .profile-repo-desc {
            color: var(--hyplus-text-primary);
            font-size: 1.08em;
            margin-bottom: 0;
            text-align: center;
        }
        .profile-stats-img {
            width: 100%;
            max-width: 340px;
            border-radius: 12px;
            box-shadow: 0 2px 8px var(--hyplus-shadow-light);
            margin-bottom: 18px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .profile-stats-counts {
            font-size: 1.25em;
            font-weight: bold;
            color: var(--hyplus-text-primary);
            text-align: center;
        }
        .profile-stats-counts .highlight {
            color: var(--hyplus-text-counter);
            font-weight: bold;
        }
        .recent-posts-block-title {
            font-size: 25px;
            font-weight: 600;
        }
        .profile-info-card {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 12px;
        }
        .profile-info-text {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        @media (max-width: 768px) {
            .profile-info-card {
                flex-direction: column;
                gap: 5px;
                align-items: center;
            }
        }
        @media screen and (min-width: 769px) {
            .profile-card-mobile-categories {
                display: none;
            }
        }
        /* 随机博文功能样式 */
        .randpost-container {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            width: 100%;
            box-sizing: border-box;
        }
        .randpost-select {
            padding: 8px 14px;
            border: 1.5px solid var(--hyplus-border-color-neutral);
            border-radius: 12px;
            background: #fff;
            color: var(--hyplus-text-nav-link);
            font-size: 0.95em;
            cursor: pointer;
            outline: none;
            transition: all 0.18s;
            min-width: 150px;
            box-sizing: border-box;
        }
        .randpost-select:hover {
            border-color: var(--hyplus-border-color-light);
            background: var(--hyplus-bg-settings);
        }
        .randpost-select:focus {
            border-color: var(--hyplus-primary-link-color);
            box-shadow: 0 0 0 3px var(--hyplus-shadow-nav);
        }
        .randpost-btn {
            padding: 8px 22px;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s;
            white-space: nowrap;
        }
        .randpost-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px var(--hyplus-shadow-nav);
        }
        .randpost-btn:active {
            transform: translateY(0);
        }
        .randpost-btn.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .custom-randpost {
            margin-bottom: 6px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@500&display=swap" rel="stylesheet" />
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('randpost-category-select');
        const randpostBtn = document.getElementById('randpost-btn');
        
        randpostBtn.addEventListener('click', function() {
            const categoryId = categorySelect.value;
            
            randpostBtn.classList.add('loading');
            randpostBtn.disabled = true;
            
            // 构建请求参数
            const params = new URLSearchParams();
            params.append('action', 'get_random_post');
            params.append('category', categoryId);
            params.append('nonce', '<?php echo wp_create_nonce("randpost_nonce"); ?>');
            
            // console.log('发送请求:', params.toString());
            
            // 调用AJAX获取随机文章
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                randpostBtn.classList.remove('loading');
                randpostBtn.disabled = false;
                
                // console.log('状态码:', xhr.status);
                // console.log('响应内容:', xhr.responseText);
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    // console.log('解析后的响应:', response);
                    
                    if (response.success && response.data) {
                        // 直接跳转到博文页面
                        window.location.href = response.data.post_url;
                    } else {
                        const message = (response.data && response.data.message) ? response.data.message : '没有找到文章';
                        alert(message);
                    }
                } catch (e) {
                    // console.error('解析错误:', e);
                    // console.error('原始响应:', xhr.responseText);
                    alert('服务器响应错误：' + xhr.status + ' ' + xhr.statusText);
                }
            };
            
            xhr.onerror = function() {
                randpostBtn.classList.remove('loading');
                randpostBtn.disabled = false;
                console.error('网络错误');
                alert('网络请求失败');
            };
            
            // xhr.onreadystatechange = function() {
            //     if (xhr.readyState === 4 && xhr.status !== 0) {
            //         console.log('完整响应状态:', xhr.status, xhr.statusText);
            //     }
            // };
            
            xhr.send(params.toString());
        });
    });
    </script>
    <div class="profile-main-row">
        <div class="profile-card profile-info-card">
            <img class="profile-avatar" src="https://www.hyperplasma.top/wp-content/uploads/2025/01/Snipaste_2023-12-08_19-40-36-150x150.png" alt="konoha" />
            <div class="profile-info-text">
                <div class="profile-desc"><a href="https://www.hyperplasma.top/user/akira37/">Akira&nbsp;-&nbsp;Hyperplasma&nbsp;-&nbsp;Hyplus</a></div>
                <div class="custom-randpost">
                    <div class="randpost-container">
                        <select id="randpost-category-select" class="randpost-select">
                            <option value="">-- 全部分类 --</option>
                            <?php
                            // 获取所有分类（包括一级和二级）
                            $categories = get_terms( array(
                                'taxonomy' => 'category',
                                'hide_empty' => false,
                                'orderby' => 'count',
                                'order' => 'DESC',
                            ) );
                            
                            if ( !empty( $categories ) && !is_wp_error( $categories ) ) {
                                // 按parent分类
                                $parent_categories = array();
                                $child_categories = array();
                                
                                foreach ( $categories as $cat ) {
                                    if ( $cat->parent === 0 ) {
                                        $parent_categories[] = $cat;
                                    } else {
                                        $child_categories[ $cat->parent ][] = $cat;
                                    }
                                }
                                
                                // 输出一级分类和二级分类
                                foreach ( $parent_categories as $parent ) {
                                    echo '<option value="' . esc_attr( $parent->term_id ) . '">' . esc_html( $parent->name ) . '</option>';
                                    
                                    // 输出该分类的子分类
                                    if ( isset( $child_categories[ $parent->term_id ] ) ) {
                                        foreach ( $child_categories[ $parent->term_id ] as $child ) {
                                            echo '<option value="' . esc_attr( $child->term_id ) . '">  ├─ ' . esc_html( $child->name ) . '</option>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </select>
                        <button id="randpost-btn" class="hyplus-nav-link randpost-btn hyplus-unselectable">随机博文</button>
                    </div>
                </div>
                <div class="profile-stats-counts"><?php echo do_shortcode('[site_content_counts]'); ?></div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * 随机博文 AJAX 处理函数（后端逻辑）
 */
function hyplus_get_random_post() {
    // 验证 nonce
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    
    if (!wp_verify_nonce($nonce, 'randpost_nonce')) {
        wp_send_json_error(array('message' => 'Nonce验证失败，请刷新页面重试'));
        wp_die();
    }
    
    // 获取分类ID
    $category_id = isset($_POST['category']) ? absint($_POST['category']) : 0;
    
    // 构建查询参数
    $args = array(
        'posts_per_page' => 1,
        'orderby' => 'rand',
        'post_type' => 'post',
        'post_status' => 'publish',
    );
    
    // 如果选择了分类，则按分类查询
    if (!empty($category_id)) {
        $args['cat'] = $category_id;
    }
    
    // 查询随机文章
    $posts = get_posts($args);
    
    if (!empty($posts)) {
        $post = $posts[0];
        wp_send_json_success(array(
            'post_title' => $post->post_title,
            'post_url' => get_permalink($post->ID),
            'post_id' => $post->ID,
        ));
    } else {
        wp_send_json_error(array('message' => '该分类暂无文章'));
    }
    
    wp_die();
}

/**
 * 初始化：注册短代码和 AJAX hooks
 * 直接注册，不依赖特定 hook（因为 WPCode snippet 执行时机可能较晚）
 */

// 注册短代码
add_shortcode('hyplus-profile', 'hyplus_profile_shortcode_callback');

// 注册 AJAX action
add_action('wp_ajax_get_random_post', 'hyplus_get_random_post');
add_action('wp_ajax_nopriv_get_random_post', 'hyplus_get_random_post');
?>