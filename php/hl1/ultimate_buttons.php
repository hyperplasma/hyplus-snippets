<!-- Ultimate Buttons v1.3 by Akira37 (Hyperplasma)
 外部组件：HyNav应用导航页面、Better Messages聊天窗口
 外部样式：`hyplus-unselectable` (Hyplus Overall); `hyplus-nav-section` `hyplus-nav-links` `hyplus-nav-group` `hyplus-nav-link` (HyNav)
 Code Type: universal
-->

<!-- HyButton按钮群 -->
<div id="refreshButton" title="刷新页面（⌘R）">↻</div>
<div id="goBackButton" title="返回上一页（⌘←）">❮</div>
<div id="goForwardButton" title="前往下一页（⌘→）">❯</div>
<div id="scrollToTopButton" title="返回顶部（⌘↑）">⬆︎</div>
<div id="toggleButtonsButton" title="收放Hyplus按钮群">×</div>
<div id="navButton" onclick="navOnClickFunc()" title="HyNav面板（⌥S）">NAV</div>

<!-- 导航弹出框 -->
<div id="navContainer">
	<!-- 标题栏按钮 -->
	<div id="backToTools" class="control-button back-button" title="返回工具选择" onclick="showToolSelector()" style="display: none; left: 10px; top: 10px;"></div>
	<div id="maximizeButton" class="control-button maximize-button" title="缩放（⌥M）" onclick="toggleMaximize(event)" style="right: 30px; top: 10px;"></div>
	<div id="closeButton" class="control-button close-button" title="关闭（⌥S）" onclick="closeNavContainer(event)" style="right: 10px; top: 10px;"></div>

	<!-- 切换按钮群 -->
	<div id="navSwitchButtons" class="nav-switch-buttons hyplus-unselectable">
		<button id="aiPageButton" class="switch-button" onclick="switchNavContent('ai')">KINA</button>
		<button id="chatPageButton" class="switch-button" onclick="switchNavContent('chat')">服务</button>
		<button id="navPageButton" class="switch-button active" onclick="switchNavContent('nav')">导航</button>
		<button id="notePageButton" class="switch-button" onclick="switchNavContent('note')">文本</button>
		<button id="settingsPageButton" class="switch-button" onclick="switchNavContent('settings')">设置</button>
	</div>

	<!-- 导航内容 -->
	<div id="navContent" class="nav-content">
		<?php
		echo do_shortcode('[wpcode id="11647"]');
		?>
		<div id="navMessage" class="hyplus-unselectable" style="color: gray; font-style: italic; text-align: center; margin-top: 16px;">Explore your Hyplusite!</div>
	</div>

	<!-- AI内容 -->
	<div id="aiContent" class="nav-content" style="display: none; padding: 0 15px;">
		<iframe id="kinaIframe" 
				src="about:blank" 
				style="width: 100%; height: 96%; border: 1px solid rgba(182,221,237, 0.85); border-radius: 12px;" 
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; camera; microphone; display-capture; fullscreen; clipboard-read; clipboard-write"
				referrerpolicy="origin"
				></iframe>
	</div>

	<!-- 文本页面 -->
	<div id="noteContent" class="nav-content" style="display: none; padding: 0 15px">
		<!-- 左列：Tutorials和TOC布局容器 -->
		<div class="note-toc-container">
			<div class="note-section hyplus-unselectable">
				<!-- Tutorials -->
				<div class="hyplus-nav-section">
					<h3>博文分类</h3>
					<div class="hyplus-nav-links">
						<div class="hyplus-nav-group">
							<a href="https://www.hyperplasma.top/category/general/guide/" class="hyplus-nav-link">软硬件指南</a>
							<a href="https://www.hyperplasma.top/category/general/hypress/" class="hyplus-nav-link">HyPress EX</a>
						</div>
						<div class="hyplus-nav-group">
							<a href="https://www.hyperplasma.top/category/dev/server/" class="hyplus-nav-link">服务端综合</a>
							<a href="https://www.hyperplasma.top/category/dev/frontend/" class="hyplus-nav-link">前端·Node.js</a>
							<a href="https://www.hyperplasma.top/category/dev/devops/" class="hyplus-nav-link">PHP·WP运维</a>
							<a href="https://www.hyperplasma.top/category/algorithm/" class="hyplus-nav-link">算法题库</a>
							<a href="https://www.hyperplasma.top/category/dev/ruankao/" class="hyplus-nav-link">系统架构设计</a>
						</div>
						<div class="hyplus-nav-group">
							<a href="https://www.hyperplasma.top/category/ai/python/" class="hyplus-nav-link">Python数据分析</a>
							<a href="https://www.hyperplasma.top/category/ai/math/" class="hyplus-nav-link">数学建模课程</a>
							<a href="https://www.hyperplasma.top/category/ai/dl/" class="hyplus-nav-link">深度学习</a>
							<a href="https://www.hyperplasma.top/category/ai/scholar/" class="hyplus-nav-link">论文精读存档</a>
						</div>
					</div>
				</div>
			</div>

			<!-- TOC -->
			<div class="toc-section" id="tocSection">
				<div id="tocHeader" class="hyplus-unselectable" style="font-size: 24px; font-weight: bold; text-align: center;">Hyplus目录</div>
				<div id="tocContent"></div>
				<br>
			</div>
		</div>
	</div>

	<!-- Tools -->
	<div id="chatContent" class="nav-content hyplus-unselectable" style="display: none;">
		<!-- 工具选择界面 -->
		<div id="toolSelector" class="tool-selector">
			<h2 class="tools-header">Hyplus服务</h2>
			<div class="tools-grid">
				<?php if (is_user_logged_in()) : ?>
				<div class="tool-card" onclick="switchTool('better_messages')">
					<div class="tool-icon">💬</div>
					<div class="tool-name">即时通讯</div>
					<div class="tool-desc">与站内好友聊天交流</div>
				</div>
				<?php else : ?>
				<div class="tool-card" onclick="window.location.href='https://www.hyperplasma.top/login/'">
					<div class="tool-icon">💬</div>
					<div class="tool-name">即时通讯</div>
					<div class="tool-desc" style="color: red;">登录后即可使用</div>
				</div>
				<?php endif; ?>
				<div class="tool-card" onclick="switchTool('wordcount')">
					<div class="tool-icon">📝</div>
					<div class="tool-name">字数统计</div>
					<div class="tool-desc">快速统计文本字数</div>
				</div>
				<div class="tool-card" onclick="switchTool('calculator')">
					<div class="tool-icon">🧮</div>
					<div class="tool-name">综合计算器</div>
					<div class="tool-desc">多功能科学计算工具</div>
				</div>
				<div class="tool-card" onclick="switchTool('encoder')">
					<div class="tool-icon">🗄️</div>
					<div class="tool-name">加密/编码器</div>
					<div class="tool-desc">文本加密与内容生成</div>
				</div>
				<!-- <div class="tool-card" onclick="switchTool('apigod')">
					<div class="tool-icon">🚀</div>
					<div class="tool-name">API-GOD</div>
					<div class="tool-desc">网络测试综合工具</div>
				</div> -->
			</div>
		</div>

		<!-- 工具内容展示区 -->
		<div id="toolContentContainer" style="display: none;">
			<?php
			$tool = isset($_GET['tool']) ? $_GET['tool'] : '';
			switch($tool) {
				case 'better_messages':
					echo do_shortcode('[better_messages]');
					break;
				case 'wordcount':
					echo do_shortcode('[wpcode id="12689"]');
					break;
				case 'calculator':
					echo do_shortcode('[wpcode id="12585"]');
					break;
				case 'encoder':
					echo do_shortcode('[wpcode id="12640"]');
					break;
				case 'apigod':
					echo do_shortcode('[wpcode id="12802"]');
					break;
			}
			?>
		</div>
	</div>

	<!-- 设置 -->
	<div id="settingsContent" class="nav-content hyplus-unselectable" style="display: none;">
		<div id="settingsHeader" style="font-size: 24px; font-weight: bold; text-align: center; margin: 15px 0;">Hyplus设置&amp;快捷键大全</div>

		<!-- 设置内容两列布局 -->
		<div class="settings-columns-container">
			<!-- 左列：复选框设置 -->
			<div class="settings-column">
				<div class="language-selector" id="sidebarRadioGroup">
					<div class="config-item">
						<input type="radio" id="sidebarRightRadio" name="sidebarPosition" value="right">
						<label for="sidebarRightRadio">侧边栏位于右侧　<span class="shortcut-key">⌥⇧S</span></label>
					</div>
					<div class="config-item">
						<input type="radio" id="sidebarLeftRadio" name="sidebarPosition" value="left">
						<label for="sidebarLeftRadio">侧边栏位于左侧</label>
					</div>
					<div class="config-item">
						<input type="radio" id="sidebarHideRadio" name="sidebarPosition" value="hide">
						<label for="sidebarHideRadio">始终隐藏侧边栏</label>
					</div>
				</div>

				<div class="language-selector" style="margin-top: 12px;">
					<div class="config-item">
						<input type="checkbox" id="headerFooterToggle" />
						<label for="headerFooterToggle">隐藏页眉页脚　<span class="shortcut-key">⌥⇧H</span></label>
					</div>
					<div class="config-item">
						<input type="checkbox" id="tempHideButtons" />
						<label for="tempHideButtons">临时隐藏Hyplus按钮群　<span class="shortcut-key">⌥⇧B</span></label>
					</div>
				</div>
			</div>

			<!-- 右列 -->
			<div class="settings-column">
				<!-- 字体缩放控制区 -->
				<div class="language-selector">
					<div class="language-selector-row">
						<span class="language-label">字体缩放:</span>
						<div class="font-size-controls">
							<button id="decreaseFontBtn" class="font-size-btn">-</button>
							<span id="fontSizeDisplay" class="font-size-display">100%</span>
							<button id="increaseFontBtn" class="font-size-btn">+</button>
							<button id="resetFontBtn" class="font-size-btn reset">还原</button>
						</div>
					</div>
				</div>

				<div class="language-selector" style="margin-top: 12px;">
					<div class="language-selector-row">
						<label class="language-label">选择语言:</label>
						<label id="languageLabel" class="language-label">
							<?php
							echo do_shortcode('[gtranslate]');
							?>
						</label>
					</div>
				</div>

				<div class="language-selector" style="margin-top: 12px;">
					<div class="language-selector-row">
						<span class="language-label">复制本页面正文:</span>
						<button id="copyContentBtn" class="font-size-btn">复制</button>
						<span id="copySuccessTip" style="color: #4CAF50; display: none;">✔︎</span>
					</div>
					<div class="language-selector-row">
						<input type="checkbox" id="addPromptCheckbox" />
						<label for="addPromptCheckbox">
							<span class="language-label">附加提问提示词（适用于<a href="https://kina.hyperplasma.top" target="_blank">KINA</a>）</span>
						</label>
					</div>
				</div>
			</div>
		</div>

		<!-- 快捷键说明 -->
		<div id="shortcutsSection" style="margin-top: 40px; padding: 0 20px;">
			<div class="shortcuts-container">
				<div class="shortcuts-column">
					<div class="shortcut-item">
						显示/隐藏导航框
						<span class="shortcut-key">⌥S</span>
					</div>
					<div class="shortcut-item">
						进入/退出最大化模式
						<span class="shortcut-key">⌥M</span>
					</div>
					<div class="shortcut-item">
						切换到上一个页面
						<span class="shortcut-key">⌥Z</span>
					</div>
					<div class="shortcut-item">
						切换到下一个页面
						<span class="shortcut-key">⌥X</span>
					</div>
				</div>
				<div class="shortcuts-column">
					<div class="shortcut-item">
						在新标签页中打开链接
						<span class="shortcut-key">⌥左键单击</span>
					</div>
					<div class="shortcut-item">
						减小字体大小
						<span class="shortcut-key">⌥-</span>
					</div>
					<div class="shortcut-item">
						增大字体大小
						<span class="shortcut-key">⌥=</span>
					</div>
					<div class="shortcut-item">
						还原字体大小
						<span class="shortcut-key">⌥0</span>
					</div>
				</div>
			</div>

			<div style="color: gray; margin-top: 26px; font-size: 11px; text-align: center;">
				⌥: Alt/Option　　⇧: Shift　　⌃: Control　　⌘: Command
			</div>
			<div style="color: gray; margin-top: 2px; font-size: 11px; text-align: center;">
				-- 注意事项 --
			</div>
			<div style="color: gray; margin-top: 2px; font-size: 11px; text-align: center;">
				iframe版<a href="https://kina.hyperplasma.top" target="_blank">KINA</a>在各浏览器中存在轻微问题，Safari浏览器无法共享网页版存储与图片上传功能，Edge浏览器可能发生轻微布局错误
			</div>
			<div style="color: gray; margin-top: 2px; font-size: 11px; text-align: center;">
				通过<a href="https://www.hyperplasma.top/article/13242/">Hyplusite Exporter</a>导出的页面不支持在线服务，且部分由PHP预生成的JS组件存在显示问题
			</div>
		</div>
		<div id="configMessage" class="hyplus-unselectable" style="color: #d6d6d6; font-size: 16px; font-style: italic; text-align: center; margin: 24px 0;">Ultimate Buttons v1.3 by Akira37</div>
	</div>
</div>

<style>
	/* 基础字体缩放 */
	:root {
		--font-scale: 1;
	}

	p, span, a, li, td, th, div:not(.font-size-controls *), 
	label:not(.font-size-controls *), 
	input[type="text"], textarea, 
	button:not(.font-size-btn),
	.post-content, .entry-content,
	article, section, blockquote, code, pre {
		font-size: calc(var(--font-scale) * 1em);
	}

	/* 按钮通用样式 */
	#scrollToTopButton,
	#toggleButtonsButton,
	#navButton,
	#goBackButton,
	#goForwardButton,
	#refreshButton {
		position: fixed;
		width: 53px;
		height: 53px;
		color: white;
		text-align: center;
		line-height: 53px;
		border-radius: 50%;
		cursor: pointer;
		z-index: 119;
		font-family: Arial, sans-serif;
		font-weight: bold;
		box-shadow: 4px 4px 10px 0 rgba(0, 0, 0, 0.5);
		transition: transform 0.2s ease, background-color 0.5s ease;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	#scrollToTopButton {
		font-size: 18px;
		bottom: 10px;
		right: 130px;
		background-color: rgba(84, 126, 239, 1);
	}

	#toggleButtonsButton {
		font-size: 26px;
		bottom: 10px;
		right: 10px;
		background-color: red;
	}

	#navButton {
		font-size: 17px;
		bottom: 10px;
		right: 70px;
		background-color: rgba(84, 126, 239, 1);
	}

	#goBackButton {
		font-size: 18px;
		bottom: 70px;
		right: 70px;
		background-color: rgba(84, 126, 239, 1);
	}

	#goForwardButton {
		font-size: 18px;
		bottom: 70px;
		right: 10px;
		background-color: rgba(84, 126, 239, 1);
	}

	#refreshButton {
		font-size: 24px;
		bottom: 130px;
		right: 10px;
		background-color: rgba(84, 126, 239, 1);
	}

	/* 鼠标悬停效果 */
	#scrollToTopButton:hover,
	#navButton:hover,
	#goBackButton:hover,
	#goForwardButton:hover,
	#refreshButton:hover {
		transform: scale(1.12);
		background-color: rgba(94, 136, 249, 1);
	}

	#toggleButtonsButton:hover {
		transform: scale(1.12);
		background-color: rgba(255, 51, 51, 1);
	}

	/* 导航框样式 */
	#navContainer {
		display: none;
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		width: 85%;
		height: 85%;
		max-width: 850px;
		overflow: hidden;
		background-color: #ffffff;
		border: 1px solid #ddd;
		box-shadow: 4px 4px 10px 0 rgba(0, 0, 0, 0.5);
		padding: 0;
		z-index: 120;
		border-radius: 12px;
	}

	@media screen and (min-width: 769px) {
		#navContainer {
			transition: all 0.3s ease;
		}
	}

	/* 导航框控制按钮 */
	.control-button {
		position: absolute;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		cursor: pointer;
		z-index: 121;
		transition: all 0.15s ease;
	}

	.control-button:hover {
		transform: scale(1.2);
	}

	.back-button {
		background-color: purple;
		color: white;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 10px;
		font-weight: bold;
	}

	.back-button:hover {
		box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
	}

	.maximize-button {
		background-color: #4CAF50;
	}

	.maximize-button:hover {
		box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
	}

	.maximize-button.maximized {
		background-color: #FF9800;
	}

	.close-button {
		background-color: red;
	}

	.close-button:hover {
		box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
	}

	/* 导航切换按钮样式 */
	.nav-switch-buttons {
		padding: 8px 15px;
		display: flex;
		justify-content: center;
		gap: 10px;
		background-color: white;
		border-radius: 12px 12px 0 0;
		overflow-x: auto;
		scroll-behavior: smooth;
		width: calc(100% - 80px);
		margin: 0 auto;
		position: relative;
		left: 0;
	}

	.nav-switch-buttons .switch-button {
		padding: 4px 12px;
		border: none;
		border-radius: 4px;
		background-color: #e6f3ff;
		color: #333;
		cursor: pointer;
		font-size: 14px !important;
		transition: background-color 0.3s ease;
		white-space: nowrap;
		min-width: fit-content;
	}

	.switch-button.active {
		background-color: #9fd2ff;
	}

	.switch-button:hover {
		color: #333;
		background-color: #8fcaff;
	}

	/* 内容区域样式 */
	.nav-content {
		height: calc(100% - 37px);
		overflow-y: auto;
		padding: 0 15px;
		max-width: 1200px;
		margin: 0 auto;
		padding-bottom: 20px;
	}

	/* 工具选择界面样式 */
	.tools-header {
		text-align: center;
		font-size: 28px;
		font-weight: bold;
		margin: 20px 0;
		color: #333;
	}

	.tools-grid {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		gap: 20px;
		padding: 20px;
		max-width: 1200px;
		margin: 0 auto;
	}

	.tool-card {
		background: #ffffff;
		border: 1px solid #b6dded;
		border-radius: 12px;
		padding: 20px;
		cursor: pointer;
		transition: all 0.3s ease;
		display: flex;
		flex-direction: column;
		align-items: center;
		text-align: center;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	}

	.tool-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
		border-color: #4a90e2;
	}

	.tool-icon {
		font-size: 36px;
		margin-bottom: 10px;
	}

	.tool-name {
		font-size: 18px;
		font-weight: bold;
		color: #333;
		margin-bottom: 8px;
	}

	.tool-desc {
		font-size: 14px;
		color: #666;
		line-height: 1.4;
	}

	/* 笔记和目录布局 */
	.note-toc-container {
		display: flex;
		gap: 20px;
		height: calc(100% - 20px);
		padding: 0 15px;
		max-width: 1200px;
		margin: 0 auto;
		padding-bottom: 20px;
	}

	.note-section {
		flex: 1;
		min-width: 0;
	}

	.toc-section {
		width: 350px;
		padding: 0 10px;
	}

	.resizable-textarea {
		min-height: 150px;
		height: 200px;
		resize: vertical;
		max-height: calc(100vh - 200px);
	}

	.resizable-textarea:focus {
		outline: none;
		border-color: #9fd2ff;
		box-shadow: 0 0 5px rgba(84, 126, 239, 0.3);
	}

	/* 目录样式 */
	#tocContent ul {
		list-style-type: none;
		padding-left: 0;
		margin: 10px 0;
	}

	#tocContent ul li {
		margin-bottom: 10px;
	}

	#tocContent ul li a {
		text-decoration: none;
		color: #0073aa;
		transition: color 0.2s ease;
	}

	#tocContent ul li a:hover {
		text-decoration: none;
		color: red;
	}

	#tocContent ul li.level-1 { margin-left: 0px; }
	#tocContent ul li.level-2 { margin-left: 15px; }
	#tocContent ul li.level-3 { margin-left: 30px; }
	#tocContent ul li.level-4 { margin-left: 45px; }
	#tocContent ul li.level-5 { margin-left: 60px; }
	#tocContent ul li.level-6 { margin-left: 75px; }

	/* 设置页面样式 */
	.settings-columns-container {
		display: flex;
		gap: 30px;
		padding: 0 20px;
		margin-bottom: 20px;
		max-width: 1200px;
		margin: 0 auto 20px;
	}

	.settings-column {
		flex: 1;
		min-width: 0;
	}

	.language-selector-row {
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.language-label {
		font-size: 14px !important;    
		color: #333;
		/* white-space: nowrap;	*/
	}

	.language-selector {
		padding: 10px;
		background-color: #f8f9fa;
		border-radius: 8px;
		border: 1px solid #b6dded;
	}

	.language-selector select,
	.language-selector .switcher {
		flex: 1;
		min-width: 0;
	}

	.config-item {
		margin: 10px 0;
		margin-left: 30px;
		display: flex;
		align-items: center;
	}

	.config-item input[type="checkbox"] {
		margin: 0;
		cursor: pointer;
	}

	.config-item label {
		margin-left: 10px;
		cursor: pointer;
		color: #333;
		font-size: 14px;
	}

	/* 快捷键说明样式 */
	.shortcuts-container {
		display: flex;
		flex-wrap: wrap;
		width: 100%;
		border: 1px solid #b6dded;
		border-radius: 4px;
	}

	.shortcuts-column {
		flex: 1;
		min-width: 250px;
	}

	.shortcuts-column:first-child {
		border-right: 1px solid #b6dded;
	}

	.shortcut-item {
		padding: 8px;
		border-bottom: 1px solid #b6dded;
		display: flex;
		justify-content: space-between;
	}

	.shortcut-key {
		color: gray;
	}

	/* 字体控制样式 */
	.font-size-controls {
		display: flex;
		align-items: center;
		gap: 8px;
		flex: 1;
	}

	.font-size-btn {
		padding: 2px 10px;
		border: 1px solid #ddd;
		border-radius: 4px;
		background-color: #fff;
		color: #333;
		cursor: pointer;
		font-size: 14px !important;
		transition: all 0.2s ease;
	}

	.font-size-btn:hover {
		color: #333;
		background-color: #f0f0f0;
	}

	.font-size-btn.reset {
		padding: 2px 8px;
		background-color: #e6f3ff;
	}

	.font-size-btn.reset:hover {
		color: #333;
		background-color: #d0e8ff;
	}

	.font-size-display {
		min-width: 50px;
		text-align: center;
		font-size: 14px;
		color: #333;
	}

	/* 响应式布局 */
	@media screen and (max-width: 768px) {
		.note-toc-container {
			flex-direction: column;
			gap: 10px;
		}

		.toc-section {
			width: 100%;
			border-left: none;
			padding-top: 10px;
		}

		.resizable-textarea {
			height: 200px;
			min-height: 150px;
		}

		.settings-columns-container {
			flex-direction: column;
			gap: 20px;
		}

		.settings-column {
			width: 100%;
		}

		.nav-switch-buttons {
			width: calc(100% - 40px);
		}

		.shortcuts-column {
			flex: 100%;
		}

		.shortcuts-column:first-child {
			border-right: none;
		}

		.shortcuts-column:first-child .shortcut-item:last-child {
			border-bottom: 1px solid #eee;
		}

		.tools-grid {
			grid-template-columns: repeat(2, 1fr);
			padding: 15px;
		}

		.tool-card {
			padding: 15px;
		}
	}

	/* 杂项样式 */
	body.nav-open {
		overflow: hidden !important;
	}

	body.nav-open .sidebar {
		overflow: hidden !important;
	}

	#navContainer.maximized {
		width: 100%;
		height: 100%;
		max-width: none;
		border-radius: 0;
	}

	.toc-section:empty {
		display: none;
	}

	#tocSection:empty {
		display: none;
	}

	#tocSection:empty + .note-section {
		width: 100%;
	}

	.note-toc-container:has(> .toc-section:empty) .note-section {
		flex: 1;
		width: 100%;
	}

	.sidebar {
		order: 2;
	}
	.content-area {
		order: 1;
		flex: 1 1 auto;
	}
	body.sidebar-left .sidebar {
		order: 1;
		width: 28.7%
			padding-left: -13px;
		margin-left: 0 !important;
		margin-right: 5px;
	}
	body.sidebar-left .content-area {
		order: 2;
		margin-right: 8px;
	}

	.language-selector.disabled {
		opacity: 0.6;
		pointer-events: none;
	}
	.language-selector.disabled input[type="radio"] {
		pointer-events: none;
	}
	.language-selector.disabled label {
		cursor: not-allowed;
	}

	@media screen and (max-width: 768px) {
		.sidebar {
			display: none !important;
		}
	}
</style>

<script>
	// 全局变量声明
	let chatBtn = null;
	let chatContent = null;
	let navContainer = null;
	let isNavMaximized = localStorage.getItem('isNavMaximized') === 'true';

	// 工具菜单相关函数
	function showToolSelector() {
		// 获取当前 URL 并解析
		const currentUrl = new URL(window.location.href);

		// 移除 tool 参数但保留其他参数和路径
		currentUrl.searchParams.delete('tool');

		// 构建新的 URL，保留路径、参数和锚点
		let newUrl = currentUrl.pathname;  // 首先获取路径

		// 添加查询参数（如果有）
		const searchParams = currentUrl.searchParams.toString();
		if (searchParams) {
			newUrl += '?' + searchParams;
		}

		// 添加锚点（如果有）
		if (currentUrl.hash) {
			newUrl += currentUrl.hash;
		}

		// 设置标志以在页面重载后保持 Nav 框显示
		localStorage.setItem('keepNavOpen', 'true');
		//		window.location.href = newUrl;
		if (typeof window.animateAndJump === 'function') {
			window.animateAndJump(newUrl);
		} else {
			window.location.href = newUrl;
		}
	}

	function switchTool(toolKey) {
		// 获取当前 URL 并解析
		const currentUrl = new URL(window.location.href);

		// 设置 tool 参数
		currentUrl.searchParams.set('tool', toolKey);

		// 构建新的 URL，保留路径和锚点
		let newUrl = currentUrl.pathname;

		// 添加查询参数
		const searchParams = currentUrl.searchParams.toString();
		if (searchParams) {
			newUrl += '?' + searchParams;
		}

		// 添加锚点（如果有）
		if (currentUrl.hash) {
			newUrl += currentUrl.hash;
		}

		// 设置标志以在页面重载后保持 Nav 框显示
		localStorage.setItem('keepNavOpen', 'true');
		//		window.location.href = newUrl;
		if (typeof window.animateAndJump === 'function') {
			window.animateAndJump(newUrl);
		} else {
			window.location.href = newUrl;
		}
	}

	// 检查 URL 参数并显示相应界面
	function checkAndLoadTool() {
		const urlParams = new URLSearchParams(window.location.search);
		const tool = urlParams.get('tool');

		if (tool) {
			// 如果有工具参数，显示工具内容和返回按钮
			document.getElementById('toolSelector').style.display = 'none';
			document.getElementById('toolContentContainer').style.display = 'block';
			document.getElementById('backToTools').style.display = 'block';
		} else {
			// 否则显示工具选择界面
			document.getElementById('toolSelector').style.display = 'block';
			document.getElementById('toolContentContainer').style.display = 'none';
			document.getElementById('backToTools').style.display = 'none';
		}
	}

	// 基础工具函数
	function debounce(func, wait) {
		let timeout;
		return function() {
			const context = this;
			const args = arguments;
			clearTimeout(timeout);
			timeout = setTimeout(() => func.apply(context, args), wait);
		}
	}

	// 页头页尾控制
	function hideHeaderFooter() {
		const header = document.querySelector('.site-header');
		const footer = document.querySelector('.site-footer');
		if (header) header.style.display = 'none';
		if (footer) footer.style.display = 'none';
	}

	function showHeaderFooter() {
		const header = document.querySelector('.site-header');
		const footer = document.querySelector('.site-footer');
		if (header) header.style.display = 'block';
		if (footer) footer.style.display = 'block';
	}

	function handleHeaderFooterToggle(event) {
		if (event.target.checked) {
			hideHeaderFooter();
			localStorage.setItem('headerFooterAlwaysHidden', 'true');
		} else {
			showHeaderFooter();
			localStorage.setItem('headerFooterAlwaysHidden', 'false');
		}
	}

	// 导航框控制
	function updateTocVisibility() {
		const tocSection = document.getElementById('tocSection');
		const tocContent = document.getElementById('tocContent');
		const noteSection = document.querySelector('.note-section');
		if (!tocContent || !tocContent.hasChildNodes()) {
			tocSection.style.display = 'none';
			noteSection.style.width = '100%';
		} else {
			tocSection.style.display = 'block';
			noteSection.style.width = '';
		}
	}

	function toggleMaximize(event) {
		event.stopPropagation();
		const navContainer = document.getElementById('navContainer');
		const maximizeButton = document.getElementById('maximizeButton');
		isNavMaximized = !isNavMaximized;
		localStorage.setItem('isNavMaximized', isNavMaximized);

		if (!isNavMaximized) {
			navContainer.classList.remove('maximized');
			maximizeButton.classList.remove('maximized');
			navContainer.style.width = '85%';
			navContainer.style.height = '85%';
			navContainer.style.maxWidth = '850px';
			navContainer.style.borderRadius = '12px';
		} else {
			navContainer.classList.add('maximized');
			maximizeButton.classList.add('maximized');
			navContainer.style.width = '100%';
			navContainer.style.height = '100%';
			navContainer.style.maxWidth = 'none';
			navContainer.style.borderRadius = '0';
		}
	}

	function navOnClickFunc() {
		const nav = document.getElementById('navContainer');
		const body = document.body;
		const maximizeButton = document.querySelector('.maximize-button');

		if (nav.style.display === 'block') {
			nav.style.display = 'none';
			body.classList.remove('nav-open');
		} else {
			nav.style.display = 'block';
			body.classList.add('nav-open');

			if (window.innerWidth <= 568) {
				isNavMaximized = true;
				localStorage.setItem('isNavMaximized', true);
				nav.classList.add('maximized');
				document.getElementById('maximizeButton').classList.add('maximized');
				nav.style.width = '100%';
				nav.style.height = '100%';
				nav.style.maxWidth = 'none';
				nav.style.borderRadius = '0';
				maximizeButton.style.display = 'none';
			} else if (isNavMaximized) {
				nav.classList.add('maximized');
				document.getElementById('maximizeButton').classList.add('maximized');
				nav.style.width = '100%';
				nav.style.height = '100%';
				nav.style.maxWidth = 'none';
				nav.style.borderRadius = '0';
			}

			// 如果在工具页面，检查并加载相应工具
			if (chatContent && chatContent.style.display !== 'none') {
				checkAndLoadTool();
			}
		}
	}

	function closeNavContainer(event) {
		event.stopPropagation();
		const navContainer = document.getElementById('navContainer');
		navContainer.style.display = 'none';
		document.body.classList.remove('nav-open');
	}

	// 按钮控制
	function scrollToTop() {
		window.scrollTo({top: 0, behavior: 'smooth'});
	}

	function toggleButtons(show) {
		const buttons = {
			scrollToTopButton: document.getElementById('scrollToTopButton'),
			navButton: document.getElementById('navButton'),
			toggleButtonsButton: document.getElementById('toggleButtonsButton'),
			goBackButton: document.getElementById('goBackButton'),
			goForwardButton: document.getElementById('goForwardButton'),
			refreshButton: document.getElementById('refreshButton')
		};

		if (show) {
			Object.values(buttons).forEach(button => { 
				if (button) button.style.display = 'block';
			});
			buttons.toggleButtonsButton.textContent = '×';
		} else {
			Object.values(buttons).forEach(button => {
				if (button && button !== buttons.toggleButtonsButton) {
					button.style.display = 'none';
				}
			});
			buttons.toggleButtonsButton.textContent = '···';
		}
		localStorage.setItem('buttonsShown', show);
	}

	// 侧边栏控制
	function showSidebar() {
		const sidebar = document.querySelector('.sidebar');
		const mainContent = document.querySelector('.site-main');
		const contentArea = document.querySelector('.content-area');
		if (sidebar) {
			sidebar.style.display = 'block';
			sidebar.style.marginLeft = '13px';
			contentArea.style.width = '70%';
			mainContent.style.paddingRight = '0';
			mainContent.style.width = 'calc(100% + 8px)';
		}
	}

	function hideSidebar() {
		const sidebar = document.querySelector('.sidebar');
		const mainContent = document.querySelector('.site-main');
		const contentArea = document.querySelector('.content-area');
		if (sidebar) {
			sidebar.style.display = 'none';
			mainContent.style.paddingRight = '0px';
			contentArea.style.width = 'calc(100%)';
			mainContent.style.width = 'calc(100%)';
		}
	}

	function handleTempHideButtons(event) {
		const buttons = [
			'scrollToTopButton',
			'toggleButtonsButton',
			'navButton',
			'goBackButton',
			'goForwardButton',
			'refreshButton'
		];
		buttons.forEach(buttonId => {
			const button = document.getElementById(buttonId);
			if (button) button.style.display = event.target.checked ? 'none' : 'block';
		});
	}

	// 字体大小控制
	function initFontSizeControls() {
		const decreaseBtn = document.getElementById('decreaseFontBtn');
		const increaseBtn = document.getElementById('increaseFontBtn');
		const resetBtn = document.getElementById('resetFontBtn');
		const display = document.getElementById('fontSizeDisplay');
		let scalePercent = parseInt(localStorage.getItem('fontScalePercent')) || 100;
		updateFontScale(scalePercent);

		decreaseBtn.addEventListener('click', (event) => {
			event.stopPropagation();
			if (scalePercent > 50) {
				scalePercent -= 2;
				updateFontScale(scalePercent);
			}
		});

		increaseBtn.addEventListener('click', (event) => {
			event.stopPropagation();
			if (scalePercent < 150) {
				scalePercent += 2;
				updateFontScale(scalePercent);
			}
		});

		resetBtn.addEventListener('click', (event) => {
			event.stopPropagation();
			scalePercent = 100;
			updateFontScale(scalePercent);
		});
	}

	function updateFontScale(scalePercent) {
		const display = document.getElementById('fontSizeDisplay');
		if (display) display.textContent = scalePercent + '%';
		const scale = scalePercent / 100;
		document.documentElement.style.setProperty('--font-scale', scale);
		localStorage.setItem('fontScalePercent', scalePercent);
	}

	// 页面切换
	function switchNavContent(page) {
		const contents = {
			nav: document.getElementById('navContent'),
			chat: document.getElementById('chatContent'),
			settings: document.getElementById('settingsContent'),
			ai: document.getElementById('aiContent'),
			note: document.getElementById('noteContent')
		};
		const buttons = {
			nav: document.getElementById('navPageButton'),
			chat: document.getElementById('chatPageButton'),
			settings: document.getElementById('settingsPageButton'),
			ai: document.getElementById('aiPageButton'),
			note: document.getElementById('notePageButton')
		};

		Object.values(contents).forEach(content => { 
			if (content) content.style.display = 'none';
		});
		Object.values(buttons).forEach(button => { 
			if (button) button.classList.remove('active');
		});

		if (contents[page]) {
			contents[page].style.display = 'block';
			buttons[page].classList.add('active');

			if (page === 'ai') {
				const kinaIframe = document.getElementById('kinaIframe');
				if (!kinaIframe.src || kinaIframe.src === 'about:blank') {
					kinaIframe.src = 'https://kina.hyperplasma.top';
				}
			}
			if (page === 'chat') {
				checkAndLoadTool();
			}
		}
		localStorage.setItem('lastVisitedNavPage', page);
	}

	function toggleSidebarPosition(isLeft) {
		if (isLeft) {
			document.body.classList.add('sidebar-left');
		} else {
			document.body.classList.remove('sidebar-left');
		}
	}

	function setSidebarRadioGroupEnabled(enabled) {
		const radioGroup = document.getElementById('sidebarRadioGroup');
		const radios = radioGroup.querySelectorAll('input[type="radio"]');
		if (enabled) {
			radioGroup.classList.remove('disabled');
			radios.forEach(radio => radio.disabled = false);
		} else {
			radioGroup.classList.add('disabled');
			radios.forEach(radio => radio.disabled = true);
		}
	}

	function setSidebarPosition(position) {
		if (position === 'left' && window.innerWidth > 768) {
			showSidebar();
			toggleSidebarPosition(true);
			localStorage.setItem('sidebarPosition', 'left');
			localStorage.setItem('sidebarAlwaysHidden', 'false');
		} else if (position === 'hide') {
			hideSidebar();
			toggleSidebarPosition(false);
			localStorage.setItem('sidebarAlwaysHidden', 'true');
			localStorage.setItem('sidebarPosition', 'right');
		} else if (window.innerWidth > 768) { // right
			showSidebar();
			toggleSidebarPosition(false);
			localStorage.setItem('sidebarPosition', 'right');
			localStorage.setItem('sidebarAlwaysHidden', 'false');
		}
		// 设置radio选中状态（防止快捷键切换时UI不同步）
		document.getElementById('sidebarRightRadio').checked = (position === 'right');
		document.getElementById('sidebarLeftRadio').checked = (position === 'left');
		document.getElementById('sidebarHideRadio').checked = (position === 'hide');
	}

	// 键盘快捷键处理
	document.addEventListener('keydown', function(event) {
		const navContainer = document.getElementById('navContainer');

		// Nav框显示/隐藏 (Alt+S)
		if (event.altKey && (event.key === 's' || event.key === 'ß')) {
			event.preventDefault();
			navOnClickFunc();
			event.stopPropagation();
		}

		// 最大化切换 (Alt+M)
		if (event.altKey && (event.key === 'm' || event.key === 'µ')) {
			event.preventDefault();
			if (navContainer.style.display === 'block') {
				toggleMaximize(event);
			}
			event.stopPropagation();
		}

		// 页面切换 (Alt+Z/X 或 Alt+←/→)
		if (navContainer.style.display === 'block') {
			if (event.altKey && (
				event.key === 'z' || event.key === 'x' || 
				event.key === 'Ω' || event.key === '≈' ||
				event.key === 'ArrowLeft' || event.key === 'ArrowRight'
			)) {
				event.preventDefault();
				const currentActive = document.querySelector('.switch-button.active');
				const pages = ['ai', 'chat', 'nav', 'note', 'settings'];
				const currentIndex = pages.indexOf(currentActive.id.replace('PageButton', ''));
				if (event.key === 'z' || event.key === 'Ω' || event.key === 'ArrowLeft') {
					const prevIndex = (currentIndex - 1 + pages.length) % pages.length;
					switchNavContent(pages[prevIndex]);
				} else {
					const nextIndex = (currentIndex + 1) % pages.length;
					switchNavContent(pages[nextIndex]);
				}
				event.stopPropagation();
			}
		}

		// 字体大小控制
		if (event.altKey) {
			switch (event.key) {
				case '-':
				case '_':
				case '–':
					event.preventDefault();
					event.stopPropagation();
					document.getElementById('decreaseFontBtn')?.click();
					break;
					case '=':
					case '+':
					case '≠':
					event.preventDefault();
					event.stopPropagation();
					document.getElementById('increaseFontBtn')?.click();
					break;
					case '0':
					case 'º':
					event.preventDefault();
					event.stopPropagation();
					document.getElementById('resetFontBtn')?.click();
					break;
			}
			}

					// Alt+Shift 组合键
					if (event.altKey && event.shiftKey) {
					switch (event.key) {
					case 'S':
					case 'Í':
					case 's':
					event.preventDefault();
					event.stopPropagation();

					if (window.innerWidth <= 768) return;

					const order = ['right', 'left', 'hide'];
					let current = 'right';
					if (document.getElementById('sidebarLeftRadio').checked) {
						current = 'left';
					} else if (document.getElementById('sidebarHideRadio').checked) {
						current = 'hide';
					}
					const nextIndex = (order.indexOf(current) + 1) % order.length;
					setSidebarPosition(order[nextIndex]);
					break;
				case 'H':
				case 'Ó':
				case 'h':
					event.preventDefault();
					event.stopPropagation();
					const headerFooterToggle = document.getElementById('headerFooterToggle');
					if (headerFooterToggle) {
						headerFooterToggle.checked = !headerFooterToggle.checked;
						handleHeaderFooterToggle({ target: headerFooterToggle });
					}
					break;
				case 'B':
				case 'ı':
				case 'b':
					event.preventDefault();
					event.stopPropagation();
					const tempHideButtons = document.getElementById('tempHideButtons');
					if (tempHideButtons) {
						tempHideButtons.checked = !tempHideButtons.checked;
						handleTempHideButtons({ target: tempHideButtons });
					}
					break;
			}
		}
	});

	// 主体点击事件处理
	document.body.addEventListener('click', function(event) {
		if (event.altKey) return;
		const nav = document.getElementById('navContainer');
		const navButton = document.getElementById('navButton');
		if (nav.style.display === 'block') {
			const rectNav = nav.getBoundingClientRect();
			const isInNav = event.clientX >= rectNav.left &&
				  event.clientX <= rectNav.right &&
				  event.clientY >= rectNav.top &&
				  event.clientY <= rectNav.bottom;
			const isNavButtonClicked = navButton.contains(event.target);
			if (!isInNav && !isNavButtonClicked) {
				nav.style.display = 'none';
				document.body.classList.remove('nav-open');
			}
		}
	});

	// 页面加载初始化
	document.addEventListener('DOMContentLoaded', function() {
		// 初始化全局变量
		chatBtn = document.getElementById('chatPageButton');
		chatContent = document.getElementById('chatContent');
		navContainer = document.getElementById('navContainer');

		// 文章目录生成
		const headers = document.querySelectorAll("article h1, article h2, article h3, article h4, article h5, article h6");
		const tocContent = document.getElementById("tocContent");
		const tocHeader = document.getElementById("tocHeader");
		const pattern = /^[0-9]+(\.[0-9]+)*(\)|\.)?[\s]/;  // 修正正则表达式
		const anchorMap = new Map();

		if (headers.length > 0) {
			const ul = document.createElement("ul");
			const fragment = document.createDocumentFragment();
			let hasContent = false;

			headers.forEach((header) => {
				const match = header.textContent.trim().match(pattern);
				if (match) {
					hasContent = true;
					const titleText = header.textContent.trim();
					const baseAnchor = titleText.replace(/[^a-zA-Z0-9\s]/g, '')  // 修正正则表达式
					.replace(/\s+/g, '_');  // 修正空格替换
					let anchor = `#${baseAnchor}`;
					let suffix = 2;
					while (anchorMap.has(anchor)) {
						anchor = `#${baseAnchor}_${suffix}`;  // 修改分隔符
						suffix++;
					}
					anchorMap.set(anchor, true);

					const li = document.createElement("li");
					const a = document.createElement("a");
					a.textContent = titleText;
					a.href = anchor;
					header.id = anchor.substring(1);

					const level = parseInt(header.tagName.substring(1), 10);
					li.classList.add(`level-${level}`);
					li.appendChild(a);
					ul.appendChild(li);
				}
			});

			if (hasContent) {
				tocHeader.style.display = "block";
				fragment.appendChild(ul);
				tocContent.appendChild(fragment);
				updateTocVisibility();  // 确保更新目录可见性
			} else {
				tocHeader.style.display = "none";
			}

			// TOC 点击处理
			tocContent.addEventListener("click", function(e) {
				if (e.target.tagName.toLowerCase() === "a") {
					e.preventDefault();
					const targetId = e.target.getAttribute("href").substring(1);
					const targetElement = document.getElementById(targetId);
					if (targetElement) {
						targetElement.scrollIntoView({ behavior: "smooth" });
					}
					setTimeout(function() {
						navContainer.style.display = "none";
						document.body.classList.remove("nav-open");
					}, 50);
				}
			});
		} else {
			tocHeader.style.display = "none";
		}
	});

	// 页面加载完成后的初始化
	window.onload = function() {
		const buttonsShown = localStorage.getItem('buttonsShown') !== 'false';
		toggleButtons(buttonsShown);

		// 侧边栏单选项初始化
		let sidebarSetting = 'right';
		if (localStorage.getItem('sidebarAlwaysHidden') === 'true' || window.innerWidth <= 768) {
			sidebarSetting = 'hide';
		} else if (window.innerWidth > 768 && localStorage.getItem('sidebarPosition') === 'left') {
			sidebarSetting = 'left';
		}
		if (window.innerWidth > 768) {
			setSidebarPosition(sidebarSetting);
		}

		// 绑定侧边栏单选事件
		document.getElementById('sidebarRightRadio').addEventListener('change', function() {
			if (this.checked) setSidebarPosition('right');
		});
		document.getElementById('sidebarLeftRadio').addEventListener('change', function() {
			if (this.checked) setSidebarPosition('left');
		});
		document.getElementById('sidebarHideRadio').addEventListener('change', function() {
			if (this.checked) setSidebarPosition('hide');
		});

		// 页头页尾控制初始化
		const isHeaderFooterHidden = localStorage.getItem('headerFooterAlwaysHidden') === 'true';
		const headerFooterToggle = document.getElementById('headerFooterToggle');
		headerFooterToggle.checked = isHeaderFooterHidden;
		if (isHeaderFooterHidden) hideHeaderFooter();
		headerFooterToggle.addEventListener('change', handleHeaderFooterToggle);

		// 临时隐藏按钮控制初始化
		const tempHideButtons = document.getElementById('tempHideButtons');
		tempHideButtons.addEventListener('change', handleTempHideButtons);

		// 笔记和字体控制初始化
		initFontSizeControls();

		// 复制本页面正文按钮事件绑定
		document.getElementById('copyContentBtn').addEventListener('click', function() {
			let content = '';
			// 获取正文
			const article = document.querySelector('article') || document.querySelector('.entry-content') || document.querySelector('.post-content');
			if (article) {
				content = article.innerText;
			} else {
				content = document.body.innerText;
			}

			// 检查复选框是否选中
			const addPrompt = document.getElementById('addPromptCheckbox').checked;
			if (addPrompt) {
				const before = '请你认真阅读学习以下内容，然后回答问题：\n```\n';
				const after = '\n```\n';
				content = before + content + after;
			}

			if (navigator.clipboard) {
				navigator.clipboard.writeText(content).then(function() {
					const tip = document.getElementById('copySuccessTip');
					if (tip) {
						tip.style.display = 'inline';
						setTimeout(() => { tip.style.display = 'none'; }, 1500);
					}
				});
			}
		});

		// 移动端禁用侧边栏单选群
		function setSidebarRadioGroupEnabled(enabled) {
			const radioGroup = document.getElementById('sidebarRadioGroup');
			const radios = radioGroup.querySelectorAll('input[type="radio"]');
			if (enabled) {
				radioGroup.classList.remove('disabled');
				radios.forEach(radio => radio.disabled = false);
			} else {
				radioGroup.classList.add('disabled');
				radios.forEach(radio => radio.disabled = true);
			}
		}
		if (window.innerWidth <= 768) {
			setSidebarRadioGroupEnabled(false);
		} else {
			setSidebarRadioGroupEnabled(true);
		}

		// 检查是否需要保持 Nav 框显示
		if (localStorage.getItem('keepNavOpen') === 'true') {
			const nav = document.getElementById('navContainer');
			const maximizeButton = document.querySelector('.maximize-button');

			nav.style.display = 'block';
			document.body.classList.add('nav-open');

			// 移动端处理
			if (window.innerWidth <= 568 || localStorage.getItem('isNavMaximized') === 'true') {
				isNavMaximized = true;
				nav.classList.add('maximized');
				document.getElementById('maximizeButton').classList.add('maximized');
				nav.style.width = '100%';
				nav.style.height = '100%';
				nav.style.maxWidth = 'none';
				nav.style.borderRadius = '0';

				// 移动端隐藏最大化按钮
				if (window.innerWidth <= 568 && maximizeButton) {
					maximizeButton.style.display = 'none';
				}
			}

			// 重置标志
			localStorage.removeItem('keepNavOpen');

			// 切换到工具页面
			switchNavContent('chat');
		} else {
			const lastVisitedPage = localStorage.getItem('lastVisitedNavPage') || 'nav';
			switchNavContent(lastVisitedPage);
		}

		updateTocVisibility();

		// 响应式布局
		const debouncedResize = debounce(function() {
			const contentArea = document.querySelector('.content-area');
			const mainContent = document.querySelector('.site-main');
			const maximizeButton = document.querySelector('.maximize-button');
			let sidebarSetting = 'right';
			if (localStorage.getItem('sidebarAlwaysHidden') === 'true') {
				sidebarSetting = 'hide';
			} else if (localStorage.getItem('sidebarPosition') === 'left') {
				sidebarSetting = 'left';
			}

			if (window.innerWidth <= 768) {
				contentArea.style.width = 'calc(100%)';
				mainContent.style.width = 'calc(100%)';
				hideSidebar();
				document.body.classList.remove('sidebar-left');
				setSidebarRadioGroupEnabled(false);

				// 移动端下隐藏最大化按钮
				if (window.innerWidth <= 568 && maximizeButton) {
					maximizeButton.style.display = 'none';
				}
			} else {
				setSidebarRadioGroupEnabled(true);
				if (sidebarSetting === 'hide') {
					hideSidebar();
				} else if (sidebarSetting === 'left') {
					showSidebar();
					toggleSidebarPosition(true);
				} else {
					showSidebar();
					toggleSidebarPosition(false);
				}

				// 非移动端显示最大化按钮
				if (maximizeButton) {
					maximizeButton.style.display = 'block';
				}
			}
			updateTocVisibility();
		}, 15);

		window.addEventListener('resize', debouncedResize);

		// 浏览按钮事件绑定
		['goBackButton', 'goForwardButton', 'refreshButton', 'toggleButtonsButton', 'scrollToTopButton'].forEach(id => {
			const button = document.getElementById(id);
			if (button) {
				button.addEventListener('click', {
					goBackButton: () => window.history.back(),
					goForwardButton: () => window.history.forward(),
					refreshButton: () => window.location.reload(),
					toggleButtonsButton: () => toggleButtons(localStorage.getItem('buttonsShown') !== 'true'),
					scrollToTopButton: scrollToTop
				}[id]);
			}
		});
	};

	// 导出需要的全局函数
	window.switchNavContent = switchNavContent;
</script>