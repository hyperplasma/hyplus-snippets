<?php
/*
	Name: Hyplus TOC
	Description: 生成Ultimate Buttons风格的目录，支持短代码[toc border=true/false]，可选边框。
	Code type: PHP
	Shortcode: [toc border=false]
	Current status: unused
*/

// 目录生成主函数
function hyplus_generate_toc($content = '', $border = false, $echo = false, $is_auto = false) {
    // 匹配所有带数字编号的标题
    preg_match_all('/<h([1-6])[^>]*>([0-9]+(\.[0-9]+)*[\)\.]?\s+[^<]*)<\/h[1-6]>/', $content, $matches, PREG_SET_ORDER);
    if (!$matches) return '';
    $anchor_map = [];
    $toc = '<div class="hyplus-toc-section'.($border ? ' hyplus-toc-bordered' : '').'">';
    $toc .= '<div class="hyplus-toc-header">Hyplus目录'.($is_auto ? '' : '').'</div>';
    $toc .= '<div class="hyplus-toc-content"><ul>';
    foreach ($matches as $m) {
        $level = intval($m[1]);
        $title = trim(strip_tags($m[2]));
        $base_anchor = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
        $base_anchor = preg_replace('/\s+/', '_', $base_anchor);
        $anchor = $base_anchor;
        $suffix = 2;
        while (in_array($anchor, $anchor_map)) {
            $anchor = $base_anchor . '_' . $suffix;
            $suffix++;
        }
        $anchor_map[] = $anchor;
        $toc .= '<li class="hyplus-toc-level-'.$level.'"><a href="#'.$anchor.'">'.$title.'</a></li>';
    }
    $toc .= '</ul></div></div>';
    if ($echo) echo $toc;
    return $toc;
}

// 短代码实现
add_shortcode('toc', function($atts) {
    $atts = shortcode_atts([
        'border' => 'false',
    ], $atts);
    global $post;
    $content = $post ? $post->post_content : '';
    $border = ($atts['border'] === 'true');
    return hyplus_generate_toc($content, $border, false, false);
});

// 自动在正文第一个标题前插入带边框目录（无隐藏按钮）
add_filter('the_content', function($content) {
    if (!is_singular() || is_admin()) return $content;
    $has_toc = strpos($content, '[toc') !== false;
    if ($has_toc) return $content;
    if (preg_match('/(<h[1-6][^>]*>)/i', $content, $m, PREG_OFFSET_CAPTURE)) {
        $toc = hyplus_generate_toc($content, true, false, true);
        $pos = $m[0][1];
        $content = substr($content, 0, $pos) . $toc . substr($content, $pos);
    }
    return $content;
});

// 目录点击平滑滚动
add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.hyplus-toc-content a').forEach(function(a) {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                var id = this.getAttribute('href').substring(1);
                var el = document.getElementById(id);
                if (el) el.scrollIntoView({behavior:'smooth'});
            });
        });
    });
    </script>
    <?php
});

// 自动为正文标题加锚点
add_filter('the_content', function($content) {
    $pattern = '/<h([1-6])([^>]*)>([0-9]+(\.[0-9]+)*[\)\.]?\s+[^<]*)<\/h[1-6]>/i';
    $anchor_map = [];
    $content = preg_replace_callback($pattern, function($m) use (&$anchor_map) {
        $title = trim(strip_tags($m[3]));
        $base_anchor = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
        $base_anchor = preg_replace('/\s+/', '_', $base_anchor);
        $anchor = $base_anchor;
        $suffix = 2;
        while (in_array($anchor, $anchor_map)) {
            $anchor = $base_anchor . '_' . $suffix;
            $suffix++;
        }
        $anchor_map[] = $anchor;
        return '<h'.$m[1].$m[2].' id="'.$anchor.'">'.$m[3].'</h'.$m[1].'>';
    }, $content);
    return $content;
}, 11);
