<?php
/**
 * KINA-2 AI 助手组件（优化版）
 * 支持多场景对话历史隔离，集成智谱GLM-4.5大模型服务
 * 短代码参数：id（必传，对话场景标识）、prompt（可选，系统指令）
 * Code type: PHP
 * Current status: unused
 */

// 注册短代码
add_shortcode('ai_chat', 'kina_2_chat_shortcode');

function kina_2_chat_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => '',       
        'prompt' => '',
        'title' => '智能助手',
        'desc' => ''
    ], $atts, 'ai_chat');

    if (empty($atts['id'])) {
        return '<div class="kina-error">错误：必须指定对话场景ID（id参数）</div>';
    }

    $history_key = "kina_chat_history_{$atts['id']}"; // 保留分组键名格式
    
    ob_start();
    ?>
    <div class="kina-chat-container bordered-block">
        <div class="kina-chat-header">
            <div>
                <div class="kina-chat-title"><?php echo esc_html($atts['title']); ?></div>
                <?php if (!empty($atts['desc'])): ?>
                    <p class="kina-chat-desc" style="font-size: 14px; color: #666; margin-top: 5px;"><?php echo esc_html($atts['desc']); ?></p>
                <?php endif; ?>
            </div>
            <button class="kina-clear-btn" 
                    data-history-key="<?php echo esc_attr($history_key); ?>"
                    title="清空历史">🧹</button>
        </div>
        
        <div class="kina-message-list" id="kina-messages-<?php echo esc_attr($atts['id']); ?>">
            <!-- 改为前端从localStorage读取后动态渲染 -->
        </div>

        <div class="kina-input-area">
            <textarea id="kina-input-<?php echo esc_attr($atts['id']); ?>" 
                      class="kina-input" 
                      placeholder="输入问题并回车发送"
                      data-history-key="<?php echo esc_attr($history_key); ?>"
                      data-system-prompt="<?php echo esc_attr($atts['prompt']); ?>"
                      style="resize: none;"></textarea>
            <button class="kina-send-btn">发送</button>
        </div>
    </div>
    <?php

    echo '<style>
        .kina-chat-container {
            background: #fff;
        }
        .kina-chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .kina-chat-title {
            font-size: 20px;
            font-weight: bold; /* 保持加粗 */
            margin: 0; /* 重置默认边距避免干扰布局 */
        }
        .kina-message-list {
            height: 400px;
            overflow-y: auto;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 14px;
        }
        .kina-message {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .kina-user .kina-content { background: #f0f4f8; }
        .kina-ai .kina-content { background: #e3f2fd; }
        .kina-content {
            padding: 10px 15px;
            border-radius: 8px;
            flex: 1;
            line-height: 1.6;
        }
        .kina-input-area {
            display: flex;
            gap: 10px;
        }
        .kina-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
        .kina-send-btn, .kina-clear-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .kina-send-btn {
            background: #1976d2;
            color: white;
        }
        .kina-send-btn:hover { background: #1565c0; }

        .kina-clear-btn {
            background: #ffffff;
            color: #333333; /* 保持文字深色 */
        }
        .kina-clear-btn:hover,  /* 悬停状态 */
        .kina-clear-btn:active, /* 鼠标按下状态 */
        .kina-clear-btn:focus { /* 焦点状态（如键盘导航选中） */
            background: #ffffff; /* 所有交互状态保持白色背景 */
        }
    </style>';

    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const historyKey = document.querySelector(".kina-input").dataset.historyKey;
            const messagesContainer = document.getElementById(`kina-messages-${historyKey.replace("kina_chat_history_", "")}`);
            const inputEl = document.querySelector(".kina-input");
            const sendBtn = document.querySelector(".kina-send-btn");

            function renderHistory() {
                const history = JSON.parse(localStorage.getItem(historyKey) || "[]");
                messagesContainer.innerHTML = history.map(msg => {
                    // 根据消息类型（用户/AI）渲染不同气泡
                    if (msg.type === "user") {
                        return `
                            <div class="kina-message kina-user">
                                <span class="kina-avatar">👤</span>
                                <div class="kina-content">${msg.content}</div>
                            </div>
                        `;
                    } else if (msg.type === "ai") {
                        return `
                            <div class="kina-message kina-ai">
                                <span class="kina-avatar">❤️</span>
                                <div class="kina-content">${msg.content}</div>
                            </div>
                        `;
                    }
                    return "";
                }).join("");
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            renderHistory(); // 初始渲染

            function sendKinaMessage(inputEl) {
                const content = inputEl.value.trim();
                if (!content) return;

                // 禁用输入框和发送按钮，更新按钮文本（修正字符串格式）
                inputEl.disabled = true;
                sendBtn.disabled = true;

                const systemPrompt = inputEl.dataset.systemPrompt;
                const history = JSON.parse(localStorage.getItem(historyKey) || "[]");
                const newUserMessage = { type: "user", content: content }; 
                const newHistory = [...history, newUserMessage];
                
                localStorage.setItem(historyKey, JSON.stringify(newHistory));
                renderHistory(); 
                inputEl.value = "";

                const formData = new URLSearchParams();
                formData.append("action", "kina_get_response");
                formData.append("system_prompt", systemPrompt);
                formData.append("user_message", content);
                formData.append("history", JSON.stringify(history));

                fetch("'.admin_url('admin-ajax.php').'", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error(`网络错误：${response.status}`); // 新增HTTP状态码检查
                    return response.json();
                })
                .then(response => {
                    if (!response.success || !response.data) { // 新增有效响应校验
                        throw new Error(response.data);
                    }
                    const updatedHistory = [...newHistory, { type: "ai", content: response.data }];
                    localStorage.setItem(historyKey, JSON.stringify(updatedHistory));
                    renderHistory(); 
                })
                .catch(error => { // 新增错误捕获
                    alert(`对话异常：${error.message}\n将重置当前对话`);
                    localStorage.removeItem(historyKey); // 清空当前场景历史记录
                    renderHistory(); // 渲染空状态
                })
                .finally(() => {
                    inputEl.disabled = false;
                    sendBtn.disabled = false;
                });
            }

            document.addEventListener("keydown", function(e) {
                if (e.target.classList.contains("kina-input") && e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    sendKinaMessage(e.target);
                }
                if (e.target.classList.contains("kina-input") && e.key === "Escape") {
                    e.target.value = "";
                }
            });

            document.addEventListener("click", function(e) {
                if (e.target.classList.contains("kina-clear-btn")) {
                    if (confirm("确认清空对话历史？")) {
                        localStorage.removeItem(historyKey);
                        messagesContainer.innerHTML = "";
                    }
                }
            });

            document.addEventListener("click", function(e) {
                if (e.target.classList.contains("kina-send-btn")) {
                    sendKinaMessage(e.target.previousElementSibling);
                }
            });
        });
    </script>';

    return ob_get_clean();
}

// 处理消息响应（调整为接收前端传递的历史记录）
add_action('wp_ajax_kina_get_response', 'kina_2_get_response');
add_action('wp_ajax_nopriv_kina_get_response', 'kina_2_get_response');
function kina_2_get_response() {
    $system_prompt = sanitize_text_field($_POST['system_prompt']);
    $user_message = sanitize_text_field($_POST['user_message']);
    $history = json_decode($_POST['history'], true); 

    // 优化：更准确的token计算（包含消息结构字段）
    $max_tokens = 98304; 
    $total_tokens = 0;
    $messages = [];

    // 1. 系统提示消息token计算（包含结构字段）
    if (!empty($system_prompt)) {
        $system_msg = ['role' => 'system', 'content' => $system_prompt];
        $messages[] = $system_msg;
        // 每个消息结构约占15token（"role":"system","content":""） + 内容长度
        $total_tokens += 15 + mb_strlen($system_prompt, 'UTF-8');
    }

    // 2. 历史消息截断（反向遍历保留最近对话）
    $filtered_history = [];
    foreach (array_reverse($history) as $msg) {
        // 每个消息结构约占20token（"role":"user","content":"" 或 "assistant"）
        $msg_tokens = 20 + mb_strlen($msg['content'], 'UTF-8');
        if ($total_tokens + $msg_tokens > $max_tokens) break;
        $filtered_history[] = $msg;
        $total_tokens += $msg_tokens;
    }
    $filtered_history = array_reverse($filtered_history); // 恢复顺序

    // 3. 添加过滤后的历史消息
    foreach ($filtered_history as $msg) {
        $messages[] = [
            'role' => $msg['type'] === "user" ? "user" : "assistant",
            'content' => $msg['content']
        ];
    }

    // 4. 当前用户消息处理（包含结构token）
    $current_msg_tokens = 20 + mb_strlen($user_message, 'UTF-8');
    if ($total_tokens + $current_msg_tokens > $max_tokens) {
        $user_message = mb_substr(
            $user_message, 
            0, 
            $max_tokens - $total_tokens - 20 // 预留结构token
        );
    }
    $messages[] = ['role' => 'user', 'content' => $user_message];

    // 构造API请求体（保持原有逻辑）
    $api_key = 'XXXX'; 
    $api_url = 'https://open.bigmodel.cn/api/paas/v4/chat/completions'; 
    $model_name = 'glm-4.5-flash';
    
    $request_body = [
        'model' => $model_name, 
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 98304
    ];

    // 发送API请求（增强错误处理）
    $response = wp_remote_post($api_url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode($request_body),
        'timeout' => 60
    ]);

    // 处理响应（增强健壮性）
    $api_response = '';
    if (is_wp_error($response)) {
        $api_response = '网络请求失败：' . $response->get_error_message();
    } else {
        $response_body = wp_remote_retrieve_body($response);
        // 新增：校验响应是否为有效JSON
        $body = json_decode($response_body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $api_response = 'API返回非JSON格式：' . substr($response_body, 0, 200); // 截取前200字符
        } else {
            $api_response = $body['choices'][0]['message']['content'] ?? 
                'API调用失败：' . ($body['code'] ?? '未知错误') . ' - ' . ($body['msg'] ?? '无错误信息');
        }
    }

    wp_send_json_success($api_response);
}

// 估算tokens、压缩历史等函数保持不变（注意：这些函数可能需要调整输入参数，因为历史记录现在由前端传递）

// 移除原数据库操作的清空历史函数（不再需要）
// add_action('wp_ajax_kina_clear_history', 'kina_2_clear_history');
// add_action('wp_ajax_nopriv_kina_clear_history', 'kina_2_clear_history');
// function kina_2_clear_history() { ... }