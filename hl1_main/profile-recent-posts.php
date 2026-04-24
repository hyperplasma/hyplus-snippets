<!-- Hyplus 导航 - Profile - Recent Posts
 Code type: Universal Snippet (HTML + PHP)
 Shortcode: [wpcode id="14382"]
-->
<div class="profile-main-row">
    <!-- 日历组件 (Extra) -->
    <div id="hy-calendar" style="text-align: center">
        <div style="display: flex; justify-content: center; align-items: center; gap: 1em; margin-bottom: 15px;">
            <button class="hyplus-nav-link" onclick="hyCalendarPrevMonth()" style="padding: 5px 10px; cursor: pointer;">← 上月</button>
            <div id="hy-calendar-title" style="font-weight: 600; min-width: 100px"></div>
            <button class="hyplus-nav-link" onclick="hyCalendarNextMonth()" style="padding: 5px 10px; cursor: pointer">下月 →</button>
        </div>
        <table id="hy-calendar-table" class="hyplus-excluded-table" style="width: 100%; border-collapse: collapse"></table>
    </div>
    <script>
        let hyCurrentDate = new Date();
        function hyCalendarRender() {
            const y = hyCurrentDate.getFullYear(), m = hyCurrentDate.getMonth();
            document.getElementById('hy-calendar-title').textContent = y + '年' + (m + 1) + '月';
            const firstDay = new Date(y, m, 1).getDay(), daysInMonth = new Date(y, m + 1, 0).getDate();
            let html = '<tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr><tr>';
            for (let i = 0; i < firstDay; i++) html += '<td></td>';
            const today = new Date(), isCurrentMonth = y === today.getFullYear() && m === today.getMonth();
            for (let d = 1; d <= daysInMonth; d++) {
                if ((firstDay + d - 1) % 7 === 0 && d > 1) html += '</tr><tr>';
                const cls = isCurrentMonth && d === today.getDate() ? ' class="hy-today"' : '';
                html += '<td' + cls + '>' + d + '</td>';
            }
            html += '</tr>';
            document.getElementById('hy-calendar-table').innerHTML = html;
        }
        function hyCalendarPrevMonth() { hyCurrentDate.setMonth(hyCurrentDate.getMonth() - 1); hyCalendarRender(); }
        function hyCalendarNextMonth() { hyCurrentDate.setMonth(hyCurrentDate.getMonth() + 1); hyCalendarRender(); }
        hyCalendarRender();
    </script>

    <div class="profile-cards-container">
        <div class="profile-card" style="text-align: left">
            <div style="font-size: 26px; font-weight: 600; text-align: left">资源分类</div>
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
        <div class="profile-card recent-posts-card" style="text-align: left">
            <div style="font-size: 26px; font-weight: 600; text-align: left">热门文章</div>
            <?php echo do_shortcode('[recently_modified_posts posts_per_page=8 show_modified_date=true]'); ?>
        </div>
    </div>
    <div style="margin-top: 30px"><?php echo do_shortcode('[wpcode id="4726"]'); ?></div>
</div>