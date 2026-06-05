<?php
/**
 * Disable Overscroll/Rubber Band Effect - PHP
 * 禁用页面橡皮筋特效（所有浏览器兼容）
 * 功能：在 Edge、Safari、Chrome 等浏览器中禁用页面滚动到边界时的橡皮筋效果
 * Code type: PHP (+ CSS + JS)
 * Current status: unused
 */
add_action('wp_footer', 'hyplus_disable_overscroll_behavior');

function hyplus_disable_overscroll_behavior() {
    ?>
    <script>
    (function() {
        // 记录上一次触摸的 Y 坐标
        var lastY = 0;
        
        // 缓存计算结果（避免重复查询 DOM）
        var scrollHeight = 0;
        var clientHeight = 0;
        var canPreventDefault = true;

        // 初始化缓存值
        function updateDimensions() {
            scrollHeight = document.documentElement.scrollHeight || document.body.scrollHeight;
            clientHeight = window.innerHeight || document.documentElement.clientHeight;
        }

        // 初始计算
        updateDimensions();
        
        // 页面大小改变时更新缓存
        window.addEventListener('resize', updateDimensions, { passive: true });
        
        // 监听触摸开始
        document.addEventListener('touchstart', function(e) {
            lastY = e.touches[0].clientY;
            canPreventDefault = true;
        }, { passive: true });

        // 监听触摸移动（禁用橡皮筋的关键）
        document.addEventListener('touchmove', function(e) {
            if (!canPreventDefault) return;
            
            var currentY = e.touches[0].clientY;
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // 判断是否在页面顶部且向下拉（产生橡皮筋）
            if (scrollTop === 0 && currentY > lastY) {
                e.preventDefault();
                canPreventDefault = false;
                return;
            }

            // 判断是否在页面底部且向上拉（产生橡皮筋）
            if (scrollTop + clientHeight >= scrollHeight && currentY < lastY) {
                e.preventDefault();
                canPreventDefault = false;
                return;
            }

            lastY = currentY;
        }, { passive: false });

        // 使用节流处理 wheel 事件（每 100ms 最多检查一次）
        var lastWheelTime = 0;
        document.addEventListener('wheel', function(e) {
            var now = Date.now();
            if (now - lastWheelTime < 100) return;
            lastWheelTime = now;

            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // 在页面顶部向上滚动
            if (scrollTop === 0 && e.deltaY < 0) {
                e.preventDefault();
                return;
            }

            // 在页面底部向下滚动
            if (scrollTop + clientHeight >= scrollHeight && e.deltaY > 0) {
                e.preventDefault();
                return;
            }
        }, { passive: false });
    })();
    </script>

    <style>
    /* 禁用默认的 overscroll 行为（非 Safari 浏览器） */
    html {
        overscroll-behavior-y: none;
    }

    body {
        overscroll-behavior-y: none;
    }

    /* 防止 iOS Safari 的弹性滚动 */
    -webkit-overflow-scrolling: auto !important;
    </script>
    <?php
}
?>
