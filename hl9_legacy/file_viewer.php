<?php
/**
 * File Viewer HTML
 * Shortcode: [wpcode id="11754"]
 * Current status: unused
 */
?>
<style>
.wp-file-viewer {
    max-width: 800px;
    margin: 20px auto;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
}

.wp-file-viewer .file-viewer-container {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.wp-file-viewer .file-list {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.wp-file-viewer .file-item {
    padding: 10px;
    margin: 5px 0;
    border-radius: 4px;
    display: flex;
    align-items: center;
}

.wp-file-viewer .file-icon {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.wp-file-viewer .file-info {
    margin-top: 20px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.wp-file-viewer .error-message {
    color: #dc3545;
    padding: 10px;
    background: #f8d7da;
    border-radius: 4px;
    margin: 10px 0;
    display: none;
}

.wp-file-viewer .file-input-wrapper {
    display: inline-block;
    position: relative;
    overflow: hidden;
}

.wp-file-viewer .file-input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
}

.wp-file-viewer .directory-input {
    display: none;
}
</style>

<div class="wp-file-viewer">
    <div class="file-input-wrapper">
        <button class="hyplus-button">选择文件</button>
        <input type="file" multiple class="file-input" id="fileInput" />
    </div>
    <div class="file-viewer-container">
        <div class="error-message"></div>
        <div class="file-list"></div>
        <div class="file-info"></div>
    </div>
</div>

<script>
class SafariFileViewer {
    constructor(container) {
        this.container = container;
        this.fileList = container.querySelector('.file-list');
        this.fileInfo = container.querySelector('.file-info');
        this.errorMessage = container.querySelector('.error-message');
        this.fileInput = container.querySelector('.file-input');
        
        console.log('Safari-compatible File Viewer v1.0.0');
        console.log('Created by hyperplasma');
        console.log('Last updated: 2025-04-25 17:13:48 UTC');
        
        this.init();
    }

    init() {
        this.fileInput.addEventListener('change', (e) => this.handleFiles(e.target.files));
    }

    handleFiles(files) {
        this.fileList.innerHTML = '';
        this.fileInfo.innerHTML = '';
        
        if (!files.length) {
            return;
        }

        // 将FileList转换为数组并排序
        const fileArray = Array.from(files).sort((a, b) => {
            return a.name.localeCompare(b.name);
        });

        fileArray.forEach(file => {
            const item = document.createElement('div');
            item.className = 'file-item';
            
            const icon = document.createElement('span');
            icon.className = 'file-icon';
            icon.innerHTML = '📄';
            
            const text = document.createElement('span');
            text.textContent = file.name;
            
            item.appendChild(icon);
            item.appendChild(text);
            
            item.addEventListener('click', () => this.displayFileInfo(file));
            
            this.fileList.appendChild(item);
        });

        // 显示第一个文件的信息
        if (fileArray.length > 0) {
            this.displayFileInfo(fileArray[0]);
        }
    }

    displayFileInfo(file) {
        this.fileInfo.innerHTML = `
            <h3>文件信息</h3>
            <p>名称: ${file.name}</p>
            <p>大小: ${this.formatFileSize(file.size)}</p>
            <p>类型: ${file.type || '未知'}</p>
            <p>最后修改: ${new Date(file.lastModified).toLocaleString()}</p>
        `;

        // 如果是图片，显示预览
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.marginTop = '10px';
                this.fileInfo.appendChild(img);
            };
            reader.readAsDataURL(file);
        }

        // 如果是文本文件，显示内容预览
        if (file.type.startsWith('text/') || file.type === '') {
            const reader = new FileReader();
            reader.onload = (e) => {
                const pre = document.createElement('pre');
                pre.style.maxHeight = '200px';
                pre.style.overflow = 'auto';
                pre.style.marginTop = '10px';
                pre.style.padding = '10px';
                pre.style.background = '#f5f5f5';
                pre.style.borderRadius = '4px';
                pre.textContent = e.target.result;
                this.fileInfo.appendChild(pre);
            };
            reader.readAsText(file);
        }
    }

    formatFileSize(bytes) {
        const units = ['B', 'KB', 'MB', 'GB', 'TB'];
        let size = bytes;
        let unitIndex = 0;
        
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        
        return `${size.toFixed(2)} ${units[unitIndex]}`;
    }

    showError(message) {
        this.errorMessage.textContent = message;
        this.errorMessage.style.display = 'block';
    }
}

// 当DOM加载完成后初始化文件查看器
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.wp-file-viewer');
    containers.forEach(container => new SafariFileViewer(container));
});
</script>