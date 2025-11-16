/**
 * HyLightbox - A lightweight image viewer with zoom, drag, and drawing capabilities.
 * Version: 1.0
 * Code type: JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
	// 创建 Lightbox 元素
	const lightboxWrapper = document.createElement('div');
	lightboxWrapper.className = 'my-lightbox-wrapper';
	lightboxWrapper.style.cssText = `
display: none;
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
background-color: rgba(0, 0, 0, 0.8);
z-index: 9999;
touch-action: none;
user-select: none;
-webkit-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
`;

	const lightboxImage = document.createElement('img');
	lightboxImage.className = 'my-lightbox-image';
	lightboxImage.style.cssText = `
position: absolute;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);
max-width: 90%;
max-height: 90%;
cursor: grab;
transition: transform 0.2s ease;
touch-action: none;
`;

	// 控制按钮的基础样式
	const buttonBasicStyle = `
border: none;
color: white;
font-size: 24px;
cursor: pointer;
width: 30px;
height: 30px;
display: inline-flex;
align-items: center;
justify-content: center;
padding: 0;
line-height: 1;
transition: opacity 0.2s;
background: none;
text-align: center;
vertical-align: middle;
`;

	// 创建控制栏
	const controlBar = document.createElement('div');
	controlBar.className = 'my-lightbox-control-bar';
	controlBar.style.cssText = `
position: fixed;
top: 20px;
left: 50%;
transform: translateX(-50%);
background-color: rgba(0, 0, 0, 0.5);
padding: 5px 15px;
border-radius: 20px;
display: flex;
align-items: center;
gap: 15px;
z-index: 10000;
`;

	// 缩小按钮
	const zoomOutBtn = document.createElement('button');
	zoomOutBtn.innerHTML = '−';
	zoomOutBtn.style.cssText = buttonBasicStyle + `
display: inline-flex;
align-items: center;
justify-content: center;
line-height: 0;
padding-bottom: 3px;
`;

	// 缩放比例显示
	const zoomLevel = document.createElement('span');
	zoomLevel.style.cssText = `
color: white;
font-size: 14px;
font-family: Arial, sans-serif;
min-width: 60px;
text-align: center;
display: inline-block;
line-height: 30px;
`;

	// 放大按钮
	const zoomInBtn = document.createElement('button');
	zoomInBtn.innerHTML = '+';
	zoomInBtn.style.cssText = buttonBasicStyle + `
display: inline-flex;
align-items: center;
justify-content: center;
line-height: 0;
padding-bottom: 2px;
`;

	// 帮助按钮容器
	const helpButtonContainer = document.createElement('div');
	helpButtonContainer.style.cssText = `
position: absolute;
top: 20px;
left: 20px;
background-color: rgba(0, 0, 0, 0.5);
border-radius: 20px;
padding: 5px 15px;
z-index: 10000;
`;

	// 帮助按钮
	const helpButton = document.createElement('button');
	helpButton.innerHTML = '?';
	helpButton.style.cssText = buttonBasicStyle + `
display: inline-flex;
align-items: center;
justify-content: center;
line-height: 0;
padding-bottom: 2px;
`;

	// 帮助提示框
	const helpTooltip = document.createElement('div');
	helpTooltip.style.cssText = `
display: none;
position: absolute;
top: 45px;
left: 0;
background-color: rgba(0, 0, 0, 0.5);
padding: 15px 20px;
border-radius: 10px;
color: white;
font-size: 14px;
width: 245px;
z-index: 9999;
line-height: 1.1;
`;

	helpTooltip.innerHTML = `
<p style="margin: 0 0 4px 0">键盘快捷键：</p>
<ul style="margin: 0; padding-left: 20px; list-style-type: disc;">
<li style="margin: 0 0 2px 0; list-style-type: disc;">+ : 放大</li>
<li style="margin: 0 0 2px 0; list-style-type: disc;">- : 缩小</li>
<li style="margin: 0 0 2px 0; list-style-type: disc;">0 : 还原</li>
<li style="margin: 0 0 2px 0; list-style-type: disc;">R : 顺时针旋转45°</li>
<li style="margin: 0 0 2px 0; list-style-type: disc;">T : 逆时针旋转45°</li>
<li style="margin: 0 0 2px 0; list-style-type: disc;">ESC : 退出HyLightbox</li>
<li style="margin: 0 0 2px 0; list-style-type: disc;">1 : 回到鼠标模式</li>
<li style="margin: 0 0 2px 0; list-style-type: disc;">2 : 进入画笔模式（尚存bug）</li>
</ul>
<p style="margin: 4px 0 0 0">移动端可使用双指缩放</p>
`;

	// 关闭按钮容器
	const closeButtonContainer = document.createElement('div');
	closeButtonContainer.style.cssText = `
position: absolute;
top: 20px;
right: 20px;
background-color: rgba(0, 0, 0, 0.5);
border-radius: 20px;
padding: 5px 15px;
z-index: 10000;
`;

	// 关闭按钮
	const closeButton = document.createElement('button');
	closeButton.className = 'my-lightbox-close';
	closeButton.innerHTML = '×';
	closeButton.style.cssText = buttonBasicStyle + `
display: inline-flex;
align-items: center;
justify-content: center;
line-height: 0;
padding-bottom: 2px;
`;

	// 在按钮创建部分添加旋转按钮容器和按钮
	const rotateButtonContainer = document.createElement('div');
	rotateButtonContainer.style.cssText = `
position: absolute;
bottom: 20px;
left: 20px;
background-color: rgba(0, 0, 0, 0.5);
border-radius: 20px;
padding: 5px 15px;
z-index: 10000;
`;

	const rotateButton = document.createElement('button');
	rotateButton.innerHTML = '↻';
	rotateButton.style.cssText = buttonBasicStyle + `
display: inline-flex;
align-items: center;
justify-content: center;
line-height: 0;
padding-bottom: 2px;
`;

	// 在按钮创建部分，在rotateButton之后添加
	const rotateCounterButton = document.createElement('button');
	rotateCounterButton.innerHTML = '↺';
	rotateCounterButton.style.cssText = buttonBasicStyle + `
display: inline-flex;
align-items: center;
justify-content: center;
line-height: 0;
padding-bottom: 2px;
margin-left: 10px;  // 添加左边距，与顺时针按钮分开
`;

	// 组装控制栏和按钮
	controlBar.appendChild(zoomOutBtn);
	controlBar.appendChild(zoomLevel);
	controlBar.appendChild(zoomInBtn);
	closeButtonContainer.appendChild(closeButton);
	helpButtonContainer.appendChild(helpButton);
	helpButtonContainer.appendChild(helpTooltip);
	rotateButtonContainer.appendChild(rotateButton);
	rotateButtonContainer.appendChild(rotateCounterButton);

	// 组装 Lightbox
	lightboxWrapper.appendChild(lightboxImage);
	lightboxWrapper.appendChild(controlBar);
	lightboxWrapper.appendChild(helpButtonContainer);
	lightboxWrapper.appendChild(closeButtonContainer);
	lightboxWrapper.appendChild(rotateButtonContainer);
	document.body.appendChild(lightboxWrapper);

	// 创建画布元素用于绘图
	const canvas = document.createElement('canvas');
	canvas.style.cssText = `
position: absolute;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);
max-width: 90%;
max-height: 90%;
pointer-events: none;
`;
	lightboxWrapper.appendChild(canvas);
	const ctx = canvas.getContext('2d');

	// 状态变量
	let isDragging = false;
	let currentX = 0;
	let currentY = 0;
	let initialX = 0;
	let initialY = 0;
	let xOffset = 0;
	let yOffset = 0;
	let scale = 1;
	let isOriginalSize = false;
	let originalWidth = 0;
	let originalHeight = 0;
	let isSingleTouch = false;
	let isHelpVisible = false;
	const MAX_SCALE = 1000; // 100000% 的缩放上限
	let isDrawing = false; // 是否处于绘图模式
	let isPainting = false; // 是否正在绘图
	let rotationAngle = 0;

	// 性能优化：使用事件委托，只绑定一次事件
	document.body.addEventListener('click', function (e) {
		const target = e.target;
		if (target.tagName === 'IMG' && !target.classList.contains('my-lightbox-image')) {
			e.preventDefault();
			openLightbox(target.src);
		}
	});

	document.body.addEventListener('mouseenter', function (e) {
		const target = e.target;
		if (target.tagName === 'IMG' && !target.classList.contains('my-lightbox-image')) {
			target.style.cursor = 'zoom-in';
		}
	}, true);

	document.body.addEventListener('mouseleave', function (e) {
		const target = e.target;
		if (target.tagName === 'IMG' && !target.classList.contains('my-lightbox-image')) {
			target.style.cursor = 'auto';
		}
	}, true);

	// 打开 Lightbox
	function openLightbox(src) {
		lightboxWrapper.style.display = 'block';
		document.body.style.overflow = 'hidden';

		const tempImg = new Image();
		tempImg.onload = function () {
			originalWidth = this.width;
			originalHeight = this.height;
			lightboxImage.src = src;
			resetImagePosition();
			// 调整画布大小
			canvas.width = lightboxImage.width;
			canvas.height = lightboxImage.height;
		};
		tempImg.src = src;
	}

	// 关闭 Lightbox
	function closeLightbox() {
		lightboxWrapper.style.display = 'none';
		document.body.style.overflow = '';
		resetImagePosition();
		isHelpVisible = false;
		helpTooltip.style.display = 'none';
		// 清空画布
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		isDrawing = false;
	}

	// 更新图片变换和显示比例
	function updateImageTransform() {
		lightboxImage.style.transform = `translate(calc(-50% + ${xOffset}px), calc(-50% + ${yOffset}px)) scale(${scale}) rotate(${rotationAngle}deg)`;
		canvas.style.transform = `translate(calc(-50% + ${xOffset}px), calc(-50% + ${yOffset}px)) scale(${scale}) rotate(${rotationAngle}deg)`;
		updateZoomLevel();
	}

	// 更新缩放比例显示
	function updateZoomLevel() {
		const percentage = Math.round(scale * 100);
		zoomLevel.textContent = `${percentage}%`;
	}

	// 重置图片位置和缩放
	function resetImagePosition() {
		xOffset = 0;
		yOffset = 0;
		scale = 1;
		rotationAngle = 0;
		isOriginalSize = false;
		updateImageTransform();
		lightboxImage.style.cursor = 'grab';
	}

	// 缩放控制函数
	function zoomIn() {
		// 根据当前缩放级别动态调整步进量
		let zoomFactor;
		if (scale < 1) {
			zoomFactor = 1.05;
		} else if (scale < 10) {
			zoomFactor = 1.125;
		} else if (scale < 100) {
			zoomFactor = 1.25;
		} else {
			zoomFactor = 1.5;
		}

		scale = Math.min(scale * zoomFactor, MAX_SCALE);
		updateImageTransform();
	}

	function zoomOut() {
		// 根据当前缩放级别动态调整步进量
		let zoomFactor;
		if (scale <= 1) {
			zoomFactor = 1.05;
		} else if (scale <= 10) {
			zoomFactor = 1.125;
		} else if (scale <= 100) {
			zoomFactor = 1.25;
		} else {
			zoomFactor = 1.5;
		}

		scale = Math.max(scale / zoomFactor, 0.01);
		updateImageTransform();
	}

	// 拖动处理函数
	function dragStart(e) {
		if (isDrawing) return; // 如果处于绘图模式，不处理拖动
		if (e.type === "touchstart") {
			if (e.touches.length === 2) {
				isSingleTouch = false;
				return;
			}
			isSingleTouch = true;
			initialX = e.touches[0].clientX - xOffset;
			initialY = e.touches[0].clientY - yOffset;
		} else {
			initialX = e.clientX - xOffset;
			initialY = e.clientY - yOffset;
		}

		if (e.target === lightboxImage) {
			isDragging = true;
			lightboxImage.style.transition = 'none';
			lightboxImage.style.cursor = 'grabbing';
		}
	}

	function drag(e) {
		if (!isDragging || isDrawing) return; // 如果处于绘图模式，不处理拖动
		e.preventDefault();

		if (e.type === "touchmove") {
			if (e.touches.length === 2 || !isSingleTouch) return;
			currentX = e.touches[0].clientX - initialX;
			currentY = e.touches[0].clientY - initialY;
		} else {
			currentX = e.clientX - initialX;
			currentY = e.clientY - initialY;
		}

		xOffset = currentX;
		yOffset = currentY;
		updateImageTransform();
	}

	function dragEnd() {
		isDragging = false;
		isSingleTouch = false;
		lightboxImage.style.transition = 'transform 0.2s ease';
		lightboxImage.style.cursor = 'grab';
	}

	// 处理缩放
	let initialDistance = 0;
	let initialScale = 1;

	function getDistance(touch1, touch2) {
		return Math.hypot(
			touch2.clientX - touch1.clientX,
			touch2.clientY - touch1.clientY
		);
	}

	function handlePinchZoom(e) {
		if (isSingleTouch) {
			drag(e);
			return;
		}

		e.preventDefault();
		e.stopPropagation();

		if (e.touches.length === 2) {
			const touch1 = e.touches[0];
			const touch2 = e.touches[1];
			const currentDistance = getDistance(touch1, touch2);

			if (initialDistance === 0) {
				initialDistance = currentDistance;
				initialScale = scale;
			} else {
				const newScale = Math.min(Math.max(initialScale * (currentDistance / initialDistance), 0.01), MAX_SCALE);
				scale = newScale;
				updateImageTransform();
			}
		}
	}

	function handleTouchEnd() {
		initialDistance = 0;
		dragEnd();
	}

	// 按钮事件和悬停效果
	[zoomInBtn, zoomOutBtn, closeButton, helpButton, rotateButton, rotateCounterButton].forEach(btn => {
		btn.addEventListener('mouseenter', function () {
			this.style.opacity = '0.8';
		});
		btn.addEventListener('mouseleave', function () {
			this.style.opacity = '1';
		});
	});

	// 帮助按钮事件
	helpButton.addEventListener('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		isHelpVisible = !isHelpVisible;
		helpTooltip.style.display = isHelpVisible ? 'block' : 'none';
	});

	// 点击其他区域关闭帮助提示
	lightboxWrapper.addEventListener('click', function (e) {
		if (isHelpVisible && !helpTooltip.contains(e.target) && e.target !== helpButton) {
			isHelpVisible = false;
			helpTooltip.style.display = 'none';
		}
		if (e.target === lightboxWrapper) {
			closeLightbox();
		}
	});

	// 添加键盘事件监听
	document.addEventListener('keydown', function (e) {
		if (lightboxWrapper.style.display === 'block') {
			switch (e.key) {
				case '+':
				case '=':
					e.preventDefault();
					zoomIn();
					break;
				case '-':
				case '_':
					e.preventDefault();
					zoomOut();
					break;
				case '0':
					e.preventDefault();
					resetImagePosition();
					break;
				case 'Escape':
					e.preventDefault();
					closeLightbox();
					break;
				case '1':
					e.preventDefault();
					isDrawing = false;
					lightboxImage.style.cursor = 'grab';
					canvas.style.pointerEvents = 'none';
					break;
				case '2':
					e.preventDefault();
					isDrawing = true;
					lightboxImage.style.cursor = 'crosshair';
					canvas.style.pointerEvents = 'auto';
					break;
				case 'r':
				case 'R':
					e.preventDefault();
					rotationAngle += 45;
					updateImageTransform();
					break;
				case 't':
				case 'T':
					e.preventDefault();
					rotationAngle -= 45;
					updateImageTransform();
					break;
				case 'ArrowUp': yOffset += 30; updateImageTransform(); break;
				case 'ArrowDown': yOffset -= 30; updateImageTransform(); break;
				case 'ArrowLeft': xOffset += 30; updateImageTransform(); break;
				case 'ArrowRight': xOffset -= 30; updateImageTransform(); break;
			}
		}
	});

	// 事件监听
	zoomInBtn.addEventListener('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		zoomIn();
	});

	zoomOutBtn.addEventListener('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		zoomOut();
	});

	rotateButton.addEventListener('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		rotationAngle += 45;
		updateImageTransform();
	});

	rotateCounterButton.addEventListener('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		rotationAngle -= 45;
		updateImageTransform();
	});

	// 鼠标事件
	lightboxWrapper.addEventListener('mousedown', dragStart, { passive: false });
	lightboxWrapper.addEventListener('mousemove', drag, { passive: false });
	lightboxWrapper.addEventListener('mouseup', dragEnd);
	lightboxWrapper.addEventListener('mouseleave', dragEnd);

	// 鼠标样式事件
	lightboxImage.addEventListener('mouseenter', function () {
		if (!isDragging && !isDrawing) {
			this.style.cursor = 'grab';
		}
	});

	lightboxImage.addEventListener('mouseleave', function () {
		if (!isDragging && !isDrawing) {
			this.style.cursor = 'grab';
		}
	});

	// 触摸事件
	lightboxWrapper.addEventListener('touchstart', dragStart, { passive: false });
	lightboxWrapper.addEventListener('touchmove', handlePinchZoom, { passive: false });
	lightboxWrapper.addEventListener('touchend', handleTouchEnd);

	// 关闭按钮事件
	closeButton.addEventListener('click', closeLightbox);

	// 阻止默认行为
	lightboxImage.addEventListener('dragstart', function (e) {
		e.preventDefault();
	});

	// 禁用页面缩放
	document.addEventListener('touchmove', function (e) {
		if (lightboxWrapper.style.display === 'block') {
			e.preventDefault();
		}
	}, { passive: false });

	// 绘图相关函数
	function startPainting(e) {
		if (!isDrawing) return;
		isPainting = true;
		const rect = canvas.getBoundingClientRect();
		const x = (e.clientX - rect.left) / scale - xOffset / scale;
		const y = (e.clientY - rect.top) / scale - yOffset / scale;
		ctx.beginPath();
		ctx.moveTo(x, y);
		ctx.strokeStyle = 'red';
		ctx.lineWidth = 2;
	}

	function paint(e) {
		if (!isPainting || !isDrawing) return;
		const rect = canvas.getBoundingClientRect();
		const x = (e.clientX - rect.left) / scale - xOffset / scale;
		const y = (e.clientY - rect.top) / scale - yOffset / scale;
		ctx.lineTo(x, y);
		ctx.stroke();
	}

	function stopPainting() {
		if (!isDrawing) return;
		isPainting = false;
		ctx.closePath();
	}

	// 绘图事件监听
	canvas.addEventListener('mousedown', startPainting);
	canvas.addEventListener('mousemove', paint);
	canvas.addEventListener('mouseup', stopPainting);
	canvas.addEventListener('mouseout', stopPainting);

	canvas.addEventListener('touchstart', function (e) {
		startPainting(e.touches[0]);
	});
	canvas.addEventListener('touchmove', function (e) {
		paint(e.touches[0]);
	});
	canvas.addEventListener('touchend', stopPainting);
});
