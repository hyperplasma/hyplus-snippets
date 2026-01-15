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
	<div id="backToTools" class="control-button back-button" title="进入Hyplus导航 Mk.II" onclick="window.location.href='https://www.hyperplasma.top/hyplus/';" style="left: 10px; top: 10px;"></div>
	<div id="maximizeButton" class="control-button maximize-button" title="缩放（⌥M）" onclick="toggleMaximize(event)" style="right: 30px; top: 10px;"></div>
	<div id="closeButton" class="control-button close-button" title="关闭（⌥S）" onclick="closeNavContainer(event)" style="right: 10px; top: 10px;"></div>

	<!-- 切换按钮群 -->
	<div id="navSwitchButtons" class="nav-switch-buttons hyplus-unselectable">
		<!-- [隐藏] <button id="chatPageButton" class="switch-button" onclick="switchNavContent('chat')">通讯</button> -->
		<!-- [隐藏] <button id="aiPageButton" class="switch-button" onclick="switchNavContent('ai')">KINA</button> -->
		<button id="navPageButton" class="switch-button active" onclick="switchNavContent('nav')">导航</button>
		<button id="notePageButton" class="switch-button" onclick="switchNavContent('note')">检索</button>
		<button id="settingsPageButton" class="switch-button" onclick="switchNavContent('settings')">设置</button>
	</div>

	<!-- 导航内容 -->
	<div id="navContent" class="nav-content">
		<!-- HyNav: Hyplus Nav Page, Ultimate Button popup addon (Desc is moved here for easier editing in Code Snippets)
		Description: currently only implemented in Ultimate Button, but the CSS is shared with other components.
		Code type: HTML (no need to compress the codes)
		Special Permissions: Direct Edit; No Header Desc; No Formatting
		Shortcode: [wpcode id="11647"] (auto-generated)
		-->
		<?php
		echo do_shortcode('[wpcode id="11647"]');
		?>
		<div id="navMessage" class="hyplus-unselectable" style="color: gray; font-style: italic; text-align: center; margin-top: 16px;">Explore your Hyplusite!</div>
	</div>

	<!-- [隐藏] AI内容
	<div id="aiContent" class="nav-content" style="display: none; padding: 0 15px;">
		<iframe id="kinaIframe" 
				src="about:blank" 
				style="width: 100%; height: 96%; border: 1px solid rgba(182,221,237, 0.85); border-radius: 12px;" 
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; camera; microphone; display-capture; fullscreen; clipboard-read; clipboard-write"
				referrerpolicy="origin"
				></iframe>
	</div>
	[隐藏] -->

	<!-- 检索页面（原“目录”页面） -->
	<div id="noteContent" class="nav-content hyplus-unselectable" style="display: none;">
		<div id="searchHeader" style="font-size: 24px; font-weight: bold; text-align: center; margin: 10px 0;">Hyplus检索&amp;目录</div>
		<!-- 搜索栏 -->
		<div id="hyplusSearchBar" style="display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 12px; margin-top: 10px; margin-bottom: 12px; max-width: 600px; margin-left: auto; margin-right: auto;">
			<div style="display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; position: relative;">
				<input id="searchInput" type="text" placeholder="Hyplus Search Plus..." class="hyplus-search-input" style="flex: 1; min-width: 180px; max-width: 100%; padding: 10px 18px; border-radius: 999px; border: 1.5px solid #c4e0f7; background: #fff; color: #175082; font-size: 18px; font-weight: 500; outline: none; box-shadow: 0 2px 6px rgba(0,0,0,0.03); pointer-events: auto;" />
				<button id="clearSearchBtn" type="button" style="display: none; position: absolute; right: 12px; background: none; border: none; color: #175082; font-size: 20px; cursor: pointer; padding: 0; width: 24px; height: 24px; line-height: 24px; text-align: center; font-weight: bold; opacity: 0.6; transition: opacity 0.2s ease; pointer-events: auto; z-index: 10;" title="清空搜索框">×</button>
			</div>
			<div id="searchEngineOptions" style="display: flex; margin-top:2px; gap: 12px; justify-content: center; flex-wrap: wrap; width: 100%;">
				<button type="button" class="sideinfo-toggle engine-btn" data-engine="hyplus">Hyplus</button>
				<button type="button" class="sideinfo-toggle engine-btn" data-engine="baidu">Baidu</button>
				<button type="button" class="sideinfo-toggle engine-btn" data-engine="google">Google</button>
				<button type="button" class="sideinfo-toggle engine-btn" data-engine="bing">Bing</button>
				<button type="button" class="sideinfo-toggle engine-btn" data-engine="scholar">谷歌学术</button>
				<button type="button" class="sideinfo-toggle engine-btn" data-engine="github">GitHub仓库</button>
			</div>
		</div>
		<div class="directory-toc-content hyplus-unselectable" id="tocSection">
			<?php echo do_shortcode('[toc mode="ub"]'); ?>
		</div>
	</div>

	<!-- [隐藏] 即时通讯页面
	<div id="chatContent" class="nav-content hyplus-unselectable" style="display: none;">
		</?php
		echo do_shortcode('[better_messages]');
		?/>
	</div>
	[隐藏] -->

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
						<label for="headerFooterToggle">隐藏页眉页脚</label>
					</div>
					<div class="config-item">
						<input type="checkbox" id="hideButtonsToggle" />
						<label for="hideButtonsToggle">临时隐藏HyButton按钮群</label>
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
		-->WORK IN PROGRESS
						</label>
					</div>
				</div>

			<?php
				if (is_single()) :
			?>
				<div class="language-selector" style="margin-top: 12px;">
					<div class="language-selector-row">
						<span class="language-label">复制本文内容:</span>
						<button id="copyContentBtn" class="font-size-btn">复制</button>
					</div>
					<div class="language-selector-row">
						<input type="checkbox" id="addPromptCheckbox" />
						<label for="addPromptCheckbox">
							<span class="language-label">附加问答提示词（适用于<a href="https://kina.hyperplasma.top" target="_blank">KINA</a>）</span>
						</label>
					</div>
				</div>
			<?php
				endif;
				if (current_user_can('administrator')) :
			?>
				<div class="language-selector" style="margin-top: 12px;">
					<div class="language-selector-row">
						<label class="language-label">编辑组件:</label>
						<label id="edit-hynav" class="language-label">
							<button class="font-size-btn"
								onclick="window.open('https://www.hyperplasma.top/wp-admin/admin.php?page=wpcode-snippet-manager&snippet_id=11647', '_blank');"
							>
								HyNav
							</button>
						</label>
						<label id="edit-nav-menu" class="language-label">
							<button class="font-size-btn"
								onclick="window.open('https://www.hyperplasma.top/wp-admin/nav-menus.php', '_blank');"
							>
								菜单
							</button>
						</label>
					</div>
				</div>
			<?php
				endif;
			?>
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
			Ultimate Buttons v1.5 by Akira37
		</div>
	</div>
</div>

<style>
/* 使用 sideinfo 的切换按钮样式作为搜索引擎按钮样式（复制自 sideinfo.php） */
.sideinfo-toggle {
	padding: 4px 12px;
	background: #ecf5f8;
	color: #175082;
	border-radius: 16px;
	border: 1.5px solid #c4e0f7;
	box-shadow: 0 2.5px 10px 0 rgba(33, 118, 193, 0.17), 0 1px 2px 0 rgba(33, 118, 193, 0.09);
	font-weight: 600;
	min-width: fit-content;
	transition: background 0.18s cubic-bezier(0.4,0,0.2,1), color 0.18s cubic-bezier(0.4,0,0.2,1), box-shadow 0.18s cubic-bezier(0.4,0,0.2,1), transform 0.18s cubic-bezier(0.4,0,0.2,1);
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

.sideinfo-toggle:hover,
.sideinfo-toggle:focus {
	background: #eaf6ff;
	color: #155a99;
	border-color: #8ecafc;
	box-shadow: 0 4px 14px 0 rgba(33, 118, 193, 0.20), 0 1.5px 4px 0 rgba(33, 118, 193, 0.13);
	transform: translateY(-1px) scale(1.025);
	z-index: 2;
}

.sideinfo-toggle:active {
	background: #dbeaf5;
	color: #155a99;
	box-shadow: 0 1px 4px 0 rgba(33, 118, 193, 0.13);
	transform: translateY(1px) scale(0.98);
}

.engine-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 90px; }
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
		background: #fbfdfe;
		border: 1px solid #b6dded;
		border-radius: 14px;
		box-shadow: 0 2px 6px rgba(0, 64, 128, 0.05);
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

	/* Responsive tweaks for settings */
	@media screen and (max-width: 768px) {
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

	// 基础工具函数
	// throttle: 每wait毫秒最多执行一次
	function throttle(func, wait) {
		let lastTime = 0;
		return function() {
			const now = Date.now();
			if (now - lastTime >= wait) {
				lastTime = now;
				func.apply(this, arguments);
			}
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
			if (page === 'note') {
				// 仅在非移动端（min-width: 769px）下将光标定位到搜索栏
				if (window.matchMedia('(min-width: 769px)').matches) {
					const searchInput = window._hyplusSearchCache?.searchInput || document.getElementById('searchInput');
					searchInput?.focus();
				}
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
				// [隐藏] 原始: const pages = ['chat', 'ai', 'nav', 'note', 'settings'];
				const pages = ['nav', 'note', 'settings'];
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

	// 主体点击事件处理（优化条件判断顺序，避免不必要的计算）
	document.body.addEventListener('click', function(event) {
		if (event.altKey) return;
		const nav = document.getElementById('navContainer');
		// 快速判断nav是否显示，避免不必要的getBoundingClientRect计算
		if (nav.style.display !== 'block') return;
		const navButton = document.getElementById('navButton');
		// 先判断是否点击了navButton，避免getBoundingClientRect计算
		if (navButton.contains(event.target)) return;
		const rectNav = nav.getBoundingClientRect();
		const isInNav = event.clientX >= rectNav.left &&
			  event.clientX <= rectNav.right &&
			  event.clientY >= rectNav.top &&
			  event.clientY <= rectNav.bottom;
		if (!isInNav) {
			nav.style.display = 'none';
			document.body.classList.remove('nav-open');
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
		// 优化：使用事件委托，只注册1对mouseenter/mouseleave，将5个按钮视作整体
		const hyButtonIds = new Set(['scrollToTopButton', 'navButton', 'goBackButton', 'goForwardButton', 'refreshButton']);
		document.addEventListener('mouseenter', function(event) {
			if (hyButtonIds.has(event.target.id)) {
				isScrollDisabled = true;
			}
		}, true);
		document.addEventListener('mouseleave', function(event) {
			if (hyButtonIds.has(event.target.id)) {
				isScrollDisabled = false;
			}
		}, true);

		// 搜索引擎配置
		const searchEngines = {
			hyplus: {
				name: 'Hyplus',
				url: 'https://www.hyperplasma.top/?s={q}',
				homepage: 'https://www.hyperplasma.top'
			},
			baidu: {
				name: 'Baidu',
				url: 'https://www.baidu.com/s?wd={q}',
				homepage: 'https://www.baidu.com'
			},
			bing: {
				name: 'Bing',
				url: 'https://www.bing.com/search?q={q}',
				homepage: 'https://www.bing.com'
			},
			google: {
				name: 'Google',
				url: 'https://www.google.com/search?q={q}',
				homepage: 'https://www.google.com'
			},
			scholar: {
				name: '谷歌学术',
				url: 'https://scholar.google.com/scholar?q={q}',
				homepage: 'https://scholar.google.com'
			},
			github: {
				name: 'GitHub仓库',
				url: 'https://github.com/search?q={q}',
				homepage: 'https://github.com/search'
			}
		};

		// 缓存搜索栏相关控件为全局变量（不再使用 cookie 或单选框）
		if (!window._hyplusSearchCache) {
			window._hyplusSearchCache = {
				searchInput: document.getElementById('searchInput'),
				engineButtons: document.querySelectorAll('.engine-btn')
			};
		}
		const searchInput = window._hyplusSearchCache.searchInput;
		const engineButtons = window._hyplusSearchCache.engineButtons;

		// 搜索执行（传入引擎 key）
		function doSearch(engine) {
			engine = engine || 'bing';
			const query = encodeURIComponent((searchInput && searchInput.value || '').trim());
			const cfg = searchEngines[engine] || searchEngines.hyplus;
			let url;
			if (query) {
				// 有查询内容时，使用搜索 URL
				url = cfg.url.replace('{q}', query);
			} else {
				// 查询为空时，跳转到首页
				url = cfg.homepage;
			}
			window.open(url, '_blank');
		}

		// 绑定引擎按钮点击事件（每个按钮会直接以该引擎执行搜索）
		if (engineButtons && engineButtons.length) {
			engineButtons.forEach(btn => {
				btn.addEventListener('click', function(e) {
					e.stopPropagation();
					const eng = this.dataset.engine;
					doSearch(eng);
				});
			});
		}

		// 在输入框按 Enter 时，默认使用 bing 搜索
		if (searchInput) {
			searchInput.addEventListener('keydown', function(e) {
				if (e.key === 'Enter') doSearch('bing');
			});
		}

		// 清空按钮逻辑
		const clearSearchBtn = document.getElementById('clearSearchBtn');
		const searchInputElement = document.getElementById('searchInput');
		if (searchInputElement && clearSearchBtn) {
			// 更新清空按钮的显示状态
			function updateClearBtnVisibility() {
				if (searchInputElement.value.trim()) {
					clearSearchBtn.style.display = 'block';
				} else {
					clearSearchBtn.style.display = 'none';
				}
			}

			// 输入框输入事件
			searchInputElement.addEventListener('input', updateClearBtnVisibility);

			// 输入框获得焦点时，如果有内容就显示清空按钮
			searchInputElement.addEventListener('focus', updateClearBtnVisibility);

			// 输入框失焦时隐藏清空按钮
			searchInputElement.addEventListener('blur', function() {
				clearSearchBtn.style.display = 'none';
			});

			// 清空按钮点击事件 - 使用mousedown以确保捕获点击
			clearSearchBtn.addEventListener('mousedown', function(e) {
				e.preventDefault();
				e.stopPropagation();
				searchInputElement.value = '';
				searchInputElement.focus();
				updateClearBtnVisibility();
				return false;
			});

			// 清空按钮点击事件 - 备用
			clearSearchBtn.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				searchInputElement.value = '';
				searchInputElement.focus();
				updateClearBtnVisibility();
				return false;
			});

			// 清空按钮悬停效果
			clearSearchBtn.addEventListener('mouseenter', function() {
				this.style.opacity = '1';
			});

			clearSearchBtn.addEventListener('mouseleave', function() {
				this.style.opacity = '0.6';
			});

			// ESC键清空搜索框
			searchInputElement.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' && searchInputElement.value.trim()) {
					e.preventDefault();
					searchInputElement.value = '';
					updateClearBtnVisibility();
				}
			});
		}
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
		const savedNavButtonsPosition = localStorage.getItem('navButtonsPosition') || 'right';
		setNavButtonsPosition(savedNavButtonsPosition);
		document.getElementById('navButtonsLeftRadio').checked = (savedNavButtonsPosition === 'left');
		document.getElementById('navButtonsRightRadio').checked = (savedNavButtonsPosition === 'right');

		// 优化：侧边栏和导航按钮位置单选框使用事件委托，各注册1个change监听
		document.addEventListener('change', function(event) {
			if (event.target.name === 'sidebarPosition' && event.target.checked) {
				setSidebarPosition(event.target.value);
			} else if (event.target.name === 'navButtonsPosition' && event.target.checked) {
				setNavButtonsPosition(event.target.value);
			}
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
		}
		const hideButtonsToggle = window._hyplusSettingsCheckboxCache.hideButtonsToggle;

		// 优化：页头页尾和隐藏按钮群复选框使用事件委托，只注册1个change监听
		document.addEventListener('change', function(event) {
			if (event.target.id === 'headerFooterToggle') {
				handleHeaderFooterToggle(event);
			} else if (event.target.id === 'hideButtonsToggle') {
				handleHideButtonsToggle(event);
			}
		});

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
					const before = '请你认真阅读学习以下内容（其中第1行是所属分类、字数统计、预估阅读时间等元信息，第2行是标题，第3行是初版发表日期、作者名、最近修改日期等元信息，之后为正文；最后几行可能有若干注解(以返回符`↩`结尾)、分类/tag信息、评论栏、网友评论等额外内容），准备据此回答问题：\n```````````````````````````\n';
					const after = '\n```````````````````````````\n';
					content = before + content + after;
				}
				if (navigator.clipboard) {
					navigator.clipboard.writeText(content).then(function() {
						alert('✓ 页面正文已复制到剪贴板');
					}).catch(function() {
						alert('✗ 复制失败，请重试');
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
		const throttledResize = throttle(function() {
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
		}, 50);

		window.addEventListener('resize', throttledResize);

		// 浏览按钮事件绑定（优化：使用事件委托和缓存节点）
		const buttonActions = {
			goBackButton: () => window.history.back(),
			goForwardButton: () => window.history.forward(),
			refreshButton: () => window.location.reload(),
			scrollToTopButton: scrollToTop
		};
		// 使用缓存的节点绑定事件
		if (window._hyplusBtnCache) {
			Object.entries(buttonActions).forEach(([buttonId, action]) => {
				const button = window._hyplusBtnCache[buttonId];
				if (button) {
					button.addEventListener('click', action);
				}
			});
		}

		// 清空按钮逻辑 (window.onload中的初始化)
		const clearSearchBtnOnLoad = document.getElementById('clearSearchBtn');
		const searchInputOnLoad = document.getElementById('searchInput');
		if (searchInputOnLoad && clearSearchBtnOnLoad) {
			// 更新清空按钮的显示状态
			function updateClearBtnVisibilityOnLoad() {
				if (searchInputOnLoad.value.trim()) {
					clearSearchBtnOnLoad.style.display = 'block';
				} else {
					clearSearchBtnOnLoad.style.display = 'none';
				}
			}

			// 输入框输入事件
			searchInputOnLoad.addEventListener('input', updateClearBtnVisibilityOnLoad);

			// 输入框获得焦点时，如果有内容就显示清空按钮
			searchInputOnLoad.addEventListener('focus', updateClearBtnVisibilityOnLoad);

			// 输入框失焦时隐藏清空按钮
			searchInputOnLoad.addEventListener('blur', function() {
				clearSearchBtnOnLoad.style.display = 'none';
			});

			// 清空按钮点击事件 - 使用mousedown以确保捕获点击
			clearSearchBtnOnLoad.addEventListener('mousedown', function(e) {
				e.preventDefault();
				e.stopPropagation();
				searchInputOnLoad.value = '';
				searchInputOnLoad.focus();
				updateClearBtnVisibilityOnLoad();
				return false;
			});

			// 清空按钮点击事件 - 备用
			clearSearchBtnOnLoad.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				searchInputOnLoad.value = '';
				searchInputOnLoad.focus();
				updateClearBtnVisibilityOnLoad();
				return false;
			});

			// 清空按钮悬停效果
			clearSearchBtnOnLoad.addEventListener('mouseenter', function() {
				this.style.opacity = '1';
			});

			clearSearchBtnOnLoad.addEventListener('mouseleave', function() {
				this.style.opacity = '0.6';
			});

			// ESC键清空搜索框
			searchInputOnLoad.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' && searchInputOnLoad.value.trim()) {
					e.preventDefault();
					searchInputOnLoad.value = '';
					updateClearBtnVisibilityOnLoad();
				}
			});
		}
	};

	// 导出需要的全局函数
	window.switchNavContent = switchNavContent;
</script>