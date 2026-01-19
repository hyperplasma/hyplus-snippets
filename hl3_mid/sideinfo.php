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
<div class="hyplus-sideinfo">
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
</style>

<script>
// 检查目录是否可用
function checkTocAvailable() {
    const toc = document.querySelector('.sideinfo-toc .toc-wrapper');
    return toc && toc.querySelector('ul') !== null;
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