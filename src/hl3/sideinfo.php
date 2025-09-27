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
    cursor: default !important;
    transform: none !important;
    pointer-events: none;
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

function removeCookie(name) {
    document.cookie = name + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
}

// 保存显示状态到cookie（仅在用户主动切换时调用）
function saveDisplayState(state) {
    setCookie('hyplus_sideinfo_state', state, 30); // 保存30天
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

// 检查目录是否可用
function checkTocAvailable() {
    const toc = document.querySelector('.sideinfo-toc .toc-wrapper');
    return toc && toc.querySelector('ul') !== null;
}

// 页面加载时恢复状态
document.addEventListener('DOMContentLoaded', function() {
    const sideinfoContainer = document.querySelector('.hyplus-sideinfo');
    if (!sideinfoContainer) return;

    const btn = sideinfoContainer.querySelector('.sideinfo-toggle');
    const cat = sideinfoContainer.querySelector('.sideinfo-cat');
    const toc = sideinfoContainer.querySelector('.sideinfo-toc');
    
    // 检查目录是否可用
    const isTocAvailable = checkTocAvailable();
    
    if (!isTocAvailable) {
        // 如果目录不可用，强制显示分类，禁用按钮
        // 但不改变 cookie 中存储的状态！
        cat.style.display = 'block';
        toc.style.display = 'none';
        btn.textContent = '分类';
        btn.classList.add('disabled');
        btn.disabled = true;
        // 不删除 cookie，这样切回有目录的页面时才能恢复之前的状态
        return;
    }

    // 目录可用时，恢复正常状态
    const savedState = getCookie('hyplus_sideinfo_state');
    btn.classList.remove('disabled');
    btn.disabled = false;
    
    if (savedState === 'toc') {
        cat.style.display = 'none';
        toc.style.display = 'block';
        btn.textContent = '目录';
        btn.dataset.state = 'toc';
    } else {
        cat.style.display = 'block';
        toc.style.display = 'none';
        btn.textContent = '分类';
        btn.dataset.state = 'cat';
    }
});

function toggleSideinfo(btn) {
    if (btn.disabled) return; // 如果按钮被禁用，不执行任何操作

    const parent = btn.parentNode.parentNode;
    const cat = parent.querySelector('.sideinfo-cat');
    const toc = parent.querySelector('.sideinfo-toc');
    
    // 检查目录是否可用
    if (!checkTocAvailable()) {
        // 如果目录不可用，强制显示分类并禁用按钮，但不改变存储的状态
        cat.style.display = 'block';
        toc.style.display = 'none';
        btn.textContent = '分类';
        btn.classList.add('disabled');
        btn.disabled = true;
        btn.dataset.state = 'cat';
        return;
    }
    
    if (btn.dataset.state === 'cat') {
        // 切换到目录
        cat.style.display = 'none';
        toc.style.display = 'block';
        btn.textContent = '目录';
        btn.dataset.state = 'toc';
        saveDisplayState('toc'); // 用户主动切换，保存状态
    } else {
        // 切换到分类
        cat.style.display = 'block';
        toc.style.display = 'none';
        btn.textContent = '分类';
        btn.dataset.state = 'cat';
        saveDisplayState('cat'); // 用户主动切换，保存状态
    }
}
</script>

<?php
    return ob_get_clean();
}

add_shortcode('hyplus_sideinfo', 'hyplus_sideinfo_shortcode');
?>