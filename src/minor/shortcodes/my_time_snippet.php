<!-- My time snippet php
 Description: 实时显示当前时间（中文）
 Code type: universal (html + js + php)
 Shortcode: [wpcode id="9590"]
-->
<?php
// 设置时区
$timezone = 'Asia/Shanghai';

// 设置默认日期格式
$date_format = 'Y 年 n 月 j 日（D）H : i : s';

// 中文星期数组
$weekdays = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
?>

<span class="current-time" style="text-align: center;">
	<?php
	try {
		$datetime = new DateTime("now", new DateTimeZone($timezone));
		// 获取星期几的索引
		$weekday_index = (int)$datetime->format('w');
		// 替换英文星期为中文星期
		$formatted_date = $datetime->format($date_format);
		$formatted_date = str_replace('Sun', $weekdays[0], $formatted_date);
		$formatted_date = str_replace('Mon', $weekdays[1], $formatted_date);
		$formatted_date = str_replace('Tue', $weekdays[2], $formatted_date);
		$formatted_date = str_replace('Wed', $weekdays[3], $formatted_date);
		$formatted_date = str_replace('Thu', $weekdays[4], $formatted_date);
		$formatted_date = str_replace('Fri', $weekdays[5], $formatted_date);
		$formatted_date = str_replace('Sat', $weekdays[6], $formatted_date);
	?>
	<span id="current-time">当前时间：<?php echo esc_html($formatted_date); ?></span>
	<?php
	} catch (Exception $e) {
	?>
	<span id="current-time">Error: <?php echo esc_html($e->getMessage()); ?></span>
	<?php
	}
	?>
</span>

<script>
	// 获取当前时间的元素
	var timeElement = document.querySelector('.current-time span');

	// 更新时间的函数
	function updateTime() {
		// 获取当前时间
		var now = new Date();
		// 调整时区为 Asia/Shanghai
		var offset = now.getTimezoneOffset() * 60000; // 转换为毫秒
		var shanghaiTime = new Date(now.getTime() + offset + 28800000); // 28800000 是东八区的偏移量（8小时 * 60 * 60 * 1000）

		// 格式化日期和时间
		var year = shanghaiTime.getFullYear();
		var month = shanghaiTime.getMonth() + 1; // 月份从0开始
		var day = shanghaiTime.getDate();
		var weekday = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'][shanghaiTime.getDay()];
		var hours = shanghaiTime.getHours().toString().padStart(2, '0');
		var minutes = shanghaiTime.getMinutes().toString().padStart(2, '0');
		var seconds = shanghaiTime.getSeconds().toString().padStart(2, '0');

		// 组合成最终的格式
		var finalTime = year + " 年 " + month + " 月 " + day + " 日（" + weekday + "）" + hours + " : " + minutes + " : " + seconds;
		timeElement.innerHTML = "当前时间：" + finalTime;
	}

	// 每秒更新时间
	setInterval(updateTime, 1000);
</script>