<?php
/**
 * Plugin Name: HyData 稳健纯净版
 * Description: 修复 row 报错，增加行删除，随机字符验证码，垃圾桶悬浮可见。
 * Version: 1.6.0
 * Current status: unused
 */

if (!defined('ABSPATH')) exit;

class HyData_Plugin {
    private static $instance = null;
    private static $scripts_enqueued = false;

    public static function get_instance() {
        if (null === self::$instance) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('hydata', [$this, 'render_shortcode']);
        add_action('wp_ajax_hydata_api', [$this, 'handle_ajax']);
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts(['id' => '', 'name' => '数据表', 'per_page' => '0'], $atts);
        if (empty($atts['id'])) return '';

        if (!self::$scripts_enqueued) {
            $this->enqueue_assets();
            self::$scripts_enqueued = true;
        }

        $uid = 'hydata_' . uniqid();
        ob_start();
        ?>
        <div id="<?php echo esc_attr($uid); ?>" class="hydata-container" 
             data-id="<?php echo esc_attr($atts['id']); ?>" 
             data-per-page="<?php echo esc_attr($atts['per_page']); ?>">
            
            <div class="hydata-status-bar hyplus-unselectable" style="display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; margin-bottom: 10px;">
                <div class="hd-status-left" style="font-size: 16px; font-weight: bold;"><?php echo esc_html($atts['name']); ?></div>
                <div class="hd-status-center">
                    <div class="hydata-pager top-pager" style="display:none;">
                        <span class="pager-btn hd-prev">&lt;</span>
                        <span class="pager-text">1 / 1</span>
                        <span class="pager-btn hd-next">&gt;</span>
                    </div>
                </div>
                <div class="hd-status-right" style="display: flex; justify-content: flex-end; gap: 15px; align-items: center;">
                    <div class="hd-btn hd-export-btn hyplus-scale" style="cursor:pointer; font-size:18px;">📥</div>
                    <?php if (current_user_can('manage_options')): ?>
                    <div class="hd-btn hd-edit-mode-btn hyplus-scale" style="cursor:pointer; font-size:18px;">🖊️</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="hydata-table-wrapper" style="overflow-x: auto;">
                <div class="hd-loading">...</div>
            </div>

            <div class="hydata-pager footer-pager hyplus-unselectable" style="display:none; justify-content:center; margin-top:15px;">
                <span class="pager-btn hd-prev">&lt;</span>
                <span class="pager-text">1 / 1</span>
                <span class="pager-btn hd-next">&gt;</span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function enqueue_assets() {
        add_action('wp_footer', function() {
            ?>
            <style>
                .hydata-container { margin: 20px 0; }
                .hd-edit-active { color: #10b981 !important; }
                .hydata-pager { display: flex; align-items: center; gap: 2px; }
                .pager-btn { cursor: pointer; padding: 0 6px; font-size: 20px; color: #43a5f5; font-weight: bold; line-height: 1; user-select: none; }
                .pager-btn.disabled { opacity: 0.1; cursor: default; }
                .pager-text { cursor: pointer; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 13px; transition: 0.2s; color: #666; }
                .pager-text:hover { background: #f5f5f5; color: #43a5f5; }

                .hydata-editing tbody tr:hover { background: rgba(0,0,0,0.03) !important; cursor: pointer; }

                /* 弹窗核心逻辑 */
                .hd-modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.25); z-index: 99999; display: flex; justify-content: center; align-items: center; }
                .hd-modal { background: #fff; width: 90%; max-width: 420px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; color: #333; }
                .hd-modal-header { padding: 12px 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
                .hd-modal-body { padding: 15px; max-height: 50vh; overflow-y: auto; }
                .hd-modal-footer { padding: 12px; border-top: 1px solid #eee; display: flex; gap: 8px; justify-content: flex-end; flex-wrap: wrap; }
                
                .hd-input-group { margin-bottom: 15px; }
                .hd-label { display: block; font-size: 12px; color: #888; margin-bottom: 4px; font-weight: bold; }
                .hd-input { width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 8px; font-family: inherit; font-size: 14px; box-sizing: border-box; }
                
                /* 垃圾桶：默认透明，悬浮可见 */
                .hd-del-btn { color: #ff4d4f; cursor: pointer; padding: 5px; opacity: 0; transition: opacity 0.2s; font-size: 18px; }
                .hd-modal-header:hover .hd-del-btn { opacity: 1; }
            </style>

            <script>
            (function($) {
                const nonce = '<?php echo wp_create_nonce("hydata_nonce"); ?>';
                const ajaxUrl = '<?php echo admin_url("admin-ajax.php"); ?>';

                // 产生混合验证码
                function getCaptcha(len=4) {
                    const chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
                    let res = '';
                    for (let i=0; i<len; i++) res += chars.charAt(Math.floor(Math.random() * chars.length));
                    return res;
                }

                class HyData {
                    constructor(el) {
                        this.$el = $(el);
                        this.id = this.$el.data('id');
                        this.perPage = parseInt(this.$el.data('per-page')) || 0;
                        this.currentPage = 1;
                        this.data = { columns: [], rows: [] };
                        this.isEditMode = false;
                        this.init();
                    }

                    init() { this.fetch(); this.bind(); }

                    bind() {
                        const self = this;
                        this.$el.on('click', 'th', function() { if (self.isEditMode) self.editHeader(); });
                        this.$el.on('click', '.hd-prev', () => { if(this.currentPage > 1) { this.currentPage--; this.render(); } });
                        this.$el.on('click', '.hd-next', () => { 
                            const max = Math.ceil(this.data.rows.length / this.perPage);
                            if(this.currentPage < max) { this.currentPage++; this.render(); } 
                        });
                        this.$el.on('click', '.hd-edit-mode-btn', function() {
                            self.isEditMode = !self.isEditMode;
                            $(this).text(self.isEditMode ? '✅' : '🖊️').toggleClass('hd-edit-active', self.isEditMode);
                            self.$el.toggleClass('hydata-editing', self.isEditMode);
                            self.render();
                        });
                        this.$el.on('click', 'tbody tr', function() { if (self.isEditMode) self.editRow($(this).data('idx')); });
                    }

                    fetch() {
                        $.post(ajaxUrl, { action: 'hydata_api', method: 'fetch', table_id: this.id, _nonce: nonce }, (res) => {
                            if (res.success) { this.data = res.data || {columns:[], rows:[]}; this.render(); }
                        });
                    }

                    render() {
                        let cols = this.data.columns || [];
                        let rows = this.data.rows || [];
                        if (cols.length === 0) cols = ['[请点击配置属性]'];

                        const total = rows.length;
                        const maxPage = (this.perPage > 0 && total > 0) ? Math.ceil(total / this.perPage) : 1;
                        let displayRows = (this.perPage > 0 && total > 0) ? rows.slice((this.currentPage-1)*this.perPage, this.currentPage*this.perPage) : rows;

                        $('.hydata-pager', this.$el).toggle(this.perPage > 0 && total > 0);
                        $('.pager-text', this.$el).text(`${this.currentPage} / ${maxPage}`);
                        $('.hd-prev', this.$el).toggleClass('disabled', this.currentPage <= 1);
                        $('.hd-next', this.$el).toggleClass('disabled', this.currentPage >= maxPage);

                        let html = '<table><thead><tr>';
                        cols.forEach(c => html += `<th>${c}</th>`);
                        html += '</tr></thead><tbody>';
                        
                        if (displayRows.length === 0) {
                            html += `<tr data-idx="-1">`;
                            cols.forEach(() => html += `<td>---</td>`);
                            html += '</tr>';
                        } else {
                            displayRows.forEach(row => {
                                let originalIdx = this.data.rows.indexOf(row);
                                html += `<tr data-idx="${originalIdx}">`;
                                cols.forEach(c => html += `<td>${row[c] || ''}</td>`);
                                html += '</tr>';
                            });
                        }
                        html += '</tbody></table>';
                        this.$el.find('.hydata-table-wrapper').html(html);
                    }

                    modal(title, body, footer, onDel = null) {
                        $('.hd-modal-overlay').remove();
                        const m = $(`
                            <div class="hd-modal-overlay">
                                <div class="hd-modal">
                                    <div class="hd-modal-header">
                                        <span>${title}</span> 
                                        ${onDel ? '<span class="hd-del-btn">🗑️</span>' : ''}
                                    </div>
                                    <div class="hd-modal-body">${body}</div>
                                    <div class="hd-modal-footer">${footer}</div>
                                </div>
                            </div>`);
                        $('body').append(m);
                        $('.hd-modal-overlay').on('click', function(e) { if(e.target===this) $(this).remove(); });
                        if(onDel) $('.hd-del-btn').on('click', onDel);
                    }

                    editHeader() {
                        const val = (this.data.columns || []).join('\n');
                        const body = `<div class="hd-input-group"><label class="hd-label">列属性 (每行一个)</label><textarea id="hd-h-val" class="hd-input" rows="8">${val}</textarea></div>`;
                        const footer = `<button class="hyplus-nav-link hd-cancel" style="background:#ccc;">取消</button><button class="hyplus-nav-link" id="hd-h-save" style="background:#43a5f5;color:#fff;">保存</button>`;
                        
                        const delFunc = () => {
                            const code = getCaptcha();
                            if(prompt(`验证码 [ ${code} ] :`) === code) {
                                if(confirm('清空全表？')) this.api('del_table', {}, () => location.reload());
                            }
                        };

                        this.modal('配置结构', body, footer, delFunc);
                        
                        $('#hd-h-save').on('click', () => {
                            const newCols = $('#hd-h-val').val().split('\n').map(s => s.trim()).filter(s => s);
                            this.api('save_cols', { cols: newCols }, () => { $('.hd-modal-overlay').remove(); this.fetch(); });
                        });
                        $('.hd-cancel').on('click', () => $('.hd-modal-overlay').remove());
                    }

                    editRow(idx) {
                        if (this.data.columns.length === 0) { this.editHeader(); return; }
                        const row = (idx !== -1) ? this.data.rows[idx] : {};
                        let body = '';
                        this.data.columns.forEach(c => {
                            body += `<div class="hd-input-group"><label class="hd-label">${c}</label><textarea class="hd-input hd-cell" data-col="${c}" rows="1">${row[c]||''}</textarea></div>`;
                        });
                        const footer = `
                            <button class="hyplus-nav-link" id="hd-r-up">↑插</button>
                            <button class="hyplus-nav-link" id="hd-r-down">↓插</button>
                            <div style="flex:1"></div>
                            <button class="hyplus-nav-link" id="hd-r-m-up">上移</button>
                            <button class="hyplus-nav-link" id="hd-r-m-down">下移</button>
                            <button class="hyplus-nav-link" id="hd-r-save" style="background:#43a5f5;color:#fff;">保存</button>
                        `;

                        const delFunc = (idx === -1) ? null : () => {
                            const code = getCaptcha();
                            if(prompt(`验证码 [ ${code} ] :`) === code) {
                                if(confirm('删除此行？')) this.api('del_row', { idx: idx }, () => { $('.hd-modal-overlay').remove(); this.fetch(); });
                            }
                        };

                        this.modal(idx===-1?'添加数据':'编辑数据', body, footer, delFunc);
                        
                        const collect = () => { let o = {}; $('.hd-cell').each(function(){ let v = $(this).val(); if(v) o[$(this).data('col')] = v; }); return o; };
                        $('#hd-r-save').on('click', () => { this.api('save_row', { idx: idx, row: collect() }, () => { $('.hd-modal-overlay').remove(); this.fetch(); }); });
                        $('#hd-r-m-up').on('click', () => { this.api('move', { idx: idx, dir: -1, row: collect() }, () => { $('.hd-modal-overlay').remove(); this.fetch(); }); });
                        $('#hd-r-m-down').on('click', () => { this.api('move', { idx: idx, dir: 1, row: collect() }, () => { $('.hd-modal-overlay').remove(); this.fetch(); }); });
                        $('#hd-r-up').on('click', () => { this.api('insert', { idx: idx, row: collect(), pos: 'up' }, () => { $('.hd-modal-overlay').remove(); this.fetch(); }); });
                        $('#hd-r-down').on('click', () => { this.api('insert', { idx: idx, row: collect(), pos: 'down' }, () => { $('.hd-modal-overlay').remove(); this.fetch(); }); });
                    }

                    api(m, p, cb) {
                        $.post(ajaxUrl, { action: 'hydata_api', method: m, table_id: this.id, _nonce: nonce, ...p }, (res) => { if(res.success) cb(); });
                    }
                }

                $(document).ready(() => { $('.hydata-container').each(function(){ new HyData(this); }); });
            })(jQuery);
            </script>
            <?php
        });
    }

    public function handle_ajax() {
        check_ajax_referer('hydata_nonce', '_nonce');
        $m = $_POST['method'];
        $opt = 'hydata_store_' . sanitize_text_field($_POST['table_id']);
        $db = get_option($opt, ['columns' => [], 'rows' => []]);

        if ($m === 'fetch') wp_send_json_success($db);
        if (!current_user_can('manage_options')) wp_send_json_error();

        // 关键修复：使用 isset() 或默认空数组，避免 Undefined array key "row"
        $post_row = isset($_POST['row']) ? (array)$_POST['row'] : [];
        $row_data = array_map('wp_kses_post', $post_row);

        if ($m === 'save_cols') {
            $db['columns'] = isset($_POST['cols']) ? (array)$_POST['cols'] : [];
            update_option($opt, $db);
        } elseif ($m === 'save_row') {
            $idx = intval($_POST['idx']);
            if ($idx === -1) $db['rows'][] = $row_data; else $db['rows'][$idx] = $row_data;
            update_option($opt, $db);
        } elseif ($m === 'move') {
            $idx = intval($_POST['idx']); $dir = intval($_POST['dir']);
            $db['rows'][$idx] = $row_data;
            $target = $idx + $dir;
            if ($target >= 0 && $target < count($db['rows'])) {
                $tmp = $db['rows'][$target]; $db['rows'][$target] = $db['rows'][$idx]; $db['rows'][$idx] = $tmp;
            }
            update_option($opt, $db);
        } elseif ($m === 'insert') {
            $idx = intval($_POST['idx']); $pos = $_POST['pos'];
            if ($idx !== -1) $db['rows'][$idx] = $row_data;
            $new_idx = ($pos === 'up') ? max(0, $idx) : ($idx + 1);
            array_splice($db['rows'], $new_idx, 0, [[]]);
            update_option($opt, $db);
        } elseif ($m === 'del_row') {
            $idx = intval($_POST['idx']);
            if (isset($db['rows'][$idx])) array_splice($db['rows'], $idx, 1);
            update_option($opt, $db);
        } elseif ($m === 'del_table') {
            delete_option($opt);
        }
        wp_send_json_success();
    }
}
HyData_Plugin::get_instance();