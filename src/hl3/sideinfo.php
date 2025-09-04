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
        <button class="sideinfo-toggle hyplus-nav-link hyplus-unselectable" onclick="toggleSideinfo(this)">分类</button>
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
    /* 可调整变量 */
    --sideinfo-fade-bg: #fff; /* 根据主题背景自行覆盖 */
    --toc-fade-size: 58px;    /* 淡出高度（增大范围） */
    --toc-max-height: 530px;  /* 目录最大高度 */
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
    margin-bottom: 12px;
    user-select: none;
}

.sideinfo-toggle.hyplus-nav-link:hover,
.sideinfo-toggle.hyplus-nav-link:focus {
    background: #eaf6ff;
    color: #155a99;
    border-color: #8ecafc;
    box-shadow: 0 4px 14px 0 rgba(33, 118, 193, 0.20), 0 1.5px 4px 0 rgba(33, 118, 193, 0.13);
    transform: translateY(-1px) scale(1.025);
    z-index: 2;
}

.sideinfo-toggle.hyplus-nav-link:active {
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
    max-height: var(--toc-max-height);
    overflow: auto;
    -ms-overflow-style: none;       /* IE/Edge */
    scrollbar-width: none;          /* Firefox */
}
.toc-wrapper::-webkit-scrollbar {   /* WebKit */
    display: none;
}

.sideinfo-toc {
    position: relative;
}

/* 顶部/底部淡出遮罩，按需显示 */
.sideinfo-toc::before,
.sideinfo-toc::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    height: var(--toc-fade-size);
    pointer-events: none;
    z-index: 1;
    display: none;
}
.sideinfo-toc.has-overflow:not(.at-top)::before {
    display: block;
    top: 0;
    background: linear-gradient(
        to bottom,
        var(--sideinfo-fade-bg) 0%,
        rgba(255, 255, 255, 0) 100%
    );
}
.sideinfo-toc.has-overflow:not(.at-bottom)::after {
    display: block;
    bottom: 0;
    background: linear-gradient(
        to top,
        var(--sideinfo-fade-bg) 0%,
        rgba(255, 255, 255, 0) 100%
    );
}
</style>

<script>
// Cookie操作函数
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// 页面加载时恢复状态
document.addEventListener('DOMContentLoaded', function() {
    const savedState = getCookie('hyplus_sideinfo_state');
    if (savedState) {
        const sideinfoContainer = document.querySelector('.hyplus-sideinfo');
        if (sideinfoContainer) {
            const btn = sideinfoContainer.querySelector('.sideinfo-toggle');
            const cat = sideinfoContainer.querySelector('.sideinfo-cat');
            const toc = sideinfoContainer.querySelector('.sideinfo-toc');

            if (savedState === 'toc') {
                cat.style.display = 'none';
                toc.style.display = 'block';
                btn.textContent = '目录';
            } else {
                cat.style.display = 'block';
                toc.style.display = 'none';
                btn.textContent = '分类';
            }
        }
    }
    initTocFade();
});

function toggleSideinfo(btn) {
    const parent = btn.parentNode.parentNode;
    const cat = parent.querySelector('.sideinfo-cat');
    const toc = parent.querySelector('.sideinfo-toc');
    
    if (cat.style.display !== 'none') {
        // 切换到目录
        cat.style.display = 'none';
        toc.style.display = 'block';
        btn.textContent = '目录';
        setCookie('hyplus_sideinfo_state', 'toc', 30); // 保存30天
    } else {
        // 切换到分类
        cat.style.display = 'block';
        toc.style.display = 'none';
        btn.textContent = '分类';
        setCookie('hyplus_sideinfo_state', 'cat', 30); // 保存30天
    }
    // 切换后更新淡出状态
    updateTocFade(toc);
}

// 初始化与更新淡出状态
function initTocFade() {
    const sideinfoContainer = document.querySelector('.hyplus-sideinfo');
    if (!sideinfoContainer) return;
    const tocPane = sideinfoContainer.querySelector('.sideinfo-toc');
    const tocWrapper = sideinfoContainer.querySelector('.toc-wrapper');
    if (!tocPane || !tocWrapper) return;

    const refresh = () => updateTocFade(tocPane);
    // 绑定滚动与尺寸变化事件
    tocWrapper.addEventListener('scroll', refresh, { passive: true });
    window.addEventListener('resize', refresh);

    // 使用 ResizeObserver 监听容器/内容变化
    if (window.ResizeObserver) {
        const ro = new ResizeObserver(refresh);
        ro.observe(tocWrapper);
        ro.observe(tocPane);
    }

    // 初始计算
    refresh();
}

function updateTocFade(tocPane) {
    if (!tocPane) return;
    const tocWrapper = tocPane.querySelector('.toc-wrapper');
    if (!tocWrapper) return;

    // 若目录隐藏，则移除状态并返回
    if (tocPane.style.display === 'none') {
        tocPane.classList.remove('has-overflow', 'at-top', 'at-bottom');
        return;
    }

    const scrollTop = tocWrapper.scrollTop;
    const maxScroll = tocWrapper.scrollHeight - tocWrapper.clientHeight;
    const hasOverflow = maxScroll > 0;

    if (!hasOverflow) {
        tocPane.classList.remove('has-overflow', 'at-top', 'at-bottom');
        return;
    }

    tocPane.classList.add('has-overflow');

    const nearTop = scrollTop <= 1;
    const nearBottom = maxScroll - scrollTop <= 1;

    if (nearTop) tocPane.classList.add('at-top'); else tocPane.classList.remove('at-top');
    if (nearBottom) tocPane.classList.add('at-bottom'); else tocPane.classList.remove('at-bottom');
}
</script>

<?php
    return ob_get_clean();
}

add_shortcode('hyplus_sideinfo', 'hyplus_sideinfo_shortcode');
?>