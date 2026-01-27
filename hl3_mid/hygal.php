<?php
/**
 * Plugin Name: Hyplus Gallery (HyGal) - Minimalist & Full Text
 * Description: 极简紧凑型画廊。取消缩放动画，标题文字支持跨行完整显示。
 * Code Type: PHP
 * Shortcode: [hygal]
 */

add_shortcode('hygal', 'hygal_full_text_handler');

function hygal_full_text_handler() {
    ob_start();
    ?>
    <style>
        /* 画廊基础容器 */
        .hygal {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
            margin: 15px 0;
            padding: 5px;
        }

        /* 图片项目容器：移除 Hover 缩放效果 */
        .hygal-item {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #eef0f2;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        /* 图片框 */
        .hygal-img-wrapper {
            width: 100%;
            aspect-ratio: 1 / 1; 
            overflow: hidden;
            background: #f7f8f9;
        }

        .hygal-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover; 
            display: block;
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* 标题文字：缩小字号，允许跨行显示 */
        .hygal-title {
            padding: 6px 4px;
            font-size: 10px; /* 进一步缩小字号 */
            color: #444;
            text-align: center;
            line-height: 1.3;
            /* 关键修改：移除 ellipsis，允许换行 */
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal; 
        }
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const galleries = document.querySelectorAll('.hygal');
        galleries.forEach(gal => {
            // 确保只处理直接子级的 img
            const imgs = gal.querySelectorAll(':scope > img');
            imgs.forEach(img => {
                const titleText = img.getAttribute('title') || "";
                
                const item = document.createElement('div');
                item.className = 'hygal-item';
                
                const wrapper = document.createElement('div');
                wrapper.className = 'hygal-img-wrapper';
                
                const titleDiv = document.createElement('div');
                titleDiv.className = 'hygal-title';
                titleDiv.textContent = titleText;
                
                // 结构重组
                img.parentNode.insertBefore(item, img);
                wrapper.appendChild(img);
                item.appendChild(wrapper);
                item.appendChild(titleDiv);
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
?>