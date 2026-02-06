<?php
/**
 * Name: Hyplus SideInfo
 * Description: 分类/目录切换组件 (Categories & TOC)
 * Code type: PHP
 * Shortcode: [hyplus_sideinfo]
 */

function hyplus_sideinfo_shortcode() {
    ob_start();
?>
<div class="hyplus-sideinfo" data-current-category-ids="<?php 
    // 获取当前分类ID（单篇文章或分类页面）
    $catIds = array();
    if (is_single()) {
        $terms = get_the_terms(get_the_ID(), 'category');
        if ($terms && !is_wp_error($terms)) {
            $catIds = wp_list_pluck($terms, 'term_id');
        }
    } elseif (is_category()) {
        $catIds = array(get_queried_object_id());
    }
    echo esc_attr(implode(',', $catIds));
?>">
    <!-- 切换按钮 -->
    <div style="text-align: center;">
        <button class="sideinfo-toggle hyplus-nav-link hyplus-unselectable" onclick="toggleSideinfo(this)" hidden aria-label="切换分类和目录">分类</button>
    </div>
    
    <!-- 分类内容 -->
    <div class="sideinfo-cat" style="padding-top: 1px;">
        <?php
        echo '<ul class="home-categories">';
        wp_list_categories([
            'show_count' => true,
            'title_li' => '',
            'hierarchical' => true
        ]);
        echo '</ul>';
        ?>
    </div>

    <!-- 目录内容 -->
    <div class="sideinfo-toc" style="display: none; word-break: break-all; overflow-wrap: anywhere;">
        <div class="toc-wrapper">
            <?php echo do_shortcode('[toc mode=widget hideparent=false]'); ?>
        </div>
    </div>
</div>

<style>
.hyplus-sideinfo {
    width: 100%;
}

.sideinfo-toggle {
    padding: 4px 12px;
    background: #ecf5f8;
    color: #175082;
    border-radius: 16px;
    border: 1.5px solid #c4e0f7;
    box-shadow: 0 2.5px 10px 0 rgba(33, 118, 193, 0.17), 0 1px 2px 0 rgba(33, 118, 193, 0.09);
    font-weight: 600;
    min-width: fit-content;
    transition:
        background 0.18s cubic-bezier(0.4,0,0.2,1),
        color 0.18s cubic-bezier(0.4,0,0.2,1),
        box-shadow 0.18s cubic-bezier(0.4,0,0.2,1),
        transform 0.18s cubic-bezier(0.4,0,0.2,1);
    outline: none;
    cursor: pointer;
}

.sideinfo-toggle.disabled {
    background: #f0f0f0 !important;
    color: #999 !important;
    border-color: #ddd !important;
    box-shadow: none !important;
    cursor: not-allowed !important;
    transform: none !important;
    pointer-events: none;
    margin-bottom: 12px;
    user-select: none;
}

.sideinfo-toggle:not(.disabled):hover,
.sideinfo-toggle:not(.disabled):focus-visible {
    background: #eaf6ff;
    color: #155a99;
    border-color: #8ecafc;
    box-shadow: 0 4px 14px 0 rgba(33, 118, 193, 0.20), 0 1.5px 4px 0 rgba(33, 118, 193, 0.13);
    transform: translateY(-1px) scale(1.025);
    z-index: 2;
}

.sideinfo-toggle:not(.disabled):active {
    background: #dbeaf5;
    color: #155a99;
    box-shadow: 0 1px 4px 0 rgba(33, 118, 193, 0.13);
    transform: translateY(1px) scale(0.98);
}

.sideinfo-cat,
.sideinfo-toc {
    width: 100%;
}

.toc-wrapper {
    width: 100%;
}

/* 高亮当前所处的分类链接，样式与 hytoc.php 中的 hyplus-toc-active 一致 */
.home-categories a.sideinfo-cat-active {
    color: #3000aa;
}
.home-categories a.sideinfo-cat-active:hover {
    color: red;
}
</style>

<script>
// 检查目录是否可用
function checkTocAvailable() {
    const toc = document.querySelector('.sideinfo-toc .toc-wrapper');
    return toc && toc.querySelector('ul') !== null;
}

// 高亮当前所处的分类
function highlightCurrentCategory() {
    const sideinfoContainer = document.querySelector('.hyplus-sideinfo');
    if (!sideinfoContainer) return;

    const catList = sideinfoContainer.querySelector('.home-categories');
    if (!catList) return;

    // 1. 尝试从数据属性获取当前分类 ID（单篇文章或分类页面）
    const currentCategoryIds = sideinfoContainer.getAttribute('data-current-category-ids');
    const catIdSet = new Set();
    if (currentCategoryIds) {
        currentCategoryIds.split(',').forEach(function(id) {
            if (id.trim()) {
                catIdSet.add(id.trim());
            }
        });
    }

    // 2. 如果通过数据属性找到了分类 ID，使用 ID 匹配；否则使用 URL 匹配
    const links = catList.querySelectorAll('a');
    if (catIdSet.size > 0) {
        // 通过分类 ID 匹配
        links.forEach(function(link) {
            const href = link.getAttribute('href');
            // 从 href 中提取分类 ID，href 格式通常是 /category/slug/ 
            // 我们可以尝试从 data-term-id 属性获取，或从 href 中提取
            let isCurrentCat = false;
            
            // 检查父元素是否有 id 或其他标记
            let liElement = link.closest('li');
            if (liElement) {
                // WordPress wp_list_categories 生成的 li 有 cat-item cat-item-{id} class
                const classMatch = liElement.className.match(/cat-item-(\d+)/);
                if (classMatch && catIdSet.has(classMatch[1])) {
                    isCurrentCat = true;
                }
            }
            
            if (isCurrentCat) {
                link.classList.add('sideinfo-cat-active');
            } else {
                link.classList.remove('sideinfo-cat-active');
            }
        });
    } else {
        // 通过 URL 匹配（分类页面）
        const currentUrl = window.location.href.split('?')[0]; // 移除查询参数
        links.forEach(function(link) {
            const href = link.getAttribute('href');
            if (href) {
                const linkUrl = href.split('?')[0]; // 移除查询参数
                // 比较 URL（去除尾部斜杠，以便形如 /category/tech/ 和 /category/tech 都能匹配）
                const normalizedCurrent = currentUrl.replace(/\/$/, '');
                const normalizedLink = linkUrl.replace(/\/$/, '');
                
                if (normalizedCurrent === normalizedLink) {
                    link.classList.add('sideinfo-cat-active');
                } else {
                    link.classList.remove('sideinfo-cat-active');
                }
            }
        });
    }
}

// 页面加载时初始化状态
document.addEventListener('DOMContentLoaded', function() {
    const sideinfoContainer = document.querySelector('.hyplus-sideinfo');
    if (!sideinfoContainer) return;

    const btn = sideinfoContainer.querySelector('.sideinfo-toggle');
    const cat = sideinfoContainer.querySelector('.sideinfo-cat');
    const toc = sideinfoContainer.querySelector('.sideinfo-toc');
    
    // 检查目录是否可用
    const isTocAvailable = checkTocAvailable();
    
    if (isTocAvailable) {
        // 目录可用，优先显示目录
        cat.style.display = 'none';
        toc.style.display = 'block';
        btn.textContent = '目录';
        btn.dataset.state = 'toc';
        btn.classList.remove('disabled');
        btn.disabled = false;
        btn.setAttribute('aria-disabled', 'false');
    } else {
        // 目录不可用，显示分类并禁用按钮
        cat.style.display = 'block';
        toc.style.display = 'none';
        btn.textContent = '分类';
        btn.dataset.state = 'cat';
        btn.classList.add('disabled');
        btn.disabled = true;
        btn.setAttribute('aria-disabled', 'true');
    }
    
    // 初始化完成，显示按钮
    btn.removeAttribute('hidden');
    
    // 高亮当前分类
    highlightCurrentCategory();
});

function toggleSideinfo(btn) {
    if (btn.disabled) return; // 如果按钮被禁用，不执行任何操作

    const parent = btn.parentNode.parentNode;
    const cat = parent.querySelector('.sideinfo-cat');
    const toc = parent.querySelector('.sideinfo-toc');
    
    if (btn.dataset.state === 'cat') {
        // 切换到目录
        cat.style.display = 'none';
        toc.style.display = 'block';
        btn.textContent = '目录';
        btn.dataset.state = 'toc';
    } else {
        // 切换到分类
        cat.style.display = 'block';
        toc.style.display = 'none';
        btn.textContent = '分类';
        btn.dataset.state = 'cat';
    }
}
</script>

<?php
    return ob_get_clean();
}

add_shortcode('hyplus_sideinfo', 'hyplus_sideinfo_shortcode');
?>