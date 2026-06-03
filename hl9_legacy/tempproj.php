<?php
/**
 * YuzuLive - 实时数据交互平台
 * Description: 通过 WebSocket 连接本地服务，实现实时数据展示（表格、图表等）
 * Code type: universal
 * Shortcode: [yuzulive]
 * Dependencies: HyplusCSS (使用全局样式变量)
 */

// 注册短代码
add_shortcode('yuzulive', 'yuzulive_render_shortcode');

function yuzulive_render_shortcode($atts) {
    ob_start();
    ?>

<!-- YuzuLive Container -->
<div id="yuzulive-container" class="yuzulive-container">
    <!-- 顶部导航栏 -->
    <div class="yuzulive-header">
        <div class="yuzulive-title">
            <span class="yuzulive-icon">🔮</span>
            <span>YuzuLive</span>
        </div>
        <div class="yuzulive-controls">
            <div class="connection-status" id="connectionStatus">
                <span class="status-dot offline"></span>
                <span class="status-text">未连接</span>
            </div>
            <button class="yuzulive-btn" id="connectBtn">
                <span class="btn-icon">🔗</span>
                连接
            </button>
            <button class="yuzulive-btn" id="disconnectBtn" disabled>
                <span class="btn-icon">✖️</span>
                断开
            </button>
            <button class="yuzulive-btn" id="clearBtn">
                <span class="btn-icon">🗑️</span>
                清空
            </button>
        </div>
    </div>

    <!-- 通道选择器 -->
    <div class="yuzulive-channel-selector">
        <label class="channel-label">数据通道:</label>
        <select id="channelSelector" class="channel-select">
            <option value="all">全部通道</option>
            <option value="training">训练数据</option>
            <option value="log">日志输出</option>
            <option value="metric">指标数据</option>
            <option value="custom">自定义</option>
        </select>
        <label class="display-label">展示方式:</label>
        <select id="displayMode" class="display-select">
            <option value="table">表格</option>
            <option value="chart">图表</option>
            <option value="log">日志</option>
            <option value="metric">指标卡片</option>
        </select>
    </div>

    <!-- 主内容区域 -->
    <div class="yuzulive-content">
        <!-- 表格视图 -->
        <div id="tableView" class="view-panel active">
            <div class="table-container">
                <table id="dataTable" class="yuzulive-table">
                    <thead>
                        <tr>
                            <th>时间</th>
                            <th>通道</th>
                            <th>类型</th>
                            <th>数据</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
                <div id="emptyTableMsg" class="empty-message">
                    <span class="empty-icon">📭</span>
                    <p>暂无数据，请连接到本地服务</p>
                </div>
            </div>
        </div>

        <!-- 图表视图 -->
        <div id="chartView" class="view-panel">
            <div class="chart-container">
                <canvas id="yuzuChart"></canvas>
                <div id="emptyChartMsg" class="empty-message">
                    <span class="empty-icon">📊</span>
                    <p>等待数据中...</p>
                </div>
            </div>
            <div class="chart-controls">
                <button class="chart-btn" id="chartClearBtn">清空图表</button>
                <button class="chart-btn" id="chartPauseBtn">暂停更新</button>
            </div>
        </div>

        <!-- 日志视图 -->
        <div id="logView" class="view-panel">
            <div class="log-container" id="logContainer"></div>
            <div id="emptyLogMsg" class="empty-message">
                <span class="empty-icon">📝</span>
                <p>暂无日志</p>
            </div>
        </div>

        <!-- 指标卡片视图 -->
        <div id="metricView" class="view-panel">
            <div class="metric-grid" id="metricGrid"></div>
            <div id="emptyMetricMsg" class="empty-message">
                <span class="empty-icon">📈</span>
                <p>暂无指标数据</p>
            </div>
        </div>
    </div>

    <!-- 数据统计 -->
    <div class="yuzulive-footer">
        <div class="stats">
            <span class="stat-item">
                <span class="stat-label">接收数据:</span>
                <span class="stat-value" id="dataCount">0</span>
            </span>
            <span class="stat-item">
                <span class="stat-label">连接时长:</span>
                <span class="stat-value" id="connectionTime">00:00:00</span>
            </span>
            <span class="stat-item">
                <span class="stat-label">延迟:</span>
                <span class="stat-value" id="latency">--</span>
            </span>
        </div>
        <div class="footer-info">
            <span>YuzuLive v1.0</span>
            <span class="separator">|</span>
            <span>本地 WebSocket 连接</span>
        </div>
    </div>
</div>

<!-- YuzuLive 设置面板 -->
<div id="yuzuSettingsModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div><strong>⚙️ YuzuLive 设置</strong></div>
            <button class="modal-close" id="closeSettings">✕</button>
        </div>
        <div class="modal-body">
            <div class="setting-group">
                <label>WebSocket 端口:</label>
                <input type="number" id="wsPort" value="8765" min="1" max="65535">
            </div>
            <div class="setting-group">
                <label>自动重连:</label>
                <input type="checkbox" id="autoReconnect" checked>
            </div>
            <div class="setting-group">
                <label>数据保留条数:</label>
                <input type="number" id="maxDataCount" value="1000" min="10" max="10000">
            </div>
            <div class="setting-group">
                <label>图表自动滚动:</label>
                <input type="checkbox" id="autoScroll" checked>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn cancel" id="cancelSettings">取消</button>
            <button class="modal-btn confirm" id="saveSettings">保存</button>
        </div>
    </div>
</div>

<style>
/* YuzuLive 主容器 */
.yuzulive-container {
    max-width: 1200px;
    margin: 0 auto;
    background: var(--hyplus-bg-container-solid);
    border-radius: 16px;
    border: 1px solid var(--hyplus-border-color-light);
    box-shadow: 0 4px 20px var(--hyplus-shadow-deep);
    overflow: hidden;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* 顶部导航栏 */
.yuzulive-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    background: linear-gradient(135deg, var(--hyplus-bg-button-light) 0%, var(--hyplus-bg-container) 100%);
    border-bottom: 1px solid var(--hyplus-border-color-light);
}

.yuzulive-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: bold;
    color: var(--hyplus-text-title);
}

.yuzulive-icon {
    font-size: 24px;
}

.yuzulive-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.connection-status {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: var(--hyplus-bg-settings);
    border-radius: 20px;
    margin-right: 10px;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #ccc;
}

.status-dot.online {
    background: #28a745;
    animation: pulse 2s infinite;
}

.status-dot.offline {
    background: #dc3545;
}

.status-dot.connecting {
    background: #ffc107;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.status-text {
    font-size: 13px;
    color: var(--hyplus-text-gray);
}

/* 按钮样式 */
.yuzulive-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: var(--hyplus-bg-button-light);
    border: 1px solid var(--hyplus-border-color-light2);
    border-radius: 8px;
    color: var(--hyplus-text-nav-link);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.yuzulive-btn:hover:not(:disabled) {
    background: var(--hyplus-bg-button-hover);
    border-color: var(--hyplus-border-color-light);
    transform: translateY(-1px);
}

.yuzulive-btn:active:not(:disabled) {
    transform: translateY(0);
}

.yuzulive-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-icon {
    font-size: 14px;
}

/* 通道选择器 */
.yuzulive-channel-selector {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 20px;
    background: var(--hyplus-bg-settings);
    border-bottom: 1px solid var(--hyplus-border-color-light);
}

.channel-label, .display-label {
    font-size: 13px;
    color: var(--hyplus-text-gray);
    font-weight: 500;
}

.channel-select, .display-select {
    padding: 6px 12px;
    border: 1px solid var(--hyplus-border-color-neutral);
    border-radius: 6px;
    background: white;
    color: var(--hyplus-text-primary);
    font-size: 13px;
    cursor: pointer;
}

/* 主内容区域 */
.yuzulive-content {
    min-height: 400px;
    position: relative;
}

.view-panel {
    display: none;
    padding: 20px;
    height: 400px;
    overflow: auto;
}

.view-panel.active {
    display: block;
}

/* 表格视图 */
.table-container {
    height: 100%;
    overflow: auto;
}

.yuzulive-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px var(--hyplus-shadow-light);
}

.yuzulive-table th {
    background: var(--hyplus-bg-button-light);
    padding: 10px 15px;
    text-align: left;
    font-weight: 600;
    color: var(--hyplus-text-heading);
    font-size: 13px;
    border-bottom: 2px solid var(--hyplus-border-color-light);
}

.yuzulive-table td {
    padding: 10px 15px;
    border-bottom: 1px solid var(--hyplus-border-color-neutral);
    font-size: 13px;
    color: var(--hyplus-text-primary);
}

.yuzulive-table tr:hover td {
    background: var(--hyplus-bg-container);
}

/* 图表视图 */
.chart-container {
    height: 350px;
    position: relative;
}

#yuzuChart {
    width: 100% !important;
    height: 100% !important;
}

.chart-controls {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.chart-btn {
    padding: 6px 14px;
    background: var(--hyplus-bg-button-light);
    border: 1px solid var(--hyplus-border-color-light);
    border-radius: 6px;
    color: var(--hyplus-text-nav-link);
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.chart-btn:hover {
    background: var(--hyplus-bg-button-hover);
}

/* 日志视图 */
.log-container {
    height: 100%;
    overflow: auto;
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 12px;
}

.log-entry {
    padding: 8px 12px;
    border-bottom: 1px solid var(--hyplus-border-color-neutral);
    display: flex;
    gap: 12px;
}

.log-time {
    color: var(--hyplus-text-gray);
    font-weight: 500;
    min-width: 80px;
}

.log-channel {
    color: var(--hyplus-primary-link-color);
    font-weight: 600;
    min-width: 80px;
}

.log-content {
    color: var(--hyplus-text-primary);
    flex: 1;
}

.log-entry.error {
    background: rgba(220, 53, 69, 0.05);
}

.log-entry.warning {
    background: rgba(255, 193, 7, 0.05);
}

/* 指标卡片视图 */
.metric-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.metric-card {
    background: white;
    border-radius: 12px;
    padding: 16px;
    border: 1px solid var(--hyplus-border-color-light);
    box-shadow: 0 2px 8px var(--hyplus-shadow-light);
}

.metric-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.metric-name {
    font-size: 13px;
    color: var(--hyplus-text-gray);
}

.metric-icon {
    font-size: 18px;
}

.metric-value {
    font-size: 28px;
    font-weight: bold;
    color: var(--hyplus-text-title);
}

.metric-unit {
    font-size: 14px;
    color: var(--hyplus-text-gray);
    margin-left: 5px;
}

.metric-change {
    font-size: 12px;
    margin-top: 5px;
}

.metric-change.positive {
    color: #28a745;
}

.metric-change.negative {
    color: #dc3545;
}

/* 空状态消息 */
.empty-message {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.empty-icon {
    font-size: 48px;
    display: block;
    margin-bottom: 15px;
}

.empty-message p {
    color: var(--hyplus-text-gray);
    font-size: 14px;
}

/* 底部统计栏 */
.yuzulive-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: var(--hyplus-bg-settings);
    border-top: 1px solid var(--hyplus-border-color-light);
}

.stats {
    display: flex;
    gap: 20px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-label {
    font-size: 12px;
    color: var(--hyplus-text-gray);
}

.stat-value {
    font-size: 13px;
    font-weight: bold;
    color: var(--hyplus-text-title);
}

.footer-info {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: var(--hyplus-text-gray);
}

.separator {
    color: var(--hyplus-border-color-neutral);
}

/* 设置模态框 */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.modal-overlay.show {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 450px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: var(--hyplus-bg-button-light);
    border-bottom: 1px solid var(--hyplus-border-color-light);
}

.modal-header h3 {
    margin: 0;
    color: var(--hyplus-text-title);
    font-size: 16px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: var(--hyplus-text-gray);
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: var(--hyplus-text-primary);
}

.modal-body {
    padding: 20px;
}

.setting-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.setting-group label {
    font-size: 14px;
    color: var(--hyplus-text-primary);
}

.setting-group input[type="number"] {
    padding: 6px 10px;
    border: 1px solid var(--hyplus-border-color-neutral);
    border-radius: 6px;
    width: 100px;
    font-size: 14px;
}

.setting-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 15px 20px;
    border-top: 1px solid var(--hyplus-border-color-light);
}

.modal-btn {
    padding: 8px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
}

.modal-btn.cancel {
    background: var(--hyplus-bg-button-light);
    color: var(--hyplus-text-gray);
}

.modal-btn.cancel:hover {
    background: var(--hyplus-bg-button-hover);
}

.modal-btn.confirm {
    background: var(--hyplus-progress-bar-color);
    color: white;
}

.modal-btn.confirm:hover {
    background: var(--hyplus-btn-back-hover);
}

/* 响应式设计 */
@media (max-width: 768px) {
    .yuzulive-header {
        flex-direction: column;
        gap: 12px;
        padding: 12px 15px;
    }
    
    .yuzulive-controls {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .yuzulive-channel-selector {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .view-panel {
        height: 300px;
        padding: 10px;
    }
    
    .metric-grid {
        grid-template-columns: 1fr;
    }
    
    .yuzulive-footer {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}
</style>

<script>
// YuzuLive 核心 JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // 全局状态
    const state = {
        ws: null,
        isConnected: false,
        dataCount: 0,
        connectionStartTime: null,
        connectionTimer: null,
        latency: '--',
        dataHistory: [],
        chartData: {
            labels: [],
            datasets: [{
                label: '数值',
                data: [],
                borderColor: '#2196f3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        chartInstance: null,
        chartPaused: false,
        settings: {
            port: 8765,
            autoReconnect: true,
            maxDataCount: 1000,
            autoScroll: true
        }
    };

    // DOM 元素
    const elements = {
        connectionStatus: document.getElementById('connectionStatus'),
        statusDot: document.querySelector('.status-dot'),
        statusText: document.querySelector('.status-text'),
        connectBtn: document.getElementById('connectBtn'),
        disconnectBtn: document.getElementById('disconnectBtn'),
        clearBtn: document.getElementById('clearBtn'),
        channelSelector: document.getElementById('channelSelector'),
        displayMode: document.getElementById('displayMode'),
        tableView: document.getElementById('tableView'),
        chartView: document.getElementById('chartView'),
        logView: document.getElementById('logView'),
        metricView: document.getElementById('metricView'),
        tableBody: document.getElementById('tableBody'),
        emptyTableMsg: document.getElementById('emptyTableMsg'),
        emptyChartMsg: document.getElementById('emptyChartMsg'),
        emptyLogMsg: document.getElementById('emptyLogMsg'),
        emptyMetricMsg: document.getElementById('emptyMetricMsg'),
        logContainer: document.getElementById('logContainer'),
        metricGrid: document.getElementById('metricGrid'),
        dataCount: document.getElementById('dataCount'),
        connectionTime: document.getElementById('connectionTime'),
        latency: document.getElementById('latency'),
        chartClearBtn: document.getElementById('chartClearBtn'),
        chartPauseBtn: document.getElementById('chartPauseBtn'),
        settingsModal: document.getElementById('yuzuSettingsModal'),
        closeSettings: document.getElementById('closeSettings'),
        saveSettings: document.getElementById('saveSettings'),
        cancelSettings: document.getElementById('cancelSettings'),
        wsPort: document.getElementById('wsPort'),
        autoReconnect: document.getElementById('autoReconnect'),
        maxDataCount: document.getElementById('maxDataCount'),
        autoScroll: document.getElementById('autoScroll')
    };

    // 初始化设置表单
    function initSettings() {
        elements.wsPort.value = state.settings.port;
        elements.autoReconnect.checked = state.settings.autoReconnect;
        elements.maxDataCount.value = state.settings.maxDataCount;
        elements.autoScroll.checked = state.settings.autoScroll;
    }

    // 保存设置
    function saveSettings() {
        state.settings.port = parseInt(elements.wsPort.value);
        state.settings.autoReconnect = elements.autoReconnect.checked;
        state.settings.maxDataCount = parseInt(elements.maxDataCount.value);
        state.settings.autoScroll = elements.autoScroll.checked;
        
        // 保存到 localStorage
        localStorage.setItem('yuzulive_settings', JSON.stringify(state.settings));
        
        elements.settingsModal.classList.remove('show');
    }

    // 加载保存的设置
    function loadSettings() {
        const saved = localStorage.getItem('yuzulive_settings');
        if (saved) {
            try {
                const savedSettings = JSON.parse(saved);
                state.settings = { ...state.settings, ...savedSettings };
            } catch (e) {
                console.error('Failed to load settings:', e);
            }
        }
        initSettings();
    }

    // 更新连接状态显示
    function updateConnectionStatus(status) {
        elements.statusDot.className = 'status-dot ' + status;
        
        const statusTexts = {
            online: '已连接',
            offline: '未连接',
            connecting: '连接中...'
        };
        
        elements.statusText.textContent = statusTexts[status] || status;
        elements.connectBtn.disabled = status !== 'offline';
        elements.disconnectBtn.disabled = status !== 'online';
        state.isConnected = status === 'online';
    }

    // 更新统计信息
    function updateStats() {
        elements.dataCount.textContent = state.dataCount;
        
        if (state.connectionStartTime) {
            const elapsed = Math.floor((Date.now() - state.connectionStartTime) / 1000);
            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;
            elements.connectionTime.textContent = 
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        }
    }

    // 连接 WebSocket
    function connect() {
        if (state.ws) return;
        
        updateConnectionStatus('connecting');
        
        const url = `ws://localhost:${state.settings.port}`;
        state.ws = new WebSocket(url);
        
        state.ws.onopen = function() {
            updateConnectionStatus('online');
            state.connectionStartTime = Date.now();
            state.connectionTimer = setInterval(updateStats, 1000);
            
            // 发送握手消息
            state.ws.send(JSON.stringify({
                type: 'handshake',
                protocol: 'yuzulive',
                version: '1.0'
            }));
        };
        
        state.ws.onmessage = function(event) {
            handleMessage(event.data);
        };
        
        state.ws.onerror = function(error) {
            console.error('WebSocket error:', error);
        };
        
        state.ws.onclose = function(event) {
            console.log('WebSocket closed:', event.code, event.reason);
            updateConnectionStatus('offline');
            
            if (state.connectionTimer) {
                clearInterval(state.connectionTimer);
                state.connectionTimer = null;
            }
            
            state.ws = null;
            state.connectionStartTime = null;
            
            // 自动重连
            if (state.settings.autoReconnect && !event.wasClean) {
                setTimeout(connect, 3000);
            }
        };
    }

    // 断开连接
    function disconnect() {
        if (state.ws) {
            state.ws.close(1000, 'User disconnected');
            state.ws = null;
        }
        updateConnectionStatus('offline');
    }

    // 处理消息
    function handleMessage(data) {
        try {
            const message = JSON.parse(data);
            processMessage(message);
        } catch (e) {
            // 非 JSON 消息，作为纯文本日志处理
            processRawMessage(data);
        }
    }

    // 处理 JSON 消息
    function processMessage(message) {
        state.dataCount++;
        const now = new Date();
        const timestamp = now.toLocaleTimeString('zh-CN', { hour12: false });
        
        // 添加到历史记录
        const record = {
            timestamp,
            channel: message.channel || 'unknown',
            type: message.type || 'data',
            payload: message.payload,
            raw: message
        };
        
        state.dataHistory.push(record);
        
        // 限制历史记录数量
        if (state.dataHistory.length > state.settings.maxDataCount) {
            state.dataHistory.shift();
        }
        
        // 根据显示模式更新视图
        const displayMode = elements.displayMode.value;
        const selectedChannel = elements.channelSelector.value;
        
        // 检查通道过滤
        if (selectedChannel !== 'all' && record.channel !== selectedChannel) {
            return;
        }
        
        // 更新表格
        addTableRow(record);
        
        // 更新图表
        if (!state.chartPaused && displayMode === 'chart') {
            updateChart(record);
        }
        
        // 更新日志
        if (displayMode === 'log') {
            addLogEntry(record);
        }
        
        // 更新指标卡片
        if (displayMode === 'metric') {
            updateMetricCards(record);
        }
        
        // 更新延迟
        if (message.latency) {
            state.latency = message.latency + 'ms';
            elements.latency.textContent = state.latency;
        }
    }

    // 处理原始消息
    function processRawMessage(data) {
        state.dataCount++;
        const now = new Date();
        const timestamp = now.toLocaleTimeString('zh-CN', { hour12: false });
        
        const record = {
            timestamp,
            channel: 'log',
            type: 'raw',
            payload: { message: data },
            raw: data
        };
        
        state.dataHistory.push(record);
        
        if (state.dataHistory.length > state.settings.maxDataCount) {
            state.dataHistory.shift();
        }
        
        addTableRow(record);
        
        if (elements.displayMode.value === 'log') {
            addLogEntry(record);
        }
    }

    // 添加表格行
    function addTableRow(record) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${record.timestamp}</td>
            <td><span class="channel-badge">${record.channel}</span></td>
            <td>${record.type}</td>
            <td class="data-cell">${formatData(record.payload)}</td>
        `;
        
        elements.tableBody.appendChild(row);
        elements.emptyTableMsg.style.display = 'none';
        
        // 自动滚动
        if (state.settings.autoScroll) {
            row.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }
    }

    // 格式化数据显示
    function formatData(data) {
        if (typeof data === 'object') {
            return '<pre class="data-pre">' + JSON.stringify(data, null, 2).substring(0, 200) + '</pre>';
        }
        return String(data).substring(0, 200);
    }

    // 更新图表
    function updateChart(record) {
        const payload = record.payload;
        
        // 尝试提取数值数据
        let value = null;
        
        if (typeof payload === 'number') {
            value = payload;
        } else if (payload && typeof payload === 'object') {
            // 尝试多个常见字段
            const possibleFields = ['value', 'data', 'result', 'loss', 'accuracy', 'score'];
            for (const field of possibleFields) {
                if (payload[field] !== undefined && typeof payload[field] === 'number') {
                    value = payload[field];
                    break;
                }
            }
        }
        
        if (value !== null) {
            state.chartData.labels.push(record.timestamp);
            state.chartData.datasets[0].data.push(value);
            
            // 限制数据点数量
            const maxPoints = 50;
            if (state.chartData.labels.length > maxPoints) {
                state.chartData.labels.shift();
                state.chartData.datasets[0].data.shift();
            }
            
            renderChart();
            elements.emptyChartMsg.style.display = 'none';
        }
    }

    // 渲染图表
    function renderChart() {
        if (!state.chartInstance) {
            // 动态加载 Chart.js
            loadChartJS().then(() => {
                const ctx = document.getElementById('yuzuChart').getContext('2d');
                state.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: state.chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 100
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: '时间'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: '数值'
                                }
                            }
                        }
                    }
                });
            });
        } else {
            state.chartInstance.update('none');
        }
    }

    // 动态加载 Chart.js
    function loadChartJS() {
        return new Promise((resolve) => {
            if (window.Chart) {
                resolve();
                return;
            }
            
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = resolve;
            document.head.appendChild(script);
        });
    }

    // 添加日志条目
    function addLogEntry(record) {
        const logEntry = document.createElement('div');
        logEntry.className = 'log-entry';
        
        // 根据类型添加样式
        if (record.type === 'error' || record.payload?.level === 'error') {
            logEntry.classList.add('error');
        } else if (record.type === 'warning' || record.payload?.level === 'warning') {
            logEntry.classList.add('warning');
        }
        
        logEntry.innerHTML = `
            <span class="log-time">[${record.timestamp}]</span>
            <span class="log-channel">${record.channel}</span>
            <span class="log-content">${formatLogContent(record.payload)}</span>
        `;
        
        elements.logContainer.appendChild(logEntry);
        elements.emptyLogMsg.style.display = 'none';
        
        if (state.settings.autoScroll) {
            logEntry.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }
    }

    // 格式化日志内容
    function formatLogContent(payload) {
        if (typeof payload === 'object') {
            if (payload.message) return payload.message;
            return JSON.stringify(payload);
        }
        return String(payload);
    }

    // 更新指标卡片
    function updateMetricCards(record) {
        const payload = record.payload;
        
        if (payload && typeof payload === 'object' && payload.type === 'metric') {
            const metricId = payload.id || record.channel;
            let card = document.getElementById('metric-' + metricId);
            
            if (!card) {
                card = document.createElement('div');
                card.id = 'metric-' + metricId;
                card.className = 'metric-card';
                elements.metricGrid.appendChild(card);
                elements.emptyMetricMsg.style.display = 'none';
            }
            
            const icon = payload.icon || '📊';
            const value = payload.value || 0;
            const unit = payload.unit || '';
            const change = payload.change;
            const changeClass = change !== undefined ? (change >= 0 ? 'positive' : 'negative') : '';
            
            card.innerHTML = `
                <div class="metric-card-header">
                    <span class="metric-name">${payload.name || metricId}</span>
                    <span class="metric-icon">${icon}</span>
                </div>
                <div class="metric-value">${value.toLocaleString()}<span class="metric-unit">${unit}</span></div>
                ${change !== undefined ? `<div class="metric-change ${changeClass}">${change >= 0 ? '↑' : '↓'} ${Math.abs(change)}%</div>` : ''}
            `;
        }
    }

    // 清空数据
    function clearData() {
        // 清空表格
        elements.tableBody.innerHTML = '';
        elements.emptyTableMsg.style.display = 'block';
        
        // 清空日志
        elements.logContainer.innerHTML = '';
        elements.emptyLogMsg.style.display = 'block';
        
        // 清空指标卡片
        elements.metricGrid.innerHTML = '';
        elements.emptyMetricMsg.style.display = 'block';
        
        // 重置图表
        if (state.chartInstance) {
            state.chartData.labels = [];
            state.chartData.datasets[0].data = [];
            state.chartInstance.update();
            elements.emptyChartMsg.style.display = 'block';
        }
        
        // 重置统计
        state.dataCount = 0;
        state.dataHistory = [];
        updateStats();
    }

    // 切换显示模式
    function switchDisplayMode(mode) {
        // 隐藏所有视图
        document.querySelectorAll('.view-panel').forEach(panel => {
            panel.classList.remove('active');
        });
        
        // 显示选中的视图
        const viewMap = {
            table: elements.tableView,
            chart: elements.chartView,
            log: elements.logView,
            metric: elements.metricView
        };
        
        if (viewMap[mode]) {
            viewMap[mode].classList.add('active');
        }
    }

    // 切换图表暂停状态
    function toggleChartPause() {
        state.chartPaused = !state.chartPaused;
        elements.chartPauseBtn.textContent = state.chartPaused ? '继续更新' : '暂停更新';
    }

    // 清空图表
    function clearChart() {
        if (state.chartInstance) {
            state.chartData.labels = [];
            state.chartData.datasets[0].data = [];
            state.chartInstance.update();
            elements.emptyChartMsg.style.display = 'block';
        }
    }

    // 初始化事件监听
    function initEventListeners() {
        elements.connectBtn.addEventListener('click', connect);
        elements.disconnectBtn.addEventListener('click', disconnect);
        elements.clearBtn.addEventListener('click', clearData);
        elements.channelSelector.addEventListener('change', function() {
            // 过滤逻辑可以在这里添加
        });
        elements.displayMode.addEventListener('change', function(e) {
            switchDisplayMode(e.target.value);
        });
        elements.chartClearBtn.addEventListener('click', clearChart);
        elements.chartPauseBtn.addEventListener('click', toggleChartPause);
        
        // 设置面板
        elements.saveSettings.addEventListener('click', saveSettings);
        elements.cancelSettings.addEventListener('click', function() {
            elements.settingsModal.classList.remove('show');
            initSettings(); // 重置表单
        });
        elements.closeSettings.addEventListener('click', function() {
            elements.settingsModal.classList.remove('show');
        });
        
        // ESC 关闭模态框
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && elements.settingsModal.classList.contains('show')) {
                elements.settingsModal.classList.remove('show');
            }
        });
    }

    // 初始化
    function init() {
        loadSettings();
        initEventListeners();
        updateStats();
        
        // 尝试自动连接（如果之前是连接状态）
        const lastConnected = localStorage.getItem('yuzulive_connected');
        if (lastConnected === 'true' && state.settings.autoReconnect) {
            setTimeout(connect, 1000);
        }
    }

    // 页面卸载时保存状态
    window.addEventListener('beforeunload', function() {
        localStorage.setItem('yuzulive_connected', String(state.isConnected));
    });

    // 启动
    init();
});
</script>

<?php
    return ob_get_clean();
}
?>