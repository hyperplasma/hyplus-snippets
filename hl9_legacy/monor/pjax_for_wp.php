<?php
/**
 * 实现 WordPress 页面的 PJAX 功能，保持 header 和 sidebar 不刷新
 * Code type: universal
 * Current status: unused (jitton of bugs)
 */

// 添加 PJAX 功能的主要代码
function implement_pjax_functionality() {
    // 注册并加载 PJAX 库
    wp_enqueue_script(
        'jquery-pjax',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery.pjax/2.0.1/jquery.pjax.min.js',
        array('jquery'),
        '2.0.1',
        true
    );

    // 加载自定义 PJAX 实现脚本
    wp_enqueue_script(
        'custom-pjax',
        get_template_directory_uri() . '/js/custom-pjax.js',
        array('jquery-pjax'),
        '1.0.0',
        true
    );

    // 为脚本传递必要的变量
    wp_localize_script(
        'custom-pjax',
        'pjaxConfig',
        array(
            'container' => '#main-content', // 主要内容容器的选择器
            'timeout' => 5000, // 请求超时时间（毫秒）
            'loadingClass' => 'is-loading' // 加载中的 CSS 类
        )
    );

    // 加载自定义 CSS
    wp_enqueue_style(
        'custom-pjax',
        get_template_directory_uri() . '/css/custom-pjax.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'implement_pjax_functionality');

// 修改 WordPress 响应头，添加 PJAX 支持
function add_pjax_support_headers() {
    if (isset($_SERVER['HTTP_X_PJAX'])) {
        header('X-PJAX-Version: ' . get_bloginfo('version'));
    }
}
add_action('template_redirect', 'add_pjax_support_headers');

// 处理 PJAX 请求，只返回必要的内容
function handle_pjax_request() {
    if (isset($_SERVER['HTTP_X_PJAX'])) {
        // 关闭不必要的输出缓冲区
        if (ob_get_level()) {
            ob_end_clean();
        }

        // 启用输出缓冲区
        ob_start();

        // 添加过滤器以修改内容
        add_filter('the_content', 'modify_content_for_pjax');
        add_filter('template_include', 'modify_template_for_pjax', 99);
    }
}
add_action('template_redirect', 'handle_pjax_request');

// 修改模板以适应 PJAX 请求
function modify_template_for_pjax($template) {
    if (isset($_SERVER['HTTP_X_PJAX'])) {
        // 返回仅包含主要内容的模板
        $pjax_template = locate_template(array('pjax-template.php'));
        if (!empty($pjax_template)) {
            return $pjax_template;
        }
    }
    return $template;
}

// 修改内容以适应 PJAX 请求
function modify_content_for_pjax($content) {
    if (isset($_SERVER['HTTP_X_PJAX'])) {
        // 添加内容过渡动画容器
        $content = '<div class="pjax-transition">' . $content . '</div>';
    }
    return $content;
}

// 更新页面标题以反映当前页面
function update_title_for_pjax() {
    if (isset($_SERVER['HTTP_X_PJAX'])) {
        echo '<title>' . wp_get_document_title() . '</title>';
    }
}
add_action('wp_head', 'update_title_for_pjax', 1);

// 为菜单链接添加 PJAX 支持
function add_pjax_to_menu_items($items, $args) {
    // 为所有菜单链接添加 PJAX 支持
    $items = str_replace('<a ', '<a data-pjax="#main-content" ', $items);
    return $items;
}
add_filter('wp_nav_menu_items', 'add_pjax_to_menu_items', 10, 2);

// 创建必要的目录和文件
function create_pjax_files() {
    $upload_dir = wp_upload_dir();
    $js_dir = $upload_dir['basedir'] . '/pjax/js';
    $css_dir = $upload_dir['basedir'] . '/pjax/css';
    $template_dir = $upload_dir['basedir'] . '/pjax/templates';

    // 创建目录
    if (!file_exists($js_dir)) {
        wp_mkdir_p($js_dir);
    }
    if (!file_exists($css_dir)) {
        wp_mkdir_p($css_dir);
    }
    if (!file_exists($template_dir)) {
        wp_mkdir_p($template_dir);
    }

    // 创建 JavaScript 文件
    $js_file = $js_dir . '/custom-pjax.js';
    if (!file_exists($js_file)) {
        $js_content = "
(function($) {
    // 文档就绪时初始化 PJAX
    $(document).ready(function() {
        // 监听 PJAX 容器内的所有链接点击
        $(document).pjax('a[data-pjax]', pjaxConfig.container, {
            fragment: pjaxConfig.container,
            timeout: pjaxConfig.timeout
        });

        // 页面加载开始时的处理
        $(document).on('pjax:send', function() {
            $(pjaxConfig.container).addClass(pjaxConfig.loadingClass);
            // 显示加载动画
            $('.pjax-loading').show();
        });

        // 页面加载完成后的处理
        $(document).on('pjax:complete', function() {
            $(pjaxConfig.container).removeClass(pjaxConfig.loadingClass);
            // 隐藏加载动画
            $('.pjax-loading').hide();
            
            // 重新初始化脚本
            initScripts();
            
            // 触发自定义事件，供其他脚本监听
            $(document).trigger('pjax:loaded');
        });

        // 处理 PJAX 错误
        $(document).on('pjax:error', function(event, xhr, textStatus, errorThrown) {
            console.error('PJAX Error:', textStatus, errorThrown);
            // 隐藏加载动画
            $('.pjax-loading').hide();
            // 显示错误信息
            $(pjaxConfig.container).html('<div class=\"error-message\">加载页面时出错，请重试。</div>');
            event.preventDefault(); // 阻止默认的错误处理
        });
    });

    // 初始化页面脚本的函数
    function initScripts() {
        // 重新初始化需要的脚本
        // 例如：导航菜单交互、评论系统等
        
        // 示例：初始化评论系统
        if (typeof initComments === 'function') {
            initComments();
        }
        
        // 示例：初始化表单验证
        if (typeof initFormValidation === 'function') {
            initFormValidation();
        }
    }
})(jQuery);";
        file_put_contents($js_file, $js_content);
    }

    // 创建 CSS 文件
    $css_file = $css_dir . '/custom-pjax.css';
    if (!file_exists($css_file)) {
        $css_content = "
/* PJAX 加载动画 */
.pjax-loading {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 5px;
    z-index: 9999;
}

.pjax-loading-spinner {
    border: 3px solid rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    border-top: 3px solid #333;
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* 内容过渡动画 */
.pjax-transition {
    opacity: 1;
    transition: opacity 0.3s ease-in-out;
}

.is-loading .pjax-transition {
    opacity: 0.5;
}

.error-message {
    color: #ff0000;
    padding: 20px;
    background-color: #ffebee;
    border-radius: 4px;
    margin: 20px 0;
}";
        file_put_contents($css_file, $css_content);
    }

    // 创建 PJAX 模板文件
    $template_file = $template_dir . '/pjax-template.php';
    if (!file_exists($template_file)) {
        $template_content = "<?php
/**
 * PJAX 专用模板，只包含主要内容
 */
get_header('pjax');
?>

<div id=\"main-content\" class=\"site-main\">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            get_template_part('template-parts/content', get_post_type());
        endwhile;
        the_posts_navigation();
    else :
        get_template_part('template-parts/content', 'none');
    endif;
    ?>
</div>

<?php
// 添加加载动画
echo '<div class=\"pjax-loading\">
    <div class=\"pjax-loading-spinner\"></div>
    <p>加载中...</p>
</div>';

get_footer('pjax');
?>";
        file_put_contents($template_file, $template_content);
    }

    // 创建 PJAX 专用头部模板
    $header_file = $template_dir . '/header-pjax.php';
    if (!file_exists($header_file)) {
        $header_content = "<?php
/**
 * PJAX 专用头部模板
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset=\"<?php bloginfo('charset'); ?>\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <!-- 不包含实际的 header 内容，仅用于结构完整性 -->
    <div id=\"pjax-header-placeholder\"></div>";
        file_put_contents($header_file, $header_content);
    }

    // 创建 PJAX 专用底部模板
    $footer_file = $template_dir . '/footer-pjax.php';
    if (!file_exists($footer_file)) {
        $footer_content = "<?php
/**
 * PJAX 专用底部模板
 */
?>
    <!-- 不包含实际的 footer 内容，仅用于结构完整性 -->
    <div id=\"pjax-footer-placeholder\"></div>
    <?php wp_footer(); ?>
</body>
</html>";
        file_put_contents($footer_file, $footer_content);
    }
}
add_action('admin_init', 'create_pjax_files');

// 注册 PJAX 模板路径
function register_pjax_template_path($template_paths) {
    $upload_dir = wp_upload_dir();
    $template_paths[] = $upload_dir['basedir'] . '/pjax/templates';
    return $template_paths;
}
add_filter('template_include_paths', 'register_pjax_template_path');
?>