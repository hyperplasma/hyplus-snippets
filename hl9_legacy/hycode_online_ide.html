<!--
HyCode - 在线IDE切换器
Version: 0.1
Created: 2025-05-26 04:09:19 UTC
Author: hyperplasma
Code type: HTML
Current status: unused
-->
<div class="hycode-container">
	<div class="hycode-main">
		<div class="hycode-main-container">
			<!-- 选择器区域 -->
			<div class="hycode-selector-row">
				<select id="hycode-language-selector" class="hycode-selector">
					<option value="java">Java (Tech.io)</option>
					<option value="cpp">C++ (Tech.io)</option>
				</select>
			</div>

			<!-- IDE显示区域 -->
			<div id="hycode-display" class="hycode-display">
				<!-- IDE将在这里动态插入 -->
			</div>
		</div>
	</div>
	<div class="hycode-version hyplus-unselectable">HyCode v0.1</div>
</div>

<style>
	.hycode-container {
		width: 100%;
		max-width: 1200px;
		margin: 20px auto;
	}

	.hycode-main {
		padding: 20px;
		box-shadow: 0 0 10px rgba(0,0,0,0.1);
		border-radius: 8px;
		background: #f9f9f9;
		box-sizing: border-box;
	}

	.hycode-main-container {
		display: flex;
		flex-direction: column;
	}

	.hycode-selector-row {
		display: flex;
		justify-content: flex-end;
		margin-bottom: 20px;
	}

	.hycode-selector {
		padding: 8px 16px;
		font-size: 16px;
		border: 1px solid #ccc;
		border-radius: 4px;
		background: #fff;
		color: #333;
		cursor: pointer;
		outline: none;
		min-width: 200px;
	}

	.hycode-selector:hover {
		border-color: #999;
	}

	.hycode-selector:focus {
		border-color: #1976d2;
		box-shadow: 0 0 0 2px rgba(25,118,210,0.1);
	}

	.hycode-display {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 4px;
		display: flex;
		flex-direction: column;
	}

	.hycode-display iframe {
		width: 100%;
		border: none;
		flex-grow: 1;
	}

	.hycode-version {
		margin-top: 10px;
		color: #aaa;
		font-size: 15px;
		font-family: inherit;
		user-select: none;
		letter-spacing: 1px;
		background: transparent;
		z-index: 2;
		align-self: flex-end;
		text-align: right;
		width: 100%;
	}

	/* 响应式设计 */
	@media (max-width: 768px) {
		.hycode-main {
			padding: 15px;
		}
		.hycode-selector {
			width: 100%;
		}
	}

	@media (max-width: 500px) {
		.hycode-main {
			padding: 10px;
		}
		.hycode-version {
			font-size: 13px;
		}
	}
</style>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const selector = document.getElementById('hycode-language-selector');
		const display = document.getElementById('hycode-display');

		// IDE配置
		const ideConfigs = {
			'java': {
				src: 'https://tech.io/snippet-widget/qUWOT10'
			},
			'cpp': {
				src: 'https://tech.io/snippet-widget/Z4zjeAX'
			}
		};

		// 切换IDE显示
		function switchIDE(value) {
			const config = ideConfigs[value];
			if (!config) return;

			// 创建新的iframe
			display.innerHTML = `
<iframe 
src="${config.src}" 
frameborder="0" 
scrolling="no" 
allowtransparency="true" 
style="visibility:hidden" 
allow="keyboard-map">
	</iframe>
`;

			// 注入Tech.io脚本
			if (void 0 === window.techioScriptInjected) {
				window.techioScriptInjected = true;
				var script = document.createElement("script");
				script.src = "https://files.codingame.com/codingame/iframe-v-1-4.js";
				(document.head || document.body).appendChild(script);
			}
		}

		// 监听选择器变化
		selector.addEventListener('change', function() {
			switchIDE(this.value);
		});

		// 初始化显示
		switchIDE(selector.value);
	});
</script>