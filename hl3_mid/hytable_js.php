<?php
/*
 * Plugin Name: HyTable.js (Global)
 * Description: 自动合并全站 Markdown 表格（#main 区域）的纵向空白单元格
 * Code Type: PHP (Footer Injection)
 */

// 使用 wp_footer 钩子将脚本注入到页面底部
add_action('wp_footer', function() {
    // 仅在前台页面执行，不在管理后台运行
    if (is_admin()) return;
    ?>
    <script type="text/javascript">
    (function() {
        /**
         * HyTable.js - 自动合并 Markdown 表格纵向空白单元格
         * 逻辑基于用户验证过的“稳定版”进行性能优化
         */
        const mergeTableRows = () => {
            // 性能优化：优先定位 #main 区域，若不存在则扫描整个 body
            const container = document.querySelector('#main') || document.body;
            const tables = container.querySelectorAll('table');

            tables.forEach(table => {
                // 针对 tbody 进行深度处理，确保表头结构不受干扰
                const rows = table.querySelectorAll('tbody tr');
                if (rows.length < 2) return; 

                // 获取列数（以第一行为准）
                const colCount = rows[0].cells.length;

                for (let colIndex = 0; colIndex < colCount; colIndex++) {
                    let lastValidCell = null;

                    for (let rowIndex = 0; rowIndex < rows.length; rowIndex++) {
                        const currentCell = rows[rowIndex].cells[colIndex];
                        if (!currentCell) continue;

                        // 核心判断：单元格是否为空（兼容处理空格、换行及 &nbsp;）
                        const isEmpty = currentCell.textContent.trim() === '' && 
                                        currentCell.innerHTML.replace(/&nbsp;/g, '').trim() === '';

                        if (isEmpty && lastValidCell) {
                            // 累加上一个有效单元格的 rowspan
                            const currentSpan = lastValidCell.rowSpan || 1;
                            lastValidCell.rowSpan = currentSpan + 1;
                            
                            // 隐藏当前空白单元格
                            currentCell.style.display = 'none';
                        } else {
                            // 遇到非空单元格，更新引用
                            lastValidCell = currentCell;
                        }
                    }
                }
            });
        };

        // 兼容不同的加载状态
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', mergeTableRows);
        } else {
            // 如果页面已经加载完成（如 AJAX 加载或延迟加载），直接运行
            mergeTableRows();
        }
    })();
    </script>
    <?php
});
?>