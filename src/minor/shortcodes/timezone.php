<?php
/**
 * Timezone Display Shortcode PHP
 * 显示不同地区的当前时间
 * Code type: universal
 * Shortcode: [wpcode id="4726"]
 */
$timezones = [
    'China' => 'Asia/Shanghai',
    'Europe' => 'Europe/Paris',
    'US EST' => 'America/New_York',
    'US PST' => 'America/Los_Angeles',
];

$date_format = 'Y-m-d H:i:s';
?>

<div class="timezone-times" style="text-align: center;">
    <?php
    foreach ($timezones as $region => $timezone) {
        try {
            $datetime = new DateTime("now", new DateTimeZone($timezone));
    ?>
    <p id="time-<?php echo esc_html($region); ?>" data-timezone="<?php echo esc_attr($timezone); ?>">
        <strong><?php echo esc_html($region); ?>:&nbsp;</strong> 
        <span class="time-value"><?php echo esc_html($datetime->format($date_format)); ?></span>
    </p>
    <?php
        } catch (Exception $e) {
    ?>
    <p id="time-<?php echo esc_html($region); ?>">
        <strong><?php echo esc_html($region); ?>:&nbsp;</strong>
        Error: <?php echo esc_html($e->getMessage()); ?>
    </p>
    <?php
        }
    }
    ?>
</div>

<script>
// 确保在页面完全加载后再初始化时钟
document.addEventListener('DOMContentLoaded', function() {
    // 创建一个Promise来等待进度条完成
    const progressComplete = new Promise((resolve) => {
        // 检查进度条是否还存在
        const checkProgress = setInterval(() => {
            if (!document.getElementById('loadingProgressBar')) {
                clearInterval(checkProgress);
                resolve();
            }
        }, 100);
        
        // 设置最大等待时间为5秒
        setTimeout(() => {
            clearInterval(checkProgress);
            resolve();
        }, 5000);
    });

    // 等待进度条完成后再启动时钟
    progressComplete.then(() => {
        initializeClock();
    });
});

function initializeClock() {
    const timeElements = document.querySelectorAll('.timezone-times p');

    function updateTime() {
        timeElements.forEach(function(element) {
            const timezone = element.dataset.timezone;
            if (!timezone) return;

            const now = new Date();
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: timezone,
                hour12: false
            };

            try {
                const formatter = new Intl.DateTimeFormat('en-US', options);
                const formattedTime = formatter.format(now);
                
                // 格式化日期时间
                const parts = formattedTime.split(', ');
                const datePart = parts[0].split('/');
                const timePart = parts[1].split(':');
                const finalTime = `${datePart[2]}-${datePart[0].padStart(2, '0')}-${datePart[1].padStart(2, '0')} ${timePart[0].padStart(2, '0')}:${timePart[1]}:${timePart[2]}`;
                
                // 只更新时间值部分，减少DOM操作
                const timeValueSpan = element.querySelector('.time-value');
                if (timeValueSpan) {
                    timeValueSpan.textContent = finalTime;
                }
            } catch (error) {
                console.error(`Error updating time for ${timezone}:`, error);
            }
        });
    }

    // 初始更新
    updateTime();
    
    // 使用 requestAnimationFrame 来优化性能
    let lastUpdate = 0;
    function animationFrame(timestamp) {
        // 每秒更新一次
        if (timestamp - lastUpdate >= 1000) {
            updateTime();
            lastUpdate = timestamp;
        }
        requestAnimationFrame(animationFrame);
    }
    
    requestAnimationFrame(animationFrame);
}
</script>