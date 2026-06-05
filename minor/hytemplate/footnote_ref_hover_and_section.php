<?php
/*
 * WordPress 脚注ref(fnref)悬浮显示脚注内容 + "Hyplus注释"标题注入
 * 注：适用于WP Githuber MD等Markdown to HTML插件生成的脚注格式；popup样式详见HyplusCSS之HyNav相关衍生样式
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
		var lastMoveTime = 0;
		var moveThrottleDelay = 100; // 毫秒
		var listeners = {}; // 存储事件监听器引用，用于后续清理

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
			// 节流：100ms内只更新一次
			var now = Date.now();
			if (now - lastMoveTime < moveThrottleDelay) {
				return;
			}
			lastMoveTime = now;
			
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
			// 如果已初始化，先清理旧的监听器（支持PJAX重新加载）
			if (isInitialized) {
				cleanupListeners();
				isInitialized = false;
			}
			
			var refs = document.querySelectorAll('a.footnote-ref');
			
			if (refs.length === 0) {
				return;
			}
			
			// 定义事件处理函数
			listeners.mouseenter = function(e){
				if (e.target && e.target.classList && e.target.classList.contains('footnote-ref')) {
					var href = e.target.getAttribute('href');
					var html = getFootnoteContent(href);
					if (html) {
						showPopup(e, html);
					}
				}
			};
			
			listeners.mousemove = function(e){
				// 只在脚注引用上监听，避免频繁触发
				if (e.target && e.target.classList && e.target.classList.contains('footnote-ref')) {
					updatePopupPosition(e);
				}
			};
			
			listeners.mouseleave = function(e){
				if (e.target && e.target.classList && e.target.classList.contains('footnote-ref')) {
					hidePopup();
				}
			};
			
			// 使用事件委托，但限制在更具体的范围内
			// 先尝试在文章容器上注册，如果没有则在document上注册
			var articleContainer = document.querySelector('article') || document.querySelector('.post') || document.querySelector('main') || document;
			
			articleContainer.addEventListener('mouseenter', listeners.mouseenter, true);
			articleContainer.addEventListener('mousemove', listeners.mousemove, true);
			articleContainer.addEventListener('mouseleave', listeners.mouseleave, true);
			
			// 在脚注区域的ol前插入h1标题
			var footnotesList = document.querySelector('div.footnotes ol');
			if (footnotesList) {
				var footnotesDiv = footnotesList.parentElement;
				// 检查是否已经存在标题，避免重复添加
				if (!footnotesDiv.querySelector('h1.footnotes-title')) {
					var title = document.createElement('h1');
					title.className = 'footnotes-title';
					title.textContent = 'Hyplus注释';
					footnotesList.parentElement.insertBefore(title, footnotesList);
					
					// 插入标题后，触发hytoc的增量添加（如果已加载）
					if (typeof window.hyplus_add_toc_header_incremental === 'function') {
						window.hyplus_add_toc_header_incremental(title);
					}
				}
			}
			
			isInitialized = true;
		}
		
		// 清理旧的事件监听器
		function cleanupListeners() {
			if (!isInitialized) return;
			
			var articleContainer = document.querySelector('article') || document.querySelector('.post') || document.querySelector('main') || document;
			
			if (listeners.mouseenter) {
				articleContainer.removeEventListener('mouseenter', listeners.mouseenter, true);
			}
			if (listeners.mousemove) {
				articleContainer.removeEventListener('mousemove', listeners.mousemove, true);
			}
			if (listeners.mouseleave) {
				articleContainer.removeEventListener('mouseleave', listeners.mouseleave, true);
			}
		}
		
		// 监听PJAX更新事件，重新初始化
		document.addEventListener('pjax:end', function() {
			contentCache = {}; // 清空缓存
			initFootnoteHover();
		});
		
		// 也监听其他可能的动态加载事件
		document.addEventListener('page:loaded', function() {
			contentCache = {};
			initFootnoteHover();
		});

		// 多次尝试初始化，以应对异步加载内容
		function tryInit() {
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', function() {
					setTimeout(initFootnoteHover, 100);
				});
			} else {
				setTimeout(initFootnoteHover, 100);
			}
			
			// 只在初始加载时设置一次重试，避免无限循环
			if (!window.footnote_retry_scheduled) {
				window.footnote_retry_scheduled = true;
				setTimeout(function() {
					if (!isInitialized && document.querySelectorAll('a.footnote-ref').length > 0) {
						initFootnoteHover();
					}
				}, 1000);
			}
		}
		
		tryInit();
	})();
	</script>
	<?php
});
?>