<?php
/*
 * WordPress 脚注ref(fnref)悬浮显示脚注内容
 * 适用于 WP Githuber MD 插件生成的脚注格式
 * 将此文件作为功能片段引入即可
 */

add_action('wp_footer', function () {
	// 仅在单篇文章页面执行
	if (!is_singular('post')) {
		return;
	}
	?>
	<script>
	(function(){
		var contentCache = {};
		var isInitialized = false;

		function getFootnoteContent(href) {
			if (!href) return null;
			
			// 检查缓存
			if (contentCache[href]) {
				return contentCache[href];
			}
			
			// 去掉#，处理各种可能的格式
			var fnId = href.replace('#', '');
			
			// 尝试直接ID查找
			var li = document.getElementById(fnId);
			
			// // 如果找不到，尝试用querySelector查询
			// if (!li) {
			// 	li = document.querySelector('#' + fnId);
			// }
			
			// if (!li) {
			// 	// 最后尝试在整个文档中搜索所有li元素
			// 	var allLis = document.querySelectorAll('li');
			// 	for (var i = 0; i < allLis.length; i++) {
			// 		if (allLis[i].id === fnId) {
			// 			li = allLis[i];
			// 			break;
			// 		}
			// 	}
			// }
			
			if (!li) {
				return null;
			}
			
			// 获取脚注内容（可能在p标签或直接在li中）
			var p = li.querySelector('p');
			var content = p || li;
			
			if (!content) {
				return null;
			}
			
			var clone = content.cloneNode(true);
			
			// 移除所有返回箭头链接（可能有多个）
			var backrefs = clone.querySelectorAll('[class*="backref"], [class*="back-ref"], [rel="footnote"]');
			backrefs.forEach(function(backref) {
				backref.remove();
			});
			
			var html = clone.innerHTML.trim();
			contentCache[href] = html;
			return html;
		}

		var popup = document.createElement('div');
		popup.className = 'footnote-hover-popup';
		document.body.appendChild(popup);
		
		// 记录popup的尺寸和视口信息，避免频繁重新计算
		var popupState = {
			width: 0,
			height: 0,
			viewportWidth: window.innerWidth,
			viewportHeight: window.innerHeight
		};
		
		// 监听窗口大小变化
		window.addEventListener('resize', function() {
			popupState.viewportWidth = window.innerWidth;
			popupState.viewportHeight = window.innerHeight;
		});

		function computePosition(e) {
			var padding = 10;
			var positions;
			
			// 根据鼠标位置智能选择位置顺序
			if (e.clientX > popupState.viewportWidth / 2) {
				// 在右半边：优先左下、左上、右下、右上
				positions = [
					{ x: e.clientX - popupState.width - 12, y: e.clientY + 12 },
					{ x: e.clientX - popupState.width - 12, y: e.clientY - popupState.height - 12 },
					{ x: e.clientX + 12, y: e.clientY + 12 },
					{ x: e.clientX + 12, y: e.clientY - popupState.height - 12 }
				];
			} else {
				// 在左半边：优先右下、右上、左下、左上
				positions = [
					{ x: e.clientX + 12, y: e.clientY + 12 },
					{ x: e.clientX + 12, y: e.clientY - popupState.height - 12 },
					{ x: e.clientX - popupState.width - 12, y: e.clientY + 12 },
					{ x: e.clientX - popupState.width - 12, y: e.clientY - popupState.height - 12 }
				];
			}
			
			// 检查位置是否在视口内
			function isValid(pos) {
				return pos.x >= padding && 
					   pos.x + popupState.width + padding <= popupState.viewportWidth &&
					   pos.y >= padding && 
					   pos.y + popupState.height + padding <= popupState.viewportHeight;
			}
			
			// 找到第一个有效位置
			var finalPos = positions[0];
			for (var i = 0; i < positions.length; i++) {
				if (isValid(positions[i])) {
					finalPos = positions[i];
					break;
				}
			}
			
			// 如果没有完全有效的位置，至少确保不超出边界
			finalPos.x = Math.max(padding, Math.min(finalPos.x, popupState.viewportWidth - popupState.width - padding));
			finalPos.y = Math.max(padding, Math.min(finalPos.y, popupState.viewportHeight - popupState.height - padding));
			
			return finalPos;
		}

		function showPopup(e, html) {
			popup.innerHTML = html;
			popup.style.display = 'block';
			
			// 使用 requestAnimationFrame 确保尺寸已计算
			requestAnimationFrame(function() {
				popupState.width = popup.offsetWidth;
				popupState.height = popup.offsetHeight;
				
				var pos = computePosition(e);
				popup.style.left = pos.x + 'px';
				popup.style.top = pos.y + 'px';
			});
		}
		
		function updatePopupPosition(e) {
			// 轻量级位置更新，无需重新获取内容
			if (popup.style.display === 'block') {
				var pos = computePosition(e);
				popup.style.left = pos.x + 'px';
				popup.style.top = pos.y + 'px';
			}
		}
		
		function hidePopup() {
			popup.style.display = 'none';
		}

		function initFootnoteHover() {
			if (isInitialized) {
				return;
			}
			
			var refs = document.querySelectorAll('a.footnote-ref');
			
			if (refs.length === 0) {
				return;
			}
			
			refs.forEach(function(ref) {
				ref.addEventListener('mouseenter', function(e){
					var href = this.getAttribute('href');
					var html = getFootnoteContent(href);
					if (html) {
						showPopup(e, html);
					}
				});
				
				// mousemove 仅更新位置，不重新获取内容（轻量级）
				ref.addEventListener('mousemove', function(e){
					updatePopupPosition(e);
				});
				
				ref.addEventListener('mouseleave', hidePopup);
			});
			
			isInitialized = true;
		}

		// 多次尝试初始化，以应对异步加载内容
		function tryInit() {
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', function() {
					setTimeout(initFootnoteHover, 100);
				});
			} else {
				setTimeout(initFootnoteHover, 100);
			}
			
			// 如果初始化失败，1秒后重试
			setTimeout(function() {
				if (!isInitialized && document.querySelectorAll('a.footnote-ref').length > 0) {
					initFootnoteHover();
				}
			}, 1000);
		}
		
		tryInit();
	})();
	</script>
	<?php
});
?>