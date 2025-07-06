<!-- API-GOD (HyNetStat) - 网络测速和API请求工具 Test Your Wifi
 Version: 0.2
 Status: under investigation
 Created: 2025-05-27 03:37:27 UTC
 Author: hyperplasma
 Code Type: HTML
 Shortcode: [wpcode id="12802"] (auto-generated)
-->

<div class="hynetstat-container">
	<div class="hynetstat-calculator">
		<div class="hynetstat-calculator-container">
			<!-- IP信息区域 -->
			<div class="hynetstat-info-section">
				<div class="hynetstat-info-block">
					<div id="hynetstat-ip" class="hynetstat-info-text">
						本机IP：获取中...
					</div>
					<div id="hynetstat-location" class="hynetstat-info-text hynetstat-location-text">
						获取中...
					</div>
				</div>
			</div>

			<!-- 请求区域 -->
			<div class="apigod-request-section">
				<div class="apigod-request-row">
					<div class="apigod-request-left">
						<!-- 请求方法选择器 -->
						<select id="apigod-method-select" class="apigod-select styled-select">
							<option value="GET">GET</option>
							<option value="POST">POST</option>
							<option value="PUT">PUT</option>
							<option value="DELETE">DELETE</option>
							<option value="PATCH">PATCH</option>
							<option value="HEAD">HEAD</option>
							<option value="OPTIONS">OPTIONS</option>
						</select>
						<!-- URL输入框 -->
						<input type="text" id="apigod-url-input" class="apigod-input" placeholder="请输入请求URL">
					</div>
					<div class="apigod-request-right">
						<!-- 发送和保存按钮 -->
						<button id="apigod-send-btn" class="apigod-action-btn apigod-send-btn">Send</button>
						<button id="apigod-save-btn" class="apigod-action-btn apigod-save-btn">Save</button>
					</div>
				</div>

				<!-- 请求配置标签页 -->
				<div class="apigod-request-tabs bordered-block">
					<div class="apigod-tab-headers">
						<button class="apigod-tab-btn active" data-tab="params">Query Params</button>
						<button class="apigod-tab-btn" data-tab="headers">Headers</button>
						<button class="apigod-tab-btn" data-tab="body">Body</button>
						<button class="apigod-tab-btn" data-tab="prereq">Pre-request Script</button>
						<button class="apigod-tab-btn" data-tab="tests">Tests</button>
					</div>

					<!-- Query Params -->
					<div class="apigod-tab-content active" id="apigod-params-tab">
						<div class="apigod-params-table">
							<table class="apigod-table apigod-bordered-table">
								<thead>
									<tr>
										<th width="40px"></th>
										<th>参数名</th>
										<th>参数值</th>
										<th>描述</th>
										<th width="40px"></th>
									</tr>
								</thead>
								<tbody id="apigod-params-list">
									<tr class="apigod-param-row">
										<td>
											<input type="checkbox" class="apigod-param-enabled" checked>
										</td>
										<td>
											<input type="text" class="apigod-input" placeholder="参数名">
										</td>
										<td>
											<input type="text" class="apigod-input" placeholder="参数值">
										</td>
										<td>
											<input type="text" class="apigod-input" placeholder="描述（可选）">
										</td>
										<td>
											<button class="apigod-row-delete-btn">❌</button>
										</td>
									</tr>
								</tbody>
							</table>
							<button id="apigod-add-param-btn" class="apigod-add-row-btn">
								+ 添加参数
							</button>
						</div>
					</div>

					<!-- Headers -->
					<div class="apigod-tab-content" id="apigod-headers-tab">
						<div class="apigod-headers-table">
							<table class="apigod-table apigod-bordered-table">
								<thead>
									<tr>
										<th width="40px"></th>
										<th>Header名</th>
										<th>Header值</th>
										<th>描述</th>
										<th width="40px"></th>
									</tr>
								</thead>
								<tbody id="apigod-headers-list">
									<tr class="apigod-header-row">
										<td>
											<input type="checkbox" class="apigod-header-enabled" checked>
										</td>
										<td>
											<input type="text" class="apigod-input" placeholder="Header名">
										</td>
										<td>
											<input type="text" class="apigod-input" placeholder="Header值">
										</td>
										<td>
											<input type="text" class="apigod-input" placeholder="描述（可选）">
										</td>
										<td>
											<button class="apigod-row-delete-btn">❌</button>
										</td>
									</tr>
								</tbody>
							</table>
							<button id="apigod-add-header-btn" class="apigod-add-row-btn">
								+ 添加Header
							</button>
							<button id="apigod-add-common-headers-btn" class="apigod-add-row-btn">
								+ 添加常用Headers
							</button>
						</div>
					</div>

					<!-- Body -->
					<div class="apigod-tab-content" id="apigod-body-tab">
						<div class="apigod-body-type-selector">
							<select id="apigod-body-type-select" class="apigod-select styled-select">
								<option value="none">none</option>
								<option value="form-data">form-data</option>
								<option value="x-www-form-urlencoded">x-www-form-urlencoded</option>
								<option value="raw">raw</option>
								<option value="binary">binary</option>
							</select>
							<select id="apigod-raw-type-select" class="apigod-select styled-select" style="display: none;">
								<option value="text">Text</option>
								<option value="json" selected>JSON</option>
								<option value="xml">XML</option>
								<option value="html">HTML</option>
							</select>
						</div>

						<!-- Form Data -->
						<div id="apigod-form-data-container" class="apigod-body-container" style="display: none;">
							<table class="apigod-table apigod-bordered-table">
								<thead>
									<tr>
										<th width="40px"></th>
										<th>键</th>
										<th>值</th>
										<th>类型</th>
										<th width="40px"></th>
									</tr>
								</thead>
								<tbody id="apigod-form-data-list"></tbody>
							</table>
							<button id="apigod-add-form-data-btn" class="apigod-add-row-btn">
								+ 添加字段
							</button>
						</div>

						<!-- URL Encoded -->
						<div id="apigod-urlencoded-container" class="apigod-body-container" style="display: none;">
							<table class="apigod-table apigod-bordered-table">
								<thead>
									<tr>
										<th width="40px"></th>
										<th>键</th>
										<th>值</th>
										<th width="40px"></th>
									</tr>
								</thead>
								<tbody id="apigod-urlencoded-list"></tbody>
							</table>
							<button id="apigod-add-urlencoded-btn" class="apigod-add-row-btn">
								+ 添加字段
							</button>
						</div>

						<!-- Raw -->
						<div id="apigod-raw-container" class="apigod-body-container" style="display: none;">
							<div id="apigod-raw-editor" class="apigod-editor"></div>
						</div>

						<!-- Binary -->
						<div id="apigod-binary-container" class="apigod-body-container" style="display: none;">
							<input type="file" id="apigod-binary-file" class="apigod-file-input">
						</div>
					</div>

					<!-- Pre-request Script -->
					<div class="apigod-tab-content" id="apigod-prereq-tab">
						<div id="apigod-prereq-editor" class="apigod-editor"></div>
					</div>

					<!-- Tests -->
					<div class="apigod-tab-content" id="apigod-tests-tab">
						<div id="apigod-tests-editor" class="apigod-editor"></div>
					</div>
				</div>
			</div>

			<!-- 响应区域 -->
			<div class="apigod-response-section bordered-block">
				<div class="apigod-response-header">
					<div class="apigod-response-status">
						<span id="apigod-response-status-code">--</span>
						<span id="apigod-response-status-text">等待请求</span>
					</div>
					<div class="apigod-response-time">
						用时：<span id="apigod-response-time">--</span> ms
					</div>
					<div class="apigod-response-size">
						大小：<span id="apigod-response-size">--</span>
					</div>
				</div>

				<div class="apigod-response-tabs">
					<div class="apigod-tab-headers">
						<button class="apigod-tab-btn active" data-tab="response">Response</button>
						<button class="apigod-tab-btn" data-tab="cookies">Cookies</button>
						<button class="apigod-tab-btn" data-tab="test-results">Test Results</button>
					</div>

					<!-- Response Body -->
					<div class="apigod-tab-content active" id="apigod-response-body-tab">
						<div class="apigod-response-body-type">
							<select id="apigod-response-view-type" class="apigod-select styled-select">
								<option value="json">JSON</option>
								<option value="xml">XML</option>
								<option value="html">HTML</option>
								<option value="js">JavaScript</option>
								<option value="raw">Raw</option>
							</select>
						</div>
						<div id="apigod-response-body-container" class="apigod-response-container"></div>
					</div>

					<!-- Response Cookies -->
					<div class="apigod-tab-content" id="apigod-response-cookies-tab">
						<div class="apigod-response-container">
							<table class="apigod-table apigod-bordered-table">
								<thead>
									<tr>
										<th>Cookie名</th>
										<th>Cookie值</th>
										<th>Domain</th>
										<th>过期时间</th>
									</tr>
								</thead>
								<tbody id="apigod-response-cookies-list"></tbody>
							</table>
						</div>
					</div>

					<!-- Test Results -->
					<div class="apigod-tab-content" id="apigod-test-results-tab">
						<div class="apigod-response-container">
							<div id="apigod-test-results"></div>
						</div>
					</div>
				</div>
			</div>

			<!-- 历史记录 -->
			<div class="apigod-history-table-container">
				<table class="apigod-history-table hyplus-excluded-table">
					<thead>
						<tr>
							<th>保存时间</th>
							<th>请求方法</th>
							<th>URL</th>
							<th>描述</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody id="apigod-history-list">
						<!-- 历史记录将在这里动态插入 -->
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<br>
	<div class="hynetstat-calculator">
		<div class="hynetstat-calculator-container">
			<!-- HyNetStat -->
			<div class="hynetstat-control-section">
				<div class="hynetstat-netstat-test-title hyplus-unselectable hynetstat-center">&nbsp;网络测速</div>
				<div class="hynetstat-server-selector">
					<select id="hynetstat-server-select" class="hynetstat-select">
						<option value="auto">自动选择最佳服务器</option>
						<option value="local" selected>Hyplus服务器（www.hyperplasma.top）</option>
					</select>
				</div>
				<div class="hynetstat-button-row">
					<button id="hynetstat-test-btn" class="hynetstat-action-btn hynetstat-test-btn hyplus-unselectable">
						测速
					</button>
					<button id="hynetstat-clear-btn" class="hynetstat-action-btn hynetstat-clear-main-btn hyplus-unselectable">
						清空
					</button>
				</div>
			</div>

			<!-- 测试进度显示 -->
			<div id="hynetstat-progress-container" class="hynetstat-progress-container" style="display: none;">
				<div class="hynetstat-progress">
					<div id="hynetstat-progress-bar" class="hynetstat-progress-bar"></div>
				</div>
				<div id="hynetstat-progress-text" class="hynetstat-progress-text">准备测速...</div>
			</div>

			<!-- 输出区域 -->
			<div class="hynetstat-output-grid">
				<!-- 第一列 -->
				<div class="hynetstat-output-column">
					<div class="hynetstat-output-block">
						<div class="hynetstat-output-row">
							<div class="hynetstat-label-title hyplus-unselectable">网络延迟</div>
							<div id="hynetstat-output-ping" class="hynetstat-result hynetstat-result-default">-</div>
						</div>
					</div>
					<div class="hynetstat-output-block">
						<div class="hynetstat-output-row">
							<div class="hynetstat-label-title hyplus-unselectable">丢包率</div>
							<div id="hynetstat-output-loss" class="hynetstat-result hynetstat-result-default">-</div>
						</div>
					</div>
					<div class="hynetstat-output-block">
						<div class="hynetstat-output-row">
							<div class="hynetstat-label-title hyplus-unselectable">抖动</div>
							<div id="hynetstat-output-jitter" class="hynetstat-result hynetstat-result-default">-</div>
						</div>
					</div>
				</div>
				<!-- 第二列 -->
				<div class="hynetstat-output-column">
					<div class="hynetstat-output-block">
						<div class="hynetstat-output-row">
							<div class="hynetstat-label-title hyplus-unselectable">下载速度</div>
							<div id="hynetstat-output-download" class="hynetstat-result hynetstat-result-default">-</div>
						</div>
					</div>
					<div class="hynetstat-output-block">
						<div class="hynetstat-output-row">
							<div class="hynetstat-label-title hyplus-unselectable">上传速度</div>
							<div id="hynetstat-output-upload" class="hynetstat-result hynetstat-result-default">-</div>
						</div>
					</div>
					<!-- 将测试节点改为质量评价 -->
					<div class="hynetstat-output-block">
						<div class="hynetstat-output-row" title="爽 > 优 > 良 > 差 > 恶 > 无信号">
							<div class="hynetstat-label-title hyplus-unselectable">质量评价</div>
							<div id="hynetstat-output-server" class="hynetstat-result hynetstat-result-default">-</div>
						</div>
					</div>
				</div>
			</div>

			<!-- 历史记录 -->
			<div class="hynetstat-history-table-container">
				<table class="hynetstat-history-table hyplus-excluded-table">
					<thead>
						<tr>
							<th>测速历史</th>
							<th>测试节点</th>
							<th>网络延迟</th>
							<th>下载速度</th>
							<th>上传速度</th>
							<th>质量评价</th>
							<th class="hynetstat-clear-header" id="hynetstat-clear-all">清除记录</th>
						</tr>
					</thead>
					<tbody id="hynetstat-history-list">
						<!-- 历史记录将在这里动态插入 -->
					</tbody>
				</table>
			</div>

			<!-- 设置面板 -->
			<div class="hynetstat-settings-panel">
				<div class="hynetstat-settings-title hyplus-unselectable hynetstat-center">测速设置</div>
				<div class="hynetstat-settings-content">
					<div class="hynetstat-settings-row">
						<div class="hynetstat-label-title hyplus-unselectable">速度单位：</div>
						<select id="hynetstat-unit-selector" class="hynetstat-unit-selector">
							<option value="Mbps" selected>Mbps</option>
							<option value="MB/s">MB/s</option>
							<option value="Kbps">Kbps</option>
							<option value="KB/s">KB/s</option>
							<option value="Gbps">Gbps</option>
							<option value="GB/s">GB/s</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="hynetstat-version hyplus-unselectable">API-GOD v0.2</div>
</div>

<style>
	.hynetstat-container {
		width: 100%;
		max-width: 700px;
		margin: 20px auto;
	}

	.hynetstat-calculator {
		padding: 20px;
		box-shadow: 0 0 10px rgba(0,0,0,0.1);
		border-radius: 8px;
		background: #f9f9f9;
		box-sizing: border-box;
	}

	.hynetstat-calculator-container {
		display: flex;
		flex-direction: column;
		gap: 15px;
	}

	/* IP信息区域样式 */
	.hynetstat-info-section {
		display: flex;
		flex-direction: column;
	}

	.hynetstat-info-block {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 15px;
		display: flex;
		flex-direction: column;
		gap: 8px;
		align-items: center;  /* 确保内容居中 */
	}

	.hynetstat-info-text {
		font-size: 15px;
		color: #333;
		font-family: monospace;
		letter-spacing: 0.5px;
		text-align: center;  /* 确保文本居中 */
		width: 100%;        /* 确保宽度充满容器 */
	}

	.hynetstat-location-text {
		color: #666;
		font-size: 14px;
	}

	/* 控制区域样式 */
	.hynetstat-control-section {
		display: flex;
		gap: 15px;
		align-items: center;
	}

	.hynetstat-server-selector {
		flex: 2;
	}

	.hynetstat-button-row {
		display: flex;
		gap: 15px;
		flex: 3;
	}

	.hynetstat-select {
		width: 100%;
		padding: 8px 12px;
		border: 1px solid #ccc;
		border-radius: 4px;
		font-size: 15px;
		background: #fff;
		cursor: pointer;
		outline: none;
	}

	.hynetstat-select:hover {
		border-color: #999;
	}

	.hynetstat-select:focus {
		border-color: #1976d2;
		box-shadow: 0 0 0 2px rgba(25,118,210,0.1);
	}

	/* 按钮样式 */
	.hynetstat-action-btn {
		padding: 8px 24px;
		border: none;
		border-radius: 4px;
		font-size: 15px;
		cursor: pointer;
		transition: all 0.2s;
		font-weight: bold;
		flex: 1;
	}

	.hynetstat-test-btn {
		background: #1976d2;
		color: #fff;
	}

	.hynetstat-test-btn:hover {
		background: #1565c0;
	}

	.hynetstat-test-btn:disabled {
		background: #90caf9;
		cursor: not-allowed;
	}

	/* 主清空按钮样式 */
	.hynetstat-clear-main-btn {
		background: #757575 !important; /* 灰色背景 */
		color: #fff !important;
	}

	.hynetstat-clear-main-btn:hover {
		background: #616161 !important; /* 鼠标悬停时的深灰色 */
	}

	/* 进度条样式 */
	.hynetstat-progress-container {
		margin: 5px 0;
	}

	.hynetstat-progress {
		width: 100%;
		height: 4px;
		background: #e0e0e0;
		border-radius: 2px;
		overflow: hidden;
	}

	.hynetstat-progress-bar {
		width: 0%;
		height: 100%;
		background: #1976d2;
		transition: width 0.3s ease-in-out;
	}

	.hynetstat-progress-text {
		text-align: center;
		font-size: 14px;
		color: #666;
		margin-top: 5px;
	}

	/* 输出区域样式 */
	.hynetstat-output-grid {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 15px;
	}

	.hynetstat-output-column {
		display: flex;
		flex-direction: column;
		gap: 15px;
	}

	.hynetstat-output-block {
		background: #fff;
		padding: 15px;
		border-radius: 4px;
		border: 1px solid #ddd;
	}

	.hynetstat-output-row {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 10px;
	}

	.hynetstat-label-title {
		font-size: 15px;
		font-weight: bold;
		color: #3b4d7a;
		letter-spacing: 1px;
		font-family: inherit;
	}

	.hynetstat-result {
		font-size: 20px;
		color: #222;
		font-family: monospace;
		flex: 1;
		text-align: center;
	}

	.hynetstat-result-default {
		color: #888;
	}

	/* 历史记录样式 */
	.hynetstat-history-table-container {
		width: 100%;
		overflow-x: auto;
		margin: 15px 0;  /* 保持与其他区块的间距 */
	}

	.hynetstat-history-table {
		width: 100%;
		min-width: 600px;
		border-collapse: separate;     /* 改为 separate 以支持圆角 */
		border-spacing: 0;            /* 确保单元格间没有间隔 */
		font-size: 14px;
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 6px;
		overflow: hidden;
	}

	.hynetstat-history-table thead {
		background: #fff;
	}

	.hynetstat-history-table th {
		padding: 12px;
		text-align: center;
		font-weight: bold;
		color: #3b4d7a;
		border-right: 1px solid #ddd;
		border-bottom: 1px solid #ddd;
		white-space: nowrap;
		background: #fff; 
	}

	.hynetstat-clear-header {
		color: #f44336 !important;
		text-align: center !important;
		white-space: nowrap !important;
		cursor: pointer;
	}

	.hynetstat-clear-header:hover {
		background-color: #fee !important;
	}

	.hynetstat-history-table td {
		padding: 12px;
		border-right: 1px solid #ddd;
		border-bottom: 1px solid #ddd;
		white-space: nowrap;
		text-align: center;
		background: #fff;
	}

	.hynetstat-history-table tbody tr:hover {
		background: #f9f9f9;
	}

	/* 移除最后一列的右边框 */
	.hynetstat-history-table th:last-child,
	.hynetstat-history-table td:last-child {
		border-right: none;
	}

	/* 移除最后一行的底部边框 */
	.hynetstat-history-table tr:last-child td {
		border-bottom: none;
	}

	.hynetstat-clear-btn {
		background: none;
		border: none;
		cursor: pointer;
		font-size: 14px;
		padding: 4px 8px;
		border-radius: 4px;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto;
	}

	.hynetstat-clear-btn:hover {
		background: #fee;
	}

	/* 设置面板样式 */
	.hynetstat-settings-panel {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 15px;
	}

	.hynetstat-settings-title {
		font-size: 16px;
		font-weight: bold;
		color: #3b4d7a;
		margin-bottom: 15px;
	}

	.hynetstat-netstat-test-title {
		font-size: 16px;
		font-weight: bold;
		color: #3b4d7a;
	}

	.hynetstat-center {
		text-align: center;
	}

	.hynetstat-settings-content {
		padding: 0 15px;
	}

	.hynetstat-settings-row {
		display: flex;
		align-items: center;
		gap: 15px;
	}

	.hynetstat-unit-selector {
		padding: 6px 12px;
		font-size: 15px;
		border: 1px solid #ccc;
		border-radius: 4px;
		background: #fff;
		color: #333;
		cursor: pointer;
		outline: none;
		min-width: 100px;
	}

	.hynetstat-version {
		margin-top: 10px;
		color: #aaa;
		font-size: 14px;
		font-family: inherit;
		user-select: none;
		letter-spacing: 1px;
		text-align: right;
	}

	/* 响应式设计 	*/
	@media (max-width: 768px) {
		.hynetstat-calculator {
			padding: 15px;
		}
		.hynetstat-control-section {
			flex-direction: column;
		}
		.hynetstat-server-selector {
			width: 100%;
		}
		.hynetstat-button-row {
			width: 100%;
		}
		.hynetstat-output-grid {
			grid-template-columns: 1fr;
		}
	}

	@media (max-width: 500px) {
		.hynetstat-calculator {
			padding: 10px;
		}
		.hynetstat-version {
			font-size: 13px;
		}
		.hynetstat-history-data {
			flex-direction: column;
			gap: 5px;
		}
	}

	/* 滚动条美化 */
	.hynetstat-history-list::-webkit-scrollbar {
		width: 6px;
		height: 6px;
	}

	.hynetstat-history-list::-webkit-scrollbar-track {
		background: #f1f1f1;
		border-radius: 3px;
	}

	.hynetstat-history-list::-webkit-scrollbar-thumb {
		background: #888;
		border-radius: 3px;
	}

	.hynetstat-history-list::-webkit-scrollbar-thumb:hover {
		background: #666;
	}

	.apigod-container {
		width: 100%;
		max-width: 1200px;
		margin: 20px auto;
	}

	.apigod-main {
		padding: 20px;
		box-shadow: 0 0 10px rgba(0,0,0,0.1);
		border-radius: 8px;
		background: #f9f9f9;
		box-sizing: border-box;
	}

	.apigod-main-container {
		display: flex;
		flex-direction: column;
		gap: 15px;
	}

	/* 请求行布局 */
	.apigod-request-row {
		display: flex;
		align-items: center;
		gap: 15px;
		padding: 0 15px;
	}

	.apigod-request-left {
		display: flex;
		flex: 1;
		gap: 15px;
	}

	.apigod-request-right {
		display: flex;
		gap: 10px;
		justify-content: center;
	}

	/* 统一下拉菜单样式 */
	.apigod-select.styled-select {
		appearance: none;
		-webkit-appearance: none;
		-moz-appearance: none;
		background: #fff url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M2 2L6 6L10 2' stroke='%233b4d7a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A") no-repeat right 0.75em center/1em;
		border: 1px solid #ccc;
		border-radius: 4px;
		padding: 8px 32px 8px 12px;
		font-size: 14px;
		transition: border-color 0.2s, box-shadow 0.2s;
		cursor: pointer;
		color: #333;
		min-width: 120px;
	}

	.apigod-select.styled-select:hover,
	.apigod-select.styled-select:focus {
		border-color: #1976d2;
		box-shadow: 0 0 0 2px rgba(25,118,210,0.08);
		outline: none;
	}

	/* 统一输入框样式 */
	.apigod-input {
		width: 100%;
		padding: 8px 12px;
		border: 1px solid #ccc;
		border-radius: 4px;
		font-size: 14px;
		outline: none;
		transition: border-color 0.2s;
		background: #fafbfc;
	}

	.apigod-input:hover {
		border-color: #999;
	}

	.apigod-input:focus {
		border-color: #1976d2;
		box-shadow: 0 0 0 2px rgba(25,118,210,0.1);
	}

	/* 按钮样式 */
	.apigod-action-btn {
		padding: 8px 24px;
		border: none;
		border-radius: 4px;
		font-size: 14px;
		cursor: pointer;
		transition: all 0.2s;
		font-weight: bold;
	}

	.apigod-send-btn {
		background: #1976d2;
		color: #fff;
	}

	.apigod-send-btn:hover {
		background: #1565c0;
	}

	.apigod-send-btn:disabled {
		background: #90caf9;
		cursor: not-allowed;
	}

	.apigod-save-btn {
		background: #4caf50;
		color: #fff;
	}

	.apigod-save-btn:hover {
		background: #43a047;
	}

	/* 外层带边框的块 */
	.bordered-block {
		border: 1px solid #e0e0e0;
		border-radius: 8px;
		margin-top: 8px;
		background: #fff;
		padding: 0;
	}

	/* Tab导航 */
	.apigod-tab-headers {
		display: flex;
		gap: 2px;
		border-bottom: 1px solid #ddd;
		background: #f5f5f5;
		padding: 0 15px;
	}

	.apigod-tab-btn {
		padding: 10px 20px;
		border: none;
		background: none;
		cursor: pointer;
		font-size: 15px;
		color: #666;
		position: relative;
		transition: all 0.2s;
		font-weight: 500;
	}

	.apigod-tab-btn:hover {
		color: #333;
		background-color: rgba(25,118,210,0.08);
	}

	.apigod-tab-btn.active {
		color: #1976d2;
		font-weight: bold;
	}

	.apigod-tab-btn.active::after {
		content: '';
		position: absolute;
		bottom: -1px;
		left: 0;
		width: 100%;
		height: 2px;
		background: #1976d2;
	}

	/* Tab内容 */
	.apigod-tab-content {
		display: none;
		padding: 20px 15px 15px 15px;
		background: #fff;
		border-bottom-left-radius: 7px;
		border-bottom-right-radius: 7px;
	}

	.apigod-tab-content.active {
		display: block;
	}

	/* 表格布局 */
	.apigod-params-table,
	.apigod-headers-table,
	.apigod-body-container {
		background: #fff;
		border-radius: 8px;
		margin-top: 15px;
		margin-bottom: 0;
		padding: 18px;
	}

	.apigod-params-table,
	.apigod-headers-table { 
		margin-top: 0; 
	}

	.apigod-bordered-table {
		width: 100%;
		border-collapse: separate;
		border-spacing: 0;
		background: #fff;
		border: 1px solid #e0e0e0;
	}

	.apigod-bordered-table th,
	.apigod-bordered-table td {
		padding: 8px 12px;
		border: 1px solid #e0e0e0;
	}

	.apigod-bordered-table th {
		text-align: left;
		font-weight: bold;
		color: #3b4d7a;
		background: #f5f5f5;
	}

	.apigod-bordered-table tr:last-child td {
		border-bottom: 1px solid #e0e0e0;
	}

	/* 编辑器容器 */
	.apigod-editor {
		height: 300px;
		border: 1px solid #ddd;
		border-radius: 4px;
		background: #fafbfc;
	}

	/* 响应区域 */
	.apigod-response-section {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 8px;
		margin-top: 20px;
	}

	.apigod-response-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 15px;
		background: #f5f5f5;
		border-bottom: 1px solid #ddd;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
	}

	.apigod-response-status {
		display: flex;
		align-items: center;
		gap: 10px;
		font-size: 16px;
	}

	#apigod-response-status-code {
		font-size: 16px;
		font-weight: bold;
	}

	/* 响应内容区 */
	.apigod-response-controls {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 10px;
		padding: 10px 15px;
		background: #f5f5f5;
		border-bottom: 1px solid #ddd;
	}

	.apigod-response-container {
		padding: 15px;
		min-height: 160px;
		max-height: 420px;
		overflow: auto;
		background: #fafbfc;
		border-radius: 5px;
	}

	.apigod-response-container.selectable {
		user-select: text;
		-webkit-user-select: text;
		cursor: text;
	}

	.apigod-copy-btn {
		display: flex;
		align-items: center;
		gap: 5px;
		padding: 6px 12px;
		border: 1px solid #ddd;
		border-radius: 4px;
		background: #fff;
		color: #666;
		cursor: pointer;
		transition: all 0.2s;
	}

	.apigod-copy-btn:hover {
		border-color: #1976d2;
		color: #1976d2;
	}

	.apigod-copy-btn svg {
		opacity: 0.7;
	}

	.apigod-copy-btn:hover svg {
		opacity: 1;
	}

	/* 添加行按钮 */
	.apigod-add-row-btn {
		margin-top: 10px;
		padding: 8px 16px;
		background: none;
		border: 1.5px dashed #1976d2;
		color: #1976d2;
		border-radius: 4px;
		cursor: pointer;
		font-size: 14px;
		font-weight: bold;
		transition: all 0.2s;
	}

	.apigod-add-row-btn:hover {
		background: rgba(25,118,210,0.07);
		color: #1976d2;
	}

	/* 删除行按钮 */
	.apigod-row-delete-btn {
		background: none;
		border: none;
		cursor: pointer;
		opacity: 0.6;
		transition: opacity 0.2s;
		font-size: 15px;
	}

	.apigod-row-delete-btn:hover {
		opacity: 1;
		color: #f44336;
	}

	/* 文件输入 */
	.apigod-file-input {
		width: 100%;
		padding: 20px;
		border: 2px dashed #ddd;
		border-radius: 4px;
		text-align: center;
		cursor: pointer;
		transition: all 0.2s;
		background: #fafbfc;
	}

	.apigod-file-input:hover {
		border-color: #1976d2;
		background: rgba(25,118,210,0.05);
	}

	/* 历史记录表 */
	.apigod-history-table-container {
		margin-top: 20px;
		overflow-x: auto;
	}

	.apigod-history-table {
		width: 100%;
		min-width: 800px;
		border-collapse: separate;
		border-spacing: 0;
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 6px;
	}

	.apigod-history-table th {
		padding: 12px;
		text-align: left;
		font-weight: bold;
		color: #3b4d7a;
		background: #f5f5f5;
		border-bottom: 1px solid #ddd;
		white-space: nowrap;
	}

	.apigod-history-table td {
		padding: 12px;
		border-bottom: 1px solid #ddd;
	}

	.apigod-history-table tr:last-child td {
		border-bottom: none;
	}

	.apigod-history-use-btn,
	.apigod-history-delete-btn {
		padding: 4px 16px;
		margin-right: 5px;
		font-size: 14px;
		border: none;
		border-radius: 4px;
		cursor: pointer;
		font-weight: bold;
		transition: background 0.2s, color 0.2s;
	}

	.apigod-history-use-btn {
		background: #1976d2;
		color: #fff;
	}

	.apigod-history-use-btn:hover {
		background: #1565c0;
	}

	.apigod-history-delete-btn {
		background: #f44336;
		color: #fff;
	}

	.apigod-history-delete-btn:hover {
		background: #d32f2f;
	}

	/* 版本信息 */
	.apigod-version {
		margin-top: 10px;
		color: #aaa;
		font-size: 14px;
		text-align: right;
	}

	/* Toast提示 */
	.apigod-toast {
		position: fixed;
		bottom: 40px;
		left: 50%;
		transform: translateX(-50%);
		z-index: 9999;
		background: #1976d2;
		color: #fff;
		padding: 10px 28px;
		border-radius: 8px;
		font-size: 15px;
		box-shadow: 0 4px 16px rgba(30,40,70,.13);
		opacity: 0;
		transition: opacity 0.3s;
		pointer-events: none;
	}

	.apigod-toast.show {
		opacity: 1;
	}

	/* 响应式布局 */
	@media (max-width: 768px) {
		.apigod-request-row {
			flex-direction: column;
		}
		.apigod-request-left {
			width: 100%;
		}
		.apigod-request-right {
			width: 100%;
		}
		.apigod-action-btn {
			flex: 1;
		}
	}
</style>


<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prettier@3.1.1/standalone.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prettier@3.1.1/parser-babel.js"></script>
<script src="https://cdn.jsdelivr.net/npm/json-formatter-js@2.3.4/dist/json-formatter.min.js"></script>

<script>
	// ======================== Toast提示 ========================
	function hynetstatShowToast(msg, type = 'info') {
		if (!msg) return;
		let old = document.getElementById('hynetstat-toast');
		if (old) old.remove();
		const toast = document.createElement('div');
		toast.id = 'hynetstat-toast';
		toast.textContent = msg;

		const colors = {
			info: '#1976d2',
			error: '#d32f2f',
			success: '#388e3c'
		};

		Object.assign(toast.style, {
			position: 'fixed',
			bottom: '40px',
			left: '50%',
			transform: 'translateX(-50%)',
			zIndex: 9999,
			background: colors[type] || colors.info,
			color: '#fff',
			padding: '10px 28px',
			borderRadius: '8px',
			fontSize: '15px',
			boxShadow: '0 4px 16px rgba(30,40,70,.13)',
			opacity: '0',
			transition: 'opacity 0.3s',
			pointerEvents: 'none',
		});
		document.body.appendChild(toast);
		setTimeout(() => { toast.style.opacity = '1'; }, 10);
		setTimeout(() => {
			toast.style.opacity = '0';
			setTimeout(() => { toast.remove(); }, 350);
		}, 2000);
	}

	// ======================== 主要功能类 ========================
	class SpeedTest {
		constructor() {
			this.initElements();
			this.initState();
			this.initHistory();
			this.bindEvents();
			this.init();
		}

		// 初始化DOM元素引用
		initElements() {
			this.elements = {
				ip: document.getElementById('hynetstat-ip'),
				location: document.getElementById('hynetstat-location'),
				testButton: document.getElementById('hynetstat-test-btn'),
				clearButton: document.getElementById('hynetstat-clear-btn'),
				serverSelect: document.getElementById('hynetstat-server-select'),
				unitSelector: document.getElementById('hynetstat-unit-selector'),
				progressContainer: document.getElementById('hynetstat-progress-container'),
				progressBar: document.getElementById('hynetstat-progress-bar'),
				progressText: document.getElementById('hynetstat-progress-text'),
				historyList: document.getElementById('hynetstat-history-list'),
				clearAllHeader: document.getElementById('hynetstat-clear-all'),

				results: {
					ping: document.getElementById('hynetstat-output-ping'),
					download: document.getElementById('hynetstat-output-download'),
					upload: document.getElementById('hynetstat-output-upload'),
					loss: document.getElementById('hynetstat-output-loss'),
					jitter: document.getElementById('hynetstat-output-jitter'),
					server: document.getElementById('hynetstat-output-server')
				}
			};
		}

		// 在 SpeedTest 类的 initState 方法中修改
		initState() {
			this.testing = false;
			this.currentTest = null;
			this.serverConfig = {
				'local': {
					name: 'Hyplus服务器',
					url: 'https://www.hyperplasma.top/speedtest.php',
					location: [0, 0]
				}
			};
			// 默认选择本地服务器
			if(this.elements.serverSelect) {
				this.elements.serverSelect.value = 'local';
			}
		}

		// 初始化历史记录
		initHistory() {
			this.history = JSON.parse(localStorage.getItem('hynetstat-history') || '[]');
			this.updateHistoryDisplay();
		}

		// 计算网络质量评级
		calculateQualityRating(ping, download, upload, loss) {
			// 如果任一指标无效，返回"无信号"
			if (!ping || !download || !upload || loss === undefined) {
				return "无信号";
			}

			// 将速度转换为Mbps便于计算
			const downloadMbps = download / 1000000; // bps to Mbps
			const uploadMbps = upload / 1000000;   // bps to Mbps

			// 各指标权重（总和为100）
			const weights = {
				upload: 65,    // 上传速度权重65%
				download: 20,  // 下载速度权重20%
				ping: 10,      // 延迟权重10%
				loss: 5        // 丢包率权重5%
			};

			// 计算各项得分（每项满分100）
			let scores = {
				// 上传速度评分：基准6Mbps为50分，30Mbps为满分
				upload: Math.min(100, (uploadMbps / 6) * 50),

				// 下载速度评分：基准50Mbps，最高200Mbps
				download: Math.min(100, (downloadMbps / 50) * 100),

				// 延迟评分：基准200ms为0分，3ms为满分
				ping: Math.max(0, 100 - ((ping - 3) * (100 / 197))),

				// 丢包率评分：0%为满分，20%及以上为0分
				loss: Math.max(0, 100 - (loss * 5))
			};

			// 计算加权总分
			const totalScore = Object.keys(weights).reduce((sum, key) => {
				return sum + (scores[key] * weights[key] / 100);
			}, 0);

			// 根据总分确定等级
			if (totalScore >= 95) {
				return "爽";    // 95-100分
			} else if (totalScore >= 90) {
				return "优";    // 90-95分
			} else if (totalScore >= 75) {
				return "良";    // 75-89分
			} else if (totalScore >= 60) {
				return "差";    // 60-74分
			} else if (totalScore >= 30) {
				return "恶";    // 30-59分
			} else {
				return "无信号"; // 0-29分
			}
		}

		// 绑定事件处理
		bindEvents() {
			this.elements.testButton.addEventListener('click', () => this.startTest());
			this.elements.clearButton.addEventListener('click', () => this.clearResults());
			this.elements.unitSelector.addEventListener('change', () => {
				// 更新当前显示的结果
				this.updateResults();
				// 更新历史记录显示
				this.updateHistoryDisplay();
			});
			this.elements.serverSelect.addEventListener('change', () => this.handleServerChange());

			this.elements.clearAllHeader.addEventListener('click', () => {
				// 添加确认对话框
				if (this.history.length > 0 && confirm('确定要清空所有测速记录吗？')) {
					this.clearAllHistory();
				}
			});

			// 添加表格行删除事件委托
			this.elements.historyList.addEventListener('click', (e) => {
				if (e.target.classList.contains('hynetstat-clear-btn')) {
					const row = e.target.closest('tr');
					const index = Array.from(row.parentElement.children).indexOf(row);
					this.removeHistoryItem(index);
				}
			});

			// 防止测速过程中关闭页面
			window.addEventListener('beforeunload', (e) => {
				if (this.testing) {
					e.preventDefault();
					e.returnValue = '测速正在进行中，确定要离开吗？';
					return e.returnValue;
				}
			});
		}

		// 初始化应用
		async init() {
			try {
				await this.getIPInfo();
			} catch (error) {
				console.error('Initialization error:', error);
				hynetstatShowToast('初始化失败，请刷新重试', 'error');
			}
		}

		async getIPInfo() {
			try {
				// 第一步：获取 IP
				const ipApis = [
					'https://api.ipify.org?format=json',
					'https://api64.ipify.org?format=json',
					'https://httpbin.org/ip'
				];

				let ip = null;
				for (const api of ipApis) {
					try {
						const response = await fetch(api);
						const data = await response.json();
						ip = data.ip || data.origin;
						if (ip) break;
					} catch (e) {
						console.warn(`IP API ${api} failed:`, e);
						continue;
					}
				}

				if (!ip) {
					throw new Error('无法获取IP地址');
				}

				// 显示 IP
				this.elements.ip.textContent = `本机IP：${ip}`;
				this.elements.location.textContent = '获取位置信息中...';

				// 第二步：使用获取到的 IP 查询位置信息
				try {
					const locationResponse = await fetch(`https://ipapi.co/${ip}/json/`);
					const locationData = await locationResponse.json();

					if (locationData && !locationData.error) {
						const locationParts = [
							locationData.city,
							locationData.region,
							locationData.country_name
						].filter(part => part && part.length > 0);

						this.elements.location.textContent = locationParts.length > 0 
							? locationParts.join(', ')
						: '位置信息未知';
					} else {
						this.elements.location.textContent = '位置信息未知';
					}
				} catch (locationError) {
					console.warn('Location lookup failed:', locationError);
					this.elements.location.textContent = '位置信息获取失败';
				}

			} catch (error) {
				console.error('IP info error:', error);
				this.elements.ip.textContent = '本机IP：获取失败';
				this.elements.location.textContent = '位置信息获取失败';

				// 3秒后重试
				setTimeout(() => {
					this.getIPInfo().catch(e => {
						hynetstatShowToast('IP信息获取失败，请检查网络连接', 'error');
					});
				}, 3000);
			}
		}

		// 测量服务器延迟
		async measurePing(serverUrl) {
			const start = performance.now();
			try {
				const controller = new AbortController();
				const timeoutId = setTimeout(() => controller.abort(), 2000);

				const response = await fetch(`${serverUrl}?action=ping`, {
					method: 'GET',
					mode: 'cors',
					cache: 'no-store',
					signal: controller.signal
				});

				if (!response.ok) {
					throw new Error('Ping 请求失败');
				}

				clearTimeout(timeoutId);
				return performance.now() - start;
			} catch (error) {
				if (error.name === 'AbortError') {
					throw new Error('服务器响应超时');
				}
				throw new Error('无法连接到服务器');
			}
		}

		// 开始测速
		async startTest() {
			if (this.testing) return;
			this.testing = true;
			this.elements.testButton.disabled = true;
			this.showProgress();

			try {
				const server = this.serverConfig[this.elements.serverSelect.value];
				if (!server) throw new Error('无效的服务器选择');

				// 测试连接性
				await this.checkServerConnection(server.url);

				this.clearResults();

				// 测试网络延迟和抖动
				const pings = [];
				let failed = 0;
				for (let i = 0; i < 10; i++) {
					try {
						const ping = await this.measurePing(server.url);
						pings.push(ping);
					} catch {
						failed++;
				}
						this.updateProgress('测试网络延迟', (i + 1) * 10);
				}

				const avgPing = pings.reduce((a, b) => a + b, 0) / pings.length;
				const jitter = Math.std(pings);
				const loss = (failed / 10) * 100;

				this.updateResults({
					ping: avgPing.toFixed(0) + ' ms',
					jitter: jitter.toFixed(1) + ' ms',
					loss: loss.toFixed(1) + '%'
				});

				// 测试下载速度
				const downloadSpeed = await this.testDownload(server.url);
				this.updateResults({
					download: this.formatSpeed(downloadSpeed)
				});

				// 测试上传速度
				const uploadSpeed = await this.testUpload(server.url);
				this.updateResults({
					upload: this.formatSpeed(uploadSpeed)
				});

				// 计算质量评级
				const qualityRating = this.calculateQualityRating(avgPing, downloadSpeed, uploadSpeed, loss);

				// 更新质量评级显示
				this.updateResults({
					server: qualityRating
				});

				// 保存历史记录
				this.saveHistory({
					time: new Date().toISOString(),
					server: server.name,
					ping: avgPing,
					download: downloadSpeed,
					upload: uploadSpeed,
					quality: qualityRating  // 添加质量评级
				});

				hynetstatShowToast('测速完成', 'success');
			} catch (error) {
				console.error('Speed test error:', error);
				hynetstatShowToast('测速失败：' + error.message, 'error');
			} finally {
				this.testing = false;
				this.elements.testButton.disabled = false;
				this.hideProgress();
			}
		}

		// 测试下载速度
		async testDownload(serverUrl) {
			const chunkSize = 1024 * 1024; // 1MB
			const testDuration = 10000; // 10秒
			const startTime = Date.now();
			let totalBytes = 0;

			try {
				while (Date.now() - startTime < testDuration) {
					const response = await fetch(`${serverUrl}?action=download&size=${chunkSize}`);
					if (!response.ok) throw new Error('下载测试失败');

					const blob = await response.blob();
					totalBytes += blob.size;

					const progress = ((Date.now() - startTime) / testDuration) * 100;
					this.updateProgress('测试下载速度...', progress);
				}

				return (totalBytes * 8) / (testDuration / 1000); // 返回比特每秒
			} catch (error) {
				throw new Error('下载测试失败: ' + error.message);
			}
		}

		// 测试上传速度
		async testUpload(serverUrl) {
			const chunkSize = 1024 * 256; // 256KB
			const testDuration = 10000; // 10秒
			const startTime = Date.now();
			let totalBytes = 0;

			try {
				const chunk = new Blob([new ArrayBuffer(chunkSize)]);

				while (Date.now() - startTime < testDuration) {
					const response = await fetch(`${serverUrl}?action=upload`, {
						method: 'POST',
						body: chunk,
						headers: {
							'Content-Type': 'application/octet-stream'
						}
					});

					if (!response.ok) throw new Error('上传测试失败');

					totalBytes += chunkSize;
					const progress = ((Date.now() - startTime) / testDuration) * 100;
					this.updateProgress('测试上传速度...', progress);
				}

				return (totalBytes * 8) / (testDuration / 1000); // 返回比特每秒
			} catch (error) {
				throw new Error('上传测试失败: ' + error.message);
			}
		}

		// 修改 checkServerConnection 方法
		async checkServerConnection(serverUrl) {
			try {
				console.log('Testing connection to:', serverUrl);

				const response = await fetch(`${serverUrl}?action=status`, {
					method: 'GET',
					mode: 'cors',
					cache: 'no-store',
					headers: {
						'Accept': 'application/json'
					}
				});

				console.log('Server response:', response);

				if (!response.ok) {
					const text = await response.text();
					console.error('Server error response:', text);
					throw new Error(`服务器状态异常 (${response.status}: ${response.statusText})\n${text}`);
				}

				try {
					const data = await response.json();
					console.log('Server data:', data);

					if (!data.ready) {
						throw new Error('服务器未就绪: ' + JSON.stringify(data));
					}

					return data;
				} catch (e) {
					console.error('JSON parse error:', e);
					throw new Error('服务器返回数据格式错误');
				}
			} catch (error) {
				console.error('Server connection error:', error);
				throw new Error(`无法连接到测速服务器: ${error.message}`);
			}
		}

		// 更新进度显示
		updateProgress(text, percent) {
			this.elements.progressContainer.style.display = 'block';
			this.elements.progressBar.style.width = `${percent}%`;
			this.elements.progressText.textContent = text;
		}

		showProgress() {
			this.elements.progressContainer.style.display = 'block';
		}

		hideProgress() {
			this.elements.progressContainer.style.display = 'none';
		}

		// 保存历史记录
		saveHistory(record) {
			record.ip = this.elements.ip && this.elements.ip.textContent
				? (this.elements.ip.textContent.replace(/^本机IP：/, '') || 'unknown')
			: 'unknown';
			this.history.unshift(record);
			if (this.history.length > 10) this.history.pop();
			localStorage.setItem('hynetstat-history', JSON.stringify(this.history));
			this.updateHistoryDisplay();
		}

		// 清空所有记录
		clearAllHistory() {
			this.history = [];
			localStorage.removeItem('hynetstat-history');
			this.updateHistoryDisplay();
			hynetstatShowToast('已清空所有测速记录', 'success');
		}

		// 删除单个历史记录项
		removeHistoryItem(index) {
			this.history.splice(index, 1);
			localStorage.setItem('hynetstat-history', JSON.stringify(this.history));
			this.updateHistoryDisplay();
			hynetstatShowToast('已删除该记录');
		}

		// 更新 updateHistoryDisplay 方法
		updateHistoryDisplay() {
			this.elements.historyList.innerHTML = this.history.map(record => `
<tr>
<td>
<div>${new Date(record.time).toLocaleString()}</div>
<div style="color:#888;font-size:12px;margin-top:2px;">
${(record.ip && record.ip !== "" && record.ip !== "获取中...") ? record.ip : "unknown"}
	</div>
	</td>
<td>${record.server}</td>
<td>${record.ping.toFixed(0)} ms</td>
<td>${this.formatSpeed(record.download)}</td>
<td>${this.formatSpeed(record.upload)}</td>
<td>${record.quality}</td>
<td>
<button class="hynetstat-clear-btn hyplus-unselectable">❌</button>
	</td>
	</tr>
`).join('');
		}

		// 清空当前结果
		clearResults() {
			Object.values(this.elements.results).forEach(el => {
				el.textContent = '-';
				el.classList.add('hynetstat-result-default');
			});
		}

		// 修改 updateResults 方法
		updateResults(results = {}) {
			if (Object.keys(results).length === 0) {
				// 如果没有传入新的结果，说明是单位转换，使用现有的值重新格式化
				const download = this.elements.results.download.textContent;
				const upload = this.elements.results.upload.textContent;

				// 只有在不是默认值"-"的情况下才进行转换
				if (download !== '-') {
					this.elements.results.download.textContent = this.convertSpeedUnit(download);
				}
				if (upload !== '-') {
					this.elements.results.upload.textContent = this.convertSpeedUnit(upload);
				}
			} else {
				// 正常更新新的结果
				for (const [key, value] of Object.entries(results)) {
					const element = this.elements.results[key];
					if (element) {
						element.textContent = value;
						element.classList.remove('hynetstat-result-default');
					}
				}
			}
		}

		convertSpeedUnit(speedText) {
			// 解析原始速度值和单位
			const match = speedText.match(/^([\d.]+)\s+(\w+\/s)$/);
			if (!match) return speedText;

			const [_, value, oldUnit] = match;
			const newUnit = this.elements.unitSelector.value;

			// 如果单位相同，直接返回
			if (oldUnit === newUnit) return speedText;

			// 将值转换为比特每秒(bps)
			let bps = parseFloat(value);
			switch (oldUnit) {
				case 'Kbps': bps *= 1000; break;
				case 'Mbps': bps *= 1000000; break;
				case 'Gbps': bps *= 1000000000; break;
				case 'B/s': bps *= 8; break;
				case 'KB/s': bps *= 8000; break;
				case 'MB/s': bps *= 8000000; break;
				case 'GB/s': bps *= 8000000000; break;
			}

			// 使用 formatSpeed 方法转换为新单位
			return this.formatSpeed(bps);
		}

		// 格式化速度显示
		formatSpeed(bps) {
			const unit = this.elements.unitSelector.value;
			let value = bps;

			switch (unit) {
				case 'Kbps': value /= 1000; break;
				case 'Mbps': value /= 1000000; break;
				case 'Gbps': value /= 1000000000; break;
				case 'B/s': value /= 8; break;
				case 'KB/s': value /= 8000; break;
				case 'MB/s': value /= 8000000; break;
				case 'GB/s': value /= 8000000000; break;
			}

			return `${value.toFixed(2)} ${unit}`;
		}

		// 处理服务器选择变更
		handleServerChange() {
			if (this.elements.serverSelect.value === 'auto') {
				hynetstatShowToast('暂不支持自动选择服务器，已切换至本地Hyplus服务器', 'info');
				this.elements.serverSelect.value = 'local';
			}
		}
	}

	// 工具函数：计算标准差
	Math.std = function(array) {
		const n = array.length;
		if (n === 0) return 0;
		const mean = array.reduce((a, b) => a + b) / n;
		return Math.sqrt(array.map(x => Math.pow(x - mean, 2)).reduce((a, b) => a + b) / n);
	};

	class ApiGod {
		constructor() {
			this.initElements();
			this.initState();
			this.initEditors();
			this.bindEvents();
			this.loadHistory();
		}

		initElements() {
			this.elements = {
				methodSelect: document.getElementById('apigod-method-select'),
				urlInput: document.getElementById('apigod-url-input'),
				sendBtn: document.getElementById('apigod-send-btn'),
				saveBtn: document.getElementById('apigod-save-btn'),
				bodyTypeSelect: document.getElementById('apigod-body-type-select'),
				rawTypeSelect: document.getElementById('apigod-raw-type-select'),
				formDataContainer: document.getElementById('apigod-form-data-container'),
				urlencodedContainer: document.getElementById('apigod-urlencoded-container'),
				rawContainer: document.getElementById('apigod-raw-container'),
				binaryContainer: document.getElementById('apigod-binary-container'),
				binaryFile: document.getElementById('apigod-binary-file'),
				responseStatusCode: document.getElementById('apigod-response-status-code'),
				responseStatusText: document.getElementById('apigod-response-status-text'),
				responseTime: document.getElementById('apigod-response-time'),
				responseSize: document.getElementById('apigod-response-size'),
				responseViewType: document.getElementById('apigod-response-view-type'),
				responseBodyContainer: document.getElementById('apigod-response-body-container'),
				responseCookiesList: document.getElementById('apigod-response-cookies-list'),
				testResults: document.getElementById('apigod-test-results'),
				historyList: document.getElementById('apigod-history-list'),
				copyBtn: document.getElementById('apigod-copy-response'),
				addParamBtn: document.getElementById('apigod-add-param-btn'),
				addHeaderBtn: document.getElementById('apigod-add-header-btn'),
				addCommonHeadersBtn: document.getElementById('apigod-add-common-headers-btn')
			};

			this.resTabBtns = document.querySelectorAll('.apigod-response-tabs .apigod-tab-btn');
			this.resTabContents = [
				document.getElementById('apigod-response-body-tab'),
				document.getElementById('apigod-response-cookies-tab'),
				document.getElementById('apigod-test-results-tab')
			];
			this.reqTabBtns = document.querySelectorAll('.apigod-request-tabs .apigod-tab-btn');
			this.reqTabContents = [
				document.getElementById('apigod-params-tab'),
				document.getElementById('apigod-headers-tab'),
				document.getElementById('apigod-body-tab'),
				document.getElementById('apigod-prereq-tab'),
				document.getElementById('apigod-tests-tab')
			];
		}

		initState() {
			this.currentRequest = {
				method: 'GET',
				url: '',
				params: [],
				headers: [],
				body: null,
				bodyType: 'none',
				rawType: 'json',
				preReqScript: '',
				tests: ''
			};
			this.editors = {
				raw: null,
				preReq: null,
				tests: null
			};
			this.lastResponse = null;
			this.isSending = false;
		}

		async initEditors() {
			require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' }});
			require(['vs/editor/editor.main'], () => {
				const commonOptions = {
					theme: 'vs',
					minimap: { enabled: false },
					automaticLayout: true,
					fontSize: 14,
					lineNumbers: 'on',
					scrollBeyondLastLine: false,
					wordWrap: 'on',
					formatOnPaste: true,
					formatOnType: true
				};

				this.editors.raw = monaco.editor.create(document.getElementById('apigod-raw-editor'), {
					...commonOptions,
					value: '',
					language: 'json'
				});

				this.editors.preReq = monaco.editor.create(document.getElementById('apigod-prereq-editor'), {
					...commonOptions,
					value: '',
					language: 'javascript'
				});

				this.editors.tests = monaco.editor.create(document.getElementById('apigod-tests-editor'), {
					...commonOptions,
					value: '',
					language: 'javascript'
				});
			});
		}
		bindEvents() {
			// 请求Tab切换
			this.reqTabBtns.forEach((btn, idx) => {
				btn.addEventListener('click', () => {
					this.reqTabBtns.forEach(b => b.classList.remove('active'));
					btn.classList.add('active');
					this.reqTabContents.forEach((c, i) => 
												c.classList.toggle('active', i === idx)
											   );
					if (['raw', 'prereq', 'tests'][idx-2]) {
						setTimeout(() => this.editors[['raw','preReq','tests'][idx-2]]?.layout(), 0);
								   }
								   });
								   });

								   // 响应Tab切换
								   this.resTabBtns.forEach((btn, idx) => {
								   btn.addEventListener('click', () => {
								   this.resTabBtns.forEach(b => b.classList.remove('active'));
								   btn.classList.add('active');
								   this.resTabContents.forEach((c, i) => 
								   c.classList.toggle('active', i === idx)
								  );
								   });
								   });

								   // Method变更
								   this.elements.methodSelect.addEventListener('change', () => {
								   this.currentRequest.method = this.elements.methodSelect.value;
								   });

								   // Body类型切换
								   this.elements.bodyTypeSelect.addEventListener('change', () => this.handleBodyTypeChange());
								   this.elements.rawTypeSelect.addEventListener('change', () => this.handleRawTypeChange());

								   // Send/Save
								   this.elements.sendBtn.addEventListener('click', () => this.sendRequest());
								   this.elements.saveBtn.addEventListener('click', () => this.saveRequest());

								   // 响应视图切换和复制
								   this.elements.responseViewType.addEventListener('change', () => this.updateResponseView(true));
								   this.elements.copyBtn?.addEventListener('click', () => this.copyResponseToClipboard());

								   // 添加参数/Header行
								   this.elements.addParamBtn?.addEventListener('click', () => this.addParamRow());
								   this.elements.addHeaderBtn?.addEventListener('click', () => this.addHeaderRow());
								   this.elements.addCommonHeadersBtn?.addEventListener('click', () => this.addCommonHeaders());

								   // 参数表格删除
								   document.getElementById('apigod-params-list')?.addEventListener('click', (e) => {
								   if (e.target.classList.contains('apigod-row-delete-btn')) {
								   e.target.closest('tr').remove();
								   }
								   });

								   document.getElementById('apigod-headers-list')?.addEventListener('click', (e) => {
								   if (e.target.classList.contains('apigod-row-delete-btn')) {
								   e.target.closest('tr').remove();
								   }
								   });
								   }

								   handleBodyTypeChange() {
								   const bodyType = this.elements.bodyTypeSelect.value;
								   this.currentRequest.bodyType = bodyType;
								   this.elements.rawTypeSelect.style.display = bodyType === 'raw' ? '' : 'none';
								   this.elements.formDataContainer.style.display = bodyType === 'form-data' ? '' : 'none';
								   this.elements.urlencodedContainer.style.display = bodyType === 'x-www-form-urlencoded' ? '' : 'none';
								   this.elements.rawContainer.style.display = bodyType === 'raw' ? '' : 'none';
								   this.elements.binaryContainer.style.display = bodyType === 'binary' ? '' : 'none';
								   if (bodyType === 'raw') {
							setTimeout(() => this.editors.raw?.layout(), 0);
									   }
									   }

									   handleRawTypeChange() {
									   const rawType = this.elements.rawTypeSelect.value;
									   this.currentRequest.rawType = rawType;
									   if (this.editors.raw) {
									   const language = {
									   'json': 'json',
									   'xml': 'xml',
									   'html': 'html',
									   'text': 'plaintext'
									   }[rawType] || 'plaintext';
									   monaco.editor.setModelLanguage(this.editors.raw.getModel(), language);
						}
					}

					addParamRow() {
						const row = document.createElement('tr');
						row.className = 'apigod-param-row';
						row.innerHTML = `
<td><input type="checkbox" class="apigod-param-enabled" checked></td>
<td><input type="text" class="apigod-input" placeholder="参数名"></td>
<td><input type="text" class="apigod-input" placeholder="参数值"></td>
<td><input type="text" class="apigod-input" placeholder="描述（可选）"></td>
<td><button class="apigod-row-delete-btn">❌</button></td>
`;
						document.getElementById('apigod-params-list').appendChild(row);
					}

					addHeaderRow() {
						const row = document.createElement('tr');
						row.className = 'apigod-header-row';
						row.innerHTML = `
<td><input type="checkbox" class="apigod-header-enabled" checked></td>
<td><input type="text" class="apigod-input" placeholder="Header名"></td>
<td><input type="text" class="apigod-input" placeholder="Header值"></td>
<td><input type="text" class="apigod-input" placeholder="描述（可选）"></td>
<td><button class="apigod-row-delete-btn">❌</button></td>
`;
						document.getElementById('apigod-headers-list').appendChild(row);
					}

					addCommonHeaders() {
						const commonHeaders = {
							'Content-Type': 'application/json',
							'Accept': 'application/json',
							'Authorization': 'Bearer ',
							'User-Agent': 'API-GOD/1.0.0',
							'Accept-Language': 'zh-CN,zh;q=0.9,en;q=0.8',
							'Cache-Control': 'no-cache',
							'X-Requested-With': 'XMLHttpRequest'
						};
						Object.entries(commonHeaders).forEach(([name, value]) => {
							const row = document.createElement('tr');
							row.className = 'apigod-header-row';
							row.innerHTML = `
<td><input type="checkbox" class="apigod-header-enabled" checked></td>
<td><input type="text" class="apigod-input" value="${name}"></td>
<td><input type="text" class="apigod-input" value="${value}"></td>
<td><input type="text" class="apigod-input" placeholder="描述（可选）"></td>
<td><button class="apigod-row-delete-btn">❌</button></td>
`;
							document.getElementById('apigod-headers-list').appendChild(row);
						});
					}

					async sendRequest() {
						if (this.isSending) return;
						this.isSending = true;
						this.elements.sendBtn.disabled = true;

						try {
							const requestData = this.collectRequestData();
							if (!requestData.url) throw new Error('请输入请求URL');

							let url = new URL(requestData.url, location.origin);
							requestData.params.forEach(param => {
								url.searchParams.append(encodeURIComponent(param.key), encodeURIComponent(param.value));
							});

							if (requestData.preReqScript) {
								try {
									eval(requestData.preReqScript);
								} catch (error) {
									throw new Error('Pre-request script error: ' + error.message);
								}
							}

							const config = {
								method: requestData.method,
								headers: requestData.headers,
								credentials: 'include'
							};

							if (requestData.bodyType === 'raw') {
								if (requestData.rawType === 'json') {
									config.headers['Content-Type'] = 'application/json';
									config.body = typeof requestData.body === 'string' ? requestData.body : JSON.stringify(requestData.body);
								} else {
									config.body = requestData.body;
								}
							} else if (requestData.bodyType === 'binary' && requestData.body) {
								config.body = requestData.body;
							}

							const startTime = performance.now();
							const response = await fetch(url.toString(), config);
							const endTime = performance.now();

							const responseData = await this.getResponseData(response);
							const size = this.calculateResponseSize(responseData);

							this.lastResponse = {
								status: response.status,
								statusText: response.statusText,
								headers: Object.fromEntries(response.headers.entries()),
								body: responseData,
								time: Math.round(endTime - startTime),
								size: size,
								contentType: response.headers.get('content-type') || ''
							};

							this.updateResponseUI();
							if (requestData.tests) this.runTests(requestData.tests);

						} catch (error) {
							this.showToast(error.message, 'error');
							this.updateResponseError(error);
						} finally {
							this.isSending = false;
							this.elements.sendBtn.disabled = false;
						}
					}

					collectRequestData() {
						const params = [];
						document.querySelectorAll('#apigod-params-list tr').forEach(row => {
							const enabled = row.querySelector('.apigod-param-enabled')?.checked;
							const inputs = row.querySelectorAll('input[type="text"]');
							if (enabled && inputs[0]?.value) {
							params.push({
							key: inputs[0].value,
								  value: inputs[1]?.value || '',
									  description: inputs[2]?.value || ''
						});
						}
						});

								  const headers = {};
								  document.querySelectorAll('#apigod-headers-list tr').forEach(row => {
								  const enabled = row.querySelector('.apigod-header-enabled')?.checked;
								  const inputs = row.querySelectorAll('input[type="text"]');
								  if (enabled && inputs[0]?.value) {
								  headers[inputs[0].value] = inputs[1]?.value || '';
						}
						});

								  let body = null;
								  const bodyType = this.elements.bodyTypeSelect.value;

								  if (bodyType === 'raw') {
								  body = this.editors.raw.getValue();
								  if (this.currentRequest.rawType === 'json') {
								  try {
								  body = JSON.parse(body);
						} catch (_) {}
						}
						} else if (bodyType === 'binary') {
								  body = this.elements.binaryFile.files[0] || null;
						}

								  return {
								  method: this.elements.methodSelect.value,
									  url: this.elements.urlInput.value,
										  params,
										  headers,
										  body,
										  bodyType,
										  rawType: this.elements.rawTypeSelect.value,
											  preReqScript: this.editors.preReq.getValue(),
												  tests: this.editors.tests.getValue()
						};
																					}

																					async getResponseData(response) {
							const contentType = response.headers.get('content-type') || '';
							if (contentType.includes('application/json')) {
								return await response.json();
							} else if (contentType.includes('xml') || 
									   contentType.includes('html') || 
									   contentType.includes('text/')) {
								return await response.text();
							} else {
								return await response.blob();
							}
						}

						calculateResponseSize(data) {
							if (data instanceof Blob) return data.size;
							if (typeof data === 'string') return new Blob([data]).size;
							return new Blob([JSON.stringify(data)]).size;
						}

						formatSize(bytes) {
							const units = ['B', 'KB', 'MB', 'GB'];
							let size = bytes, unitIndex = 0;
							while (size >= 1024 && unitIndex < units.length - 1) {
								size /= 1024;
								unitIndex++;
							}
							return `${size.toFixed(1)} ${units[unitIndex]}`;
						}

						updateResponseUI() {
							if (!this.lastResponse) return;

							this.elements.responseStatusCode.textContent = this.lastResponse.status;
							this.elements.responseStatusText.textContent = this.lastResponse.statusText;
							this.elements.responseTime.textContent = this.lastResponse.time;
							this.elements.responseSize.textContent = this.formatSize(this.lastResponse.size);

							this.autoSelectResponseViewType();
							this.updateResponseView();
							this.updateResponseCookies();
						}

						autoSelectResponseViewType() {
							if (!this.lastResponse) return;
							const ct = this.lastResponse.contentType.toLowerCase();
							const sel = this.elements.responseViewType;

							if (ct.includes('json')) sel.value = 'json';
							else if (ct.includes('xml')) sel.value = 'xml';
							else if (ct.includes('html')) sel.value = 'html';
							else if (ct.includes('javascript')) sel.value = 'js';
							else sel.value = 'raw';
						}

						updateResponseView(forceManual = false) {
							if (!this.lastResponse) return;

							const viewType = this.elements.responseViewType.value;
							const container = this.elements.responseBodyContainer;
							container.innerHTML = '';
							container.className = 'apigod-response-container selectable';

							if (!forceManual) this.autoSelectResponseViewType();

							try {
								if (this.lastResponse.body instanceof Blob) {
									const link = document.createElement('a');
									link.href = URL.createObjectURL(this.lastResponse.body);
									link.download = 'response.bin';
									link.textContent = '下载文件';
									container.appendChild(link);
								} else if (viewType === 'json' && typeof this.lastResponse.body === 'object') {
									const formatter = new JSONFormatter(this.lastResponse.body, 3);
									container.appendChild(formatter.render());
								} else if (viewType === 'xml' || viewType === 'html') {
									const pre = document.createElement('pre');
									let code = this.lastResponse.body;
									if (typeof code !== 'string') code = JSON.stringify(code, null, 2);
									try {
										code = prettier.format(code, {
											parser: viewType,
											plugins: prettierPlugins
										});
									} catch(e) {}
									pre.textContent = code;
									container.appendChild(pre);
								} else if (viewType === 'js') {
									const pre = document.createElement('pre');
									let code = this.lastResponse.body;
									if (typeof code !== 'string') code = JSON.stringify(code, null, 2);
									try {
										code = prettier.format(code, {
											parser: 'babel',
											plugins: prettierPlugins
										});
									} catch(e) {}
									pre.textContent = code;
									container.appendChild(pre);
								} else {
									const pre = document.createElement('pre');
									if (typeof this.lastResponse.body === 'object') {
										pre.textContent = JSON.stringify(this.lastResponse.body, null, 2);
									} else {
										pre.textContent = this.lastResponse.body;
									}
									container.appendChild(pre);
								}
							} catch (error) {
								container.textContent = '无法显示响应数据：' + error.message;
							}
						}

						copyResponseToClipboard() {
							const container = this.elements.responseBodyContainer;
							const text = container.innerText || container.textContent;

							if (navigator.clipboard && window.isSecureContext) {
								navigator.clipboard.writeText(text).then(() => {
									this.showToast('已复制到剪贴板');
								}).catch(() => {
									this.showToast('复制失败', 'error');
								});
							} else {
								const textarea = document.createElement('textarea');
								textarea.value = text;
								textarea.style.position = 'fixed';
								textarea.style.left = '-9999px';
								document.body.appendChild(textarea);
								textarea.select();
								try {
									document.execCommand('copy');
									this.showToast('已复制到剪贴板');
								} catch (err) {
									this.showToast('复制失败', 'error');
								}
								document.body.removeChild(textarea);
							}
						}

						updateResponseCookies() {
							const cookiesList = this.elements.responseCookiesList;
							cookiesList.innerHTML = '';

							const cookieHeader = this.lastResponse.headers['set-cookie'];
							if (cookieHeader) {
								const cookies = Array.isArray(cookieHeader) ? cookieHeader : [cookieHeader];
								cookies.forEach(cookie => {
									const parts = cookie.split(';').map(part => part.trim());
									const [nameValue, ...attributes] = parts;
									const [name, value] = nameValue.split('=').map(s => s.trim());

									const row = document.createElement('tr');
									row.innerHTML = `
<td>${name}</td>
<td>${value}</td>
<td>${attributes.find(a=>a.startsWith('Domain='))?.split('=')[1] || '-'}</td>
										<td>${attributes.find(a=>a.startsWith('Expires='))?.split('=')[1] || '-'}</td>
									`;
cookiesList.appendChild(row);
});
}
}

runTests(testScript) {
const testResults = this.elements.testResults;
testResults.innerHTML = '';

try {
const tests = {
results: [],
assert: function(condition, message) {
if (!condition) throw new Error(message);
}
};

const response = this.lastResponse;
eval(testScript);

if (tests.results.length > 0) {
const resultsList = document.createElement('ul');
resultsList.className = 'apigod-test-results-list';
tests.results.forEach(result => {
const li = document.createElement('li');
li.className = `apigod-test-result ${result.success ? 'success' : 'failure'}`;
									li.textContent = result.message;
									resultsList.appendChild(li);
								});
								testResults.appendChild(resultsList);
							} else {
								testResults.textContent = '没有执行任何测试';
							}
						} catch (error) {
							testResults.innerHTML = `<div class="apigod-test-error">测试执行错误：${error.message}</div>`;
						}
					}

					saveRequest() {
						try {
							const requestData = this.collectRequestData();
							if (!requestData.url) throw new Error('请输入请求URL');

							const desc = prompt('请输入保存描述：');
							if (!desc) return;

							const history = JSON.parse(localStorage.getItem('apigod-history') || '[]');
							const record = {
								id: Date.now(),
								name: desc,
								timestamp: new Date().toISOString(),
								data: requestData
							};

							history.unshift(record);
							if (history.length > 50) history.pop();

							localStorage.setItem('apigod-history', JSON.stringify(history));
							this.updateHistoryList();
							this.showToast('请求已保存', 'success');
						} catch (error) {
							this.showToast(error.message, 'error');
						}
					}

					loadHistory() {
						this.updateHistoryList();
						this.bindHistoryEvents();
					}

					updateHistoryList() {
						const history = JSON.parse(localStorage.getItem('apigod-history') || '[]');
						const list = this.elements.historyList;
						if (!list) return;

						list.innerHTML = history.map(record => `
<tr data-id="${record.id}">
<td>${new Date(record.timestamp).toLocaleString()}</td>
<td>${record.data.method}</td>
<td title="${record.data.url}">${record.data.url}</td>
<td>${record.name}</td>
<td>
<button class="apigod-history-use-btn">使用</button>
<button class="apigod-history-delete-btn">❌</button>
	</td>
	</tr>
`).join('');
					}

					bindHistoryEvents() {
						this.elements.historyList?.addEventListener('click', (e) => {
						const row = e.target.closest('tr');
						if (!row) return;

						const id = parseInt(row.dataset.id);
						const history = JSON.parse(localStorage.getItem('apigod-history') || '[]');
						const record = history.find(r => r.id === id);
						if (!record) return;

						if (e.target.classList.contains('apigod-history-use-btn')) {
						this.loadRequest(record.data);
					} else if (e.target.classList.contains('apigod-history-delete-btn')) {
						if (confirm('确定要删除这条记录吗？')) {
						const newHistory = history.filter(r => r.id !== id);
						localStorage.setItem('apigod-history', JSON.stringify(newHistory));
						this.updateHistoryList();
						this.showToast('记录已删除');
					}
					}
					});
					}

						loadRequest(requestData) {
						this.elements.methodSelect.value = requestData.method;
						this.elements.urlInput.value = requestData.url;

						document.getElementById('apigod-params-list').innerHTML = '';
						requestData.params.forEach(param => {
						this.addParamRow();
						const row = document.querySelector('#apigod-params-list tr:last-child');
						const inputs = row.querySelectorAll('input[type="text"]');
						inputs[0].value = param.key;
						inputs[1].value = param.value;
						inputs[2].value = param.description || '';
					});

						document.getElementById('apigod-headers-list').innerHTML = '';
						Object.entries(requestData.headers).forEach(([name, value]) => {
						this.addHeaderRow();
						const row = document.querySelector('#apigod-headers-list tr:last-child');
						const inputs = row.querySelectorAll('input[type="text"]');
						inputs[0].value = name;
						inputs[1].value = value;
					});

						this.elements.bodyTypeSelect.value = requestData.bodyType;
						this.handleBodyTypeChange();

						if (requestData.bodyType === 'raw') {
						this.elements.rawTypeSelect.value = requestData.rawType;
						this.handleRawTypeChange();
						this.editors.raw.setValue(
						typeof requestData.body === 'string' 
							? requestData.body 
						: JSON.stringify(requestData.body, null, 2)
						);
					}

					this.editors.preReq.setValue(requestData.preReqScript || '');
					this.editors.tests.setValue(requestData.tests || '');
					this.showToast('请求已加载', 'success');
				}

									 updateResponseError(error) {
					this.elements.responseStatusCode.textContent = '---';
					this.elements.responseStatusText.textContent = '请求失败';
					this.elements.responseTime.textContent = '---';
					this.elements.responseSize.textContent = '---';
					const container = this.elements.responseBodyContainer;
					container.innerHTML = `<div class="apigod-error-message">${error.message}</div>`;
				}

				showToast(message, type = 'info') {
					let toast = document.getElementById('apigod-toast');
					if (toast) toast.remove();

					toast = document.createElement('div');
					toast.id = 'apigod-toast';
					toast.className = `apigod-toast apigod-toast-${type}`;
					toast.textContent = message;

					document.body.appendChild(toast);
					setTimeout(() => toast.classList.add('show'), 10);
					setTimeout(() => {
						toast.classList.remove('show');
						setTimeout(() => toast.remove(), 300);
					}, 3000);
				}
			}

									// 初始化prettierPlugins全局变量
									window.prettierPlugins = window.prettierPlugins || [
									window.prettierPlugins,
									window.prettierPluginsBabel,
									window.prettierPluginsHtml
									].filter(Boolean).flat();

			if (!window.prettierPlugins.length) {
				window.prettierPlugins = [];
				if (window.prettierPluginsBabel) {
					window.prettierPlugins.push(window.prettierPluginsBabel);
				}
				if (window.prettierPluginsHtml) {
					window.prettierPlugins.push(window.prettierPluginsHtml);
				}
				if (window.prettier && window.prettierPlugins) {
					window.prettierPlugins = window.prettierPlugins;
				}
			}

			// DOM加载完成后初始化
			document.addEventListener('DOMContentLoaded', () => {
				window.apiGod = new ApiGod();
			});

			// 错误处理
			window.addEventListener('error', (event) => {
				console.error('API-GOD Error:', event.error);
				if (window.apiGod) {
					window.apiGod.showToast('发生错误: ' + event.error.message, 'error');
				}
			});

			window.addEventListener('unhandledrejection', (event) => {
				console.error('API-GOD Unhandled Promise Rejection:', event.reason);
				if (window.apiGod) {
					window.apiGod.showToast('Promise错误: ' + event.reason.message, 'error');
				}
			});

			// 初始化应用
			document.addEventListener('DOMContentLoaded', () => {
				new SpeedTest();
			});
</script>