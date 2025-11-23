<!-- Ultimate Buttons by Akira37 (Hyperplasma)
 外部组件：HyNav应用导航页面、Better Messages聊天窗口……
 外部样式：`hyplus-unselectable` (Hyplus Overall); `hyplus-nav-section` `hyplus-nav-links` `hyplus-nav-group` `hyplus-nav-link` (HyNav)
 Code Type: universal
-->

<!-- HyButton按钮群 -->
<div id="scrollToTopButton" title="返回顶部（⌘↑）">⇧</div>
<div id="navButton" onclick="navOnClickFunc()" title="HyNav面板（⌥S）">✬</div>
<div id="goBackButton" title="返回上一页（⌘←）">❮</div>
<div id="goForwardButton" title="前往下一页（⌘→）">❯</div>
<div id="refreshButton" title="刷新页面（⌘R）">↻</div>

<!-- 导航弹出框 -->
<div id="navContainer">
	<!-- 标题栏按钮 -->
	<div id="backToTools" class="control-button back-button" title="返回工具选择" onclick="showToolSelector()" style="display: none; left: 10px; top: 10px;"></div>
	<div id="maximizeButton" class="control-button maximize-button" title="缩放（⌥M）" onclick="toggleMaximize(event)" style="right: 30px; top: 10px;"></div>
	<div id="closeButton" class="control-button close-button" title="关闭（⌥S）" onclick="closeNavContainer(event)" style="right: 10px; top: 10px;"></div>

	<!-- 切换按钮群 -->
	<div id="navSwitchButtons" class="nav-switch-buttons hyplus-unselectable">
		<button id="chatPageButton" class="switch-button" onclick="switchNavContent('chat')">服务</button>
		<button id="aiPageButton" class="switch-button" onclick="switchNavContent('ai')">KINA</button>
		<button id="navPageButton" class="switch-button active" onclick="switchNavContent('nav')">导航</button>
		<button id="notePageButton" class="switch-button" onclick="switchNavContent('note')">检索</button>
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

	<!-- 检索页面（原“目录”页面） -->
	<div id="noteContent" class="nav-content hyplus-unselectable" style="display: none;">
		<div id="searchHeader" style="font-size: 24px; font-weight: bold; text-align: center; margin: 10px 0;">Hyplus检索&amp;目录</div>
		<!-- 搜索栏 -->
		<div id="hyplusSearchBar" style="display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 8px; margin-top: 18px; margin-bottom: 15px; max-width: 600px; margin-left: auto; margin-right: auto;">
			<div style="display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%;">
				<input id="searchInput" type="text" placeholder="Hyplus Search Plus..." style="flex: 1; min-width: 180px; max-width: 400px; padding: 4px 12px; border-radius: 6px; border: 1.5px solid #c4e0f7; background: #fff; color: #175082; font-size: 16px; font-weight: 500; outline: none;" />
				<button id="searchBtn" class="hyplus-search-btn" title="搜索">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="7" stroke="#155a99" stroke-width="2"/><line x1="14.2929" y1="14.7071" x2="18" y2="18.4142" stroke="#155a99" stroke-width="2" stroke-linecap="round"/></svg>
				</button>
			</div>
			<div id="searchEngineOptions" style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
				<label style="display: flex; align-items: center; gap: 4px; font-size: 13px; color: #175082; cursor: pointer;">
					<input type="radio" name="searchEngine" value="hyplus" checked style="cursor: pointer;" />
					<span>Hyplus</span>
				</label>
				<label style="display: flex; align-items: center; gap: 4px; font-size: 13px; color: #175082; cursor: pointer;">
					<input type="radio" name="searchEngine" value="baidu" style="cursor: pointer;" />
					<span>Baidu</span>
				</label>
				<label style="display: flex; align-items: center; gap: 4px; font-size: 13px; color: #175082; cursor: pointer;">
					<input type="radio" name="searchEngine" value="google" style="cursor: pointer;" />
					<span>Google</span>
				</label>
				<label style="display: flex; align-items: center; gap: 4px; font-size: 13px; color: #175082; cursor: pointer;">
					<input type="radio" name="searchEngine" value="bing" style="cursor: pointer;" />
					<span>Bing</span>
				</label>
			</div>
		</div>
		<div class="directory-toc-content hyplus-unselectable" id="tocSection">
			<?php echo do_shortcode('[toc mode="ub"]'); ?>
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
					<div class="tool-desc" style="color: red;">登录后可用</div>
				</div>
				<?php endif; ?>
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
				<div class="tool-card" onclick="switchTool('hypreview')">
					<div class="tool-icon">📷</div>
					<div class="tool-name">图片预览</div>
					<div class="tool-desc">媒体文件在线演示</div>
				</div>
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
				case 'calculator':
					echo do_shortcode('[wpcode id="12585"]');
					break;
				case 'encoder':
					echo do_shortcode('[wpcode id="12640"]');
					break;
				case 'hypreview':
					echo do_shortcode('[wpcode id="14220"]');
					break;
			}
			?>
		</div>
	</div>

	<!-- 设置 -->
	<div id="settingsContent" class="nav-content hyplus-unselectable" style="display: none;">
		<div id="settingsHeader" style="font-size: 24px; font-weight: bold; text-align: center; margin: 10px 0;">Hyplus设置&amp;快捷键大全</div>

		<!-- 设置内容两列布局 -->
		<div class="settings-columns-container">
			<!-- 左列：复选框设置 -->
			<div class="settings-column">
				<div class="language-selector" id="sidebarRadioGroup">
					<div class="config-item">
						<input type="radio" id="sidebarRightRadio" name="sidebarPosition" value="right">
						<label for="sidebarRightRadio">侧边栏位于右侧</label>
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

				<div class="language-selector" id="navButtonsRadioGroup" style="margin-top: 12px;">
					<div class="config-item">
						<input type="radio" id="navButtonsLeftRadio" name="navButtonsPosition" value="left">
						<label for="navButtonsLeftRadio">导航按钮群位于左侧</label>
					</div>
					<div class="config-item">
						<input type="radio" id="navButtonsRightRadio" name="navButtonsPosition" value="right">
						<label for="navButtonsRightRadio">导航按钮群位于右侧</label>
					</div>
				</div>

				<div class="language-selector" style="margin-top: 12px;">
					<div class="config-item">
						<input type="checkbox" id="headerFooterToggle" />
						<label for="headerFooterToggle">隐藏页眉页脚&nbsp;<span class="shortcut-key">⌥⇧H</span></label>
					</div>
					<div class="config-item">
						<input type="checkbox" id="hideButtonsToggle" />
						<label for="hideButtonsToggle">临时隐藏HyButton按钮群&nbsp;<span class="shortcut-key">⌥⇧Y</span></label>
					</div>

				</div>
			</div>

			<!-- 右列 -->
			<div class="settings-column">
				<!-- 字体选择区 -->
				<div class="language-selector">
					<div class="language-selector-row">
						<span class="language-label">字体选择:</span>
						<select id="fontSelect" class="font-select">
							<option value="default">默认字体</option>
							<option value="times">Times New Roman</option>
							<option value="bilibili">HarmonyOS Sans</option>
							<option value="monaco">Monaco</option>
							<option value="cursive">Ma Shan Zheng</option>
						</select>
					</div>
				</div>

				<!-- 字体缩放控制区 -->
				<div class="language-selector" style="margin-top: 12px;">
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
						<label class="language-label">全文翻译:</label>
						<label id="languageLabel" class="language-label">
							<!-- php
							echo do_shortcode('[gtranslate]'); // Need GTranslate Plugin
		-->Currently unavailable
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
				通过<a href="https://www.hyperplasma.top/?p=13242">Hyplusite Exporter</a>导出的页面不支持在线服务，且部分由PHP预生成的JS组件存在显示问题
			</div>
		</div>
		<div id="configMessage" class="hyplus-unselectable" style="color: #d6d6d6; font-size: 16px; font-style: italic; text-align: center; margin: 24px 0;">
			Ultimate Buttons v1.4.1 by Akira37
		</div>
	</div>
</div>

<style>
/* Hyplus 检索栏搜索按钮美化与交互动画 */
.hyplus-search-btn {
	background: #eaf6ff;
	border: 1.5px solid #8ecafc;
	border-radius: 6px;
	padding: 4px 12px;
	cursor: pointer;
	color: #155a99;
	font-size: 18px;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: background 0.18s, color 0.18s, border-color 0.18s, box-shadow 0.18s, transform 0.12s;
	box-shadow: 0 2.5px 10px 0 rgba(33, 118, 193, 0.07);
	outline: none;
}
.hyplus-search-btn:hover, .hyplus-search-btn:focus {
	background: #e0f0ff;
	color: #0d4a7a;
	border-color: #43a5f5;
	box-shadow: 0 4px 14px 0 rgba(33, 118, 193, 0.13);
	transform: translateY(-2px) scale(1.04);
}
.hyplus-search-btn:active {
	background: #dbeaf5;
	color: #155a99;
	border-color: #2e8ad6;
	box-shadow: 0 1px 4px 0 rgba(33, 118, 193, 0.10);
	transform: translateY(1px) scale(0.97);
}
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
    #navButton,
    #goBackButton,
    #goForwardButton,
    #refreshButton {
        position: fixed;
        width: 30px;
        height: 50px;
        color: white;
        text-align: center;
        line-height: 50px;
        cursor: pointer;
        z-index: 119;
        font-family: Arial, sans-serif;
        font-weight: bold;
        box-shadow: 0 2px 6px 1px rgba(102, 139, 139, 0.45);
        transition: transform 0.2s ease, background-color 0.5s ease;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: none;
        margin: 0;
    }

    /* 右侧布局（默认） */
    body:not(.nav-buttons-left) #scrollToTopButton,
    body:not(.nav-buttons-left) #navButton,
    body:not(.nav-buttons-left) #goBackButton,
    body:not(.nav-buttons-left) #goForwardButton,
    body:not(.nav-buttons-left) #refreshButton {
        right: 0;
    }

    /* 左侧布局 */
    body.nav-buttons-left #scrollToTopButton,
    body.nav-buttons-left #navButton,
    body.nav-buttons-left #goBackButton,
    body.nav-buttons-left #goForwardButton,
    body.nav-buttons-left #refreshButton {
        left: 0;
    }

    #scrollToTopButton {
        font-size: 18px;
        bottom: 300px;
        background-color: rgb(33, 182, 137);
    }

    #navButton {
        font-size: 20px;
        bottom: 250px;
        background-color: rgba(220, 38, 127, 1);
    }

    #goBackButton {
        font-size: 18px;
        bottom: 200px;
        background-color: rgba(84, 126, 239, 1);
    }

    #goForwardButton {
        font-size: 18px;
        bottom: 150px;
        background-color: rgba(84, 126, 239, 1);
    }

    #refreshButton {
        font-size: 20px;
        bottom: 100px;
        background-color: rgba(40, 167, 69, 1);
    }

    /* 右侧布局圆角（默认） */
    body:not(.nav-buttons-left) #scrollToTopButton {
        border-radius: 8px 0 0 0;
    }
    body:not(.nav-buttons-left) #navButton,
    body:not(.nav-buttons-left) #goBackButton,
    body:not(.nav-buttons-left) #goForwardButton {
        border-radius: 0;
    }
    body:not(.nav-buttons-left) #refreshButton {
        border-radius: 0 0 0 8px;
    }

    /* 左侧布局圆角 */
    body.nav-buttons-left #scrollToTopButton {
        border-radius: 0 8px 0 0;
    }
    body.nav-buttons-left #navButton,
    body.nav-buttons-left #goBackButton,
    body.nav-buttons-left #goForwardButton {
        border-radius: 0;
    }
    body.nav-buttons-left #refreshButton {
        border-radius: 0 0 8px 0;
    }

    /* 鼠标悬停效果 */
    #scrollToTopButton:hover {
        transform: scale(1.05);
        background-color: rgba(52, 211, 161, 1);
    }

    #navButton:hover {
        transform: scale(1.05);
        background-color: rgba(240, 58, 147, 1);
    }

    #goBackButton:hover,
    #goForwardButton:hover {
        transform: scale(1.05);
        background-color: rgba(94, 136, 249, 1);
    }

    #refreshButton:hover {
        transform: scale(1.05);
        background-color: rgba(60, 187, 89, 1);
    }

    /* 按钮组悬浮时禁用滚轮 */
    #scrollToTopButton:hover,
    #navButton:hover,
    #goBackButton:hover,
    #goForwardButton:hover,
    #refreshButton:hover {
        pointer-events: auto;
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
        font-size: 24px;
        font-weight: bold;
        margin: 15px 0;
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


	/* 目录内容样式 */
	.directory-toc-content {
		font-size: 1.25em;
		min-width: 220px;
		max-width: 600px;
		margin: 0 auto;
		text-align: left;
		word-break: break-all;
		display: flex;
		flex-direction: column;
		align-items: center;
	}

    /* 目录内容样式（兼容旧实现，推荐使用hyplus-toc样式） */
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

    /* Hyplus TOC 通用样式 */
    /* .hyplus-toc-container {
        margin: 0 0 18px 0;
    } */
    .hyplus-toc-header {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 8px;
    }
    .hyplus-toc-content ul {
        list-style-type: none;
        padding-left: 0;
        margin: 10px 0;
    }
    .hyplus-toc-content ul li {
        margin-bottom: 10px;
    }
    .hyplus-toc-content ul li a {
        text-decoration: none;
        color: #0073aa;
        transition: color 0.2s ease;
    }
    .hyplus-toc-content ul li a:hover {
        color: red;
    }
    .hyplus-toc-content ul li.level-1 { margin-left: 0px; }
    .hyplus-toc-content ul li.level-2 { margin-left: 15px; }
    .hyplus-toc-content ul li.level-3 { margin-left: 30px; }
    .hyplus-toc-content ul li.level-4 { margin-left: 45px; }
    .hyplus-toc-content ul li.level-5 { margin-left: 60px; }
    .hyplus-toc-content ul li.level-6 { margin-left: 75px; }

    /* post模式专用样式 */
	.hyplus-toc-container[data-toc-mode="post"] {
		background: #fff;
		border: 1.5px solid #b6dded;
		border-radius: 12px;
		padding: 16px 22px 12px 22px;
		display: inline-block;
		max-width: 100%;
		margin: 0 0 18px 0;
		box-sizing: border-box;
		vertical-align: top;
	}
	.hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-header {
		text-align: center;
		margin-bottom: 10px;
		font-weight: bold;
		color: #333;
		padding: 0;
	}
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-2 { margin-left: 12px; }
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-3 { margin-left: 24px; }
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-4 { margin-left: 36px; }
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-5 { margin-left: 48px; }
    .hyplus-toc-container[data-toc-mode="post"] .hyplus-toc-content ul li.level-6 { margin-left: 60px; }

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
        /* border: 1px solid #b6dded; */
        border-radius: 4px;
    }

    .shortcuts-column {
        flex: 1;
        min-width: 250px;
    }

    /* .shortcuts-column:first-child {
        border-right: 1px solid #b6dded;
    } */

    .shortcut-item {
        padding: 8px;
        /* border-bottom: 1px solid #b6dded;	*/
        border: 1px solid #b6dded;
        display: flex;
        justify-content: space-between;
    }

    .shortcut-key {
        color: gray;
    }

    /* 字体控制样式 */
    .font-select {
        max-width: 180px;
        padding: 4px 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #fff;
        color: #333;
        font-size: 14px !important;
        flex: 1;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 16px;
        padding-right: 32px;
    }

    .font-select:focus {
        outline: none;
        border-color: #b6dded;
        box-shadow: 0 0 0 2px rgba(182, 221, 237, 0.25);
    }

    .font-select:hover {
        border-color: #b6dded;
    }

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
		.directory-toc-center {
			margin-top: 8px;
			margin-bottom: 16px;
		}
		.directory-toc-content {
			max-width: 98vw;
			font-size: 1.05em;
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

	/* Responsive tweaks for tools and settings: collapse to single column on small screens */
	@media screen and (max-width: 768px) {
		.tools-grid {
			grid-template-columns: 1fr !important;
			gap: 12px;
			padding: 12px;
		}

		.tool-card {
			padding: 14px;
		}

		.settings-columns-container {
			flex-direction: column;
			gap: 12px;
			padding: 0 12px;
			align-items: stretch;
		}

		.settings-column {
			width: 100%;
		}

		.language-selector {
			padding: 10px;
		}
	}
</style>

<script>
	// 全局变量声明
	let chatBtn = null;
	let chatContent = null;
	let navContainer = null;
	let isNavMaximized = localStorage.getItem('isNavMaximized') === 'true';
	let defaultBodyFont = window.getComputedStyle(document.body).fontFamily;

	// 字体控制函数
	function setFontFamily(font) {
		switch (font) {
			case 'default':
				document.body.style.fontFamily = defaultBodyFont;
				document.body.style.fontWeight = "400";
				break;
			case 'times':
				document.body.style.fontFamily = "'Times New Roman', Times, serif";
				document.body.style.fontWeight = "500";
				break;
			case 'bilibili':
				document.body.style.fontFamily = "'HarmonyOS Sans', 'HarmonyOS Sans SC', 'Source Han Sans CN', sans-serif";
				document.body.style.fontWeight = "400";
				break;
			case 'monaco':
				document.body.style.fontFamily = "Monaco, Consolas, 'Courier New', monospace";
				document.body.style.fontWeight = "400";
				break;
			case 'cursive':
				document.body.style.fontFamily = "'Ma Shan Zheng', 'Xingkai SC', 'Kaiti SC', 'STKaiti', 'Segoe Script', 'Bradley Hand', cursive, sans-serif";
				document.body.style.fontWeight = "400";
				break;
			default:
				document.body.style.fontFamily = defaultBodyFont;
				document.body.style.fontWeight = "400";
		}
		localStorage.setItem('selectedFont', font);
	}

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

	// 隐藏/显示Hyplus按钮群
	function hideHyplusButtons() {
		const buttons = [
			document.getElementById('scrollToTopButton'),
			document.getElementById('navButton'),
			document.getElementById('goBackButton'),
			document.getElementById('goForwardButton'),
			document.getElementById('refreshButton')
		];
		buttons.forEach(btn => {
			if (btn) btn.style.display = 'none';
		});
	}

	function showHyplusButtons() {
		const buttons = [
			document.getElementById('scrollToTopButton'),
			document.getElementById('navButton'),
			document.getElementById('goBackButton'),
			document.getElementById('goForwardButton'),
			document.getElementById('refreshButton')
		];
		buttons.forEach(btn => {
			if (btn) btn.style.display = 'block';
		});
	}

	function handleHideButtonsToggle(event) {
		if (event.target.checked) {
			hideHyplusButtons();
		} else {
			showHyplusButtons();
		}
	}

	// 导航框控制
	function updateTocVisibility() {
		const tocSection = document.getElementById('tocSection');
		const tocContent = tocSection ? tocSection.querySelector('.hyplus-toc-content') : null;
		const noteSection = document.querySelector('.note-section');
		if (!tocContent || !tocContent.hasChildNodes()) {
			if (tocSection) tocSection.style.display = 'none';
			if (noteSection) noteSection.style.width = '100%';
		} else {
			if (tocSection) tocSection.style.display = '';
			if (noteSection) noteSection.style.width = '';
		}
	}

	function toggleMaximize(event) {
		event.stopPropagation();
		const navContainer = document.getElementById('navContainer');
		// 缓存最大化、关闭、返回工具按钮为全局变量
		if (!window._hyplusNavBtnCache) {
			window._hyplusNavBtnCache = {
				maximizeButton: document.getElementById('maximizeButton'),
				backToTools: document.getElementById('backToTools'),
				closeButton: document.getElementById('closeButton')
			};
		}
		const maximizeButton = window._hyplusNavBtnCache.maximizeButton;
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
			ai: document.getElementById('aiContent'),
			settings: document.getElementById('settingsContent'),
			note: document.getElementById('noteContent')
		};
		const buttons = {
			nav: document.getElementById('navPageButton'),
			chat: document.getElementById('chatPageButton'),
			ai: document.getElementById('aiPageButton'),
			settings: document.getElementById('settingsPageButton'),
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

		// Nav框显示/隐藏 (Alt+S) - only when Shift is NOT pressed so Alt+Shift combos can be handled separately
		if (event.altKey && !event.shiftKey && (event.key === 's' || event.key === 'ß')) {
			event.preventDefault();
			navOnClickFunc();
			event.stopPropagation();
		}

		// 最大化切换 (Alt+M) - avoid intercepting Alt+Shift+M
		if (event.altKey && !event.shiftKey && (event.key === 'm' || event.key === 'µ')) {
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
				const pages = ['chat', 'ai', 'nav', 'note', 'settings'];
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

		// 字体大小控制 (Alt + key) - only when Shift is NOT pressed to leave Alt+Shift combos for other handlers
		if (event.altKey && !event.shiftKey) {
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

					// Alt+Shift 组合键（轮询侧边栏位置和切换页眉页脚）
					if (event.altKey && event.shiftKey) {
						// normalize key to lower-case letter when applicable
						const k = (event.key || '').toString();
						const keyLower = k.length === 1 ? k.toLowerCase() : k;

						switch (keyLower) {
							case 's':
								// 在移动端不切换
								if (window.innerWidth <= 768) {
									event.preventDefault();
									event.stopPropagation();
									return;
								}
								event.preventDefault();
								event.stopPropagation();
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

							case 'h':
								event.preventDefault();
								event.stopPropagation();
								const headerFooterToggle = document.getElementById('headerFooterToggle');
								if (headerFooterToggle) {
									headerFooterToggle.checked = !headerFooterToggle.checked;
									handleHeaderFooterToggle({ target: headerFooterToggle });
								}
								break;

							case 'y':
								event.preventDefault();
								event.stopPropagation();
								const hideButtonsToggle = document.getElementById('hideButtonsToggle');
								if (hideButtonsToggle) {
									hideButtonsToggle.checked = !hideButtonsToggle.checked;
									handleHideButtonsToggle({ target: hideButtonsToggle });
								}
								break;
						}
					}
	});

	/**
	 * Alt + Click to Open Links in New Tab
	 */
	document.addEventListener('click', function (event) {
		// 只响应鼠标左键点击
		if (event.button !== 0) return;

		// 检查是否按下了 Alt/Option 键
		if (!event.altKey) return;

		// 查找可能的跳转元素（a标签或带有 data-href 属性等）
		let el = event.target;
		while (el && el !== document.body) {
			if (
				(el.tagName === 'A' && el.href) ||
				el.hasAttribute('data-href') ||
				el.onclick
			) {
				break;
			}
			el = el.parentElement;
		}
		if (!el || el === document.body) return;

		// 获取目标链接
		let url = '';
		if (el.tagName === 'A' && el.href) {
			url = el.href;
		} else if (el.hasAttribute('data-href')) {
			url = el.getAttribute('data-href');
		}

		// 如未获取到URL，则不处理
		if (!url) return;

		// 阻止默认跳转
		event.preventDefault();
		// 新标签页打开
		window.open(url, '_blank');
	}, true);


	/**
	 * Random post function for PAT category page
	 */
	function goToRandomPost(type, min, max) {
		var num = Math.floor(Math.random() * (max - min + 1)) + min;
		var url = `https://www.hyperplasma.top/${type}-${num}`;
		window.location.href = url;
	}

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

	// HyButton按钮群滚轮禁用性能优化（单一监听器+状态标志，风险极低）
	let isScrollDisabled = false;
	document.addEventListener('wheel', function(e) {
		if (isScrollDisabled) e.preventDefault();
	}, { passive: false });
	document.addEventListener('DOMContentLoaded', function() {
		// 缓存字体选择和缩放相关控件为全局变量
		if (!window._hyplusFontCache) {
			window._hyplusFontCache = {
				fontSelect: document.getElementById('fontSelect'),
				decreaseFontBtn: document.getElementById('decreaseFontBtn'),
				increaseFontBtn: document.getElementById('increaseFontBtn'),
				resetFontBtn: document.getElementById('resetFontBtn'),
				fontSizeDisplay: document.getElementById('fontSizeDisplay')
			};
		}
		// 初始化字体选择
		const fontSelect = window._hyplusFontCache.fontSelect;
		if (fontSelect) {
			const savedFont = localStorage.getItem('selectedFont') || 'default';
			fontSelect.value = savedFont;
			setFontFamily(savedFont);
			fontSelect.addEventListener('change', function() {
				setFontFamily(this.value);
			});
		}
		// 笔记和字体控制初始化
		initFontSizeControls();
		// 缓存HyButton按钮群五个按钮为全局变量
		if (!window._hyplusBtnCache) {
			window._hyplusBtnCache = {
				scrollToTopButton: document.getElementById('scrollToTopButton'),
				navButton: document.getElementById('navButton'),
				goBackButton: document.getElementById('goBackButton'),
				goForwardButton: document.getElementById('goForwardButton'),
				refreshButton: document.getElementById('refreshButton')
			};
		}
		const hyButtons = [
			window._hyplusBtnCache.scrollToTopButton,
			window._hyplusBtnCache.navButton,
			window._hyplusBtnCache.goBackButton,
			window._hyplusBtnCache.goForwardButton,
			window._hyplusBtnCache.refreshButton
		];
		hyButtons.forEach(button => {
			if (button) {
				button.addEventListener('mouseenter', function() {
					isScrollDisabled = true;
				});
				button.addEventListener('mouseleave', function() {
					isScrollDisabled = false;
				});
			}
		});

		// 搜索引擎配置
		const searchEngines = {
			hyplus: {
				name: 'Hyplus',
				url: 'https://www.hyperplasma.top/?s={q}'
			},
			baidu: {
				name: 'Baidu',
				url: 'https://www.baidu.com/s?wd={q}'
			},
			bing: {
				name: 'Bing',
				url: 'https://www.bing.com/search?q={q}'
			},
			google: {
				name: 'Google',
				url: 'https://www.google.com/search?q={q}'
			}
		};

		// 缓存搜索栏相关控件为全局变量
		if (!window._hyplusSearchCache) {
			window._hyplusSearchCache = {
				searchInput: document.getElementById('searchInput'),
				searchBtn: document.getElementById('searchBtn'),
				searchEngineRadios: document.querySelectorAll('input[name="searchEngine"]')
			};
		}
		const searchInput = window._hyplusSearchCache.searchInput;
		const searchBtn = window._hyplusSearchCache.searchBtn;
		const searchEngineRadios = window._hyplusSearchCache.searchEngineRadios;

		// 读取cookie
		function getCookie(name) {
			const value = `; ${document.cookie}`;
			const parts = value.split(`; ${name}=`);
			if (parts.length === 2) return parts.pop().split(';').shift();
		}
		// 设置cookie
		function setCookie(name, value, days) {
			let expires = '';
			if (days) {
				const date = new Date();
				date.setTime(date.getTime() + (days*24*60*60*1000));
				expires = "; expires=" + date.toUTCString();
			}
			document.cookie = name + "=" + value + expires + "; path=/";
		}

		// 初始化搜索引擎选择
		const savedEngine = getCookie('hyplus_search_engine');
		if (savedEngine && searchEngines[savedEngine]) {
			document.querySelector(`input[name="searchEngine"][value="${savedEngine}"]`).checked = true;
		}

		searchEngineRadios.forEach(radio => {
			radio.addEventListener('change', function() {
				if (this.checked) {
					setCookie('hyplus_search_engine', this.value, 365);
				}
			});
		});

		// 搜索执行
		function doSearch() {
			const engine = document.querySelector('input[name="searchEngine"]:checked').value;
			const query = encodeURIComponent(searchInput.value.trim());
			if (!query) {
				searchInput.focus();
				return;
			}
			const url = searchEngines[engine].url.replace('{q}', query);
			window.open(url, '_blank');
		}
		searchBtn.addEventListener('click', doSearch);
		searchInput.addEventListener('keydown', function(e) {
			if (e.key === 'Enter') doSearch();
		});
	});

	// 页面加载完成后的初始化
	window.onload = function() {
		// 首先切换到上次访问的页面（确保切换功能正常工作）
		const lastVisitedPage = localStorage.getItem('lastVisitedNavPage') || 'nav';
		switchNavContent(lastVisitedPage);

		// 初始化字体选择
		const fontSelect = document.getElementById('fontSelect');
		if (fontSelect) {
			const savedFont = localStorage.getItem('selectedFont') || 'default';
			fontSelect.value = savedFont;
			setFontFamily(savedFont);

			fontSelect.addEventListener('change', function() {
				setFontFamily(this.value);
			});
		}

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

		// 缓存侧边栏单选按钮为全局变量
		if (!window._sidebarRadioCache) {
			window._sidebarRadioCache = {
				right: document.getElementById('sidebarRightRadio'),
				left: document.getElementById('sidebarLeftRadio'),
				hide: document.getElementById('sidebarHideRadio')
			};
		}
		window._sidebarRadioCache.right && window._sidebarRadioCache.right.addEventListener('change', function() {
			if (this.checked) setSidebarPosition('right');
		});
		window._sidebarRadioCache.left && window._sidebarRadioCache.left.addEventListener('change', function() {
			if (this.checked) setSidebarPosition('left');
		});
		window._sidebarRadioCache.hide && window._sidebarRadioCache.hide.addEventListener('change', function() {
			if (this.checked) setSidebarPosition('hide');
		});

		// 设置导航按钮位置
		function setNavButtonsPosition(position) {
			if (position === 'left') {
				document.body.classList.add('nav-buttons-left');
				localStorage.setItem('navButtonsPosition', 'left');
			} else {
				document.body.classList.remove('nav-buttons-left');
				localStorage.setItem('navButtonsPosition', 'right');
			}
		}

		// 初始化导航按钮位置
		const savedNavButtonsPosition = localStorage.getItem('navButtonsPosition') || 'left';
		setNavButtonsPosition(savedNavButtonsPosition);
		document.getElementById('navButtonsLeftRadio').checked = (savedNavButtonsPosition === 'left');
		document.getElementById('navButtonsRightRadio').checked = (savedNavButtonsPosition === 'right');

		// 缓存导航按钮位置单选按钮为全局变量
		if (!window._navButtonsRadioCache) {
			window._navButtonsRadioCache = {
				left: document.getElementById('navButtonsLeftRadio'),
				right: document.getElementById('navButtonsRightRadio')
			};
		}
		window._navButtonsRadioCache.left && window._navButtonsRadioCache.left.addEventListener('change', function() {
			if (this.checked) setNavButtonsPosition('left');
		});
		window._navButtonsRadioCache.right && window._navButtonsRadioCache.right.addEventListener('change', function() {
			if (this.checked) setNavButtonsPosition('right');
		});

		// 缓存设置区复选框为全局变量
		if (!window._hyplusSettingsCheckboxCache) {
			window._hyplusSettingsCheckboxCache = {
				headerFooterToggle: document.getElementById('headerFooterToggle'),
				hideButtonsToggle: document.getElementById('hideButtonsToggle')
			};
		}
		const isHeaderFooterHidden = localStorage.getItem('headerFooterAlwaysHidden') === 'true';
		const headerFooterToggle = window._hyplusSettingsCheckboxCache.headerFooterToggle;
		if (headerFooterToggle) {
			headerFooterToggle.checked = isHeaderFooterHidden;
			if (isHeaderFooterHidden) hideHeaderFooter();
			headerFooterToggle.addEventListener('change', handleHeaderFooterToggle);
		}
		const hideButtonsToggle = window._hyplusSettingsCheckboxCache.hideButtonsToggle;
		if (hideButtonsToggle) {
			hideButtonsToggle.addEventListener('change', handleHideButtonsToggle);
		}

		// 笔记和字体控制初始化
		initFontSizeControls();

		// 缓存复制正文按钮和附加提问提示词复选框为全局变量
		if (!window._hyplusCopyCache) {
			window._hyplusCopyCache = {
				copyContentBtn: document.getElementById('copyContentBtn'),
				addPromptCheckbox: document.getElementById('addPromptCheckbox')
			};
		}
		const copyContentBtn = window._hyplusCopyCache.copyContentBtn;
		const addPromptCheckbox = window._hyplusCopyCache.addPromptCheckbox;
		if (copyContentBtn) {
			copyContentBtn.addEventListener('click', function() {
				let content = '';
				// 获取正文
				const article = document.querySelector('#main');
				if (article) {
					content = article.innerText;
				} else {
					content = document.body.innerText;
				}
				// 检查复选框是否选中
				const addPrompt = addPromptCheckbox && addPromptCheckbox.checked;
				if (addPrompt) {
					const before = '请你认真阅读学习以下内容，然后回答问题：\n```````````````````````````\n';
					const after = '\n```````````````````````````\n';
					content = before + content + after;
				}
				if (navigator.clipboard) {
					navigator.clipboard.writeText(content).then(function() {
						if (!window._hyplusCopyTipCache) {
							window._hyplusCopyTipCache = document.getElementById('copySuccessTip');
						}
						const tip = window._hyplusCopyTipCache;
						if (tip) {
							tip.style.display = 'inline';
							setTimeout(() => { tip.style.display = 'none'; }, 1500);
						}
					});
				}
			});
		}

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
		['goBackButton', 'goForwardButton', 'refreshButton', 'scrollToTopButton'].forEach(id => {
			const button = document.getElementById(id);
			if (button) {
				button.addEventListener('click', {
					goBackButton: () => window.history.back(),
					goForwardButton: () => window.history.forward(),
					refreshButton: () => window.location.reload(),
					scrollToTopButton: scrollToTop
				}[id]);
			}
		});
	};

	// 导出需要的全局函数
	window.switchNavContent = switchNavContent;
</script>